<?php
declare(strict_types=1);

namespace Tests\Integration\PublicApi;

/**
 * Extracts the code samples out of the API documentation and runs them.
 *
 * Syntax checking was not enough. A sample can parse perfectly and still call a
 * field that does not exist, send a header the server rejects, or verify a
 * signature incorrectly. The only way to know a sample works is to point it at
 * a real server and execute it, which is what this does.
 *
 * The samples in the docs address the live host with a placeholder key, because
 * that is what a reader needs to see. Before running, both are rewritten to
 * address the local test server with a real key. Nothing else is altered, so
 * what runs is the code on the page.
 */
final class DocSampleRunner
{
    public const DOC_BASE = 'https://argorobots.com/v1';
    public const DOC_KEY = 'ab_...';

    /** Samples that verify a signature take arguments rather than making a call. */
    public const KIND_REQUEST = 'request';
    public const KIND_VERIFY = 'verify';

    /**
     * Every sample in the documentation, as
     * [id => ['lang','code','kind','page']].
     */
    public static function all(): array
    {
        $samples = [];

        foreach (glob(PROJECT_ROOT . '/documentation/pages/api/*.php') ?: [] as $page) {
            $src = (string) file_get_contents($page);
            $name = basename($page, '.php');

            preg_match_all(
                // \r?\n throughout, because git checks these files out with
                // CRLF on Windows and a bare \n silently matches nothing.
                "/argo_code_block\(<<<'CODE'\r?\n(.*?)\r?\nCODE, '([a-z]+)'/s",
                $src,
                $singles,
                PREG_SET_ORDER
            );
            foreach ($singles as $i => [, $code, $lang]) {
                $samples["$name-block$i-$lang"] = self::describe($lang, $code, $name);
            }

            preg_match_all(
                "/'lang' => '([a-z]+)', 'code' => <<<'CODE'\r?\n(.*?)\r?\nCODE\]/s",
                $src,
                $tabbed,
                PREG_SET_ORDER
            );
            foreach ($tabbed as $i => [, $lang, $code]) {
                $samples["$name-tab$i-$lang"] = self::describe($lang, $code, $name);
            }
        }

        ksort($samples);

        return $samples;
    }

    private static function describe(string $lang, string $code, string $page): array
    {
        $isVerify = str_contains($code, 'argo_signature_is_valid')
            || str_contains($code, 'ArgoSignatureIsValid')
            || str_contains($code, 'argoSignatureIsValid');

        return [
            'lang' => $lang,
            // Normalised because a sample's exact bytes matter: CRLF would
            // change the body that gets signed, and so the signature with it.
            'code' => str_replace("\r\n", "\n", $code),
            'page' => $page,
            'kind' => $isVerify ? self::KIND_VERIFY : self::KIND_REQUEST,
        ];
    }

    /** Point a sample at the local server with a working key. */
    public static function localise(string $code, string $baseUrl, string $key): string
    {
        return str_replace([self::DOC_BASE, self::DOC_KEY], [$baseUrl, $key], $code);
    }

    /**
     * Drivers appended to a verification sample so it proves itself.
     *
     * Four vectors, because a function that returns true for everything would
     * otherwise pass: a genuine signature, a forged one, one that is correctly
     * signed but too old to accept, and a header that is not a signature at all.
     */
    public static function verifyDriver(string $lang): string
    {
        $body = '{"a":1}';
        $secret = 'whsec_test';

        switch ($lang) {
            case 'php':
                return <<<PHP


\$__body = '$body';
\$__secret = '$secret';
\$__now = time();
\$__sign = fn(int \$t) => hash_hmac('sha256', \$t . '.' . \$__body, \$__secret);

\$__good = argo_signature_is_valid(\$__body, "t=\$__now,v1={\$__sign(\$__now)}", \$__secret);
\$__forged = argo_signature_is_valid(\$__body, "t=\$__now,v1=" . str_repeat('0', 64), \$__secret);
\$__stale = argo_signature_is_valid(\$__body, 't=' . (\$__now - 400) . ',v1=' . \$__sign(\$__now - 400), \$__secret);
\$__junk = argo_signature_is_valid(\$__body, 'not-a-signature', \$__secret);

echo (\$__good && !\$__forged && !\$__stale && !\$__junk) ? 'VERIFY-OK' : 'VERIFY-FAIL';
PHP;

            case 'js':
                return <<<JS


import { createHmac } from "node:crypto";
const __body = '$body';
const __secret = "$secret";
const __now = Math.floor(Date.now() / 1000);
const __sign = (t) => createHmac("sha256", __secret).update(`\${t}.\${__body}`).digest("hex");

const __good = argoSignatureIsValid(__body, `t=\${__now},v1=\${__sign(__now)}`, __secret);
const __forged = argoSignatureIsValid(__body, `t=\${__now},v1=\${"0".repeat(64)}`, __secret);
const __stale = argoSignatureIsValid(__body, `t=\${__now - 400},v1=\${__sign(__now - 400)}`, __secret);
const __junk = argoSignatureIsValid(__body, "not-a-signature", __secret);

process.stdout.write(__good && !__forged && !__stale && !__junk ? "VERIFY-OK" : "VERIFY-FAIL");
JS;

            case 'python':
                return <<<PY


__body = '$body'
__secret = "$secret"
__now = int(time.time())


def __sign(t):
    return hmac.new(__secret.encode(), f"{t}.{__body}".encode(), hashlib.sha256).hexdigest()


__good = argo_signature_is_valid(__body, f"t={__now},v1={__sign(__now)}", __secret)
__forged = argo_signature_is_valid(__body, f"t={__now},v1={'0' * 64}", __secret)
__stale = argo_signature_is_valid(__body, f"t={__now - 400},v1={__sign(__now - 400)}", __secret)
__junk = argo_signature_is_valid(__body, "not-a-signature", __secret)

print("VERIFY-OK" if __good and not __forged and not __stale and not __junk else "VERIFY-FAIL", end="")
PY;
        }

        return '';
    }

    /** The C# driver body, which lives inside the generated project. */
    public static function csharpVerifyDriver(): string
    {
        return <<<'CS'
        var body = "{\"a\":1}";
        var secret = "whsec_test";
        var now = DateTimeOffset.UtcNow.ToUnixTimeSeconds();

        string Sign(long t)
        {
            using var h = new HMACSHA256(Encoding.UTF8.GetBytes(secret));
            return Convert.ToHexString(
                h.ComputeHash(Encoding.UTF8.GetBytes($"{t}.{body}"))).ToLowerInvariant();
        }

        var good = ArgoSignatureIsValid(body, $"t={now},v1={Sign(now)}", secret);
        var forged = ArgoSignatureIsValid(body, $"t={now},v1={new string('0', 64)}", secret);
        var stale = ArgoSignatureIsValid(body, $"t={now - 400},v1={Sign(now - 400)}", secret);
        var junk = ArgoSignatureIsValid(body, "not-a-signature", secret);

        Console.Write(good && !forged && !stale && !junk ? "VERIFY-OK" : "VERIFY-FAIL");
CS;
    }

    /** Run a command, returning [exitCode, stdout, stderr]. */
    public static function exec(array $command, ?string $cwd = null, int $timeout = 60): array
    {
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($command, $descriptors, $pipes, $cwd);

        if (!is_resource($process)) {
            return [-1, '', 'could not start ' . ($command[0] ?? '?')];
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout = '';
        $stderr = '';
        $deadline = microtime(true) + $timeout;

        while (true) {
            $stdout .= stream_get_contents($pipes[1]);
            $stderr .= stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }
            if (microtime(true) > $deadline) {
                proc_terminate($process);
                $stderr .= "\ntimed out after {$timeout}s";
                break;
            }
            usleep(20000);
        }

        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($process);

        return [$exit, $stdout, $stderr];
    }

    /** Whether an interpreter is on PATH. */
    public static function have(string $binary): bool
    {
        static $cache = [];
        if (isset($cache[$binary])) {
            return $cache[$binary];
        }
        [$exit] = self::exec([$binary, '--version'], null, 20);

        return $cache[$binary] = ($exit === 0);
    }
}
