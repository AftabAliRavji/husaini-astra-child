<?php
/**
 * Featured Content Module
 * Husaini Islamic Centre Peterborough
 * CPT + Shortcode
 */

/**
 * Register Featured Content CPT
 */
if ( ! function_exists( 'hic_register_featured_content_cpt' ) ) {

    function hic_register_featured_content_cpt() {

        $labels = array(
            'name'               => 'Featured Content',
            'singular_name'      => 'Featured Item',
            'menu_name'          => 'Featured Content',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Featured Item',
            'edit_item'          => 'Edit Featured Item',
            'new_item'           => 'New Featured Item',
            'view_item'          => 'View Featured Item',
            'search_items'       => 'Search Featured Content',
            'not_found'          => 'No featured content found',
            'not_found_in_trash' => 'No featured content found in trash',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'rewrite'            => array( 'slug' => 'featured-content' ),
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'menu_icon'          => 'dashicons-star-filled',
            'show_in_rest'       => true,
        );

        register_post_type( 'featured-content', $args );
    }

    add_action( 'init', 'hic_register_featured_content_cpt' );
}

/**
 * Featured Content Shortcode
 */
if ( ! function_exists( 'hic_featured_content_shortcode' ) ) {

    function hic_featured_content_shortcode( $atts ) {

        $query = new WP_Query( array(
            'post_type'      => 'featured-content',
            'posts_per_page' => 3,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );

        ob_start();
        ?>

        <div class="hic-featured-grid">
            <?php if ( $query->have_posts() ) : ?>
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="hic-featured-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>

                        <h3><?php the_title(); ?></h3>

                        <p>
                            <?php
                            if ( has_excerpt() ) {
                                the_excerpt();
                            } else {
                                echo esc_html( wp_trim_words( get_the_content(), 20 ) );
                            }
                            ?>
                        </p>

                        <a href="<?php the_permalink(); ?>" class="hic-featured-link">Find out more</a>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <?php
        return ob_get_clean();
    }

    add_shortcode( 'hic_featured_content', 'hic_featured_content_shortcode' );
}
