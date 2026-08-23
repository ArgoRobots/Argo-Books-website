<?php
declare(strict_types=1);

namespace Tests\Unit\PublicApi;

use ApiResponseSent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Webhook signing, subscription matching, and endpoint URL safety.
 *
 * The URL tests are the important ones: without them this feature is a
 * server-side request forgery primitive that anyone holding a key can point at
 * our own network.
 */
final class WebhookTest extends TestCase
{
    private function expectRejection(callable $fn): array
    {
        try {
            $fn();
        } catch (ApiResponseSent $e) {
            return $e->payload['error'] ?? [];
        }
        $this->fail('Expected rejection, got acceptance.');
    }

    // -- signing -------------------------------------------------------------

    public function testSignatureHasTheDocumentedShape(): void
    {
        $signature = api_webhook_signature('whsec_test', 1787000000, '{"a":1}');

        $this->assertMatchesRegularExpression('/^t=1787000000,v1=[0-9a-f]{64}$/', $signature);
    }

    public function testSignatureVerifiesWithTheSharedSecret(): void
    {
        $secret = 'whsec_test';
        $timestamp = 1787000000;
        $body = '{"type":"revenue.imported"}';

        preg_match('/t=(\d+),v1=([0-9a-f]+)/', api_webhook_signature($secret, $timestamp, $body), $m);

        $this->assertTrue(hash_equals(hash_hmac('sha256', $m[1] . '.' . $body, $secret), $m[2]));
    }

    public function testChangingTheBodyBreaksTheSignature(): void
    {
        $secret = 'whsec_test';
        $timestamp = 1787000000;

        preg_match('/v1=([0-9a-f]+)/', api_webhook_signature($secret, $timestamp, '{"amount":100}'), $original);
        preg_match('/v1=([0-9a-f]+)/', api_webhook_signature($secret, $timestamp, '{"amount":999}'), $tampered);

        $this->assertNotSame($original[1], $tampered[1]);
    }

    /**
     * The timestamp is inside the signed material, so a captured delivery cannot
     * be replayed later with a fresher timestamp bolted on.
     */
    public function testTimestampIsCoveredBySignature(): void
    {
        $secret = 'whsec_test';
        $body = '{"a":1}';

        preg_match('/v1=([0-9a-f]+)/', api_webhook_signature($secret, 1787000000, $body), $a);
        preg_match('/v1=([0-9a-f]+)/', api_webhook_signature($secret, 1787000001, $body), $b);

        $this->assertNotSame($a[1], $b[1]);
    }

    public function testSigningSecretIsPrefixedForLeakScanners(): void
    {
        $this->assertMatchesRegularExpression('/^whsec_[0-9a-f]{48}$/', api_generate_signing_secret());
    }

    // -- subscription matching -----------------------------------------------

    public function testNullSubscriptionMeansEveryEvent(): void
    {
        $this->assertTrue(api_endpoint_wants(null, 'revenue.imported'));
        $this->assertTrue(api_endpoint_wants(null, 'import_batch.reverted'));
    }

    public function testEmptySubscriptionMeansEveryEvent(): void
    {
        $this->assertTrue(api_endpoint_wants('[]', 'revenue.imported'));
    }

    public function testSubscriptionFiltersToItsListedTypes(): void
    {
        $subscribed = json_encode(['revenue.imported']);

        $this->assertTrue(api_endpoint_wants($subscribed, 'revenue.imported'));
        $this->assertFalse(api_endpoint_wants($subscribed, 'expense.imported'));
    }

    // -- subscription validation ---------------------------------------------

    public function testUnknownEventTypeIsRejected(): void
    {
        $error = $this->expectRejection(fn () => api_validate_event_types(['revenue.exploded']));

        $this->assertSame('parameter_invalid_value', $error['code']);
        $this->assertSame('enabled_events', $error['param']);
    }

    public function testWildcardNormalisesToNull(): void
    {
        $this->assertNull(api_validate_event_types(['*']));
        $this->assertNull(api_validate_event_types([]));
        $this->assertNull(api_validate_event_types(null));
    }

    public function testValidTypesAreStoredDeduplicated(): void
    {
        $stored = api_validate_event_types(['revenue.imported', 'revenue.imported', 'expense.rejected']);

        $this->assertSame(['revenue.imported', 'expense.rejected'], json_decode((string) $stored, true));
    }

    public function testEveryDocumentedEventTypeIsAccepted(): void
    {
        foreach (API_EVENT_TYPES as $type) {
            $this->assertNotNull(api_validate_event_types([$type]), "$type should be accepted");
        }
    }

    /**
     * There is deliberately no <object>.created event: the developer who created
     * it already knows. If one is ever added, this test should be the thing that
     * makes someone justify it.
     */
    public function testNoCreatedEventsAreEmitted(): void
    {
        foreach (API_EVENT_TYPES as $type) {
            $this->assertStringNotContainsString('.created', $type);
        }
    }

    // -- endpoint URL safety -------------------------------------------------

    public function testPlainHttpIsRejected(): void
    {
        $error = $this->expectRejection(fn () => api_validate_webhook_url('http://example.com/hook'));

        $this->assertSame('parameter_invalid_value', $error['code']);
        $this->assertSame('url', $error['param']);
    }

    public function testLocalhostIsRejected(): void
    {
        $this->assertSame(
            'parameter_invalid_value',
            $this->expectRejection(fn () => api_validate_webhook_url('https://localhost/hook'))['code']
        );
    }

    #[DataProvider('privateAddresses')]
    public function testPrivateAddressesAreRejected(string $url): void
    {
        $this->assertSame(
            'parameter_invalid_value',
            $this->expectRejection(fn () => api_validate_webhook_url($url))['code'],
            "$url should be refused"
        );
    }

    public static function privateAddresses(): array
    {
        return [
            'loopback ip'      => ['https://127.0.0.1/hook'],
            'private class a'  => ['https://10.0.0.5/hook'],
            'private class c'  => ['https://192.168.1.10/hook'],
            'link local'       => ['https://169.254.169.254/latest/meta-data'],
            'internal suffix'  => ['https://vault.internal/hook'],
        ];
    }

    public function testPublicHttpsUrlIsAccepted(): void
    {
        $this->assertSame('https://example.com/hook', api_validate_webhook_url('https://example.com/hook'));
    }

    public function testGarbageUrlIsRejected(): void
    {
        $this->assertSame(
            'parameter_invalid_value',
            $this->expectRejection(fn () => api_validate_webhook_url('not a url'))['code']
        );
    }
}
