<?php
/**
 * Responsive 16:9 YouTube embed used at the top of feature landing pages.
 * Renders a full <section class="feature-detail-section"> so it drops in
 * directly after the hero, between other feature-detail sections.
 *
 * Uses youtube-nocookie.com (privacy-enhanced mode).
 *
 * @param string $videoId YouTube video ID (the part after v= or youtu.be/)
 * @param string $title    Accessible title for the iframe
 */
function feature_video_section(string $videoId, string $title): void
{
    $id = htmlspecialchars($videoId, ENT_QUOTES);
    $titleAttr = htmlspecialchars($title, ENT_QUOTES);
    ?>
    <!-- =============================================
         DEMO VIDEO
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="animate-on-scroll" style="max-width: 900px; margin: 0 auto;">
                <div style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);">
                    <iframe src="https://www.youtube-nocookie.com/embed/<?= $id ?>" title="<?= $titleAttr ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                </div>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Inline 16:9 YouTube embed for documentation pages, sized to match the
 * 75%-width centered images those pages use at the top of an article.
 *
 * @param string $videoId YouTube video ID (the part after v= or youtu.be/)
 * @param string $title    Accessible title for the iframe
 */
function docs_video_embed(string $videoId, string $title): void
{
    $id = htmlspecialchars($videoId, ENT_QUOTES);
    $titleAttr = htmlspecialchars($title, ENT_QUOTES);
    // padding-bottom is 75% (width) * 9/16 to keep a true 16:9 ratio.
    ?>
            <div style="position: relative; width: 75%; margin: 0 auto 2rem auto; padding-bottom: 42.1875%; height: 0; overflow: hidden; border-radius: 8px;">
                <iframe src="https://www.youtube-nocookie.com/embed/<?= $id ?>" title="<?= $titleAttr ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
            </div>
    <?php
}
