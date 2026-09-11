<?php
declare(strict_types=1);

namespace Tests\Integration\Community;

use DOMDocument;
use Tests\Helpers\DatabaseTestCase;

require_once PROJECT_ROOT . '/community/formatting/formatting_functions.php';
require_once PROJECT_ROOT . '/community/mentions/mentions.php';

/**
 * view_post.php renders posts as process_mentions(render_formatted_text(...)),
 * so an @name inside a link's URL must not turn into markup inside the href.
 */
final class MentionsInFormattedTextTest extends DatabaseTestCase
{
    public function test_mention_inside_a_link_url_leaves_the_href_intact(): void
    {
        $username = 'mention_' . bin2hex(random_bytes(4));
        $this->seedCommunityUser($username);
        $url = "https://github.com/@{$username}";

        $html = process_mentions(render_formatted_text("[profile]({$url}) and hi @{$username}"));

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $hrefs = [];
        foreach ($doc->getElementsByTagName('a') as $a) {
            $hrefs[] = $a->getAttribute('href');
        }

        $this->assertSame([$url, 'users/profile.php?username=' . $username], $hrefs, $html);
    }
}
