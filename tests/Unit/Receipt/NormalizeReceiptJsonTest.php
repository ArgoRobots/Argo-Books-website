<?php
declare(strict_types=1);

namespace Tests\Unit\Receipt;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../api/receipt/receipt_scan_lib.php';

final class NormalizeReceiptJsonTest extends TestCase
{
    public function test_parses_valid_model_json(): void
    {
        $content = json_encode([
            'supplierName' => 'Walmart',
            'transactionDate' => '2026-06-21',
            'subtotal' => 80.0,
            'taxes' => [['name' => 'GST', 'amount' => 4.0], ['name' => 'PST', 'amount' => 3.5]],
            'discounts' => [['name' => 'Member', 'amount' => 2.0]],
            'totalAmount' => 87.5,
            'currencyCode' => 'CAD',
            'paymentMethod' => 'Credit Card',
            'confidence' => 0.93,
            'lineItems' => [
                ['description' => 'Bananas', 'quantity' => 1, 'unitPrice' => 2.5, 'totalPrice' => 2.5, 'confidence' => 0.95],
            ],
        ]);

        $out = receipt_scan_normalize($content);

        $this->assertTrue($out['ok']);
        $this->assertSame('Walmart', $out['receipt']['supplierName']);
        $this->assertSame('CAD', $out['receipt']['currencyCode']);
        $this->assertEqualsWithDelta(7.5, $out['receipt']['taxTotal'], 0.001);
        $this->assertCount(1, $out['receipt']['lineItems']);
        $this->assertEqualsWithDelta(0.93, $out['receipt']['confidence'], 0.001);
        $this->assertCount(1, $out['receipt']['discounts']);
    }

    public function test_strips_markdown_fences(): void
    {
        $content = "```json\n{\"supplierName\":\"Shop\",\"totalAmount\":5,\"confidence\":0.8,\"lineItems\":[]}\n```";
        $out = receipt_scan_normalize($content);
        $this->assertTrue($out['ok']);
        $this->assertSame('Shop', $out['receipt']['supplierName']);
    }

    public function test_defaults_currency_to_usd_when_missing(): void
    {
        $content = json_encode(['supplierName' => 'X', 'totalAmount' => 1, 'confidence' => 0.9, 'lineItems' => []]);
        $out = receipt_scan_normalize($content);
        $this->assertTrue($out['ok']);
        $this->assertSame('USD', $out['receipt']['currencyCode']);
    }

    public function test_not_a_receipt_returns_unreadable(): void
    {
        $content = json_encode(['error' => 'Not a valid receipt', 'confidence' => 0.0]);
        $out = receipt_scan_normalize($content);
        $this->assertFalse($out['ok']);
        $this->assertSame('unreadable', $out['error']);
    }

    public function test_garbage_returns_unreadable(): void
    {
        $out = receipt_scan_normalize('not json at all');
        $this->assertFalse($out['ok']);
        $this->assertSame('unreadable', $out['error']);
    }
}
