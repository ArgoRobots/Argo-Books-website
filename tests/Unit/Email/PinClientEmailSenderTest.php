<?php
declare(strict_types=1);

namespace Tests\Unit\Email;

use PHPUnit\Framework\TestCase;

/**
 * The invoice and purchase-order email endpoints relay mail for desktop
 * clients. The envelope sender must always be our configured address; what the
 * client sends as "from" is only good for a Reply-To.
 */
final class PinClientEmailSenderTest extends TestCase
{
    private const DEFAULT_FROM = 'noreply@argorobots.com';
    private const DEFAULT_NAME = 'Argo Books';

    public function test_client_cannot_send_as_another_argorobots_address(): void
    {
        $sender = pin_client_email_sender(
            ['from' => 'billing@argorobots.com', 'fromName' => 'Argo Books Billing'],
            self::DEFAULT_FROM,
            self::DEFAULT_NAME
        );
        $this->assertSame(self::DEFAULT_FROM, $sender['fromEmail']);
    }

    public function test_client_cannot_send_as_an_outside_address(): void
    {
        $sender = pin_client_email_sender(['from' => 'ceo@bank.example'], self::DEFAULT_FROM, self::DEFAULT_NAME);
        $this->assertSame(self::DEFAULT_FROM, $sender['fromEmail']);
    }

    public function test_merchant_display_name_is_kept(): void
    {
        $sender = pin_client_email_sender(['fromName' => 'Acme Plumbing'], self::DEFAULT_FROM, self::DEFAULT_NAME);
        $this->assertSame('Acme Plumbing', $sender['fromName']);
    }

    public function test_missing_display_name_falls_back_to_default(): void
    {
        $sender = pin_client_email_sender([], self::DEFAULT_FROM, self::DEFAULT_NAME);
        $this->assertSame(self::DEFAULT_NAME, $sender['fromName']);
        $this->assertNull($sender['replyTo']);
    }

    public function test_client_from_address_becomes_the_reply_to(): void
    {
        $sender = pin_client_email_sender(['from' => 'owner@acme.example'], self::DEFAULT_FROM, self::DEFAULT_NAME);
        $this->assertSame('owner@acme.example', $sender['replyTo']);
    }

    public function test_explicit_reply_to_wins_over_from(): void
    {
        $sender = pin_client_email_sender(
            ['from' => 'owner@acme.example', 'replyTo' => 'support@acme.example'],
            self::DEFAULT_FROM,
            self::DEFAULT_NAME
        );
        $this->assertSame('support@acme.example', $sender['replyTo']);
    }
}
