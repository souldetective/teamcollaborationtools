<?php
get_header();

$option_page_id         = (int) get_option( 'page_on_front' );
$option                 = function_exists( 'acf_add_options_page' ) ? 'option' : ( $option_page_id ?: 'option' );
$hero_title             = aichatbotfree_get_field( 'hero_heading', $option );
$hero_subheading        = aichatbotfree_get_field( 'hero_subheading', $option );
$hero_icons             = aichatbotfree_get_field( 'hero_icons', $option );
$hero_background_color  = aichatbotfree_get_field( 'hero_background_color', $option );
$hero_background_image  = aichatbotfree_get_field( 'hero_background_image', $option );
$cta_primary_label      = aichatbotfree_get_field( 'hero_cta_primary_label', $option );
$cta_primary_url        = aichatbotfree_get_field( 'hero_cta_primary_url', $option );
$cta_secondary_label    = aichatbotfree_get_field( 'hero_cta_secondary_label', $option );
$cta_secondary_url      = aichatbotfree_get_field( 'hero_cta_secondary_url', $option );
$hero_reason_title      = aichatbotfree_get_field( 'hero_reason_title', $option, __( 'Why aichatbotfree.net?', 'aichatbotfree' ) );
$hero_reason_items      = aichatbotfree_get_field(
    'hero_reason_items',
    $option,
    [
        [ 'text' => __( 'Objective testing of free vs paid chatbot builders.', 'aichatbotfree' ) ],
        [ 'text' => __( 'Use-case guidance across industries and platforms.', 'aichatbotfree' ) ],
        [ 'text' => __( 'Affiliate transparency and always-updated reviews.', 'aichatbotfree' ) ],
    ]
);
$category_cards         = aichatbotfree_get_field( 'category_cards', $option );
$categories_title       = aichatbotfree_get_field( 'categories_title', $option, __( 'Browse by Category', 'aichatbotfree' ) );
$categories_intro       = aichatbotfree_get_field( 'categories_intro', $option );
$pillar_title           = aichatbotfree_get_field( 'pillar_title', $option, __( 'Featured Pillar Articles', 'aichatbotfree' ) );
$pillar_intro           = aichatbotfree_get_field( 'pillar_intro', $option );
$pillar_articles        = aichatbotfree_get_field( 'pillar_articles', $option );
$tool_highlight_title   = aichatbotfree_get_field( 'tool_highlight_title', $option );
$tool_highlight_intro   = aichatbotfree_get_field( 'tool_highlight_intro', $option );
$tool_highlight_manual  = aichatbotfree_get_field( 'tool_highlight', $option );
$tool_highlight_terms   = (array) aichatbotfree_get_field( 'tool_highlight_terms', $option, [] );
$tool_highlight_limit   = (int) aichatbotfree_get_field( 'tool_highlight_count', $option, 4 );
$tool_headers           = aichatbotfree_get_field( 'tool_highlight_headers', $option );
$free_headers           = aichatbotfree_get_field( 'free_headers', $option );
$paid_headers           = aichatbotfree_get_field( 'paid_headers', $option );
$free_comparison        = aichatbotfree_get_field( 'free_comparison', $option );
$paid_comparison        = aichatbotfree_get_field( 'paid_comparison', $option );
$use_cases_title        = aichatbotfree_get_field( 'use_cases_title', $option );
$use_cases_intro        = aichatbotfree_get_field( 'use_cases_intro', $option );
$use_cases              = aichatbotfree_get_field( 'use_cases', $option );
$latest_title           = aichatbotfree_get_field( 'latest_title', $option );
$latest_intro           = aichatbotfree_get_field( 'latest_intro', $option );
$latest_category        = aichatbotfree_get_field( 'latest_category', $option );
$latest_count           = (int) aichatbotfree_get_field( 'latest_count', $option, 3 );
$trust_title            = aichatbotfree_get_field( 'trust_title', $option );
$trust_items            = aichatbotfree_get_field( 'trust_items', $option );
$trust_copy             = aichatbotfree_get_field( 'trust_copy', $option );

$hero_styles = [];
if ( $hero_background_color ) {
    $hero_styles[] = 'background:' . $hero_background_color;
}
if ( $hero_background_image && isset( $hero_background_image['url'] ) ) {
    $hero_styles[] = 'background-image:url(' . esc_url( $hero_background_image['url'] ) . ')';
    $hero_styles[] = 'background-size:cover';
    $hero_styles[] = 'background-position:center';
}
$hero_style_attr = $hero_styles ? ' style="' . esc_attr( implode( ';', $hero_styles ) ) . '"' : '';

if ( empty( $hero_reason_items ) ) {
    $hero_reason_items = [
        [ 'text' => __( 'Objective testing of free vs paid chatbot builders.', 'aichatbotfree' ) ],
        [ 'text' => __( 'Use-case guidance across industries and platforms.', 'aichatbotfree' ) ],
        [ 'text' => __( 'Affiliate transparency and always-updated reviews.', 'aichatbotfree' ) ],
    ];
}

if ( ! $hero_reason_title ) {
    $hero_reason_title = __( 'Why aichatbotfree.net?', 'aichatbotfree' );
}

$tool_highlight = [];
if ( ! empty( $tool_highlight_terms ) ) {
    $tool_query = new WP_Query(
        [
            'post_type'      => 'chatbot_tool',
            'posts_per_page' => $tool_highlight_limit,
            'tax_query'      => [
                [
                    'taxonomy' => 'tool_type',
                    'field'    => 'term_id',
                    'terms'    => $tool_highlight_terms,
                ],
            ],
        ]
    );
    if ( $tool_query->have_posts() ) {
        $tool_highlight = $tool_query->posts;
    }
    wp_reset_postdata();
}

if ( empty( $tool_highlight ) && ! empty( $tool_highlight_manual ) ) {
    $tool_highlight = $tool_highlight_manual;
}
$highlight_has_website = false;
if ( ! empty( $tool_highlight ) ) {
    foreach ( $tool_highlight as $highlight_post ) {
        $highlight_id = $highlight_post instanceof WP_Post ? $highlight_post->ID : ( is_numeric( $highlight_post ) ? (int) $highlight_post : 0 );
        if ( ! $highlight_id ) {
            continue;
        }

        $link_data = aichatbotfree_get_affiliate_link_data( $highlight_id );

        if ( $link_data['url'] && $link_data['title'] ) {
            $highlight_has_website = true;
            break;
        }
    }
}
$free_show_website = aichatbotfree_should_show_website_column( $free_comparison );
$paid_show_website = aichatbotfree_should_show_website_column( $paid_comparison );
?>
<section class="hero"<?php echo $hero_style_attr; ?>>
    <div class="container hero-grid">
        <div>
            <div class="badge"><?php esc_html_e( 'AI Chatbot Guides & Reviews', 'aichatbotfree' ); ?></div>
            <h1><?php echo esc_html( $hero_title ? $hero_title : get_bloginfo( 'name' ) . ' – ' . __( 'Find the Best Free & AI Chatbots for Your Business', 'aichatbotfree' ) ); ?></h1>
            <?php if ( $hero_subheading ) : ?>
                <p><?php echo esc_html( $hero_subheading ); ?></p>
            <?php else : ?>
                <p><?php esc_html_e( 'We compare chatbot builders, highlight free vs paid plans, and map the best tools by industry.', 'aichatbotfree' ); ?></p>
            <?php endif; ?>
            <?php if ( $hero_icons ) : ?>
                <div class="hero-icons">
                    <?php foreach ( $hero_icons as $icon ) : ?>
                        <span class="pill">
                            <span class="pill-icon"><?php echo esc_html( $icon['icon'] ?? '' ); ?></span>
                            <?php echo esc_html( $icon['text'] ?? '' ); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="hero-actions">
                <?php if ( $cta_primary_label && $cta_primary_url ) : ?>
                    <a class="button primary" href="<?php echo esc_url( $cta_primary_url ); ?>"><?php echo esc_html( $cta_primary_label ); ?></a>
                <?php endif; ?>
                <?php if ( $cta_secondary_label && $cta_secondary_url ) : ?>
                    <a class="button secondary" href="<?php echo esc_url( $cta_secondary_url ); ?>"><?php echo esc_html( $cta_secondary_label ); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-card">
            <h3><?php echo esc_html( $hero_reason_title ); ?></h3>
            <?php if ( $hero_reason_items ) : ?>
                <ul>
                    <?php foreach ( $hero_reason_items as $reason ) : ?>
                        <?php
                        $reason_text   = trim( (string) ( $reason['text'] ?? '' ) );
                        if ( '' === $reason_text ) {
                            continue;
                        }
                        $reason_status = strtolower( (string) ( $reason['status'] ?? ( $reason['type'] ?? '' ) ) );
                        if ( preg_match( '/^(check|cross)\\s*:\\s*(.+)$/i', $reason_text, $matches ) ) {
                            $reason_status = strtolower( $matches[1] );
                            $reason_text   = trim( $matches[2] );
                        }
                        $reason_class = '';
                        if ( 'check' === $reason_status ) {
                            $reason_class = 'is-check';
                        } elseif ( 'cross' === $reason_status ) {
                            $reason_class = 'is-cross';
                        }
                        ?>
                        <li<?php echo $reason_class ? ' class="' . esc_attr( $reason_class ) . '"' : ''; ?>><?php echo esc_html( $reason_text ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section categories">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html( $categories_title ); ?></h2>
            <?php if ( $categories_intro ) : ?>
                <p><?php echo esc_html( $categories_intro ); ?></p>
            <?php endif; ?>
        </div>
        <div class="grid cards category-folders">
            <?php
            if ( $category_cards ) {
                foreach ( $category_cards as $card ) {
                    $category = $card['category'] ?? null;
                    $accent   = $card['accent_color'] ?? '#0066ff';
                    $icon     = $card['icon'] ?? '📂';
                    $link     = $category ? get_category_link( $category ) : '#';
                    $desc     = $category && ! empty( $category->description ) ? $category->description : __( 'Dive into guides for this category.', 'aichatbotfree' );
                    echo '<div class="card folder" style="--folder-accent:' . esc_attr( $accent ) . '">';
                    echo '<div class="folder-top">';
                    echo '<span class="folder-icon">' . esc_html( $icon ) . '</span>';
                    echo '<span class="folder-label">' . esc_html( $category ? $category->name : __( 'Category', 'aichatbotfree' ) ) . '</span>';
                    echo '</div>';
                    echo '<p>' . esc_html( $desc ) . '</p>';
                    // Convert CTA to text link to align with the standard Read Review styling.
                    echo '<a class="read-review-link cta-text-link" href="' . esc_url( $link ) . '">' . esc_html__( 'View Guides', 'aichatbotfree' ) . '</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . esc_html__( 'Add category cards in Homepage Options > Category Cards.', 'aichatbotfree' ) . '</p>';
            }
            ?>
        </div>
    </div>
</section>

<section class="section pillars">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html( $pillar_title ); ?></h2>
            <?php if ( $pillar_intro ) : ?>
                <p><?php echo esc_html( $pillar_intro ); ?></p>
            <?php endif; ?>
        </div>
        <div class="grid cards pillar-grid">
            <?php
            if ( $pillar_articles ) {
                foreach ( $pillar_articles as $post ) {
                    setup_postdata( $post );
                    ?>
                    <div class="card pillar-card">
                        <h3><?php the_title(); ?></h3>
                        <?php
                        // Apply a consistent excerpt length to keep cards visually balanced without truncating titles.
                        $pillar_excerpt = wp_trim_words( get_the_excerpt(), 28, '…' );
                        ?>
                        <p class="pillar-card__excerpt"><?php echo esc_html( $pillar_excerpt ); ?></p>
                        <div class="pillar-card__footer">
                            <a class="read-review-link cta-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read Guide', 'aichatbotfree' ); ?></a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__( 'Select pillar articles in Homepage Options.', 'aichatbotfree' ) . '</p>';
            }
            ?>
        </div>
    </div>
</section>

<section class="section tool-highlight">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html( $tool_highlight_title ); ?></h2>
            <?php if ( $tool_highlight_intro ) : ?>
                <p><?php echo esc_html( $tool_highlight_intro ); ?></p>
            <?php endif; ?>
        </div>
        <div class="card">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html( $tool_headers['tool'] ?? __( 'Tool', 'aichatbotfree' ) ); ?></th>
                        <th><?php echo esc_html( $tool_headers['free_plan'] ?? __( 'Free Plan', 'aichatbotfree' ) ); ?></th>
                        <th><?php echo esc_html( $tool_headers['channels'] ?? __( 'Channels', 'aichatbotfree' ) ); ?></th>
                        <th><?php echo esc_html( $tool_headers['ai_support'] ?? __( 'AI Support', 'aichatbotfree' ) ); ?></th>
                        <th><?php echo esc_html( $tool_headers['best_for'] ?? __( 'Best For', 'aichatbotfree' ) ); ?></th>
                        <th><?php echo esc_html( $tool_headers['rating'] ?? __( 'Rating', 'aichatbotfree' ) ); ?></th>
                        <?php if ( $highlight_has_website ) : ?>
                            <th><?php esc_html_e( 'Website', 'aichatbotfree' ); ?></th>
                        <?php endif; ?>
                        <th><?php esc_html_e( 'Read Review', 'aichatbotfree' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( $tool_highlight ) {
                        foreach ( $tool_highlight as $post ) {
                            setup_postdata( $post );
                            $free_plan = aichatbotfree_get_field( 'free_limits', $post->ID );
                            $channels  = aichatbotfree_get_field( 'supported_channels', $post->ID );
                            $ai        = aichatbotfree_get_field( 'ai_support', $post->ID );
                            $best_for  = aichatbotfree_get_field( 'best_for', $post->ID );
                            $rating    = aichatbotfree_get_field( 'star_rating', $post->ID );
                            $affiliate = aichatbotfree_get_affiliate_link_data( get_the_ID() );
                            $has_site  = $highlight_has_website && $affiliate['url'] && $affiliate['title'];
                            // When the Website column is enabled but data is missing, merge the CTA cell to avoid empty cells.
                            $review_cell_attributes = $highlight_has_website && ! $has_site ? ' colspan="2"' : '';
                            // Prefer the homepage-specific title when provided; fall back to the normal post title.
                            $homepage_title = get_field( 'homepage_section_title', get_the_ID() );
                            ?>
                            <tr>
                                <td><?php echo esc_html( $homepage_title ?: get_the_title() ); ?></td>
                                <td><?php echo esc_html( $free_plan ); ?></td>
                                <td><?php echo esc_html( $channels ); ?></td>
                                <td><?php echo esc_html( $ai ); ?></td>
                                <td><?php echo esc_html( $best_for ); ?></td>
                                <td><?php echo aichatbotfree_render_rating( $rating ); ?></td>
                                <?php if ( $has_site ) : ?>
                                    <td><a class="website-link read-review-link cta-text-link" href="<?php echo esc_url( $affiliate['url'] ); ?>" rel="nofollow noopener" target="_blank"><?php echo esc_html( $affiliate['title'] ); ?></a></td>
                                <?php endif; ?>
                                <td<?php echo $review_cell_attributes; ?>><a class="read-review-link cta-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read Review', 'aichatbotfree' ); ?></a></td>
                            </tr>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        $highlight_columns = 7 + ( $highlight_has_website ? 1 : 0 );
                        echo '<tr><td colspan="' . esc_attr( $highlight_columns ) . '">' . esc_html__( 'Choose chatbot tools via taxonomies or manual picks in Homepage Options.', 'aichatbotfree' ) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="section comparison-double">
    <div class="container comparison-stack">
        <div class="comparison-stack__header">
            <h2><?php esc_html_e( 'Free vs Paid Chatbot Plans', 'aichatbotfree' ); ?></h2>
            <p><?php esc_html_e( 'Quickly compare plans and jump to in-depth reviews.', 'aichatbotfree' ); ?></p>
        </div>
        <div class="comparison-stack__tables">
            <div class="card comparison-stack__table">
                <h3><?php esc_html_e( 'Free Plan Comparison', 'aichatbotfree' ); ?></h3>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html( $free_headers['tool'] ?? __( 'Tool', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $free_headers['plan'] ?? __( 'Free Plan', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $free_headers['channels'] ?? __( 'Channels', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $free_headers['ai'] ?? __( 'AI', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $free_headers['rating'] ?? __( 'Rating', 'aichatbotfree' ) ); ?></th>
                            <?php if ( $free_show_website ) : ?>
                                <th><?php esc_html_e( 'Website', 'aichatbotfree' ); ?></th>
                            <?php endif; ?>
                            <th><?php esc_html_e( 'Read Review', 'aichatbotfree' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php aichatbotfree_render_comparison_rows( $free_comparison, 'free', $free_show_website ); ?>
                    </tbody>
                </table>
            </div>
            <div class="card comparison-stack__table">
                <h3><?php esc_html_e( 'Paid Plan Comparison', 'aichatbotfree' ); ?></h3>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html( $paid_headers['tool'] ?? __( 'Tool', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $paid_headers['price'] ?? __( 'Starting At', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $paid_headers['channels'] ?? __( 'Channels', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $paid_headers['ai'] ?? __( 'AI', 'aichatbotfree' ) ); ?></th>
                            <th><?php echo esc_html( $paid_headers['rating'] ?? __( 'Rating', 'aichatbotfree' ) ); ?></th>
                            <?php if ( $paid_show_website ) : ?>
                                <th><?php esc_html_e( 'Website', 'aichatbotfree' ); ?></th>
                            <?php endif; ?>
                            <th><?php esc_html_e( 'Read Review', 'aichatbotfree' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php aichatbotfree_render_comparison_rows( $paid_comparison, 'paid', $paid_show_website ); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<section class="section use-cases">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html( $use_cases_title ); ?></h2>
            <?php if ( $use_cases_intro ) : ?>
                <p><?php echo esc_html( $use_cases_intro ); ?></p>
            <?php endif; ?>
        </div>
        <div class="grid cards use-case-grid">
            <?php
            if ( $use_cases ) {
                foreach ( $use_cases as $case ) {
                    $category = $case['category'] ?? null;
                    $link     = $category ? get_category_link( $category ) : '#';
                    $bg_image = $case['background']['url'] ?? '';
                    $bg_color = $case['background_color'] ?? '';
                    $styles   = [];
                    if ( $bg_image ) {
                        $styles[] = 'background-image:url(' . esc_url( $bg_image ) . ')';
                        $styles[] = 'background-size:cover';
                        $styles[] = 'background-position:center';
                    }
                    if ( $bg_color ) {
                        $styles[] = 'background-color:' . $bg_color;
                    }
                    $style_attr = $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';
                    echo '<div class="card use-case"' . $style_attr . '>';
                    if ( ! empty( $case['icon'] ) ) {
                        echo '<div class="use-case-icon">' . esc_html( $case['icon'] ) . '</div>';
                    }
                    echo '<h3>' . esc_html( $case['title'] ?? '' ) . '</h3>';
                    echo '<p>' . esc_html( $case['description'] ?? '' ) . '</p>';
                    // Switch to text-only CTA to mirror comparison table link styling.
                    echo '<a class="read-review-link cta-text-link" href="' . esc_url( $link ) . '">' . esc_html__( 'View Use Case', 'aichatbotfree' ) . '</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . esc_html__( 'Add industry cards in Homepage Options.', 'aichatbotfree' ) . '</p>';
            }
            ?>
        </div>
    </div>
</section>

<section class="section latest-posts">
    <div class="container">
        <div class="section-title">
            <h2><?php echo esc_html( $latest_title ); ?></h2>
            <?php if ( $latest_intro ) : ?>
                <p><?php echo esc_html( $latest_intro ); ?></p>
            <?php endif; ?>
        </div>
        <div class="grid latest-grid">
            <?php
            $latest_args = [
                'post_type'      => 'post',
                'posts_per_page' => $latest_count ? $latest_count : 3,
            ];
            if ( $latest_category ) {
                $latest_args['cat'] = $latest_category;
            }
            $latest = new WP_Query( $latest_args );
            if ( $latest->have_posts() ) {
                while ( $latest->have_posts() ) {
                    $latest->the_post();
                    ?>
                    <article class="latest-card">
                        <div class="meta"><?php echo esc_html( get_the_date() ); ?></div>
                        <h3><?php the_title(); ?></h3>
                        <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
                        <div class="latest-card__footer">
                            <a class="read-review-link cta-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read Article', 'aichatbotfree' ); ?></a>
                        </div>
                    </article>
                    <?php
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>

<section class="section trust">
    <div class="container trust-grid">
        <div class="section-title">
            <h2><?php echo esc_html( $trust_title ); ?></h2>
        </div>
        <?php if ( $trust_items ) : ?>
            <div class="grid cards trust-cards">
                <?php foreach ( $trust_items as $item ) : ?>
                    <div class="card trust-card">
                        <?php if ( ! empty( $item['icon'] ) ) : ?>
                            <div class="trust-icon"><?php echo esc_html( $item['icon'] ); ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $item['heading'] ) ) : ?>
                            <h3><?php echo esc_html( $item['heading'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $item['text'] ) ) : ?>
                            <p><?php echo esc_html( $item['text'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ( $trust_copy ) : ?>
            <p><?php echo esc_html( $trust_copy ); ?></p>
        <?php else : ?>
            <p><?php esc_html_e( 'We manually test chatbot tools, disclose affiliate partnerships, and keep our comparisons objective and refreshed.', 'aichatbotfree' ); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
