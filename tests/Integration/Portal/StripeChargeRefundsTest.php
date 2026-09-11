<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Stripe\ApiRequestor;
use Stripe\Charge;
use Stripe\HttpClient\ClientInterface;
use Stripe\Stripe;
use Tests\Helpers\DatabaseTestCase;

/**
 * charge.refunded on API versions from 2022-11-15 on carries no `refunds`
 * list, so the handler has to fetch it. Every refund must land under its own
 * refund id: that is the key the desktop refund flow writes too.
 */
final class StripeChargeRefundsTest extends DatabaseTestCase
{
    private const ACCOUNT = 'acct_test_connected';

    private FakeStripeHttpClient $stripeHttp;
    private ?string $previousApiKey;
    private int $companyId;
    private string $paymentIntentId;
    private string $chargeId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousApiKey = Stripe::getApiKey();
        Stripe::setApiKey('sk_test_fake');
        $this->stripeHttp = new FakeStripeHttpClient();
        ApiRequestor::setHttpClient($this->stripeHttp);

        $this->companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($this->companyId, 'INV-SR-1', 100.00, balanceDue: 0.00, status: 'paid');
        $this->paymentIntentId = 'pi_sr_' . bin2hex(random_bytes(4));
        $this->chargeId = 'ch_sr_' . bin2hex(random_bytes(4));
        $this->pdo->prepare(
            "INSERT INTO portal_payments
             (company_id, invoice_id, customer_name, amount, processing_fee,
              currency, payment_method, provider_payment_id, provider_transaction_id,
              reference_number, status, payment_environment, created_at)
             VALUES (?, 'INV-SR-1', 'Test Customer', 100.00, 0.00,
                     'USD', 'stripe', ?, ?, ?, 'completed', 'sandbox', NOW())"
        )->execute([$this->companyId, $this->paymentIntentId, $this->chargeId, 'PAY-' . date('Ymd') . '-SR0001']);
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);
        Stripe::setApiKey($this->previousApiKey);
        parent::tearDown();
    }

    public function test_refund_already_written_by_the_desktop_flow_is_not_recorded_again(): void
    {
        refund_record_ledger($this->pdo, $this->refundRequest(3000), 're_desktop_A', ['environment' => 'sandbox']);

        $this->stripeHttp->respondWith($this->refundList([
            $this->refund('re_desktop_A', 3000),
            $this->refund('re_dashboard_B', 2000),
        ]));
        apply_stripe_charge_refunds($this->pdo, $this->chargeWithoutRefundList(5000), false, self::ACCOUNT);

        $this->assertSame(['refund_re_dashboard_B', 'refund_re_desktop_A'], $this->refundRowKeys());
        $this->assertSame(50.00, $this->balanceDue());
        $this->assertContains('Stripe-Account: ' . self::ACCOUNT, $this->stripeHttp->requests[0]['headers']);
    }

    public function test_a_second_partial_refund_is_recorded_rather_than_dropped(): void
    {
        $this->stripeHttp->respondWith($this->refundList([$this->refund('re_first', 3000)]));
        apply_stripe_charge_refunds($this->pdo, $this->chargeWithoutRefundList(3000), false, self::ACCOUNT);

        $this->stripeHttp->respondWith($this->refundList([
            $this->refund('re_second', 2000),
            $this->refund('re_first', 3000),
        ]));
        apply_stripe_charge_refunds($this->pdo, $this->chargeWithoutRefundList(5000), false, self::ACCOUNT);

        $this->assertSame(['refund_re_first', 'refund_re_second'], $this->refundRowKeys());
        $this->assertSame(50.00, $this->balanceDue());
    }

    public function test_completes_the_refund_request_named_in_the_refund_metadata(): void
    {
        $requestId = $this->seedRefundRequest(4000, 'processing');

        $this->stripeHttp->respondWith($this->refundList([
            $this->refund('re_argo', 4000, ['argo_request_id' => (string) $requestId]),
        ]));
        apply_stripe_charge_refunds($this->pdo, $this->chargeWithoutRefundList(4000), false, self::ACCOUNT);

        $stmt = $this->pdo->prepare('SELECT state, provider_refund_id FROM refund_requests WHERE id = ?');
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();
        $this->assertSame('completed', $request['state']);
        $this->assertSame('re_argo', $request['provider_refund_id']);
        $this->assertSame(['refund_re_argo'], $this->refundRowKeys());
    }

    public function test_uses_the_refund_list_in_the_event_when_the_api_version_still_sends_it(): void
    {
        $charge = Charge::constructFrom($this->chargeFields(3000) + [
            'refunds' => $this->refundList([$this->refund('re_embedded', 3000)]),
        ]);

        apply_stripe_charge_refunds($this->pdo, $charge, false, self::ACCOUNT);

        $this->assertSame([], $this->stripeHttp->requests);
        $this->assertSame(['refund_re_embedded'], $this->refundRowKeys());
    }

    private function chargeFields(int $amountRefunded): array
    {
        return [
            'id' => $this->chargeId,
            'object' => 'charge',
            'amount' => 10000,
            'amount_refunded' => $amountRefunded,
            'currency' => 'usd',
            'payment_intent' => $this->paymentIntentId,
            'refunded' => $amountRefunded >= 10000,
        ];
    }

    private function chargeWithoutRefundList(int $amountRefunded): Charge
    {
        return Charge::constructFrom($this->chargeFields($amountRefunded));
    }

    private function refund(string $id, int $amount, array $metadata = []): array
    {
        return [
            'id' => $id,
            'object' => 'refund',
            'amount' => $amount,
            'charge' => $this->chargeId,
            'payment_intent' => $this->paymentIntentId,
            'currency' => 'usd',
            'status' => 'succeeded',
            'metadata' => $metadata,
        ];
    }

    private function refundList(array $refunds): array
    {
        return ['object' => 'list', 'url' => '/v1/refunds', 'has_more' => false, 'data' => $refunds];
    }

    private function refundRequest(int $amountCents): array
    {
        return [
            'id' => 0,
            'company_id' => $this->companyId,
            'invoice_id' => 'INV-SR-1',
            'invoice_number' => 'INV-SR-1',
            'customer_name' => 'Test Customer',
            'provider' => 'stripe',
            'provider_payment_id' => $this->paymentIntentId,
            'amount_cents' => $amountCents,
            'currency' => 'USD',
        ];
    }

    private function seedRefundRequest(int $amountCents, string $state): int
    {
        $this->pdo->prepare(
            "INSERT INTO refund_requests
             (company_id, invoice_id, invoice_number, customer_name, provider,
              provider_payment_id, amount_cents, currency, state)
             VALUES (?, 'INV-SR-1', 'INV-SR-1', 'Test Customer', 'stripe', ?, ?, 'USD', ?)"
        )->execute([$this->companyId, $this->paymentIntentId, $amountCents, $state]);
        return (int) $this->pdo->lastInsertId();
    }

    /** @return string[] */
    private function refundRowKeys(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT provider_payment_id FROM portal_payments
             WHERE company_id = ? AND amount < 0 ORDER BY provider_payment_id'
        );
        $stmt->execute([$this->companyId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function balanceDue(): float
    {
        $stmt = $this->pdo->prepare('SELECT balance_due FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$this->companyId, 'INV-SR-1']);
        return (float) $stmt->fetch()['balance_due'];
    }
}

/** Answers Stripe API calls from a queue so no request leaves the machine. */
final class FakeStripeHttpClient implements ClientInterface
{
    /** @var array<int, array{method: string, url: string, headers: array, params: array}> */
    public array $requests = [];
    private array $responses = [];

    public function respondWith(array $body): void
    {
        $this->responses[] = $body;
    }

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        $this->requests[] = ['method' => $method, 'url' => $absUrl, 'headers' => $headers, 'params' => $params];
        if ($this->responses === []) {
            throw new \RuntimeException("Unexpected Stripe request: $method $absUrl");
        }
        return [json_encode(array_shift($this->responses)), 200, []];
    }
}
