<?php
/**
 * Tests for Router class.
 *
 * @package BlockScaffolding
 */

namespace XWP\BlockScaffolding;

use Mockery;
use WP_Mock;

/**
 * Tests for the Router class.
 */
class TestRouter extends TestCase {

	/**
	 * Test init.
	 *
	 * @covers \XWP\BlockScaffolding\Router::init()
	 */
	public function test_init() {
		$plugin = new Router( Mockery::mock( Plugin::class ) );

		WP_Mock::expectActionAdded( 'enqueue_block_editor_assets', [ $plugin, 'enqueue_editor_assets' ], 10, 1 );

		WP_Mock::userFunction( 'register_block_type' )
			->once()
			->with(
				'scaffolding/scaffolding-block',
				Mockery::type( 'array' )
			);

		$plugin->init();
	}

	/**
	 * Test enqueue_editor_assets.
	 *
	 * @covers \XWP\BlockScaffolding\Router::enqueue_editor_assets()
	 */
	public function test_enqueue_editor_assets() {
		$plugin = Mockery::mock( Plugin::class );

		$plugin->shouldReceive( 'asset_url' )
			->once()
			->with( 'js/dist/editor.js' )
			->andReturn( 'http://example.com/js/dist/editor.js' );

		$plugin->shouldReceive( 'asset_version' )
			->once()
			->andReturn( '1.2.3' );

		WP_Mock::userFunction( 'wp_enqueue_script' )
			->once()
			->with(
				'block-scaffolding-js',
				'http://example.com/js/dist/editor.js',
				Mockery::type( 'array' ),
				'1.2.3'
			);

		$block_extend = new Router( $plugin );
		$block_extend->enqueue_editor_assets();
	}

	/**
	 * Test scaffolding_block_render_amp_information.
	 *
	 * @covers \XWP\BlockScaffolding\Router::scaffolding_block_render_amp_information()
	 */
	public function test_scaffolding_block_render_amp_information() {
		$plugin = new Router( Mockery::mock( Plugin::class ) );

		WP_Mock::userFunction( 'wp_count_posts' )
			->once()
			->with( 'amp_validated_url' )
			->andReturn( (object) [ 'publish' => '1' ] );

		WP_Mock::userFunction( 'wp_count_terms' )
			->once()
			->with( 'amp_validation_error' )
			->andReturn( '1' );

		WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'amp-options' )
			->andReturn( [ 'theme_support' => 'standard' ] );

		$plugin->scaffolding_block_render_amp_information( array( 'isTemplateModeVisible' => true ) );
	}
}
