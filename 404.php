<?php
/**
 * Custom 404 template for AI Chatbot Free theme.
 */
get_header();
?>
<main class="section">
  <div class="container">
    <div class="card" style="text-align:center; padding:40px;">
      <p class="badge" style="display:inline-block; background:rgba(255,255,255,0.06); color:var(--muted);">Error 404</p>
      <h1>Page not found</h1>
      <p>We couldn't find the page you're looking for. Try searching for a chatbot guide, tool review, or industry use case.</p>
      <?php get_search_form(); ?>
      <div style="margin-top:20px;">
        <a class="button primary" href="<?php echo esc_url(home_url('/')); ?>">Return to homepage</a>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
