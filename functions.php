<?php
/**
 * Astra Child Theme Functions
 */
error_log('HIC child theme functions.php loaded');

add_action( 'wp_enqueue_scripts', 'hic_child_enqueue_styles' );
function hic_child_enqueue_styles() {
    // Load parent theme CSS
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );

    // Load child theme CSS
    wp_enqueue_style( 'hic-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style') );
}

if ( ! defined( 'ABSPATH' ) ) exit;

require_once get_stylesheet_directory() . '/snippets/prayer-times.php';
require_once get_stylesheet_directory() . '/snippets/featured-content.php';
require_once get_stylesheet_directory() . '/snippets/latest-news.php';

add_action( 'astra_header', function() {
    echo do_shortcode('[INSERT_ELEMENTOR id="2780"]');
});


/* ----------------------------------------
   HIC — Share Icon + Mobile Fix
   ---------------------------------------- */
function hic_share_fix_script() {
    ?>
    <script>
    document.addEventListener('click', function(e) {

        const shareContainer = e.target.closest('.hic-share-container');

        // If the click is inside the share area, handle share + stop collapse
        if (shareContainer) {
            const eventUrl   = encodeURIComponent(shareContainer.getAttribute('data-url'));
            const eventTitle = encodeURIComponent(document.title);

            // FACEBOOK
            if (e.target.closest('.hic-share.fb')) {
                e.preventDefault();
                window.open('https://www.facebook.com/sharer/sharer.php?u=' + eventUrl, '_blank');
            }

            // TWITTER / X
            if (e.target.closest('.hic-share.tw')) {
                e.preventDefault();
                window.open('https://twitter.com/intent/tweet?url=' + eventUrl + '&text=' + eventTitle, '_blank');
            }

            // WHATSAPP
            if (e.target.closest('.hic-share.wa')) {
                e.preventDefault();
                window.open('https://wa.me/?text=' + eventUrl, '_blank');
            }

            // LINKEDIN
            if (e.target.closest('.hic-share.ln')) {
                e.preventDefault();
                window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + eventUrl, '_blank');
            }

            // EMAIL
            if (e.target.closest('.hic-share.em')) {
                e.preventDefault();
                window.location.href = 'mailto:?subject=' + eventTitle + '&body=' + eventUrl;
            }

            // In all cases, prevent the event from collapsing the card
            e.stopPropagation();
            return;
        }

    }, true); // capture phase so we beat the collapse handler
    </script>
    <?php
}
add_action('wp_footer', 'hic_share_fix_script');
function hic_enqueue_custom_scripts() {
    // Only load on the homepage
    if ( is_front_page() ) {
        wp_enqueue_script(
            'hic-home-reorder',
            get_stylesheet_directory_uri() . '/js/home-reorder.js',
            array(),
            '1.0',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'hic_enqueue_custom_scripts' );



