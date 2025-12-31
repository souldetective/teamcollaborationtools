<?php get_header(); ?>
<div class="container section">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article class="card">
        <h1><?php the_title(); ?></h1>
        <div class="meta"><?php echo esc_html( get_the_date() ); ?></div>
        <div class="entry-content"><?php the_content(); ?></div>
        <?php
        $pricing         = aichatbotfree_get_field( 'pricing' );
        $free            = aichatbotfree_get_field( 'free_limits' );
        $channels        = aichatbotfree_get_field( 'supported_channels' );
        $ai              = aichatbotfree_get_field( 'ai_support' );
        $best_for        = aichatbotfree_get_field( 'best_for' );
        $affiliate       = aichatbotfree_get_field( 'affiliate_url' );
        $affiliate_title = aichatbotfree_get_field( 'affiliate_link_title' );
        $star_rating     = aichatbotfree_get_field( 'star_rating' );
        $rating_note     = aichatbotfree_get_field( 'rating_note' );
        $table_points    = [
            __( 'Pricing (from /month)', 'aichatbotfree' ) => $pricing,
            __( 'Free Plan Limits', 'aichatbotfree' )      => $free,
            __( 'Supported Channels', 'aichatbotfree' )    => $channels,
            __( 'AI Support', 'aichatbotfree' )            => $ai,
            __( 'Best For', 'aichatbotfree' )              => $best_for,
        ];
        ?>
        <?php if ( ( is_numeric( $star_rating ) && '' !== $star_rating ) || $rating_note ) : ?>
            <div class="tool-rating-block">
                <?php if ( is_numeric( $star_rating ) && '' !== $star_rating ) : ?>
                    <div class="tool-rating-stars">
                        <?php echo aichatbotfree_render_rating( (float) $star_rating ); ?>
                    </div>
                <?php endif; ?>
                <?php if ( $rating_note ) : ?>
                    <p class="tool-rating-note"><?php echo esc_html( $rating_note ); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="responsive-table single-tool-table">
            <table>
                <thead>
                    <tr>
                        <?php foreach ( $table_points as $label => $value ) : ?>
                            <?php if ( ! empty( $value ) ) : ?>
                                <th><?php echo esc_html( $label ); ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ( $affiliate ) : ?>
                            <th><?php esc_html_e( 'Website', 'aichatbotfree' ); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php foreach ( $table_points as $label => $value ) : ?>
                            <?php if ( ! empty( $value ) ) : ?>
                                <td data-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $value ); ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
        <?php if ( $affiliate ) : ?>
            <td data-label="<?php esc_attr_e( 'Website', 'aichatbotfree' ); ?>">
                <a class="website-link cta-text-link" href="<?php echo esc_url( $affiliate ); ?>" target="_blank" rel="nofollow noopener">
                    <?php
                    // Fallback to legacy CTA text when no custom title is provided.
                    echo esc_html( $affiliate_title ? $affiliate_title : __( 'Get Started', 'aichatbotfree' ) );
                    ?>
                </a>
            </td>
        <?php endif; ?>
    </tr>
</tbody>
</table>
</div>
        <?php if ( function_exists( 'have_rows' ) && have_rows( 'article_sections' ) ) : ?>
            <div class="article-sections">
                <?php get_template_part( 'template-parts/article-sections' ); ?>
            </div>
        <?php elseif ( function_exists( 'have_rows' ) && have_rows( 'comparison_sections' ) ) : ?>
            <div class="comparison-sections-wrapper">
                <?php get_template_part( 'template-parts/comparison-sections' ); ?>
            </div>
        <?php endif; ?>
    </article>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
