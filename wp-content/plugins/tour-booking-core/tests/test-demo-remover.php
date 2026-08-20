<?php
class Test_Demo_Remover extends WP_UnitTestCase {

	public function test_remove_deletes_only_demo_marked_posts() {
		$demo_tour_id = self::factory()->post->create( array( 'post_type' => 'tour' ) );
		update_post_meta( $demo_tour_id, 'tbc_is_demo', true );

		$real_tour_id = self::factory()->post->create( array( 'post_type' => 'tour' ) );

		Tbc_Demo_Remover::remove();

		$this->assertNull( get_post( $demo_tour_id ) );
		$this->assertNotNull( get_post( $real_tour_id ) );
	}

	public function test_remove_covers_every_demo_post_type() {
		Tbc_Demo_Importer::import();

		Tbc_Demo_Remover::remove();

		foreach ( Tbc_Demo_Remover::DEMO_POST_TYPES as $post_type ) {
			$remaining = get_posts(
				array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'meta_key'       => 'tbc_is_demo',
					'meta_value'     => '1',
				)
			);
			$this->assertCount( 0, $remaining, "Leftover demo posts in {$post_type}" );
		}
	}

	public function test_remove_returns_deleted_count() {
		self::factory()->post->create(
			array(
				'post_type' => 'tour',
				'meta_input' => array( 'tbc_is_demo' => true ),
			)
		);

		$deleted = Tbc_Demo_Remover::remove();

		$this->assertGreaterThanOrEqual( 1, $deleted );
	}
}
