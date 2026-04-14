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

/**
 * Enqueue child theme JS
 */
function hic_enqueue_prayer_times_script() {
    wp_enqueue_script(
        'hic-prayer-times',
        get_stylesheet_directory_uri() . '/js/hic-prayer-times.js',
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'hic_enqueue_prayer_times_script');


/**
 * HIC Today Prayer Times Shortcode (Final Option C Version)
 */
function hic_today_prayer_times_shortcode() {

    // Today and tomorrow dates
    $today      = current_time('timestamp');
    $today_str  = date('Ymd', $today);
    $tomorrow   = strtotime('+1 day', $today);
    $tomorrow_str = date('Ymd', $tomorrow);

    // Query today
    $today_q = new WP_Query([
        'post_type'      => 'prayer_times',
        'posts_per_page' => 1,
        'meta_query'     => [
            [
                'key'     => 'gregorian_date',
                'value'   => $today_str,
                'compare' => 'LIKE',
            ],
        ],
    ]);

    // Query tomorrow
    $tomorrow_q = new WP_Query([
        'post_type'      => 'prayer_times',
        'posts_per_page' => 1,
        'meta_query'     => [
            [
                'key'     => 'gregorian_date',
                'value'   => $tomorrow_str,
                'compare' => 'LIKE',
            ],
        ],
    ]);

    if ( ! $today_q->have_posts() ) {
        return '<div class="hic-prayer-times-error">No prayer times found for today.</div>';
    }

    $today_q->the_post();
    $today_id = get_the_ID();

    // Today fields
    $gregorian_date = get_field('gregorian_date', $today_id);
    $islamic_date   = get_field('islamic_date', $today_id);
    $imsaak         = get_field('imsaak', $today_id);
    $fajr           = get_field('fajr', $today_id);
    $sunrise        = get_field('sunrise', $today_id);
    $zohr           = get_field('zohr', $today_id);
    $sunset         = get_field('sunset', $today_id);
    $maghrib        = get_field('maghrib', $today_id);

    // Tomorrow fields
    $tomorrow_fajr = $tomorrow_sunrise = $tomorrow_zohr = $tomorrow_maghrib = '';

    if ( $tomorrow_q->have_posts() ) {
        $tomorrow_q->the_post();
        $tomorrow_id    = get_the_ID();
        $tomorrow_fajr  = get_field('fajr', $tomorrow_id);
        $tomorrow_sunrise = get_field('sunrise', $tomorrow_id);
        $tomorrow_zohr  = get_field('zohr', $tomorrow_id);
        $tomorrow_maghrib = get_field('maghrib', $tomorrow_id);
    }

    wp_reset_postdata();

    ob_start();
    ?>

    <div class="hic-prayer-times-bar">

        <!-- LEFT SIDE -->
        <div class="hic-pt-left">
            <div class="hic-pt-date-icon">
                <svg viewBox="0 0 24 24" class="hic-pt-icon-calendar" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="17" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="hic-pt-date-text">
                <div class="hic-pt-gregorian"><?php echo esc_html($gregorian_date); ?></div>
                <div class="hic-pt-islamic"><?php echo esc_html($islamic_date); ?></div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="hic-pt-right">

            <!-- MOSQUE ICON AS ITS OWN PILL -->
            <div class="hic-pt-item hic-pt-mosque">
                <span class="hic-pt-mosque-icon">
                    <svg viewBox="0 0 24 24" class="hic-pt-icon-mosque" aria-hidden="true">
                        <path d="M4 20h16v-6l-3-2v-2l-5-3-5 3v2l-3 2v6z" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>

            <!-- TODAY ROW -->
            <div class="hic-pt-today-row">

                <div class="hic-pt-item">
                    <span class="hic-pt-label">Imsaak</span>
                    <span class="hic-pt-time"><?php echo esc_html($imsaak); ?></span>
                </div>

                <div class="hic-pt-item">
                    <span class="hic-pt-label">Fajr</span>
                    <span class="hic-pt-time"><?php echo esc_html($fajr); ?></span>
                </div>

                <div class="hic-pt-item">
                    <span class="hic-pt-label">Sunrise</span>
                    <span class="hic-pt-time"><?php echo esc_html($sunrise); ?></span>
                </div>

                <div class="hic-pt-item">
                    <span class="hic-pt-label">Zohr</span>
                    <span class="hic-pt-time"><?php echo esc_html($zohr); ?></span>
                </div>

                <div class="hic-pt-item hic-pt-next">
                    <span class="hic-pt-label">Sunset</span>
                    <span class="hic-pt-time"><?php echo esc_html($sunset); ?></span>
                </div>

                <div class="hic-pt-item">
                    <span class="hic-pt-label">Maghrib</span>
                    <span class="hic-pt-time"><?php echo esc_html($maghrib); ?></span>
                </div>

            </div>

            <!-- TOMORROW ROW -->
            <?php if ( $tomorrow_fajr ) : ?>
            <div class="hic-pt-tomorrow-row">

                <!-- Tomorrow label (plain text, no pill) -->
                <div class="hic-pt-tomorrow-label">Tomorrow →</div>

                <?php if ( $tomorrow_fajr ) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item">
                    <span class="hic-pt-label">Fajr</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_fajr); ?></span>
                </div>
                <?php endif; ?>

                <?php if ( $tomorrow_sunrise ) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item">
                    <span class="hic-pt-label">Sunrise</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_sunrise); ?></span>
                </div>
                <?php endif; ?>

                <?php if ( $tomorrow_zohr ) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item">
                    <span class="hic-pt-label">Zohr</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_zohr); ?></span>
                </div>
                <?php endif; ?>

                <?php if ( $tomorrow_maghrib ) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item">
                    <span class="hic-pt-label">Maghrib</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_maghrib); ?></span>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('hic_today_prayer_times', 'hic_today_prayer_times_shortcode');
add_action( 'astra_header', function() {
    echo do_shortcode('[INSERT_ELEMENTOR id="2780"]');
} );

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



