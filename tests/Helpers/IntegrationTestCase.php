<?php
declare(strict_types=1);

namespace Tests\Helpers;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests against functions that manage their own
 * transactions (e.g. redeem_premium_key, _recreate_subscription_for_key).
 *
 * MySQL doesn't nest transactions, so wrapping the test in beginTransaction()
 * would conflict with the function's own commit/rollback. Instead we track
 * fixture rows and clean them up in tearDown().
 */
abstract class IntegrationTestCase extends TestCase
{
    protected PDO $pdo;

    /** @var string[] */
    private array $seededKeys = [];
    /** @var string[] */
    private array $seededSubscriptions = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = $GLOBALS['pdo'];
    }

    protected function tearDown(): void
    {
        if (!empty($this->seededKeys)) {
            $placeholders = implode(',', array_fill(0, count($this->seededKeys), '?'));
            $this->pdo->prepare(
                "DELETE FROM premium_subscription_keys WHERE subscription_key IN ($placeholders)"
            )->execute($this->seededKeys);
        }
        if (!empty($this->seededSubscriptions)) {
            $placeholders = implode(',', array_fill(0, count($this->seededSubscriptions), '?'));
            $this->pdo->prepare(
                "DELETE FROM premium_subscriptions WHERE subscription_id IN ($placeholders)"
            )->execute($this->seededSubscriptions);
        }
        // Catch-all for subscriptions created by the function under test
        // (transaction_id holds the original key for free-key redemptions).
        if (!empty($this->seededKeys)) {
            $placeholders = implode(',', array_fill(0, count($this->seededKeys), '?'));
            $this->pdo->prepare(
                "DELETE FROM premium_subscriptions WHERE transaction_id IN ($placeholders)"
            )->execute($this->seededKeys);
        }
        parent::tearDown();
    }

    /**
     * Register a subscription_id (created by the function under test, not by
     * the seed helpers) so it'll be cleaned up in tearDown().
     */
    protected function trackSubscription(string $subscriptionId): void
    {
        $this->seededSubscriptions[] = $subscriptionId;
    }

    protected function seedPremiumKey(int $durationMonths = 1, ?string $email = null): string
    {
        $key = 'PREM-TEST-' . bin2hex(random_bytes(4));
        $stmt = $this->pdo->prepare(
            'INSERT INTO premium_subscription_keys
             (subscription_key, email, duration_months, created_at)
             VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$key, $email, $durationMonths]);
        $this->seededKeys[] = $key;
        return $key;
    }

    protected function seedRedeemedKey(string $deviceId, string $subscriptionId, int $durationMonths = 12): string
    {
        $key = $this->seedPremiumKey($durationMonths);
        $stmt = $this->pdo->prepare(
            'UPDATE premium_subscription_keys
             SET redeemed_at = NOW(), device_id = ?, subscription_id = ?
             WHERE subscription_key = ?'
        );
        $stmt->execute([$deviceId, $subscriptionId, $key]);
        $this->seededSubscriptions[] = $subscriptionId;
        return $key;
    }

    protected function seedSubscription(
        string $subscriptionId,
        string $endDate,
        string $status = 'active',
        string $billingCycle = 'yearly',
        ?string $transactionId = null
    ): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO premium_subscriptions
             (subscription_id, billing_cycle, amount, currency, start_date, end_date,
              status, payment_method, transaction_id, auto_renew, environment, created_at)
             VALUES (?, ?, 0.00, 'CAD', NOW(), ?, ?, 'free_key', ?, 0, 'sandbox', NOW())"
        );
        $stmt->execute([$subscriptionId, $billingCycle, $endDate, $status, $transactionId ?? $subscriptionId]);
        $this->seededSubscriptions[] = $subscriptionId;
    }
}
