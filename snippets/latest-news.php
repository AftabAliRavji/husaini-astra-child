<?php
/**
 * Latest News Module
 * Husaini Islamic Centre Peterborough
 * Shortcode: [hic_latest_news]
 */

if ( ! function_exists( 'hic_latest_news_shortcode' ) ) {

    function hic_latest_news_shortcode( $atts ) {

        $query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'category_name'  => 'latest-news',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        ob_start();
        ?>

        <div class="hic-news-list">

            <?php if ( $query->have_posts() ) : ?>
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>

                    <div class="hic-news-card">

                        <?php if ( has_post_thumbnail() ) : ?>
                            <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>

                        <div class="hic-news-content">

                            <a class="hic-news-category" href="<?php echo esc_url( get_category_link( get_cat_ID( 'Latest News' ) ) ); ?>">
                                Latest News
                            </a>

                            <h3><?php the_title(); ?></h3>

                            <div class="hic-news-date">
                                <?php echo get_the_date( 'j M' ); ?> at <?php echo get_the_time( 'g:i a' ); ?>
                            </div>

                            <p>
                                <?php
                                if ( has_excerpt() ) {
                                    the_excerpt();
                                } else {
                                    echo esc_html( wp_trim_words( get_the_content(), 20 ) );
                                }
                                ?>
                            </p>

                            <a href="<?php the_permalink(); ?>">Read more</a>

                        </div>

                    </div>

                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </div>

        <?php
        return ob_get_clean();
    }

    add_shortcode( 'hic_latest_news', 'hic_latest_news_shortcode' );
}
