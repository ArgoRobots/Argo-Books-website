<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\DatabaseTestCase;

require_once PROJECT_ROOT . '/api/receipt/scan_quota.php';

/**
 * Receipt scans cost real money per call, so the premium allowance must only
 * go to subscriptions from this environment. Tests run as sandbox.
 */
final class ReceiptScanQuotaEnvironmentTest extends DatabaseTestCase
{
    private function seedLicense(string $subscriptionId, string $environment): string
    {
        $this->seedSubscription($subscriptionId, (new \DateTime('+30 days'))->format('Y-m-d H:i:s'));
        $this->pdo->prepare('UPDATE premium_subscriptions SET environment = ? WHERE subscription_id = ?')
            ->execute([$environment, $subscriptionId]);
        return $this->seedRedeemedKey('device-scan-quota', $subscriptionId);
    }

    public function test_premium_allowance_for_a_subscription_from_this_environment(): void
    {
        $key = $this->seedLicense('PREM-SCAN-SAME-ENVS-AAAA', 'sandbox');

        $identity = receipt_scan_quota_identity($this->pdo, $key, null);

        $this->assertSame('premium', $identity['tier']);
    }

    public function test_no_premium_allowance_for_a_subscription_from_the_other_environment(): void
    {
        $key = $this->seedLicense('PREM-SCAN-OTHR-ENVS-BBBB', 'production');

        $this->assertNull(receipt_scan_quota_identity($this->pdo, $key, null));
        // Older installs send the subscription id itself as the key.
        $this->assertNull(receipt_scan_quota_identity($this->pdo, 'PREM-SCAN-OTHR-ENVS-BBBB', null));
    }
}
