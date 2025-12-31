<?php get_header(); ?>
<div class="container section">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article class="card" style="margin-bottom:20px;">
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="meta"><?php echo esc_html( get_the_date() ); ?></div>
        <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
    </article>
<?php endwhile; the_posts_pagination(); else : ?>
    <p><?php esc_html_e( 'No posts found.', 'aichatbotfree' ); ?></p>
<?php endif; ?>
</div>
<?php get_footer(); ?>
