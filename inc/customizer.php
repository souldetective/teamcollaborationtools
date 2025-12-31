<?php
/**
 * Customizer settings for branding and navigation.
 *
 * @package aichatbotfree
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register footer branding controls.
 */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section(
		'aichatbotfree_footer_branding',
		[
			'title'       => __( 'Footer Branding', 'aichatbotfree' ),
			'description' => __( 'Control the footer logo independently from the Site Identity logo.', 'aichatbotfree' ),
			'priority'    => 45,
		]
	);

	$wp_customize->add_setting(
		'aichatbotfree_footer_logo',
		[
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'aichatbotfree_footer_logo',
			[
				'label'       => __( 'Footer Logo', 'aichatbotfree' ),
				'section'     => 'aichatbotfree_footer_branding',
				'description' => __( 'Upload a dedicated logo for the footer. It will fall back to the Site Identity logo when empty.', 'aichatbotfree' ),
			]
		)
	);

	$wp_customize->add_setting(
		'aichatbotfree_footer_logo_max_width',
		[
			'default'           => 180,
			'sanitize_callback' => function ( $value ) {
				$width = absint( $value );

				return $width > 0 ? $width : 0;
			},
			'transport'         => 'refresh',
		]
	);

	$wp_customize->add_control(
		'aichatbotfree_footer_logo_max_width',
		[
			'label'       => __( 'Footer Logo Max Width (px)', 'aichatbotfree' ),
			'section'     => 'aichatbotfree_footer_branding',
			'type'        => 'number',
			'input_attrs' => [
				'min'  => 60,
				'max'  => 400,
				'step' => 10,
			],
			'description' => __( 'Control the maximum width for the footer logo to keep layouts tidy.', 'aichatbotfree' ),
		]
	);
} );
