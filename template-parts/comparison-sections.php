<?php
/**
 * Comparison sections renderer.
 *
 * Renders ACF repeater comparison_sections with nested feature blocks.
 *
 * @package aichatbotfree
 */

$sections            = isset( $args['sections'] ) && is_array( $args['sections'] ) ? $args['sections'] : null;
$container_tag       = isset( $args['container_tag'] ) && $args['container_tag'] ? $args['container_tag'] : 'section';
$container_classes   = [ 'comparison-sections' ];
$style_attr          = '';
$style_attr_param    = isset( $args['style_attr'] ) ? $args['style_attr'] : '';
$additional_classes  = isset( $args['container_classes'] ) ? $args['container_classes'] : '';

if ( $additional_classes ) {
	if ( is_array( $additional_classes ) ) {
		$container_classes = array_merge( $container_classes, $additional_classes );
	} elseif ( is_string( $additional_classes ) ) {
		$container_classes[] = $additional_classes;
	}
}

if ( '' !== $style_attr_param ) {
	$style_attr = ' ' . trim( (string) $style_attr_param );
}

if ( ! $sections && function_exists( 'have_rows' ) && have_rows( 'comparison_sections' ) ) {
	$sections = [];

	while ( have_rows( 'comparison_sections' ) ) {
		the_row();

		$feature_blocks = [];

		if ( have_rows( 'feature_blocks' ) ) {
			while ( have_rows( 'feature_blocks' ) ) {
				the_row();

				$left_rows  = [];
				$right_rows = [];

				if ( have_rows( 'left_product_rows' ) ) {
					while ( have_rows( 'left_product_rows' ) ) {
						the_row();
						$left_row_text = get_sub_field( 'left_row_text' );

						if ( $left_row_text ) {
							$left_rows[] = $left_row_text;
						}
					}
				}

				if ( have_rows( 'right_product_rows' ) ) {
					while ( have_rows( 'right_product_rows' ) ) {
						the_row();
						$right_row_text = get_sub_field( 'right_row_text' );

						if ( $right_row_text ) {
							$right_rows[] = $right_row_text;
						}
					}
				}

				$feature_blocks[] = [
					'feature_title'      => get_sub_field( 'feature_title' ),
					'left_product_title' => get_sub_field( 'left_product_title' ),
					'right_product_title'=> get_sub_field( 'right_product_title' ),
					'left_product_rows'  => $left_rows,
					'right_product_rows' => $right_rows,
					'winner_label'       => get_sub_field( 'winner_label' ),
					'winner_link'        => get_sub_field( 'winner_link' ),
				];
			}
		}

		$sections[] = [
			'comparison_title' => get_sub_field( 'comparison_title' ),
			'comparison_intro' => get_sub_field( 'comparison_intro' ),
			'feature_blocks'   => $feature_blocks,
		];
	}
}

if ( empty( $sections ) ) {
	return;
}

if ( ! function_exists( 'aichatbotfree_comparison_extract_rows' ) ) {
	/**
	 * Extract sanitized row text from repeater arrays or raw string values.
	 *
	 * @param array|string $rows     Row data from ACF or manual args.
	 * @param string       $text_key Field key to pull from repeater rows.
	 *
	 * @return array
	 */
	function aichatbotfree_comparison_extract_rows( $rows, $text_key ) {
		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return [];
		}

		$sanitized_rows = [];

		foreach ( $rows as $row ) {
			$text_value = '';

			if ( is_array( $row ) && isset( $row[ $text_key ] ) ) {
				$text_value = $row[ $text_key ];
			} elseif ( ! is_array( $row ) && ! is_object( $row ) ) {
				$text_value = $row;
			}

			$text_value = trim( (string) $text_value );

			if ( '' !== $text_value ) {
				$sanitized_rows[] = $text_value;
			}
		}

		return $sanitized_rows;
	}
}
?>

<<?php echo esc_attr( $container_tag ); ?> class="<?php echo esc_attr( implode( ' ', array_filter( $container_classes ) ) ); ?>"<?php echo $style_attr; ?>>
	<?php
	foreach ( $sections as $section ) :
		$section_title = isset( $section['comparison_title'] ) ? $section['comparison_title'] : '';
		$section_intro = isset( $section['comparison_intro'] ) ? $section['comparison_intro'] : '';
		$features      = isset( $section['feature_blocks'] ) && is_array( $section['feature_blocks'] ) ? $section['feature_blocks'] : [];
		?>
		<article class="comparison-section">
			<?php if ( $section_title ) : ?>
				<h2 class="comparison-section__title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $section_intro ) : ?>
				<div class="comparison-section__intro">
					<?php echo wp_kses_post( $section_intro ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $features ) ) : ?>
				<div class="comparison-section__features">
					<?php
					foreach ( $features as $feature_block ) :
						$feature_title = isset( $feature_block['feature_title'] ) ? $feature_block['feature_title'] : '';
						$left_title    = isset( $feature_block['left_product_title'] ) ? $feature_block['left_product_title'] : '';
						$right_title   = isset( $feature_block['right_product_title'] ) ? $feature_block['right_product_title'] : '';
						$winner_label  = isset( $feature_block['winner_label'] ) ? $feature_block['winner_label'] : '';
						$winner_link   = isset( $feature_block['winner_link'] ) ? $feature_block['winner_link'] : '';
						$left_rows     = aichatbotfree_comparison_extract_rows( isset( $feature_block['left_product_rows'] ) ? $feature_block['left_product_rows'] : [], 'left_row_text' );
						$right_rows    = aichatbotfree_comparison_extract_rows( isset( $feature_block['right_product_rows'] ) ? $feature_block['right_product_rows'] : [], 'right_row_text' );
						?>
						<div class="comparison-feature">
							<?php if ( $feature_title ) : ?>
								<h3 class="comparison-feature__title"><?php echo esc_html( $feature_title ); ?></h3>
							<?php endif; ?>

							<div class="comparison-feature__columns">
								<div class="comparison-feature__column comparison-feature__column--left">
									<?php if ( $left_title ) : ?>
										<h4 class="comparison-feature__column-title"><?php echo esc_html( $left_title ); ?></h4>
									<?php endif; ?>

									<?php if ( ! empty( $left_rows ) ) : ?>
										<ul class="comparison-feature__list">
											<?php
											foreach ( $left_rows as $left_row_text ) :
												if ( ! $left_row_text ) {
													continue;
												}
												?>
												<li class="comparison-feature__list-item"><?php echo esc_html( $left_row_text ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>

								<div class="comparison-feature__column comparison-feature__column--right">
									<?php if ( $right_title ) : ?>
										<h4 class="comparison-feature__column-title"><?php echo esc_html( $right_title ); ?></h4>
									<?php endif; ?>

									<?php if ( ! empty( $right_rows ) ) : ?>
										<ul class="comparison-feature__list">
											<?php
											foreach ( $right_rows as $right_row_text ) :
												if ( ! $right_row_text ) {
													continue;
												}
												?>
												<li class="comparison-feature__list-item"><?php echo esc_html( $right_row_text ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</div>

							<?php if ( $winner_label && $winner_link ) : ?>
								<div class="comparison-feature__winner">
									<a class="comparison-feature__winner-link cta-text-link" href="<?php echo esc_url( $winner_link ); ?>">
										<?php echo esc_html( $winner_label ); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
						<?php
					endforeach;
					?>
				</div>
			<?php endif; ?>
		</article>
		<?php
	endforeach;
	?>
</<?php echo esc_attr( $container_tag ); ?>>
