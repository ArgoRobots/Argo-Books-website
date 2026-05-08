<?php
declare(strict_types=1);

namespace Tests\Integration\Auth;

use Tests\Helpers\DatabaseTestCase;

final class AuthenticatePortalRequestTest extends DatabaseTestCase
{
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = [
            'HTTP_X_API_KEY' => $_SERVER['HTTP_X_API_KEY'] ?? null,
            'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        ];
        unset($_SERVER['HTTP_X_API_KEY'], $_SERVER['HTTP_AUTHORIZATION']);
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

    public function test_returns_null_when_no_auth_header(): void
    {
        $this->assertNull(authenticate_portal_request());
    }

    public function test_returns_null_when_x_api_key_does_not_match_any_company(): void
    {
        $_SERVER['HTTP_X_API_KEY'] = 'completely_invalid_key_xyz';
        $this->assertNull(authenticate_portal_request());
    }

    public function test_returns_null_when_bearer_token_does_not_match(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer some_random_string_that_is_not_a_real_key';
        $this->assertNull(authenticate_portal_request());
    }

    public function test_returns_company_when_x_api_key_matches(): void
    {
        $seed = $this->seedPortalCompanyWithApiKey('Acme Co');
        $_SERVER['HTTP_X_API_KEY'] = $seed['api_key'];

        $company = authenticate_portal_request();
        $this->assertNotNull($company);
        $this->assertSame($seed['id'], (int) $company['id']);
        $this->assertSame('Acme Co', $company['company_name']);
    }

    public function test_returns_company_when_authorization_bearer_matches(): void
    {
        $seed = $this->seedPortalCompanyWithApiKey('Bearer Co');
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $seed['api_key'];

        $company = authenticate_portal_request();
        $this->assertNotNull($company);
        $this->assertSame($seed['id'], (int) $company['id']);
    }

    public function test_x_api_key_takes_priority_over_authorization_header(): void
    {
        $a = $this->seedPortalCompanyWithApiKey('A Co');
        $b = $this->seedPortalCompanyWithApiKey('B Co');

        $_SERVER['HTTP_X_API_KEY'] = $a['api_key'];
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $b['api_key'];

        $company = authenticate_portal_request();
        $this->assertSame('A Co', $company['company_name']);
    }
}
