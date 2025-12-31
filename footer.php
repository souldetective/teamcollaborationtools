</main>
<footer class="site-footer" role="contentinfo">
    <div class="container footer-grid">
        <div class="footer-brand">
            <div class="footer-logo">
                <?php echo aichatbotfree_get_footer_logo_html(); ?>
            </div>
            <p class="footer-note"><?php echo esc_html__( 'AIChatBotFree compares free and paid chatbot builders with transparent reviews to help you choose the right tool.', 'aichatbotfree' ); ?></p>
        </div>

        <nav class="footer-column" data-footer-column="about" aria-label="<?php esc_attr_e( 'Footer About Links', 'aichatbotfree' ); ?>">
            <h4><?php esc_html_e( 'About', 'aichatbotfree' ); ?></h4>
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'footer_about',
                    'container'      => false,
                    'fallback_cb'    => '__return_empty_string',
                    'menu_class'     => 'footer-menu',
                    'depth'          => 1,
                ]
            );
            ?>
        </nav>

        <nav class="footer-column" data-footer-column="guides" aria-label="<?php esc_attr_e( 'Footer Guides Links', 'aichatbotfree' ); ?>">
            <h4><?php esc_html_e( 'Guides', 'aichatbotfree' ); ?></h4>
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'footer_guides',
                    'container'      => false,
                    'fallback_cb'    => '__return_empty_string',
                    'menu_class'     => 'footer-menu',
                    'depth'          => 1,
                ]
            );
            ?>
        </nav>

        <nav class="footer-column" data-footer-column="industry" aria-label="<?php esc_attr_e( 'Footer Industry Links', 'aichatbotfree' ); ?>">
            <h4><?php esc_html_e( 'Industries', 'aichatbotfree' ); ?></h4>
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'footer_industry',
                    'container'      => false,
                    'fallback_cb'    => '__return_empty_string',
                    'menu_class'     => 'footer-menu',
                    'depth'          => 1,
                ]
            );
            ?>
        </nav>

    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
