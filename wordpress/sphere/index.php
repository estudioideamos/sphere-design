<?php if (!defined("ABSPATH")) {
    exit();
} ?><!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo(
    "charset",
); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head><body id="top" <?php body_class(
    is_front_page() ? "home" : "inner",
); ?>><?php
wp_body_open();
$header = sphere_fragment("header");
$header = str_replace('aria-current="page"', "", $header);
$slug = is_front_page()
    ? "index"
    : (is_singular("post")
        ? "insights"
        : get_post_field("post_name", get_queried_object_id()));
$url = home_url($slug === "index" ? "/" : "/" . $slug . "/");
$header = str_replace(
    'href="' . esc_url($url) . '"',
    'href="' . esc_url($url) . '" aria-current="page"',
    $header,
);
echo $header;
?><main id="main">
<?php if (is_page("portfolio")) {
    sphere_portfolio();
} elseif (is_page("insights") || is_home()) {
    sphere_journal();
} elseif (is_singular("post")) {
    while (have_posts()) {
        the_post(); ?>
<article class="journal-article"><header class="article-heading wrap"><a class="text-link" href="<?php echo esc_url(
    home_url("/insights/"),
); ?>">All insights ↗</a><p class="eyebrow">PERSPECTIVE</p><h1><?php the_title(); ?></h1><p class="article-deck"><?php echo esc_html(
    get_the_excerpt(),
); ?></p><p class="sample-note">Sphere Design · <?php echo esc_html(
    get_the_date("F Y"),
); ?></p></header><figure class="article-cover wrap"><?php the_post_thumbnail(
    "full",
); ?></figure><div class="article-layout wrap"><aside><span class="eyebrow">IN THIS STORY</span><ol id="article-toc"></ol></aside><div class="article-copy"><?php echo sphere_markup(
    apply_filters("the_content", get_the_content()),
); ?></div></div></article>
<?php
    }
} elseif (have_posts()) {
    while (have_posts()) {
        the_post();
        echo sphere_markup(do_shortcode(get_the_content()));
    }
} else {
    echo '<section class="page-title wrap"><h1>Page not found.</h1><a href="' .
        esc_url(home_url("/")) .
        '">Back to home ↗</a></section>';
} ?>
<?php if (is_page("portfolio") || is_page("insights") || is_singular("post")) {
    echo sphere_fragment("closing");
} ?>
</main><?php
echo sphere_fragment("footer"), sphere_fragment("modal");
wp_footer();
?></body></html>
