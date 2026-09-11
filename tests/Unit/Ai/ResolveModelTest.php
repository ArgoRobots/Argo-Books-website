<?php
declare(strict_types=1);

namespace Tests\Unit\Ai;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/api/ai/_models.php';

/**
 * The free AI path authenticates with a self-asserted X-Device-Id, so it must
 * not be able to choose the model it runs on. Every desktop and mobile build
 * sends "gemini-2.5-flash", which is remapped to the server default.
 */
final class ResolveModelTest extends TestCase
{
    private array $envBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['GEMINI_MODEL', 'GEMINI_MODEL_EXTRACTION'] as $key) {
            $this->envBackup[$key] = $_ENV[$key] ?? null;
        }
        $_ENV['GEMINI_MODEL'] = 'gemini-3.1-flash-lite';
        $_ENV['GEMINI_MODEL_EXTRACTION'] = 'gemini-3.5-flash';
    }

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $value) {
            if ($value === null) {
                unset($_ENV[$key]);
            } else {
                $_ENV[$key] = $value;
            }
        }
        parent::tearDown();
    }

    public function test_free_request_for_the_pro_model_gets_the_default_text_model(): void
    {
        $this->assertSame('gemini-3.1-flash-lite', ai_resolve_model('gemini-2.5-pro', false, false));
    }

    public function test_free_vision_request_for_the_pro_model_gets_the_extraction_model(): void
    {
        $this->assertSame('gemini-3.5-flash', ai_resolve_model('gemini-2.5-pro', true, false));
    }

    public function test_free_text_request_cannot_pick_the_extraction_model(): void
    {
        $this->assertSame('gemini-3.1-flash-lite', ai_resolve_model('gemini-3.5-flash', false, false));
    }

    public function test_what_the_desktop_app_sends_resolves_as_before_on_both_paths(): void
    {
        foreach ([false, true] as $licensed) {
            $this->assertSame('gemini-3.1-flash-lite', ai_resolve_model('gemini-2.5-flash', false, $licensed));
            $this->assertSame('gemini-3.5-flash', ai_resolve_model('gemini-2.5-flash', true, $licensed));
            $this->assertSame('gemini-3.1-flash-lite', ai_resolve_model('', false, $licensed));
        }
    }

    public function test_licensed_request_keeps_a_supported_model(): void
    {
        $this->assertSame('gemini-2.5-pro', ai_resolve_model('gemini-2.5-pro', false, true));
        $this->assertSame('gemini-3.5-flash', ai_resolve_model('gemini-3.5-flash', false, true));
    }

    public function test_unsupported_model_is_rejected_on_both_paths(): void
    {
        $this->assertNull(ai_resolve_model('gpt-4o', false, false));
        $this->assertNull(ai_resolve_model('gpt-4o', false, true));
        $this->assertNull(ai_resolve_model('gemini-2.5-pro/../x', false, true));
    }
}
