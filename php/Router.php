<?php
/**
 * Router class.
 *
 * @package BlockScaffolding
 */

namespace XWP\BlockScaffolding;

/**
 * Plugin Router.
 */
class Router {

	/**
	 * Plugin interface.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Setup the plugin instance.
	 *
	 * @param Plugin $plugin Instance of the plugin abstraction.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );

		// Register gutenberg block.
		$this->scaffolding_register_block();
	}

	/**
	 * Load our block assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'block-scaffolding-js',
			$this->plugin->asset_url( 'js/dist/editor.js' ),
			[
				'lodash',
				'react',
				'wp-block-editor',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-components',
				'wp-editor',
			],
			$this->plugin->asset_version()
		);
	}

	/**
	 * Register gutenberg block.
	 *
	 * @return void
	 */
	public function scaffolding_register_block() {
		register_block_type(
			'scaffolding/scaffolding-block',
			[
				'editor_script'   => 'block-scaffolding-js',
				'render_callback' => [ $this, 'scaffolding_block_render_amp_information' ],
				'attributes'      => [
					'isTemplateModeVisible' => [
						'type'    => 'boolean',
						'default' => true,
					],
				],
			]
		);
	}

	/**
	 * Block render callback method
	 *
	 * @param array $attributes attributes.
	 *
	 * @return string
	 */
	public function scaffolding_block_render_amp_information( $attributes ) {

		$content = '<h1>' . __( 'AMP Validation Statistics', 'block-scaffolding' ) . '</h1>';

		/* translators: %1$d: validated URLs */
		$content .= '<p>' . esc_html( sprintf( __( 'There are %1$d validated URLs.', 'block-scaffolding' ), $this->get_validated_url() ) ) . '</p>';

		/* translators: %1$d: validation errors */
		$content .= '<p>' . esc_html( sprintf( __( 'There are %1$d validation errors.', 'block-scaffolding' ), $this->get_validation_error() ) ) . '</p>';

		/* translators: %1$s: template mode */
		$content .= ( $attributes['isTemplateModeVisible'] ) ? '<p>' . esc_html( sprintf( __( 'The template mode is %1$s.', 'block-scaffolding' ), $this->get_template_mode() ) ) . '</p>' : '';

		return $content;
	}

	/**
	 * Get number of AMP validated URLs.
	 *
	 * @return integer
	 */
	public function get_validated_url() {
		return wp_count_posts( 'amp_validated_url' )->publish;
	}

	/**
	 * Get AMP taxonomy terms count.
	 *
	 * @return integer
	 */
	public function get_validation_error() {
		return wp_count_terms( 'amp_validation_error' );
	}

	/**
	 * Get AMP template mode.
	 *
	 * @return string
	 */
	public function get_template_mode() {

		// Get AMP settings.
		$amp_options = get_option( 'amp-options' );

		if ( ! empty( $amp_options['theme_support'] ) ) {
			return $amp_options['theme_support'];
		} else {
			return __( 'unknown', 'block-scaffolding' );
		}

	}
}
