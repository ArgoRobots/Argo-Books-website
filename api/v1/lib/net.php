<?php
declare(strict_types=1);

/**
 * Outbound host safety, shared by webhook endpoint registration and by the
 * delivery cron.
 *
 * Both sides have to agree, and both have to check. Validating only at
 * registration leaves DNS rebinding wide open: a name that resolves to a public
 * address while the endpoint is being created can resolve to a private one by
 * the time the cron delivers to it. So the cron re-checks immediately before
 * every POST.
 *
 * The rules here fail CLOSED. A host we cannot resolve is refused rather than
 * waved through, because "we could not tell" and "it is safe" are not the same
 * answer when the consequence is our own server making the request.
 */

/**
 * Normalise the host from a URL: strips the brackets that parse_url keeps
 * around an IPv6 literal, and lowercases.
 *
 * Without the bracket strip, `https://[::1]/` sails through every IP check,
 * because `[::1]` is not a valid IP string and is not a resolvable name either.
 */
function api_normalise_host(string $host): string
{
    $host = strtolower(trim($host));
    if (str_starts_with($host, '[') && str_ends_with($host, ']')) {
        $host = substr($host, 1, -1);
    }
    return $host;
}

/**
 * True only for an address we are willing to send a request to.
 *
 * Covers IPv4 and IPv6, and rejects the IPv4-mapped IPv6 forms (::ffff:127.0.0.1)
 * that would otherwise smuggle a loopback address past an IPv6-only check.
 */
function api_ip_is_public(string $ip): bool
{
    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return false;
    }

    // ::ffff:127.0.0.1 and friends: unwrap to the IPv4 address and judge that.
    if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/i', $ip, $m)) {
        $ip = $m[1];
    }

    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) !== false;
}

/**
 * Every address a host resolves to, IPv4 and IPv6. An IP literal resolves to
 * itself. Returns [] when nothing could be resolved.
 */
function api_resolve_host(string $host): array
{
    $host = api_normalise_host($host);
    if ($host === '') {
        return [];
    }

    if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
        return [$host];
    }

    $ips = [];

    // Suppressed rather than checked: dns_get_record emits a warning for a name
    // that does not exist, which is an expected outcome here, not a fault.
    $records = @dns_get_record($host, DNS_A | DNS_AAAA);
    if (is_array($records)) {
        foreach ($records as $record) {
            if (!empty($record['ip'])) {
                $ips[] = $record['ip'];
            }
            if (!empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }
    }

    // gethostbyname as a backstop for resolvers that dns_get_record cannot use.
    // It returns the input unchanged on failure, hence the comparison.
    if ($ips === []) {
        $v4 = gethostbyname($host);
        if ($v4 !== $host && filter_var($v4, FILTER_VALIDATE_IP) !== false) {
            $ips[] = $v4;
        }
    }

    return array_values(array_unique($ips));
}

/**
 * True when a host resolves and EVERY address it resolves to is public.
 *
 * Every, not any: a name that returns both a public and a private address would
 * otherwise be usable to reach the private one.
 */
function api_host_is_public(string $host): bool
{
    $ips = api_resolve_host($host);
    if ($ips === []) {
        return false;
    }

    foreach ($ips as $ip) {
        if (!api_ip_is_public($ip)) {
            return false;
        }
    }

    return true;
}

/** Hostnames that never belong to a public endpoint, whatever DNS claims. */
function api_host_is_reserved_name(string $host): bool
{
    $host = api_normalise_host($host);

    return $host === 'localhost'
        || str_ends_with($host, '.localhost')
        || str_ends_with($host, '.local')
        || str_ends_with($host, '.internal')
        || str_ends_with($host, '.home.arpa');
}
