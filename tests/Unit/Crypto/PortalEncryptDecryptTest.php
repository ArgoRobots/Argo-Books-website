<?php
declare(strict_types=1);

namespace Tests\Unit\Crypto;

use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PortalEncryptDecryptTest extends TestCase
{
    private bool $hadOriginalKey;
    private string $originalKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadOriginalKey = isset($_ENV['PORTAL_ENCRYPTION_KEY']);
        $this->originalKey = $_ENV['PORTAL_ENCRYPTION_KEY'] ?? '';
    }

    protected function tearDown(): void
    {
        // Restore the exact prior state — distinguish "set to empty string"
        // from "unset" so later tests see the same env they would normally see.
        if ($this->hadOriginalKey) {
            $_ENV['PORTAL_ENCRYPTION_KEY'] = $this->originalKey;
        } else {
            unset($_ENV['PORTAL_ENCRYPTION_KEY']);
        }
        parent::tearDown();
    }

    public function test_roundtrip_returns_original_plaintext(): void
    {
        $plaintext = 'Sensitive customer data: 4242-4242-4242-4242';
        $ciphertext = portal_encrypt($plaintext);
        $this->assertNotSame($plaintext, $ciphertext);
        $this->assertSame($plaintext, portal_decrypt($ciphertext));
    }

    public function test_each_encryption_uses_unique_iv(): void
    {
        $plaintext = 'same input';
        $a = portal_encrypt($plaintext);
        $b = portal_encrypt($plaintext);
        $this->assertNotSame($a, $b, 'Two encryptions of identical plaintext must yield different ciphertexts (random IV)');
        $this->assertSame($plaintext, portal_decrypt($a));
        $this->assertSame($plaintext, portal_decrypt($b));
    }

    public function test_decrypt_throws_on_tampered_ciphertext(): void
    {
        $ciphertext = portal_encrypt('original');
        // Flip a byte in the middle of the base64 payload — corrupts ciphertext
        // (and tag), causing GCM verification to fail.
        $tampered = substr_replace($ciphertext, 'X', strlen($ciphertext) - 4, 1);

        $this->expectException(RuntimeException::class);
        portal_decrypt($tampered);
    }

    public function test_throws_when_key_is_wrong_length(): void
    {
        $_ENV['PORTAL_ENCRYPTION_KEY'] = 'abc';
        $this->expectException(RuntimeException::class);
        portal_encrypt('anything');
    }
}
