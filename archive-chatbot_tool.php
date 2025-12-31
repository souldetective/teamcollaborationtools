<?php get_header(); ?>
<div class="container section">
    <div class="section-title">
        <h1><?php post_type_archive_title(); ?></h1>
        <p><?php esc_html_e( 'Browse in-depth reviews of chatbot builders and platforms.', 'aichatbotfree' ); ?></p>
    </div>
    <div class="grid cards">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article class="card">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>
                <a class="read-review-link cta-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read Review', 'aichatbotfree' ); ?></a>
            </article>
        <?php endwhile; else : ?>
            <p><?php esc_html_e( 'No tools found.', 'aichatbotfree' ); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
