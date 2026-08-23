<?php
declare(strict_types=1);

namespace Tests\Integration\PublicApi;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Base for the /v1 integration tests.
 *
 * These exist because the unit suite covers only pure functions, which left the
 * riskiest parts of the API, authentication, the idempotency claim, batch
 * atomicity, and environment scoping, verified by hand exactly once and by
 * nothing thereafter.
 *
 * Each test gets its own account and keys, and every row it creates is torn
 * down afterwards, so tests do not see each other's data.
 */
abstract class ApiIntegrationTestCase extends TestCase
{
    protected PDO $pdo;
    protected int $accountId;
    protected string $accountPublicId;
    protected string $writeKey;
    protected string $readKey;

    /** Account in the OTHER environment, for proving the scoping holds. */
    protected int $otherEnvAccountId;
    protected string $otherEnvKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = $GLOBALS['pdo'];
        $this->ensureSchema();

        [$this->accountId, $this->accountPublicId] = $this->makeAccount(api_env());
        $this->writeKey = $this->makeKey($this->accountId, 'read,write', api_env());
        $this->readKey = $this->makeKey($this->accountId, 'read', api_env());

        $otherEnv = api_env() === 'production' ? 'sandbox' : 'production';
        [$this->otherEnvAccountId] = $this->makeAccount($otherEnv);
        $this->otherEnvKey = $this->makeKey($this->otherEnvAccountId, 'read,write', $otherEnv);

        $this->resetRequestState();
    }

    protected function tearDown(): void
    {
        // ON DELETE CASCADE takes every row that hangs off the account with it.
        $stmt = $this->pdo->prepare('DELETE FROM api_accounts WHERE id IN (?, ?)');
        $stmt->execute([$this->accountId, $this->otherEnvAccountId]);
        $this->resetRequestState();
        parent::tearDown();
    }

    /**
     * The api_* tables are part of mysql_schema.sql, but the test database is
     * not rebuilt from it, so create them on demand rather than making every
     * developer remember a manual step.
     */
    private function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        $sql = (string) file_get_contents(PROJECT_ROOT . '/mysql_schema.sql');
        foreach (preg_split('/;\s*\n/', $sql) as $statement) {
            $statement = trim($statement);
            if (stripos($statement, 'CREATE TABLE') === false || !str_contains($statement, 'api_')) {
                continue;
            }
            if (!preg_match('/CREATE TABLE IF NOT EXISTS (api_\w+)/', $statement)) {
                continue;
            }
            $this->pdo->exec($statement);
        }
        $done = true;
    }

    /** @return array{0:int,1:string} */
    protected function makeAccount(string $environment): array
    {
        $publicId = api_generate_id('acct');
        $this->pdo->prepare(
            'INSERT INTO api_accounts (public_id, owner_identity_hash, company_uid, display_name, environment)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([
            $publicId,
            hash('sha256', 'phpunit-' . $publicId),
            'phpunit-' . bin2hex(random_bytes(6)),
            'PHPUnit Co',
            $environment,
        ]);

        return [(int) $this->pdo->lastInsertId(), $publicId];
    }

    protected function makeKey(int $accountId, string $scopes, string $environment): string
    {
        $secret = api_generate_secret_key();
        $this->pdo->prepare(
            'INSERT INTO api_keys (account_id, public_id, key_hash, key_hint, label, scopes, environment)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $accountId,
            api_generate_id('key'),
            hash('sha256', $secret),
            api_key_hint($secret),
            'phpunit',
            $scopes,
            $environment,
        ]);

        return $secret;
    }

    /** Present a key the way a real caller would. */
    protected function authenticateAs(string $secret): array
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $secret;
        return api_authenticate();
    }

    /** Superglobals leak between tests otherwise, since there is no real request. */
    protected function resetRequestState(): void
    {
        unset(
            $_SERVER['HTTP_AUTHORIZATION'],
            $_SERVER['HTTP_X_API_KEY'],
            $_SERVER['HTTP_IDEMPOTENCY_KEY'],
            $_SERVER['HTTP_ARGO_VERSION']
        );
        $_GET = [];
    }

    /** Insert a resource row directly, bypassing the HTTP layer. */
    protected function seedObject(string $table, int $accountId, array $columns, string $environment = null): string
    {
        $publicId = $columns['public_id'] ?? api_generate_id('cus');
        $columns['public_id'] = $publicId;
        $columns['account_id'] = $accountId;
        $columns['environment'] = $environment ?? api_env();

        $names = implode(', ', array_keys($columns));
        $holders = implode(', ', array_fill(0, count($columns), '?'));
        $this->pdo->prepare("INSERT INTO $table ($names) VALUES ($holders)")
            ->execute(array_values($columns));

        return $publicId;
    }

    /** Run a handler that ends by calling api_json, and return [status, payload]. */
    protected function capture(callable $fn): array
    {
        try {
            $fn();
        } catch (\ApiResponseSent $e) {
            return [$e->status, $e->payload];
        }
        $this->fail('Expected the handler to send a response, but it returned normally.');
    }
}
