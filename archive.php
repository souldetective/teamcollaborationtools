<?php
/**
 * Archive template for AI Chatbot Free theme.
 */
get_header();
?>
<main class="section">
  <div class="container">
    <div class="section-title">
      <div>
        <p class="badge" style="display:inline-block; background:rgba(255,255,255,0.06); color:var(--muted); margin:0;">
          Archive
        </p>
        <h1 style="margin-top:10px;"><?php the_archive_title(); ?></h1>
        <?php if (get_the_archive_description()) : ?>
          <p><?php echo wp_kses_post(get_the_archive_description()); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php if (have_posts()) : ?>
      <div class="grid cards">
        <?php while (have_posts()) : the_post(); ?>
          <article class="card">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p class="card-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>
            <a class="read-review-link cta-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'aichatbotfree' ); ?></a>
          </article>
        <?php endwhile; ?>
      </div>
      <div style="margin-top:24px;">
        <?php the_posts_pagination([
          'mid_size' => 2,
          'prev_text' => __('« Prev', 'aichatbotfree'),
          'next_text' => __('Next »', 'aichatbotfree'),
        ]); ?>
      </div>
    <?php else : ?>
      <div class="card">
        <h3>No content found</h3>
        <p>Try browsing other categories or search for a chatbot guide.</p>
        <?php get_search_form(); ?>
      </div>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
