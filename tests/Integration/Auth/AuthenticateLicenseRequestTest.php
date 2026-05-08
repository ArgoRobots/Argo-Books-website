<?php
declare(strict_types=1);

namespace Tests\Integration\Auth;

use DateTimeImmutable;
use Tests\Helpers\DatabaseTestCase;

final class AuthenticateLicenseRequestTest extends DatabaseTestCase
{
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = [
            'HTTP_X_LICENSE_KEY' => $_SERVER['HTTP_X_LICENSE_KEY'] ?? null,
            'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        ];
        unset($_SERVER['HTTP_X_LICENSE_KEY'], $_SERVER['HTTP_AUTHORIZATION']);
    }

    protected function tearDown(): void
    {
        foreach ($this->serverBackup as $key => $value) {
            if ($value === null) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }
        parent::tearDown();
    }

    private function seedActiveLicense(string $subscriptionId, string $endDateOffset = '+30 days'): string
    {
        $endDate = (new DateTimeImmutable($endDateOffset))->format('Y-m-d H:i:s');
        $this->seedSubscription($subscriptionId, $endDate, 'active');
        return $this->seedRedeemedKey('device-hash-x', $subscriptionId);
    }

    public function test_returns_null_when_no_auth_header(): void
    {
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_license_key_does_not_exist(): void
    {
        $_SERVER['HTTP_X_LICENSE_KEY'] = 'PREM-DOES-NOTE-XIST-AAAA';
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_key_not_redeemed(): void
    {
        $key = $this->seedPremiumKey(); // unredeemed by default
        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_linked_subscription_does_not_exist(): void
    {
        $key = $this->seedRedeemedKey('device-x', 'PREM-NOSUCH-SUBS-AAAA-BBBB');
        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_subscription_end_date_is_in_past(): void
    {
        $key = $this->seedActiveLicense('PREM-AUTH-SUB1-AAAA-BBBB', '-5 days');
        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_subscription_status_is_expired(): void
    {
        $endDate = (new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');
        $this->seedSubscription('PREM-AUTH-EXPI-AAAA-CCCC', $endDate, 'expired');
        $key = $this->seedRedeemedKey('device-x', 'PREM-AUTH-EXPI-AAAA-CCCC');

        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_null_when_subscription_status_is_past_due(): void
    {
        $endDate = (new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');
        $this->seedSubscription('PREM-AUTH-PAST-AAAA-DDDD', $endDate, 'past_due');
        $key = $this->seedRedeemedKey('device-x', 'PREM-AUTH-PAST-AAAA-DDDD');

        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $this->assertNull(authenticate_license_request());
    }

    public function test_returns_auth_payload_when_active_and_future(): void
    {
        $key = $this->seedActiveLicense('PREM-AUTH-OKKK-AAAA-EEEE');
        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;

        $auth = authenticate_license_request();
        $this->assertNotNull($auth);
        $this->assertSame(hash('sha256', $key), $auth['license_key_hash']);
        $this->assertSame('PREM-AUTH-OKKK-AAAA-EEEE', $auth['subscription_id']);
    }

    public function test_returns_auth_payload_when_status_is_cancelled_but_end_date_is_future(): void
    {
        // Cancelled means "active until end_date, no auto-renew" — still
        // valid for license purposes until end_date passes.
        $endDate = (new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');
        $this->seedSubscription('PREM-AUTH-CANC-AAAA-FFFF', $endDate, 'cancelled');
        $key = $this->seedRedeemedKey('device-x', 'PREM-AUTH-CANC-AAAA-FFFF');

        $_SERVER['HTTP_X_LICENSE_KEY'] = $key;
        $auth = authenticate_license_request();
        $this->assertNotNull($auth);
        $this->assertSame('PREM-AUTH-CANC-AAAA-FFFF', $auth['subscription_id']);
    }

    public function test_authorization_bearer_header_works_equivalently(): void
    {
        $key = $this->seedActiveLicense('PREM-AUTH-BEAR-AAAA-GGGG');
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $key;

        $auth = authenticate_license_request();
        $this->assertNotNull($auth);
        $this->assertSame('PREM-AUTH-BEAR-AAAA-GGGG', $auth['subscription_id']);
    }
}
