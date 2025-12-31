<?php get_header(); ?>
<div class="container section">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article class="card">
            <h1><?php the_title(); ?></h1>
            <div class="meta"><?php echo esc_html( get_the_date() ); ?></div>
            <div class="entry-content"><?php the_content(); ?></div>

            <?php if ( function_exists( 'have_rows' ) && have_rows( 'article_sections' ) ) : ?>
                <?php get_template_part( 'template-parts/article-sections' ); ?>
            <?php elseif ( function_exists( 'have_rows' ) && have_rows( 'comparison_sections' ) ) : ?>
                <div class="comparison-sections-wrapper">
                    <?php get_template_part( 'template-parts/comparison-sections' ); ?>
                </div>
            <?php endif; ?>
        </article>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
