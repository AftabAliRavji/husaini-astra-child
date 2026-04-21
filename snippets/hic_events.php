<?php
/**
 * Events Module
 * Husaini Islamic Centre Peterborough
 * Shortcode: [hussaini_events]
 */

if ( ! function_exists( 'hic_events_shortcode' ) ) {

    function hic_events_shortcode( $atts ) {

        // Shortcode defaults
        $atts = shortcode_atts( [
            'limit' => 6,
        ], $atts );

        // Admin override (ACF field on Site Settings page)
        $settings_page_id = 2969;
        $admin_limit = get_field( 'hic_events_limit', $settings_page_id );

        // Priority: Admin setting → Shortcode → Default
        $limit = $admin_limit ?: intval( $atts['limit'] );

        $args = [
            'post_type'      => 'tribe_events',
            'posts_per_page' => intval( $limit ),
            'meta_key'       => '_EventStartDate',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'eventDisplay'   => 'custom',
        ];

        $events = new WP_Query( $args );

        if ( ! $events->have_posts() ) {
            return '<p>No upcoming events.</p>';
        }

        ob_start();

        echo '<div class="hic-events-wrapper">';

        while ( $events->have_posts() ) {
            $events->the_post();

            $event_id   = get_the_ID();
            $title      = get_the_title();
            $permalink  = get_permalink();
            $raw_date   = tribe_get_start_date( $event_id, false, 'Y-m-d' );
            $start_time = tribe_get_start_time( $event_id, 'g:i A' );
            $excerpt    = wp_trim_words( get_the_excerpt(), 20 );
            $content    = apply_filters( 'the_content', get_the_content() );

            // Date parts
            $day   = date( 'j', strtotime( $raw_date ) );
            $month = date( 'M', strtotime( $raw_date ) );
            $year  = date( 'Y', strtotime( $raw_date ) );

            // Category
            $categories = get_the_terms( $event_id, 'tribe_events_cat' );
            $category_name = '';
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                $category_name = $categories[0]->name;
            }

            // Custom fields
            $show_expanded = get_post_meta( $event_id, 'hic_show_expanded', true );
            $speaker       = get_post_meta( $event_id, 'hic_speaker', true );
            $location      = get_post_meta( $event_id, 'hic_location', true );

            // Timetable rows
            $timetable_rows = [];
            $i = 1;
            while ( true ) {
                $time  = get_post_meta( $event_id, 'hic_time_' . $i, true );
                $label = get_post_meta( $event_id, 'hic_label_' . $i, true );
                if ( empty( $time ) && empty( $label ) ) {
                    break;
                }
                if ( ! empty( $time ) || ! empty( $label ) ) {
                    $timetable_rows[] = [
                        'time'  => $time,
                        'label' => $label,
                    ];
                }
                $i++;
            }

            // Featured image
            $featured_img = get_the_post_thumbnail( $event_id, 'large', [ 'class' => 'hic-event-featured-image' ] );

            echo '<div class="hic-event-card">';

            // Collapsed header
            echo '<div class="hic-event-collapsed">';

            echo '<div class="hic-event-date">
                    <span class="hic-event-day">' . esc_html( $day ) . '</span>
                    <span class="hic-event-month">' . esc_html( $month ) . '</span>
                    <span class="hic-event-year">' . esc_html( $year ) . '</span>
                  </div>';

            echo '<div class="hic-event-main">';
                if ( ! empty( $category_name ) ) {
                    echo '<span class="hic-event-category">' . esc_html( $category_name ) . '</span>';
                }
                echo '<h3 class="hic-event-title">' . esc_html( $title ) . '</h3>';
                echo '<p class="hic-event-time">' . esc_html( $start_time ) . '</p>';
            echo '</div>';

            echo '<div class="hic-event-right">
                    <div class="hic-event-chevron">
                        <span class="hic-chevron-icon"></span>
                    </div>

                    <div class="hic-share-container" data-url="' . esc_url( $permalink ) . '">
                        <div class="hic-share-container-inner">
                            <span class="hic-share-trigger"><i class="fa fa-share-alt"></i></span>
                            <div class="hic-share-menu">
                                <a href="#" class="hic-share fb"><i class="fa fa-facebook"></i></a>
                                <a href="#" class="hic-share tw"><i class="fa fa-twitter"></i></a>
                                <a href="#" class="hic-share wa"><i class="fa fa-whatsapp"></i></a>
                                <a href="#" class="hic-share ln"><i class="fa fa-linkedin"></i></a>
                                <a href="#" class="hic-share em"><i class="fa fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                  </div>';

            echo '</div>'; // collapsed

            // Expanded content
            if ( ! empty( $show_expanded ) ) {
                echo '<div class="hic-event-expanded">
                        <div class="hic-event-expanded-inner">

                            <div class="hic-event-left">';

                                if ( ! empty( $featured_img ) ) {
                                    echo '<div class="hic-event-image-wrapper">' . $featured_img . '</div>';
                                }

                                if ( ! empty( $speaker ) || ! empty( $location ) ) {
                                    echo '<div class="hic-event-meta">';
                                    if ( ! empty( $speaker ) ) {
                                        echo '<p class="hic-event-speaker"><strong>Speaker:</strong> ' . esc_html( $speaker ) . '</p>';
                                    }
                                    if ( ! empty( $location ) ) {
                                        echo '<p class="hic-event-location"><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                                    }
                                    echo '</div>';
                                }

                                echo '<div class="hic-event-actions">
                                        <a href="' . esc_url( $permalink ) . '" class="hic-event-button">Find out more</a>
                                      </div>';

                            echo '</div>'; // left

                            echo '<div class="hic-event-details">';

                                if ( ! empty( $content ) ) {
                                    echo '<div class="hic-event-description">' . $content . '</div>';
                                }

                                if ( ! empty( $timetable_rows ) ) {
                                    echo '<div class="hic-event-timetable">
                                            <h4 class="hic-timetable-heading">Programme</h4>
                                            <ul class="hic-timetable-list">';
                                    foreach ( $timetable_rows as $row ) {
                                        echo '<li class="hic-timetable-row">';
                                        if ( ! empty( $row['time'] ) ) {
                                            echo '<span class="hic-timetable-time">' . esc_html( $row['time'] ) . '</span>';
                                        }
                                        if ( ! empty( $row['label'] ) ) {
                                            echo '<span class="hic-timetable-label">' . esc_html( $row['label'] ) . '</span>';
                                        }
                                        echo '</li>';
                                    }
                                    echo '</ul>
                                          </div>';
                                }

                            echo '</div>'; // details

                        echo '</div>
                      </div>';
            }

            echo '</div>'; // card
        }

        echo '<div class="hic-events-footer">
                <a href="' . esc_url( get_post_type_archive_link( 'tribe_events' ) ) . '" class="hic-all-events-button">All Events</a>
              </div>';

        echo '</div>'; // wrapper

        wp_reset_postdata();

        return ob_get_clean();
    }

    add_shortcode( 'hussaini_events', 'hic_events_shortcode' );
}
