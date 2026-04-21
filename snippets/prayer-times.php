<?php
/**
 * Prayer Times Module
 * Husaini Islamic Centre Peterborough
 * Modular snippet for shortcode + JS enqueue
 */

/**
 * Enqueue Prayer Times JS
 */
function hic_enqueue_prayer_times_script() {
    wp_enqueue_script(
        'hic-prayer-times',
        get_stylesheet_directory_uri() . '/js/hic-prayer-times.js',
        array(),
        '1.1',
        true
    );
}
add_action('wp_enqueue_scripts', 'hic_enqueue_prayer_times_script');


/**
 * Prayer Times Shortcode
 */
function hic_today_prayer_times_shortcode() {

    // Today + Tomorrow
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

    if (!$today_q->have_posts()) {
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
    $tomorrow_imsaak = $tomorrow_fajr = $tomorrow_sunrise = $tomorrow_zohr = $tomorrow_maghrib = '';

    if ($tomorrow_q->have_posts()) {
        $tomorrow_q->the_post();
        $tomorrow_id      = get_the_ID();
        $tomorrow_imsaak  = get_field('imsaak', $tomorrow_id);
        $tomorrow_fajr    = get_field('fajr', $tomorrow_id);
        $tomorrow_sunrise = get_field('sunrise', $tomorrow_id);
        $tomorrow_zohr    = get_field('zohr', $tomorrow_id);
        $tomorrow_maghrib = get_field('maghrib', $tomorrow_id);
    }

    wp_reset_postdata();

    ob_start();
    ?>

    <div class="hic-prayer-times-bar"
        data-imsaak="<?php echo esc_attr($imsaak); ?>"
        data-fajr="<?php echo esc_attr($fajr); ?>"
        data-sunrise="<?php echo esc_attr($sunrise); ?>"
        data-zohr="<?php echo esc_attr($zohr); ?>"
        data-sunset="<?php echo esc_attr($sunset); ?>"
        data-maghrib="<?php echo esc_attr($maghrib); ?>"

        data-tomorrow-imsaak="<?php echo esc_attr($tomorrow_imsaak); ?>"
        data-tomorrow-fajr="<?php echo esc_attr($tomorrow_fajr); ?>"
        data-tomorrow-sunrise="<?php echo esc_attr($tomorrow_sunrise); ?>"
        data-tomorrow-zohr="<?php echo esc_attr($tomorrow_zohr); ?>"
        data-tomorrow-maghrib="<?php echo esc_attr($tomorrow_maghrib); ?>"
    >

        <!-- LEFT SIDE -->
        <div class="hic-pt-left">
            <div class="hic-pt-date-icon">
                <!-- calendar icon -->
            </div>
            <div class="hic-pt-date-text">
                <div class="hic-pt-gregorian"><?php echo esc_html($gregorian_date); ?></div>
                <div class="hic-pt-islamic"><?php echo esc_html($islamic_date); ?></div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="hic-pt-right">

            <div class="hic-pt-item hic-pt-mosque">
    	      <span class="hic-pt-mosque-icon">
               <svg viewBox="0 0 24 24" class="hic-pt-icon-mosque" aria-hidden="true">
               <path d="M4 20h16v-6l-3-2v-2l-5-3-5 3v2l-3 2v6z" fill="none" stroke="currentColor" stroke-width="1.5"/>
               <path d="M12 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
             </span>
            </div>


            <!-- TODAY -->
            <div class="hic-pt-today-row">

                <div class="hic-pt-item" data-prayer="imsaak">
                    <span class="hic-pt-label">Imsaak</span>
                    <span class="hic-pt-time"><?php echo esc_html($imsaak); ?></span>
                </div>

                <div class="hic-pt-item" data-prayer="fajr">
                    <span class="hic-pt-label">Fajr</span>
                    <span class="hic-pt-time"><?php echo esc_html($fajr); ?></span>
                </div>

                <div class="hic-pt-item" data-prayer="sunrise">
                    <span class="hic-pt-label">Sunrise</span>
                    <span class="hic-pt-time"><?php echo esc_html($sunrise); ?></span>
                </div>

                <div class="hic-pt-item" data-prayer="zohr">
                    <span class="hic-pt-label">Zohr</span>
                    <span class="hic-pt-time"><?php echo esc_html($zohr); ?></span>
                </div>

                <div class="hic-pt-item" data-prayer="sunset">
                    <span class="hic-pt-label">Sunset</span>
                    <span class="hic-pt-time"><?php echo esc_html($sunset); ?></span>
                </div>

                <div class="hic-pt-item" data-prayer="maghrib">
                    <span class="hic-pt-label">Maghrib</span>
                    <span class="hic-pt-time"><?php echo esc_html($maghrib); ?></span>
                </div>

            </div>

            <!-- TOMORROW -->
            <?php if ($tomorrow_fajr) : ?>
            <div class="hic-pt-tomorrow-row">

                <div class="hic-pt-tomorrow-label">Tomorrow →</div>

                <?php if ($tomorrow_imsaak) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item" data-prayer="tomorrow-imsaak">
                    <span class="hic-pt-label">Imsaak</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_imsaak); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($tomorrow_fajr) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item" data-prayer="tomorrow-fajr">
                    <span class="hic-pt-label">Fajr</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_fajr); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($tomorrow_sunrise) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item" data-prayer="tomorrow-sunrise">
                    <span class="hic-pt-label">Sunrise</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_sunrise); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($tomorrow_zohr) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item" data-prayer="tomorrow-zohr">
                    <span class="hic-pt-label">Zohr</span>
                    <span class="hic-pt-time"><?php echo esc_html($tomorrow_zohr); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($tomorrow_maghrib) : ?>
                <div class="hic-pt-item hic-pt-tomorrow-item" data-prayer="tomorrow-maghrib">
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
