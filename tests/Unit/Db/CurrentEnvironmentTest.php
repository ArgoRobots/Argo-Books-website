<?php
declare(strict_types=1);

namespace Tests\Unit\Db;

use PHPUnit\Framework\TestCase;

final class CurrentEnvironmentTest extends TestCase
{
    private ?string $originalAppEnv;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalAppEnv = $_ENV['APP_ENV'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalAppEnv === null) {
            unset($_ENV['APP_ENV']);
        } else {
            $_ENV['APP_ENV'] = $this->originalAppEnv;
        }
        parent::tearDown();
    }

    public function test_returns_production_when_app_env_is_production(): void
    {
        $_ENV['APP_ENV'] = 'production';
        $this->assertSame('production', current_environment());
    }

    public function test_returns_sandbox_when_app_env_is_development(): void
    {
        $_ENV['APP_ENV'] = 'development';
        $this->assertSame('sandbox', current_environment());
    }

    public function test_returns_sandbox_when_app_env_is_unset(): void
    {
        unset($_ENV['APP_ENV']);
        $this->assertSame('sandbox', current_environment());
    }
}
