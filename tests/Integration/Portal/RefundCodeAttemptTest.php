<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

/**
 * The refund confirmation code allows five guesses. The limit has to hold
 * when several requests read the same attempt count at once.
 */
final class RefundCodeAttemptTest extends DatabaseTestCase
{
    private function seedCode(int $attempts): int
    {
        $companyId = $this->seedPortalCompany();
        $this->pdo->prepare(
            "INSERT INTO refund_requests
             (company_id, invoice_id, invoice_number, provider, provider_payment_id,
              amount_cents, currency, state)
             VALUES (?, 'INV-CODE-1', 'INV-CODE-1', 'stripe', 'pi_code_test', 1000, 'USD', 'pending_code')"
        )->execute([$companyId]);
        $requestId = (int) $this->pdo->lastInsertId();

        $this->pdo->prepare(
            "INSERT INTO refund_email_codes (refund_request_id, code_hash, attempts, expires_at)
             VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
        )->execute([$requestId, refund_hash_code('123456', (string) $requestId), $attempts]);
        return (int) $this->pdo->lastInsertId();
    }

    private function attempts(int $codeId): int
    {
        $stmt = $this->pdo->prepare('SELECT attempts FROM refund_email_codes WHERE id = ?');
        $stmt->execute([$codeId]);
        return (int) $stmt->fetchColumn();
    }

    public function test_each_claim_returns_the_attempt_number_it_used(): void
    {
        $codeId = $this->seedCode(0);

        $this->assertSame(1, refund_claim_code_attempt($this->pdo, $codeId));
        $this->assertSame(2, refund_claim_code_attempt($this->pdo, $codeId));
    }

    public function test_requests_that_all_read_four_attempts_get_only_one_guess_between_them(): void
    {
        $codeId = $this->seedCode(4);

        $this->assertSame(5, refund_claim_code_attempt($this->pdo, $codeId));
        $this->assertNull(refund_claim_code_attempt($this->pdo, $codeId));
        $this->assertNull(refund_claim_code_attempt($this->pdo, $codeId));
        $this->assertSame(5, $this->attempts($codeId));
    }
}
