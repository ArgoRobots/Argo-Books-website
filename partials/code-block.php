<?php
declare(strict_types=1);

/**
 * Syntax-highlighted code blocks for the documentation.
 *
 * Highlighting is done here rather than in the browser so the page needs no
 * extra library and the colours are already correct on first paint. The
 * languages supported are only the ones the docs actually use; anything else
 * renders as plain monospace, which is a fine outcome rather than a broken one.
 *
 * Escaping happens inside the tokeniser, never before it. Escaping first would
 * mean the patterns had to match against `&quot;` and `&amp;`, which is how
 * highlighters end up mangling code that contains an ampersand.
 */

/**
 * Render a single code block with a copy button.
 *
 * @param string  $code  Raw source. Not pre-escaped.
 * @param string  $lang  json, bash, php, csharp, js, python, http, or text.
 * @param ?string $label Small caption in the block's title bar.
 */
function argo_code_block(string $code, string $lang = 'text', ?string $label = null): string
{
    $code = rtrim($code);
    $highlighted = argo_highlight_code($code, $lang);

    // The copy button reads this attribute rather than the rendered text, so
    // the highlighting spans can never end up in what the reader pastes.
    $raw = htmlspecialchars($code, ENT_QUOTES);
    $caption = htmlspecialchars($label ?? argo_code_lang_label($lang), ENT_QUOTES);
    $button = argo_code_copy_button();

    return <<<HTML
<div class="code-block" data-code="$raw">
    <div class="code-block-bar">
        <span class="code-block-label">$caption</span>
        $button
    </div>
    <pre><code>$highlighted</code></pre>
</div>
HTML;
}

/**
 * One block showing the same call in several languages.
 *
 * Every variant is rendered up front and hidden with CSS rather than fetched on
 * demand: the point is that a reader switches instantly, and four extra
 * snippets weigh nothing next to a round trip. The chosen language sticks
 * across every block on the site, so a C# developer picks it once.
 *
 * @param array $variants Ordered [label => ['lang' => string, 'code' => string]].
 */
function argo_code_tabs(array $variants, ?string $caption = null): string
{
    if ($variants === []) {
        return '';
    }

    $tabs = '';
    $panes = '';
    $first = true;

    foreach ($variants as $label => $variant) {
        $slug = preg_replace('/[^a-z0-9]+/', '', strtolower((string) $label));
        $code = rtrim((string) $variant['code']);
        $active = $first ? ' is-active' : '';
        $labelHtml = htmlspecialchars((string) $label, ENT_QUOTES);

        $tabs .= '<button type="button" class="code-tab' . $active . '" role="tab"'
            . ' aria-selected="' . ($first ? 'true' : 'false') . '"'
            . ' data-variant="' . $slug . '">' . $labelHtml . '</button>';

        $panes .= '<div class="code-variant' . $active . '" data-variant="' . $slug . '"'
            . ' data-code="' . htmlspecialchars($code, ENT_QUOTES) . '">'
            . '<pre><code>' . argo_highlight_code($code, (string) $variant['lang']) . '</code></pre>'
            . '</div>';

        $first = false;
    }

    $captionHtml = $caption === null
        ? ''
        : '<span class="code-block-label">' . htmlspecialchars($caption, ENT_QUOTES) . '</span>';
    $button = argo_code_copy_button();

    return <<<HTML
<div class="code-block code-block-tabbed">
    <div class="code-block-bar">
        <div class="code-tabs" role="tablist">$tabs</div>
        <div class="code-bar-right">
            $captionHtml
            $button
        </div>
    </div>
    $panes
</div>
HTML;
}

function argo_code_copy_button(): string
{
    return <<<HTML
<button type="button" class="code-copy" aria-label="Copy code to clipboard">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
                <span class="code-copy-text">Copy</span>
            </button>
HTML;
}

function argo_code_lang_label(string $lang): string
{
    return [
        'json'   => 'JSON',
        'bash'   => 'Shell',
        'php'    => 'PHP',
        'http'   => 'HTTP',
        'csharp' => 'C#',
        'js'     => 'JavaScript',
        'python' => 'Python',
        'xml'    => 'XML',
    ][$lang] ?? 'Example';
}

/**
 * Tokenise and escape in one pass.
 *
 * Everything a pattern matches is wrapped in a span; everything between matches
 * is escaped and emitted as-is. One regex per language keeps precedence
 * explicit, which matters because a string containing a `#` must be a string,
 * not the start of a comment.
 */
function argo_highlight_code(string $code, string $lang): string
{
    $pattern = argo_highlight_pattern($lang);
    if ($pattern === null) {
        return htmlspecialchars($code, ENT_QUOTES);
    }

    $out = '';
    $offset = 0;
    $length = strlen($code);

    while ($offset < $length
        && preg_match($pattern, $code, $m, PREG_OFFSET_CAPTURE, $offset) === 1) {
        $start = $m[0][1];
        $text = $m[0][0];

        $out .= htmlspecialchars(substr($code, $offset, $start - $offset), ENT_QUOTES);

        // The first named group that participated names the token class.
        $class = null;
        foreach ($m as $name => $capture) {
            if (is_string($name) && $capture[1] !== -1 && $capture[0] !== '') {
                $class = $name;
                break;
            }
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES);
        $out .= $class === null ? $escaped : '<span class="tok-' . $class . '">' . $escaped . '</span>';

        // Zero-length matches would spin forever.
        $offset = $start + max(1, strlen($text));
    }

    return $out . htmlspecialchars(substr($code, $offset), ENT_QUOTES);
}

/** One alternation per language, ordered so the greediest correct rule wins. */
function argo_highlight_pattern(string $lang): ?string
{
    // Built from parts because these are dense enough already without also
    // fighting escaping inside one long literal.
    $dq = '"(?:[^"\\\\]|\\\\.)*"';
    $sq = "'(?:[^'\\\\]|\\\\.)*'";
    $lineComment = '\/\/[^\n]*';

    switch ($lang) {
        case 'json':
            return '/'
                . '(?<key>' . $dq . '(?=\s*:))'
                . '|(?<str>' . $dq . ')'
                . '|(?<lit>\b(?:true|false|null)\b)'
                . '|(?<num>-?\b\d+(?:\.\d+)?\b)'
                . '|(?<punc>[{}\[\],:])'
                . '/';

        case 'bash':
            return '/'
                . '(?<comment>\#[^\n]*)'
                . '|(?<str>' . $sq . '|' . $dq . ')'
                . '|(?<cmd>^\s*[a-z][\w.-]*)'
                . '|(?<url>https?:\/\/[^\s\\\\\'"]+)'
                . '|(?<flag>(?<=\s)--?[A-Za-z][\w-]*)'
                . '|(?<cont>\\\\$)'
                . '/m';

        case 'php':
            return '/'
                . '(?<comment>' . $lineComment . ')'
                . '|(?<str>' . $sq . '|' . $dq . ')'
                . '|(?<tag><\?php|\?>)'
                . '|(?<kw>\b(?:if|else|exit|return|function|foreach|as|true|false|null|use|new|throw|try|catch)\b)'
                . '|(?<var>\$\w+)'
                . '|(?<fn>\b[a-z_]\w*(?=\s*\())'
                . '|(?<num>\b\d+\b)'
                . '/';

        case 'csharp':
            return '/'
                . '(?<comment>' . $lineComment . ')'
                . '|(?<str>\$?@?' . $dq . ')'
                . '|(?<kw>\b(?:using|var|new|await|async|public|private|static|class|record|return|if|else|foreach|in|null|true|false|string|int|long|decimal|bool|void|throw|try|catch)\b)'
                . '|(?<fn>\b[A-Za-z_]\w*(?=\s*[<(]))'
                . '|(?<num>\b\d+\b)'
                . '/';

        case 'js':
            return '/'
                . '(?<comment>' . $lineComment . ')'
                . '|(?<str>`(?:[^`\\\\]|\\\\.)*`|' . $sq . '|' . $dq . ')'
                . '|(?<kw>\b(?:const|let|var|await|async|function|return|if|else|for|of|new|null|true|false|throw|try|catch)\b)'
                . '|(?<fn>\b[A-Za-z_$]\w*(?=\s*\())'
                . '|(?<num>\b\d+\b)'
                . '/';

        case 'python':
            return '/'
                . '(?<comment>\#[^\n]*)'
                . '|(?<str>[frb]?' . $sq . '|[frb]?' . $dq . ')'
                . '|(?<kw>\b(?:import|from|def|return|if|elif|else|for|in|with|as|not|and|or|None|True|False|raise|try|except)\b)'
                . '|(?<fn>\b[a-z_]\w*(?=\s*\())'
                . '|(?<num>\b\d+\b)'
                . '/';

        case 'http':
            return '/'
                . '(?<key>^[A-Za-z][\w-]*(?=:))'
                . '|(?<str>(?<=:\s)[^\n]+)'
                . '/m';

        // Covers XAML and MSBuild project files. Attribute values are matched
        // before attribute names so a name inside a value stays a string, and
        // comments come first so markup inside one is not tokenised.
        case 'xml':
            return '/'
                . '(?<comment><!--.*?-->)'
                . '|(?<str>' . $dq . '|' . $sq . ')'
                . '|(?<tag><\/?[A-Za-z_][\w.:-]*|\/?>)'
                . '|(?<key>[A-Za-z_][\w.:-]*(?=\s*=))'
                . '/s';
    }

    return null;
}
