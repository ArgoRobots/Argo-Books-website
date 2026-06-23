<?php
declare(strict_types=1);

namespace Tests\Unit\Receipt;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../api/receipt/receipt_scan_lib.php';

final class BuildReceiptCsvTest extends TestCase
{
    public function test_csv_contains_summary_and_line_items(): void
    {
        $receipt = [
            'supplierName' => 'Walmart',
            'transactionDate' => '2026-06-21',
            'subtotal' => 80.0,
            'taxTotal' => 7.5,
            'totalAmount' => 87.5,
            'currencyCode' => 'CAD',
            'paymentMethod' => 'Credit Card',
            'taxes' => [['name' => 'GST', 'amount' => 4.0]],
            'discounts' => [],
            'lineItems' => [
                ['description' => 'Bananas', 'quantity' => 1, 'unitPrice' => 2.5, 'totalPrice' => 2.5, 'confidence' => 0.95],
            ],
        ];

        $csv = receipt_scan_build_csv($receipt);

        $this->assertStringContainsString('Walmart', $csv);
        $this->assertStringContainsString('Bananas', $csv);
        $this->assertStringContainsString('Description,Quantity,Unit Price,Total', $csv);
    }

    public function test_csv_escapes_commas_in_description(): void
    {
        $receipt = [
            'supplierName' => 'Shop', 'transactionDate' => '', 'subtotal' => 0.0, 'taxTotal' => 0.0,
            'totalAmount' => 0.0, 'currencyCode' => 'USD', 'paymentMethod' => '', 'taxes' => [], 'discounts' => [],
            'lineItems' => [
                ['description' => 'Milk, 2L', 'quantity' => 1, 'unitPrice' => 3.0, 'totalPrice' => 3.0, 'confidence' => 0.9],
            ],
        ];
        $csv = receipt_scan_build_csv($receipt);
        $this->assertStringContainsString('"Milk, 2L"', $csv);
    }
}
