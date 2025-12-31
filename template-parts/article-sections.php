<?php
/**
 * Article sections flexible renderer.
 *
 * Expected to be included via get_template_part( 'template-parts/article-sections' ).
 */

if ( ! have_rows( 'article_sections' ) ) {
    return;
}

$faq_schema_entities  = [];
$faq_schema_override  = '';
$accordion_index      = 0;

if ( ! function_exists( 'aichatbotfree_article_section_style_attr' ) ) {
    /**
     * Builds an inline style attribute for flexible modules using color picker values.
     *
     * @return string
     */
    function aichatbotfree_article_section_style_attr() {
        $styles = [];

        $bg   = get_sub_field( 'section_background' );
        $text = get_sub_field( 'section_text_color' );

        if ( $bg ) {
            $sanitized_bg = sanitize_hex_color( $bg );
            $styles[]     = '--section-bg:' . ( $sanitized_bg ? $sanitized_bg : esc_attr( $bg ) );
        }

        if ( $text ) {
            $sanitized_text = sanitize_hex_color( $text );
            $styles[]       = '--section-color:' . ( $sanitized_text ? $sanitized_text : esc_attr( $text ) );
        }

        if ( empty( $styles ) ) {
            return '';
        }

        return ' style="' . esc_attr( implode( ';', $styles ) ) . '"';
    }
}
?>
<div class="article-sections-wrapper">
    <?php
    while ( have_rows( 'article_sections' ) ) {
        the_row();
        $layout = get_row_layout();

        switch ( $layout ) {
            case 'hero_section':
                $title = get_sub_field( 'hero_title' );
                $intro = get_sub_field( 'hero_intro' );
                $ctas  = get_sub_field( 'hero_ctas' );
                ?>
                <section class="article-hero"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="article-hero__inner">
                        <?php if ( $title ) : ?>
                            <h1 class="article-hero__title"><?php echo esc_html( $title ); ?></h1>
                        <?php endif; ?>
                        <?php if ( $intro ) : ?>
                            <p class="article-hero__intro"><?php echo esc_html( $intro ); ?></p>
                        <?php endif; ?>
                        <?php if ( $ctas ) : ?>
                            <div class="article-hero__ctas">
                                <?php
                                foreach ( $ctas as $cta ) :
                                    $label = isset( $cta['cta_label'] ) ? $cta['cta_label'] : '';
                                    $url   = isset( $cta['cta_url'] ) ? $cta['cta_url'] : '';
                                    if ( ! $label || ! $url ) {
                                        continue;
                                    }
                                    ?>
                                    <a class="button primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'featured_grid':
                $featured_title       = get_sub_field( 'featured_overall_title' );
                $featured_icon        = get_sub_field( 'featured_icon' );
                $featured_description = get_sub_field( 'featured_description' );
                $featured_cards       = get_sub_field( 'featured_cards' );

                if ( ! $featured_title && ! $featured_icon && ! $featured_description && empty( $featured_cards ) ) {
                    break;
                }
                ?>
                <section class="article-section featured-grid"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="featured-grid__header">
                        <?php if ( $featured_icon && isset( $featured_icon['ID'] ) ) : ?>
                            <div class="featured-grid__icon" aria-hidden="true">
                                <?php echo wp_get_attachment_image( $featured_icon['ID'], 'thumbnail' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $featured_title ) : ?>
                            <h2 class="featured-grid__title"><?php echo esc_html( $featured_title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $featured_description ) : ?>
                            <div class="featured-grid__description"><?php echo wp_kses_post( $featured_description ); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $featured_cards ) ) : ?>
                        <div class="featured-grid__cards">
                            <?php foreach ( $featured_cards as $card ) :
                                $card_icon        = isset( $card['card_icon']['ID'] ) ? $card['card_icon']['ID'] : null;
                                $card_title       = isset( $card['card_title'] ) ? $card['card_title'] : '';
                                $card_description = isset( $card['card_description'] ) ? $card['card_description'] : '';

                                if ( ! $card_icon && ! $card_title && ! $card_description ) {
                                    continue;
                                }
                                ?>
                                <div class="featured-card">
                                    <?php if ( $card_icon ) : ?>
                                        <div class="featured-card__icon" aria-hidden="true">
                                            <?php echo wp_get_attachment_image( $card_icon, 'thumbnail' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $card_title ) : ?>
                                        <h3 class="featured-card__title"><?php echo esc_html( $card_title ); ?></h3>
                                    <?php endif; ?>
                                    <?php if ( $card_description ) : ?>
                                        <div class="featured-card__description"><?php echo wp_kses_post( $card_description ); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
                <?php
                break;

            case 'at_a_glance':
                $glance_items = get_sub_field( 'glance_items' );

                // Build a sanitized list of title + anchor pairs. The anchor is stored as a fragment (#example-section) and
                // must match an id attribute elsewhere on the same page so native browser scrolling works without JS.
                $list_items = [];

                if ( ! empty( $glance_items ) ) {
                    foreach ( $glance_items as $glance_item ) {
                        $title  = isset( $glance_item['glance_label'] ) ? trim( $glance_item['glance_label'] ) : '';
                        $anchor = '';

                        if ( isset( $glance_item['glance_url'] ) ) {
                            $anchor = trim( $glance_item['glance_url'] );
                        }

                        // Backward compatibility: fall back to the previous description field if it stores the anchor.
                        if ( ! $anchor && isset( $glance_item['glance_desc'] ) ) {
                            $anchor = trim( $glance_item['glance_desc'] );
                        }

                        if ( ! $title || ! $anchor ) {
                            continue;
                        }

                        $anchor = ltrim( $anchor, '#' );

                        if ( '' === $anchor ) {
                            continue;
                        }

                        $list_items[] = [
                            'title'  => $title,
                            'anchor' => $anchor,
                        ];
                    }
                }

                if ( empty( $list_items ) ) {
                    break;
                }

                $glance_count = count( $list_items );
                ?>
                <section class="article-section article-glance"<?php echo aichatbotfree_article_section_style_attr(); ?> itemscope itemtype="https://schema.org/TableOfContents" role="navigation" aria-label="<?php esc_attr_e( 'At a Glance', 'aichatbotfree' ); ?>">
                    <div class="section-heading">
                        <h2 id="at-a-glance">
                            <?php
                            printf(
                                esc_html__( 'At a Glance: %1$d key section%2$s', 'aichatbotfree' ),
                                (int) $glance_count,
                                1 === $glance_count ? '' : 's'
                            );
                            ?>
                        </h2>
                    </div>
                    <ul class="glance-list">
                        <?php foreach ( $list_items as $index => $list_item ) :
                            $anchor = ltrim( $list_item['anchor'], '#' );
                            $href   = '#' . $anchor;
                            ?>
                            <li class="glance-list__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a href="<?php echo esc_url( $href ); ?>" itemprop="url">
                                    <span itemprop="name"><?php echo esc_html( $list_item['title'] ); ?></span>
                                </a>
                                <meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>" />
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php
                break;

            case 'icon_grid':
                $icon_grid_title = get_sub_field( 'icon_grid_title' );
                $icons           = get_sub_field( 'icon_items' );

                if ( ! $icon_grid_title && empty( $icons ) ) {
                    break;
                }
                ?>
                <section class="article-section icon-grid"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <?php if ( $icon_grid_title ) : ?>
                        <div class="section-heading">
                            <h2><?php echo esc_html( $icon_grid_title ); ?></h2>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $icons ) ) : ?>
                        <div class="icon-grid__items">
                            <?php foreach ( $icons as $icon ) :
                                $image = isset( $icon['icon_image']['ID'] ) ? $icon['icon_image']['ID'] : null;
                                $text  = isset( $icon['icon_label'] ) ? $icon['icon_label'] : '';

                                if ( ! $image && ! $text ) {
                                    continue;
                                }
                                ?>
                                <div class="icon-grid__item">
                                    <?php if ( $image ) : ?>
                                        <div class="icon-grid__icon" aria-hidden="true"><?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?></div>
                                    <?php endif; ?>
                                    <?php if ( $text ) : ?>
                                        <span class="icon-grid__text"><?php echo esc_html( $text ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
                <?php
                break;

            case 'chatbot_types':
                $section_title = get_sub_field( 'type_section_title' );
                $types         = get_sub_field( 'type_items' );
                $best_tools    = get_sub_field( 'best_tools' );

                if ( empty( $types ) && empty( $best_tools ) ) {
                    break;
                }

                $bg_color   = get_sub_field( 'type_bg_color' );
                $font_color = get_sub_field( 'type_font_color' );
                $style_bits = [];

                if ( $bg_color ) {
                    $sanitized_bg = sanitize_hex_color( $bg_color );
                    $color_value  = $sanitized_bg ? $sanitized_bg : esc_attr( $bg_color );
                    $style_bits[] = 'background-color:' . $color_value;
                    $style_bits[] = '--section-bg:' . $color_value;
                }

                if ( $font_color ) {
                    $sanitized_font = sanitize_hex_color( $font_color );
                    $font_value     = $sanitized_font ? $sanitized_font : esc_attr( $font_color );
                    $style_bits[]   = 'color:' . $font_value;
                    $style_bits[]   = '--section-color:' . $font_value;
                }

                $style_attr = $style_bits ? ' style="' . esc_attr( implode( ';', $style_bits ) ) . '"' : '';

                ?>
                <section class="article-section chatbot-types">
                    <div class="chatbot-types-section"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                        <?php if ( $section_title ) : ?>
                            <h2 class="chatbot-types-title"><?php echo esc_html( $section_title ); ?></h2>
                        <?php else : ?>
                            <h2 class="chatbot-types-title"><?php esc_html_e( 'Types of Chatbots', 'aichatbotfree' ); ?></h2>
                        <?php endif; ?>

                        <?php if ( ! empty( $types ) ) : ?>
                            <div class="chatbot-type-accordion" data-accordion="chatbot-types">
                                <?php foreach ( $types as $type ) :
                                    $accordion_index++;
                                    $button_id = 'type-accordion-' . $accordion_index;

                                    $type_title = isset( $type['type_title'] ) ? $type['type_title'] : '';
                                    $type_desc  = isset( $type['type_desc'] ) ? $type['type_desc'] : '';
                                    $pros       = isset( $type['type_pros'] ) ? (array) $type['type_pros'] : [];
                                    $cons       = isset( $type['type_cons'] ) ? (array) $type['type_cons'] : [];
                                    $tools      = isset( $type['type_tools'] ) ? (array) $type['type_tools'] : [];

                                    $pros = array_values( array_filter( $pros, function ( $item ) {
                                        return ! empty( $item['pro_item'] );
                                    } ) );

                                    $cons = array_values( array_filter( $cons, function ( $item ) {
                                        return ! empty( $item['con_item'] );
                                    } ) );

                                    $tools = array_values( array_filter( $tools, function ( $item ) {
                                        return ! empty( $item['tool_name'] );
                                    } ) );

                                    if ( ! $type_title && ! $type_desc && empty( $pros ) && empty( $cons ) && empty( $tools ) ) {
                                        continue;
                                    }
                                    ?>
                                    <div class="chatbot-type-accordion-item">
                                        <button class="chatbot-type-accordion-header" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $button_id ); ?>" id="<?php echo esc_attr( $button_id ); ?>-button">
                                            <span class="chatbot-type-title"><?php echo esc_html( $type_title ); ?></span>
                                            <span class="accordion-icon" aria-hidden="true">+</span>
                                        </button>
                                        <div class="chatbot-type-accordion-body" id="<?php echo esc_attr( $button_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $button_id ); ?>-button">
                                            <?php if ( $type_desc ) : ?>
                                                <div class="type-description"><?php echo wp_kses_post( $type_desc ); ?></div>
                                            <?php endif; ?>

                                            <?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
                                                <div class="pros-cons-wrapper">
                                                    <div class="pros-column">
                                                        <h4><?php esc_html_e( 'Pros', 'aichatbotfree' ); ?></h4>
                                                        <?php if ( ! empty( $pros ) ) : ?>
                                                            <ul>
                                                                <?php foreach ( $pros as $pro ) : ?>
                                                                    <li><span class="tick-icon" aria-hidden="true">✓</span><?php echo esc_html( $pro['pro_item'] ); ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="cons-column">
                                                        <h4><?php esc_html_e( 'Cons', 'aichatbotfree' ); ?></h4>
                                                        <?php if ( ! empty( $cons ) ) : ?>
                                                            <ul>
                                                                <?php foreach ( $cons as $con ) : ?>
                                                                    <li><span class="cross-icon" aria-hidden="true">✕</span><?php echo esc_html( $con['con_item'] ); ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( ! empty( $tools ) ) : ?>
                                                <div class="type-tools">
                                                    <h4><?php esc_html_e( 'Best Tools', 'aichatbotfree' ); ?></h4>
                                                    <ul class="type-tools-list">
                                                        <?php foreach ( $tools as $tool ) :
                                                            $tool_name = isset( $tool['tool_name'] ) ? $tool['tool_name'] : '';
                                                            $tool_link = isset( $tool['tool_link'] ) ? $tool['tool_link'] : '';
                                                            ?>
                                                            <li>
                                                                <?php if ( $tool_link ) : ?>
                                                                    <a href="<?php echo esc_url( $tool_link ); ?>" target="_blank" rel="nofollow noopener"><?php echo esc_html( $tool_name ); ?></a>
                                                                <?php else : ?>
                                                                    <?php echo esc_html( $tool_name ); ?>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $best_tools ) ) : ?>
                            <div class="chatbot-best-tools">
                                <div class="best-tools-header">
                                    <h3><?php esc_html_e( 'Best Tools', 'aichatbotfree' ); ?></h3>
                                </div>
                                <div class="best-tools-grid">
                                    <?php foreach ( $best_tools as $tool ) :
                                        $tool_name = isset( $tool['tool_name'] ) ? $tool['tool_name'] : '';
                                        $tool_link = isset( $tool['tool_link'] ) ? $tool['tool_link'] : '';
                                        if ( ! $tool_name ) {
                                            continue;
                                        }
                                        ?>
                                        <div class="best-tools-cell">
                                            <span class="tool-name"><?php echo esc_html( $tool_name ); ?></span>
                                            <?php if ( $tool_link ) : ?>
                                                <a href="<?php echo esc_url( $tool_link ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Visit', 'aichatbotfree' ); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'how_it_works':
                $how_title = get_sub_field( 'how_it_works_title' );
                $how_intro = get_sub_field( 'how_it_works_intro' );
                $steps     = get_sub_field( 'how_it_works_steps' );

                if ( empty( $how_title ) && empty( $how_intro ) && empty( $steps ) ) {
                    break;
                }

                $has_intro = ! empty( $how_intro );
                ?>
                <section class="article-section how-it-works"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="how-it-works__inner<?php echo $has_intro ? ' has-intro' : ''; ?>">
                        <?php if ( $how_title ) : ?>
                            <h2 class="how-it-works__title"><?php echo esc_html( $how_title ); ?></h2>
                        <?php endif; ?>

                        <?php if ( $how_intro ) : ?>
                            <div class="how-it-works__intro">
                                <?php echo wp_kses_post( $how_intro ); // Only render intro if it contains content. ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $steps ) : ?>
                            <div class="how-it-works__steps">
                                <?php
                                $step_index = 1;
                                foreach ( $steps as $step ) :
                                    $step_desc = isset( $step['step_description'] ) ? $step['step_description'] : '';

                                    if ( ! $step_desc ) {
                                        $step_index++;
                                        continue;
                                    }
                                    ?>
                                    <div class="how-it-works__step">
                                        <div class="how-it-works__step-number" aria-hidden="true"><?php echo esc_html( $step_index ); ?></div>
                                        <div class="how-it-works__step-body"><?php echo wp_kses_post( $step_desc ); ?></div>
                                    </div>
                                    <?php
                                    $step_index++;
                                endforeach;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'feature_grid':
                $features = get_sub_field( 'feature_items' );
                if ( empty( $features ) ) {
                    break;
                }
                ?>
                <section class="article-section feature-grid"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="feature-grid__items">
                        <?php foreach ( $features as $feature ) :
                            $icon  = isset( $feature['feature_icon'] ) ? $feature['feature_icon'] : '';
                            $title = isset( $feature['feature_title'] ) ? $feature['feature_title'] : '';
                            $desc  = isset( $feature['feature_desc'] ) ? $feature['feature_desc'] : '';
                            ?>
                            <div class="feature-card">
                                <?php if ( $icon ) : ?>
                                    <div class="feature-card__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></div>
                                <?php endif; ?>
                                <?php if ( $title ) : ?>
                                    <h3><?php echo esc_html( $title ); ?></h3>
                                <?php endif; ?>
                                <?php if ( $desc ) : ?>
                                    <p><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php
                break;

            case 'comparison_sections':
                $comparison_sections = get_sub_field( 'comparison_sections' );

                if ( empty( $comparison_sections ) ) {
                    break;
                }

                $comparison_style_attr = aichatbotfree_article_section_style_attr();

                get_template_part(
                    'template-parts/comparison-sections',
                    null,
                    [
                        'sections'           => $comparison_sections,
                        'container_classes'  => 'article-section',
                        'style_attr'         => $comparison_style_attr,
                    ]
                );
                break;

            case 'comparison_table':
                $rows = get_sub_field( 'table_rows' );
                if ( empty( $rows ) ) {
                    break;
                }
                ?>
                <section class="article-section comparison-table"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="section-heading">
                        <h2><?php esc_html_e( 'Comparison Table', 'aichatbotfree' ); ?></h2>
                    </div>
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Tool', 'aichatbotfree' ); ?></th>
                                    <th><?php esc_html_e( 'Free Plan', 'aichatbotfree' ); ?></th>
                                    <th><?php esc_html_e( 'AI Support', 'aichatbotfree' ); ?></th>
                                    <th><?php esc_html_e( 'Best For', 'aichatbotfree' ); ?></th>
                                    <th><?php esc_html_e( 'Action', 'aichatbotfree' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $rows as $row ) :
                                    $name   = isset( $row['tool_name'] ) ? $row['tool_name'] : '';
                                    $is_free = ! empty( $row['tool_free'] );
                                    $ai     = isset( $row['tool_ai'] ) ? $row['tool_ai'] : '';
                                    $best   = isset( $row['tool_best_for'] ) ? $row['tool_best_for'] : '';
                                    $url    = isset( $row['tool_affiliate_url'] ) ? $row['tool_affiliate_url'] : '';
                                    $btn    = isset( $row['tool_button_text'] ) ? $row['tool_button_text'] : '';
                                    ?>
                                    <tr>
                                        <td data-label="<?php esc_attr_e( 'Tool', 'aichatbotfree' ); ?>"><?php echo esc_html( $name ); ?></td>
                                        <td data-label="<?php esc_attr_e( 'Free Plan', 'aichatbotfree' ); ?>">
                                            <?php echo $is_free ? esc_html__( 'Yes', 'aichatbotfree' ) : esc_html__( 'No', 'aichatbotfree' ); ?>
                                        </td>
                                        <td data-label="<?php esc_attr_e( 'AI Support', 'aichatbotfree' ); ?>"><?php echo esc_html( $ai ); ?></td>
                                        <td data-label="<?php esc_attr_e( 'Best For', 'aichatbotfree' ); ?>"><?php echo esc_html( $best ); ?></td>
                                    <td data-label="<?php esc_attr_e( 'Action', 'aichatbotfree' ); ?>">
                                        <?php if ( $url && $btn ) : ?>
                                            <a class="read-review-link cta-text-link" href="<?php echo esc_url( $url ); ?>" rel="nofollow noopener" target="_blank"><?php echo esc_html( $btn ); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <?php
                break;

            case 'industry_cards':
                $industry_title       = get_sub_field( 'industry_overall_title' );
                $industry_description = get_sub_field( 'industry_description' );
                $industries           = get_sub_field( 'industry_cards_group' );

                if ( ! $industry_title && ! $industry_description && empty( $industries ) ) {
                    break;
                }
                ?>
                <section class="article-section industry-cards"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <?php if ( $industry_title || $industry_description ) : ?>
                        <div class="section-heading">
                            <?php if ( $industry_title ) : ?>
                                <h2><?php echo esc_html( $industry_title ); ?></h2>
                            <?php endif; ?>
                            <?php if ( $industry_description ) : ?>
                                <div class="section-description"><?php echo wp_kses_post( $industry_description ); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $industries ) ) : ?>
                        <div class="industry-grid">
                            <?php foreach ( $industries as $industry ) :
                                $title = isset( $industry['industry_title'] ) ? $industry['industry_title'] : '';
                                $desc  = isset( $industry['industry_description_card'] ) ? $industry['industry_description_card'] : '';
                                $icon  = isset( $industry['industry_icon']['ID'] ) ? $industry['industry_icon']['ID'] : null;

                                if ( ! $title && ! $desc && ! $icon ) {
                                    continue;
                                }
                                ?>
                                <div class="industry-card">
                                    <?php if ( $icon ) : ?>
                                        <div class="industry-card__icon" aria-hidden="true">
                                            <?php echo wp_get_attachment_image( $icon, 'medium' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $title ) : ?>
                                        <h3><?php echo esc_html( $title ); ?></h3>
                                    <?php endif; ?>
                                    <?php if ( $desc ) : ?>
                                        <div class="industry-card__description"><?php echo wp_kses_post( $desc ); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
                <?php
                break;

            case 'internal_links':
                $links = get_sub_field( 'internal_items' );
                if ( empty( $links ) ) {
                    break;
                }
                ?>
                <section class="article-section internal-links"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="section-heading">
                        <h2><?php esc_html_e( 'Further Reading', 'aichatbotfree' ); ?></h2>
                    </div>
                    <ul class="internal-links__list">
                        <?php foreach ( $links as $link ) :
                            $title = isset( $link['internal_link_title'] ) ? $link['internal_link_title'] : '';
                            $url   = isset( $link['internal_link_url'] ) ? $link['internal_link_url'] : '';
                            if ( ! $title || ! $url ) {
                                continue;
                            }
                            ?>
                            <li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php
                break;

            case 'content_block':
                $heading = get_sub_field( 'cb_heading' );
                $content = get_sub_field( 'cb_content' );
                $rows    = get_sub_field( 'cb_rows' );

                if ( ! function_exists( 'aichatbotfree_cb_render_centered_title' ) ) {
                    /**
                     * Render a centered title overlay for a Content Block column.
                     *
                     * @param array $column Column data from ACF.
                     *
                     * @return string
                     */
                    function aichatbotfree_cb_render_centered_title( $column ) {
                        $title = isset( $column['centered_title'] ) ? trim( $column['centered_title'] ) : '';

                        if ( '' === $title ) {
                            return '';
                        }

                        $title_color_raw = isset( $column['centered_title_color'] ) ? $column['centered_title_color'] : '';
                        $title_color     = $title_color_raw ? ( sanitize_hex_color( $title_color_raw ) ?: $title_color_raw ) : '';
                        $title_size_raw  = isset( $column['centered_title_font_size'] ) ? $column['centered_title_font_size'] : '';
                        $title_size      = '';

                        if ( '' !== $title_size_raw ) {
                            $numeric_size = preg_replace( '/[^0-9.]/', '', (string) $title_size_raw );
                            if ( '' !== $numeric_size ) {
                                $title_size = $numeric_size . 'px';
                            }
                        }

                        $styles = [];

                        if ( $title_color ) {
                            $styles[] = '--cb-centered-title-color:' . $title_color;
                        }

                        if ( $title_size ) {
                            $styles[] = '--cb-centered-title-size:' . $title_size;
                        }

                        $style_attr = $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';

                        return '<div class="content-block__centered-title"' . $style_attr . '><span>' . esc_html( $title ) . '</span></div>';
                    }
                }

                if ( ! function_exists( 'aichatbotfree_cb_render_column' ) ) {
                    /**
                     * Render a Content Block column based on its type.
                     *
                     * @param array  $column Column data from ACF.
                     * @param string $type   image|text.
                     *
                     * @return string
                     */
                    function aichatbotfree_cb_render_column( $column, $type ) {
                        $background_color_raw = isset( $column['background_color'] ) ? $column['background_color'] : '';
                        $background_color     = $background_color_raw ? ( sanitize_hex_color( $background_color_raw ) ?: $background_color_raw ) : '';
                        $title                = isset( $column['title'] ) ? $column['title'] : '';
                        $title_color_raw      = isset( $column['title_color'] ) ? $column['title_color'] : '';
                        $title_color          = $title_color_raw ? ( sanitize_hex_color( $title_color_raw ) ?: $title_color_raw ) : '';
                        $title_size_raw       = isset( $column['title_font_size'] ) ? $column['title_font_size'] : '';
                        $title_size           = '';
                        $centered_title       = aichatbotfree_cb_render_centered_title( $column );

                        if ( '' !== $title_size_raw ) {
                            $numeric_size = preg_replace( '/[^0-9.]/', '', (string) $title_size_raw );
                            if ( '' !== $numeric_size ) {
                                $title_size = $numeric_size . 'px';
                            }
                        }

                        if ( 'image' === $type ) {
                            $background_image = isset( $column['background_image'] ) ? $column['background_image'] : null;
                            $image_id         = null;

                            if ( is_array( $background_image ) && isset( $background_image['ID'] ) ) {
                                $image_id = $background_image['ID'];
                            } elseif ( is_numeric( $background_image ) ) {
                                $image_id = (int) $background_image;
                            }

                            $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';

                            if ( $image_url ) {
                                $styles = [];

                                if ( $background_color ) {
                                    $styles[] = '--cb-col-bg:' . $background_color;
                                    $styles[] = 'background-color:' . $background_color;
                                }

                                $styles[] = "background-image: url('" . esc_url( $image_url ) . "')";
                                $styles[] = 'background-size:cover';
                                $styles[] = 'background-position:center';
                                $styles[] = 'background-repeat:no-repeat';

                                $style_attr = $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';

                                // Image column: full-cover background only with inline cover properties for reliability.
                                return '<div class="content-block__column content-block__column--image' . ( $centered_title ? ' content-block__column--has-centered-title' : '' ) . '"' . $style_attr . '>' . $centered_title . '</div>';
                            }

                            if ( ! $background_color && '' === trim( $title ) ) {
                                return '';
                            }

                            $styles = [];
                            if ( $background_color ) {
                                $styles[] = '--cb-col-bg:' . $background_color;
                            }
                            if ( $title_color ) {
                                $styles[] = '--cb-title-color:' . $title_color;
                            }
                            if ( $title_size ) {
                                $styles[] = '--cb-title-size:' . $title_size;
                            }

                            $style_attr = $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';
                            $title_html = $title ? '<h3>' . esc_html( $title ) . '</h3>' : '';

                            // Image column fallback: color block with centered title.
                            return '<div class="content-block__column content-block__column--color content-block__column--image-fallback' . ( $centered_title ? ' content-block__column--has-centered-title' : '' ) . '"' . $style_attr . '><div class="content-block__title-wrap">' . $title_html . '</div>' . $centered_title . '</div>';
                        }

                        $column_content   = isset( $column['column_content'] ) ? $column['column_content'] : '';
                        $text_color_raw   = isset( $column['text_color'] ) ? $column['text_color'] : '';
                        $text_color       = $text_color_raw ? ( sanitize_hex_color( $text_color_raw ) ?: $text_color_raw ) : '';
                        $link_color_raw   = isset( $column['link_color'] ) ? $column['link_color'] : '';
                        $link_color       = $link_color_raw ? ( sanitize_hex_color( $link_color_raw ) ?: $link_color_raw ) : '';
                        $hover_color_raw  = isset( $column['link_hover_color'] ) ? $column['link_hover_color'] : '';
                        $hover_color      = $hover_color_raw ? ( sanitize_hex_color( $hover_color_raw ) ?: $hover_color_raw ) : '';

                        if ( ! $column_content ) {
                            return '';
                        }

                        $styles = [];
                        if ( $background_color ) {
                            $styles[] = '--cb-col-bg:' . $background_color;
                        }
                        if ( $text_color ) {
                            $styles[] = '--cb-text-color:' . $text_color;
                        }
                        if ( $link_color ) {
                            $styles[] = '--cb-link-color:' . $link_color;
                        }
                        if ( $hover_color ) {
                            $styles[] = '--cb-link-hover-color:' . $hover_color;
                        }

                        $style_attr = $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';

                        $column_markup  = '<div class="content-block__column content-block__column--color content-block__column--text' . ( $centered_title ? ' content-block__column--has-centered-title' : '' ) . '"' . $style_attr . '>';
                        $column_markup .= '<div class="content-block__column-content">' . wp_kses_post( $column_content ) . '</div>';
                        $column_markup .= $centered_title;
                        $column_markup .= '</div>';

                        return $column_markup;
                    }
                }

                if ( ! $heading && ! $content && empty( $rows ) ) {
                    break;
                }
                ?>
                <section class="article-section content-block"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <?php if ( $heading ) : ?>
                        <h2><?php echo esc_html( $heading ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $content ) : ?>
                        <div class="content-block__body"><?php echo wp_kses_post( $content ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $rows ) ) : ?>
                        <div class="content-block__rows">
                            <?php
                            foreach ( $rows as $row ) {
                                $left_column  = isset( $row['left_column'] ) ? $row['left_column'] : [];
                                $right_column = isset( $row['right_column'] ) ? $row['right_column'] : [];

                                $layout_choice = isset( $row['column_layout'] ) ? $row['column_layout'] : '';

                                // Determine which side acts as image vs text based on the row layout selector.
                                // The selector enforces a single image column per row (image left/text right OR text left/image right).
                                if ( 'text_left_image_right' === $layout_choice ) {
                                    $left_type  = 'text';
                                    $right_type = 'image';
                                } else {
                                    // Default + legacy fallback: treat rows without a saved selection as image left / text right.
                                    $left_type  = 'image';
                                    $right_type = 'text';
                                }

                                $column_html = [
                                    aichatbotfree_cb_render_column( $left_column, $left_type ),
                                    aichatbotfree_cb_render_column( $right_column, $right_type ),
                                ];

                                $column_html = array_filter( $column_html );

                                if ( empty( $column_html ) ) {
                                    continue;
                                }
                                ?>
                                <div class="content-block__row">
                                    <?php echo implode( '', $column_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </section>
                <?php
                break;

            case 'faq_section':
                $faq_title = get_sub_field( 'faq_title' );
                $faq_items = get_sub_field( 'faq_items' );
                $schema_override = get_sub_field( 'faq_schema_jsonld' );
                if ( empty( $faq_items ) ) {
                    break;
                }

                if ( $schema_override ) {
                    $faq_schema_override = $schema_override;
                }
                ?>
                <section class="article-section faq-section"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <?php if ( $faq_title ) : ?>
                        <div class="section-heading">
                            <h2><?php echo esc_html( $faq_title ); ?></h2>
                        </div>
                    <?php endif; ?>
                    <div class="article-accordion" data-accordion="faq">
                        <?php foreach ( $faq_items as $faq ) :
                            $question = isset( $faq['faq_question'] ) ? $faq['faq_question'] : '';
                            $answer   = isset( $faq['faq_answer'] ) ? $faq['faq_answer'] : '';
                            $accordion_index++;
                            $faq_id = 'faq-' . $accordion_index;

                            if ( $question && $answer ) {
                                $faq_schema_entities[] = [
                                    '@type'          => 'Question',
                                    'name'           => wp_strip_all_tags( $question ),
                                    'acceptedAnswer' => [
                                        '@type' => 'Answer',
                                        'text'  => wp_strip_all_tags( $answer ),
                                    ],
                                ];
                            }
                            ?>
                            <div class="accordion-item">
                                <button class="accordion-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $faq_id ); ?>" id="<?php echo esc_attr( $faq_id ); ?>-button">
                                    <span><?php echo esc_html( $question ); ?></span>
                                    <span class="accordion-toggle__chevron" aria-hidden="true">▸</span>
                                </button>
                                <div class="accordion-panel" id="<?php echo esc_attr( $faq_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $faq_id ); ?>-button">
                                    <div class="accordion-panel__desc"><?php echo wp_kses_post( $answer ); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php
                break;

            case 'cta_section':
                $cta_title = get_sub_field( 'footer_cta_title' );
                $cta_desc  = get_sub_field( 'footer_cta_desc' );
                $cta_label = get_sub_field( 'footer_cta_label' );
                $cta_url   = get_sub_field( 'footer_cta_url' );
                if ( ! $cta_title && ! $cta_desc ) {
                    break;
                }
                ?>
                <section class="article-section cta-section"<?php echo aichatbotfree_article_section_style_attr(); ?>>
                    <div class="cta-section__inner">
                        <?php if ( $cta_title ) : ?>
                            <h2><?php echo esc_html( $cta_title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $cta_desc ) : ?>
                            <p><?php echo esc_html( $cta_desc ); ?></p>
                        <?php endif; ?>
                        <?php if ( $cta_label && $cta_url ) : ?>
                            <a class="button primary" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;
        }
    }
    ?>
</div>
<?php
$faq_schema_json = '';

if ( $faq_schema_override ) {
    $faq_schema_json = $faq_schema_override;
} elseif ( ! empty( $faq_schema_entities ) ) {
    $faq_schema_json = wp_json_encode(
        [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faq_schema_entities,
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}

if ( $faq_schema_json ) :
    ?>
    <script type="application/ld+json">
        <?php echo $faq_schema_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </script>
<?php endif; ?>
<script>
(function(){
    const accordions = document.querySelectorAll('.article-accordion');
    if (!accordions.length) return;

    accordions.forEach(function(group){
        group.addEventListener('click', function(event){
            const button = event.target.closest('.accordion-toggle');
            if (!button || !group.contains(button)) return;

            const expanded = button.getAttribute('aria-expanded') === 'true';
            const panelId = button.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            if (!panel) return;

            const allButtons = group.querySelectorAll('.accordion-toggle');
            const allPanels  = group.querySelectorAll('.accordion-panel');

            allButtons.forEach(function(btn){
                if (btn !== button) {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.classList.remove('is-active');
                }
            });

            allPanels.forEach(function(p){
                if (p !== panel) {
                    p.classList.remove('is-open');
                    p.style.maxHeight = '0px';
                }
            });

            if (!expanded) {
                button.setAttribute('aria-expanded', 'true');
                button.classList.add('is-active');
                panel.classList.add('is-open');
                panel.style.maxHeight = panel.scrollHeight + 'px';
            } else {
                button.setAttribute('aria-expanded', 'false');
                button.classList.remove('is-active');
                panel.classList.remove('is-open');
                panel.style.maxHeight = '0px';
            }
        });
    });
})();

(function(){
    const typeAccordions = document.querySelectorAll('.chatbot-type-accordion');
    if (!typeAccordions.length) return;

    typeAccordions.forEach(function(group){
        const items = Array.from(group.querySelectorAll('.chatbot-type-accordion-item'));
        if (!items.length) return;

        const openItem = function(target, bodyEl, headerEl){
            target.classList.add('open');
            if (headerEl) {
                headerEl.setAttribute('aria-expanded', 'true');
            }
            if (bodyEl) {
                bodyEl.style.maxHeight = bodyEl.scrollHeight + 'px';
            }
        };

        const closeItem = function(target, bodyEl, headerEl){
            target.classList.remove('open');
            if (headerEl) {
                headerEl.setAttribute('aria-expanded', 'false');
            }
            if (bodyEl) {
                bodyEl.style.maxHeight = '0px';
            }
        };

        items.forEach(function(item, index){
            const header = item.querySelector('.chatbot-type-accordion-header');
            const body   = item.querySelector('.chatbot-type-accordion-body');
            if (!header || !body) return;

            closeItem(item, body, header);

            header.addEventListener('click', function(){
                const isFirst = index === 0;
                const isOpen = item.classList.contains('open');

                if (isFirst) {
                    openItem(item, body, header);
                    return;
                }

                if (isOpen) {
                    closeItem(item, body, header);
                } else {
                    openItem(item, body, header);
                }
            });
        });

        const firstItem = items[0];
        const firstHeader = firstItem ? firstItem.querySelector('.chatbot-type-accordion-header') : null;
        const firstBody   = firstItem ? firstItem.querySelector('.chatbot-type-accordion-body') : null;

        if (firstItem && firstHeader && firstBody) {
            openItem(firstItem, firstBody, firstHeader);
        }
    });
})();
</script>
