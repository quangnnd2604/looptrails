<?php
/**
 * Unified Tour Editor Meta Boxes.
 * Allows managing all tour child entities (Itinerary Days, Vehicle Options,
 * Accommodation, Transfer Options, Add-ons, and Availability Rules) from
 * a single Tour edit screen.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Tour_Editor {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
		add_action( 'save_post_tour', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'disable_block_editor_for_tour' ), 10, 2 );
	}

	public static function disable_block_editor_for_tour( $use_block_editor, $post_type ) {
		if ( 'tour' === $post_type ) {
			return false;
		}
		return $use_block_editor;
	}

	public static function enqueue_assets( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || 'tour' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'tbc-admin-tour-editor-css',
			plugins_url( 'assets/css/admin-tour-editor.css', TBC_PLUGIN_FILE ),
			array(),
			TBC_DB_VERSION
		);

		wp_enqueue_script(
			'tbc-admin-tour-editor-js',
			plugins_url( 'assets/js/admin-tour-editor.js', TBC_PLUGIN_FILE ),
			array(),
			TBC_DB_VERSION,
			true
		);
	}

	public static function register_meta_boxes() {
		add_meta_box(
			'tbc_itinerary_metabox',
			__( 'Lịch trình theo ngày (Itinerary Days)', 'tour-booking-core' ),
			array( __CLASS__, 'render_itinerary' ),
			'tour',
			'normal',
			'high'
		);

		add_meta_box(
			'tbc_vehicles_metabox',
			__( 'Phương tiện & Giá (Vehicles & Pricing)', 'tour-booking-core' ),
			array( __CLASS__, 'render_vehicles' ),
			'tour',
			'normal',
			'high'
		);

		add_meta_box(
			'tbc_accommodation_metabox',
			__( 'Chỗ ở (Accommodation)', 'tour-booking-core' ),
			array( __CLASS__, 'render_accommodation' ),
			'tour',
			'normal',
			'default'
		);

		add_meta_box(
			'tbc_transfer_metabox',
			__( 'Đưa đón (Transfer Options)', 'tour-booking-core' ),
			array( __CLASS__, 'render_transfer' ),
			'tour',
			'normal',
			'default'
		);

		add_meta_box(
			'tbc_addons_metabox',
			__( 'Dịch vụ thêm (Add-ons)', 'tour-booking-core' ),
			array( __CLASS__, 'render_addons' ),
			'tour',
			'normal',
			'default'
		);

		add_meta_box(
			'tbc_availability_metabox',
			__( 'Lịch khởi hành & Chỗ trống (Availability Rules)', 'tour-booking-core' ),
			array( __CLASS__, 'render_availability' ),
			'tour',
			'normal',
			'default'
		);
	}

	// =========================================================================
	// 1. Itinerary Days Meta Box
	// =========================================================================
	public static function render_itinerary( $post ) {
		wp_nonce_field( 'tbc_save_tour_editor', 'tbc_tour_editor_nonce' );

		$days = get_posts( array(
			'post_type'      => 'itinerary_day',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'tbc_tour_id',
					'value'   => $post->ID,
					'compare' => '=',
				),
			),
		) );
		usort( $days, function ( $a, $b ) {
			$day_a = (int) get_post_meta( $a->ID, 'tbc_day_number', true );
			$day_b = (int) get_post_meta( $b->ID, 'tbc_day_number', true );
			return $day_a <=> $day_b;
		} );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-itinerary-repeater">
			<thead>
				<tr>
					<th style="width:70px"><?php esc_html_e( 'Ngày', 'tour-booking-core' ); ?></th>
					<th style="width:28%"><?php esc_html_e( 'Tiêu đề lộ trình', 'tour-booking-core' ); ?></th>
					<th><?php esc_html_e( 'Mô tả chi tiết', 'tour-booking-core' ); ?></th>
					<th style="width:18%"><?php esc_html_e( 'Bao gồm (Included)', 'tour-booking-core' ); ?></th>
					<th style="width:18%"><?php esc_html_e( 'Không bao gồm (Excluded)', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $days as $i => $day ) : ?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $day->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="number" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][day_number]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_day_number', true ) ); ?>" min="1" />
					</td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $day->post_title ); ?>" /></td>
					<td><textarea name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][description]" rows="2"><?php echo esc_textarea( $day->post_content ); ?></textarea></td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][included]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_included', true ) ); ?>" /></td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][excluded]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_excluded', true ) ); ?>" /></td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_itinerary[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_itinerary[template][delete]" value="0" />
						<input type="number" name="tbc_itinerary[template][day_number]" value="" min="1" />
					</td>
					<td><input type="text" name="tbc_itinerary[template][title]" value="" placeholder="<?php esc_attr_e( 'Ví dụ: Hà Giang → Quản Bạ → Yên Minh', 'tour-booking-core' ); ?>" /></td>
					<td><textarea name="tbc_itinerary[template][description]" rows="2" placeholder="<?php esc_attr_e( 'Mô tả lịch trình trong ngày...', 'tour-booking-core' ); ?>"></textarea></td>
					<td><input type="text" name="tbc_itinerary[template][included]" value="" placeholder="<?php esc_attr_e( 'Breakfast, guide, fuel...', 'tour-booking-core' ); ?>" /></td>
					<td><input type="text" name="tbc_itinerary[template][excluded]" value="" placeholder="<?php esc_attr_e( 'Personal drinks...', 'tour-booking-core' ); ?>" /></td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-itinerary-repeater">+ <?php esc_html_e( 'Thêm ngày lịch trình', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// 2. Vehicle Options Meta Box
	// =========================================================================
	public static function render_vehicles( $post ) {
		$can_edit_price = current_user_can( 'edit_tbc_prices' );
		$vehicles       = get_posts( array(
			'post_type'      => 'vehicle_option',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'tbc_tour_id',
					'value'   => $post->ID,
					'compare' => '=',
				),
			),
		) );
		usort( $vehicles, function ( $a, $b ) {
			$p_a = (int) get_post_meta( $a->ID, 'tbc_price_vnd', true );
			$p_b = (int) get_post_meta( $b->ID, 'tbc_price_vnd', true );
			return $p_a <=> $p_b;
		} );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-vehicles-repeater">
			<thead>
				<tr>
					<th style="width:30%"><?php esc_html_e( 'Tên hạng phương tiện', 'tour-booking-core' ); ?></th>
					<th style="width:25%"><?php esc_html_e( 'Loại xe (Vehicle Type)', 'tour-booking-core' ); ?></th>
					<th style="width:25%"><?php esc_html_e( 'Giá tiền (VND / người)', 'tour-booking-core' ); ?></th>
					<th style="width:12%"><?php esc_html_e( 'Sức chứa (Người)', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $vehicles as $i => $v ) :
					$v_type   = get_post_meta( $v->ID, 'tbc_vehicle_type', true );
					$price    = get_post_meta( $v->ID, 'tbc_price_vnd', true );
					$capacity = get_post_meta( $v->ID, 'tbc_capacity', true );
				?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $v->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="text" name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $v->post_title ); ?>" />
					</td>
					<td>
						<select name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][vehicle_type]">
							<option value="motorbike" <?php selected( $v_type, 'motorbike' ); ?>>Motorbike (Xe máy)</option>
							<option value="jeep" <?php selected( $v_type, 'jeep' ); ?>>Jeep / 4x4 (Xe địa hình)</option>
							<option value="car" <?php selected( $v_type, 'car' ); ?>>Car (Ô tô du lịch)</option>
							<option value="bus" <?php selected( $v_type, 'bus' ); ?>>Bus / Van (Xe buýt/Limousine)</option>
							<option value="other" <?php selected( $v_type, 'other' ); ?>>Khác</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][price_vnd]" value="<?php echo esc_attr( $price ); ?>" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td>
						<input type="number" name="tbc_vehicles[<?php echo esc_attr( $i ); ?>][capacity]" value="<?php echo esc_attr( $capacity ); ?>" min="1" />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_vehicles[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_vehicles[template][delete]" value="0" />
						<input type="text" name="tbc_vehicles[template][title]" value="" placeholder="<?php esc_attr_e( 'Ví dụ: Easy Rider Honda XR150', 'tour-booking-core' ); ?>" />
					</td>
					<td>
						<select name="tbc_vehicles[template][vehicle_type]">
							<option value="motorbike">Motorbike (Xe máy)</option>
							<option value="jeep">Jeep / 4x4 (Xe địa hình)</option>
							<option value="car">Car (Ô tô du lịch)</option>
							<option value="bus">Bus / Van (Xe buýt/Limousine)</option>
							<option value="other">Khác</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_vehicles[template][price_vnd]" value="0" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td>
						<input type="number" name="tbc_vehicles[template][capacity]" value="2" min="1" />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-vehicles-repeater">+ <?php esc_html_e( 'Thêm phương tiện', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// 3. Accommodation Meta Box
	// =========================================================================
	public static function render_accommodation( $post ) {
		$can_edit_price = current_user_can( 'edit_tbc_prices' );
		$items          = get_posts( array(
			'post_type'      => 'accommodation',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $post->ID,
		) );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-accommodation-repeater">
			<thead>
				<tr>
					<th style="width:30%"><?php esc_html_e( 'Tên chỗ ở / Phòng', 'tour-booking-core' ); ?></th>
					<th><?php esc_html_e( 'Mô tả / Tiện nghi', 'tour-booking-core' ); ?></th>
					<th style="width:22%"><?php esc_html_e( 'Giá phụ thu VND', 'tour-booking-core' ); ?></th>
					<th style="width:15%;text-align:center"><?php esc_html_e( 'Gói nâng cấp?', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $i => $item ) :
					$price   = get_post_meta( $item->ID, 'tbc_price_vnd', true );
					$upgrade = (bool) get_post_meta( $item->ID, 'tbc_upgrade', true );
				?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $item->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="text" name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item->post_title ); ?>" />
					</td>
					<td><textarea name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][description]" rows="2"><?php echo esc_textarea( $item->post_content ); ?></textarea></td>
					<td>
						<input type="number" name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][price_vnd]" value="<?php echo esc_attr( $price ); ?>" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<label><input type="checkbox" name="tbc_accommodation[<?php echo esc_attr( $i ); ?>][upgrade]" value="1" <?php checked( $upgrade ); ?> /> Nâng cấp</label>
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_accommodation[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_accommodation[template][delete]" value="0" />
						<input type="text" name="tbc_accommodation[template][title]" value="" placeholder="<?php esc_attr_e( 'Ví dụ: Deluxe Private Room', 'tour-booking-core' ); ?>" />
					</td>
					<td><textarea name="tbc_accommodation[template][description]" rows="2" placeholder="<?php esc_attr_e( 'Mô tả tiện nghi phòng...', 'tour-booking-core' ); ?>"></textarea></td>
					<td>
						<input type="number" name="tbc_accommodation[template][price_vnd]" value="0" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<label><input type="checkbox" name="tbc_accommodation[template][upgrade]" value="1" /> Nâng cấp</label>
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-accommodation-repeater">+ <?php esc_html_e( 'Thêm chỗ ở', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// 4. Transfer Options Meta Box
	// =========================================================================
	public static function render_transfer( $post ) {
		$can_edit_price = current_user_can( 'edit_tbc_prices' );
		$items          = get_posts( array(
			'post_type'      => 'transfer_option',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $post->ID,
		) );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-transfer-repeater">
			<thead>
				<tr>
					<th style="width:40%"><?php esc_html_e( 'Tên dịch vụ đưa đón', 'tour-booking-core' ); ?></th>
					<th style="width:25%"><?php esc_html_e( 'Chiều di chuyển (Direction)', 'tour-booking-core' ); ?></th>
					<th style="width:25%"><?php esc_html_e( 'Giá tiền VND', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $i => $item ) :
					$direction = get_post_meta( $item->ID, 'tbc_direction', true );
					$price     = get_post_meta( $item->ID, 'tbc_price_vnd', true );
				?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_transfer[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $item->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_transfer[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="text" name="tbc_transfer[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item->post_title ); ?>" />
					</td>
					<td>
						<select name="tbc_transfer[<?php echo esc_attr( $i ); ?>][direction]">
							<option value="to" <?php selected( $direction, 'to' ); ?>>Chiều đi (To Destination)</option>
							<option value="from" <?php selected( $direction, 'from' ); ?>>Chiều về (From Destination)</option>
							<option value="roundtrip" <?php selected( $direction, 'roundtrip' ); ?>>Khứ hồi (Roundtrip)</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_transfer[<?php echo esc_attr( $i ); ?>][price_vnd]" value="<?php echo esc_attr( $price ); ?>" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_transfer[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_transfer[template][delete]" value="0" />
						<input type="text" name="tbc_transfer[template][title]" value="" placeholder="<?php esc_attr_e( 'Ví dụ: Cabin Limousine Hà Nội - Hà Giang', 'tour-booking-core' ); ?>" />
					</td>
					<td>
						<select name="tbc_transfer[template][direction]">
							<option value="to">Chiều đi (To Destination)</option>
							<option value="from">Chiều về (From Destination)</option>
							<option value="roundtrip">Khứ hồi (Roundtrip)</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_transfer[template][price_vnd]" value="0" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-transfer-repeater">+ <?php esc_html_e( 'Thêm dịch vụ đưa đón', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// 5. Add-ons Meta Box
	// =========================================================================
	public static function render_addons( $post ) {
		$can_edit_price = current_user_can( 'edit_tbc_prices' );
		$items          = get_posts( array(
			'post_type'      => 'addon',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $post->ID,
		) );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-addons-repeater">
			<thead>
				<tr>
					<th style="width:35%"><?php esc_html_e( 'Tên dịch vụ thêm', 'tour-booking-core' ); ?></th>
					<th><?php esc_html_e( 'Mô tả dịch vụ', 'tour-booking-core' ); ?></th>
					<th style="width:25%"><?php esc_html_e( 'Giá tiền VND', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $i => $item ) :
					$price = get_post_meta( $item->ID, 'tbc_price_vnd', true );
				?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_addons[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $item->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_addons[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="text" name="tbc_addons[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item->post_title ); ?>" />
					</td>
					<td><textarea name="tbc_addons[<?php echo esc_attr( $i ); ?>][description]" rows="2"><?php echo esc_textarea( $item->post_content ); ?></textarea></td>
					<td>
						<input type="number" name="tbc_addons[<?php echo esc_attr( $i ); ?>][price_vnd]" value="<?php echo esc_attr( $price ); ?>" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_addons[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_addons[template][delete]" value="0" />
						<input type="text" name="tbc_addons[template][title]" value="" placeholder="<?php esc_attr_e( 'Ví dụ: Bảo hiểm du lịch quốc tế', 'tour-booking-core' ); ?>" />
					</td>
					<td><textarea name="tbc_addons[template][description]" rows="2" placeholder="<?php esc_attr_e( 'Mô tả quyền lợi...', 'tour-booking-core' ); ?>"></textarea></td>
					<td>
						<input type="number" name="tbc_addons[template][price_vnd]" value="0" step="1000" min="0" <?php disabled( ! $can_edit_price ); ?> />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-addons-repeater">+ <?php esc_html_e( 'Thêm dịch vụ thêm', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// 6. Availability Rules Meta Box
	// =========================================================================
	public static function render_availability( $post ) {
		$items = get_posts( array(
			'post_type'      => 'availability_rule',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'tbc_tour_id',
					'value'   => $post->ID,
					'compare' => '=',
				),
			),
		) );
		usort( $items, function ( $a, $b ) {
			$d_a = (string) get_post_meta( $a->ID, 'tbc_date', true );
			$d_b = (string) get_post_meta( $b->ID, 'tbc_date', true );
			return strcmp( $d_a, $d_b );
		} );
		?>
		<table class="widefat striped tbc-repeater" id="tbc-availability-repeater">
			<thead>
				<tr>
					<th style="width:35%"><?php esc_html_e( 'Ngày khởi hành (Date)', 'tour-booking-core' ); ?></th>
					<th style="width:35%"><?php esc_html_e( 'Trạng thái (State)', 'tour-booking-core' ); ?></th>
					<th style="width:20%"><?php esc_html_e( 'Số chỗ (Capacity)', 'tour-booking-core' ); ?></th>
					<th style="width:60px;text-align:center"><?php esc_html_e( 'Thao tác', 'tour-booking-core' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $i => $item ) :
					$date     = get_post_meta( $item->ID, 'tbc_date', true );
					$state    = get_post_meta( $item->ID, 'tbc_state', true );
					$capacity = get_post_meta( $item->ID, 'tbc_capacity', true );
				?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_availability[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $item->ID ); ?>" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_availability[<?php echo esc_attr( $i ); ?>][delete]" value="0" />
						<input type="date" name="tbc_availability[<?php echo esc_attr( $i ); ?>][date]" value="<?php echo esc_attr( $date ); ?>" />
					</td>
					<td>
						<select name="tbc_availability[<?php echo esc_attr( $i ); ?>][state]">
							<option value="available" <?php selected( $state, 'available' ); ?>>Còn chỗ (Available)</option>
							<option value="limited" <?php selected( $state, 'limited' ); ?>>Sắp hết chỗ (Limited)</option>
							<option value="sold_out" <?php selected( $state, 'sold_out' ); ?>>Hết chỗ (Sold Out)</option>
							<option value="blocked" <?php selected( $state, 'blocked' ); ?>>Đóng / Khóa (Blocked)</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_availability[<?php echo esc_attr( $i ); ?>][capacity]" value="<?php echo esc_attr( $capacity ); ?>" min="0" />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<template class="tbc-row-template">
				<tr class="tbc-repeater-row" data-is-new="1">
					<td>
						<input type="hidden" name="tbc_availability[template][post_id]" value="0" />
						<input type="hidden" class="tbc-delete-flag" name="tbc_availability[template][delete]" value="0" />
						<input type="date" name="tbc_availability[template][date]" value="" />
					</td>
					<td>
						<select name="tbc_availability[template][state]">
							<option value="available">Còn chỗ (Available)</option>
							<option value="limited">Sắp hết chỗ (Limited)</option>
							<option value="sold_out">Hết chỗ (Sold Out)</option>
							<option value="blocked">Đóng / Khóa (Blocked)</option>
						</select>
					</td>
					<td>
						<input type="number" name="tbc_availability[template][capacity]" value="10" min="0" />
					</td>
					<td style="text-align:center">
						<button type="button" class="button-link-delete tbc-remove-row-btn"><?php esc_html_e( 'Xóa', 'tour-booking-core' ); ?></button>
						<button type="button" class="button-link tbc-undo-row-btn" style="display:none;"><?php esc_html_e( 'Hoàn tác', 'tour-booking-core' ); ?></button>
					</td>
				</tr>
			</template>
		</table>
		<button type="button" class="button button-secondary tbc-add-row" data-repeater="tbc-availability-repeater">+ <?php esc_html_e( 'Thêm ngày khởi hành', 'tour-booking-core' ); ?></button>
		<?php
	}

	// =========================================================================
	// Unified Save Handler
	// =========================================================================
	public static function save( $post_id, $post = null ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['tbc_tour_editor_nonce'] ) || ! wp_verify_nonce( $_POST['tbc_tour_editor_nonce'], 'tbc_save_tour_editor' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$can_edit_price = current_user_can( 'edit_tbc_prices' );

		// 1. Save Itinerary Days
		if ( isset( $_POST['tbc_itinerary'] ) && is_array( $_POST['tbc_itinerary'] ) ) {
			$rows = wp_unslash( $_POST['tbc_itinerary'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
					}
					continue;
				}

				$title       = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
				$description = isset( $row['description'] ) ? sanitize_textarea_field( $row['description'] ) : '';
				$day_number  = isset( $row['day_number'] ) ? absint( $row['day_number'] ) : 1;
				$included    = isset( $row['included'] ) ? sanitize_text_field( $row['included'] ) : '';
				$excluded    = isset( $row['excluded'] ) ? sanitize_text_field( $row['excluded'] ) : '';

				if ( empty( $title ) && empty( $description ) ) {
					continue;
				}

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'           => $existing_id,
						'post_title'   => $title,
						'post_content' => $description,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'    => 'itinerary_day',
						'post_title'   => $title,
						'post_content' => $description,
						'post_status'  => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				update_post_meta( $child_id, 'tbc_day_number', $day_number );
				update_post_meta( $child_id, 'tbc_included', $included );
				update_post_meta( $child_id, 'tbc_excluded', $excluded );
			}
		}

		// 2. Save Vehicle Options
		$vehicles_changed = false;
		if ( isset( $_POST['tbc_vehicles'] ) && is_array( $_POST['tbc_vehicles'] ) ) {
			$rows = wp_unslash( $_POST['tbc_vehicles'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
						$vehicles_changed = true;
					}
					continue;
				}

				$title        = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
				$vehicle_type = isset( $row['vehicle_type'] ) ? sanitize_text_field( $row['vehicle_type'] ) : 'motorbike';
				$price_vnd    = isset( $row['price_vnd'] ) ? intval( $row['price_vnd'] ) : 0;
				$capacity     = isset( $row['capacity'] ) ? absint( $row['capacity'] ) : 2;

				if ( empty( $title ) ) {
					continue;
				}

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'         => $existing_id,
						'post_title' => $title,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'   => 'vehicle_option',
						'post_title'  => $title,
						'post_status' => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				update_post_meta( $child_id, 'tbc_vehicle_type', $vehicle_type );
				update_post_meta( $child_id, 'tbc_capacity', $capacity );

				if ( $can_edit_price ) {
					update_post_meta( $child_id, 'tbc_price_vnd', $price_vnd );
				}

				$vehicles_changed = true;
			}
		}

		if ( $vehicles_changed && class_exists( 'Tbc_Pricing_Engine' ) ) {
			$cheapest = Tbc_Pricing_Engine::get_cheapest_vehicle_for_tour( $post_id );
			if ( $cheapest ) {
				update_post_meta( $post_id, 'tbc_price_from_vnd', $cheapest['price_vnd'] );
			}
		}

		// 3. Save Accommodation
		if ( isset( $_POST['tbc_accommodation'] ) && is_array( $_POST['tbc_accommodation'] ) ) {
			$rows = wp_unslash( $_POST['tbc_accommodation'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
					}
					continue;
				}

				$title       = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
				$description = isset( $row['description'] ) ? sanitize_textarea_field( $row['description'] ) : '';
				$price_vnd   = isset( $row['price_vnd'] ) ? intval( $row['price_vnd'] ) : 0;
				$upgrade     = ! empty( $row['upgrade'] );

				if ( empty( $title ) ) {
					continue;
				}

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'           => $existing_id,
						'post_title'   => $title,
						'post_content' => $description,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'    => 'accommodation',
						'post_title'   => $title,
						'post_content' => $description,
						'post_status'  => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				update_post_meta( $child_id, 'tbc_upgrade', $upgrade );

				if ( $can_edit_price ) {
					update_post_meta( $child_id, 'tbc_price_vnd', $price_vnd );
				}
			}
		}

		// 4. Save Transfer Options
		if ( isset( $_POST['tbc_transfer'] ) && is_array( $_POST['tbc_transfer'] ) ) {
			$rows = wp_unslash( $_POST['tbc_transfer'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
					}
					continue;
				}

				$title     = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
				$direction = isset( $row['direction'] ) ? sanitize_text_field( $row['direction'] ) : 'to';
				$price_vnd = isset( $row['price_vnd'] ) ? intval( $row['price_vnd'] ) : 0;

				if ( empty( $title ) ) {
					continue;
				}

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'         => $existing_id,
						'post_title' => $title,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'   => 'transfer_option',
						'post_title'  => $title,
						'post_status' => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				update_post_meta( $child_id, 'tbc_direction', $direction );

				if ( $can_edit_price ) {
					update_post_meta( $child_id, 'tbc_price_vnd', $price_vnd );
				}
			}
		}

		// 5. Save Add-ons
		if ( isset( $_POST['tbc_addons'] ) && is_array( $_POST['tbc_addons'] ) ) {
			$rows = wp_unslash( $_POST['tbc_addons'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
					}
					continue;
				}

				$title       = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
				$description = isset( $row['description'] ) ? sanitize_textarea_field( $row['description'] ) : '';
				$price_vnd   = isset( $row['price_vnd'] ) ? intval( $row['price_vnd'] ) : 0;

				if ( empty( $title ) ) {
					continue;
				}

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'           => $existing_id,
						'post_title'   => $title,
						'post_content' => $description,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'    => 'addon',
						'post_title'   => $title,
						'post_content' => $description,
						'post_status'  => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				if ( $can_edit_price ) {
					update_post_meta( $child_id, 'tbc_price_vnd', $price_vnd );
				}
			}
		}

		// 6. Save Availability Rules
		if ( isset( $_POST['tbc_availability'] ) && is_array( $_POST['tbc_availability'] ) ) {
			$rows = wp_unslash( $_POST['tbc_availability'] );
			foreach ( $rows as $row ) {
				$existing_id = isset( $row['post_id'] ) ? absint( $row['post_id'] ) : 0;
				$is_delete   = ! empty( $row['delete'] );

				if ( $is_delete ) {
					if ( $existing_id ) {
						wp_delete_post( $existing_id, true );
					}
					continue;
				}

				$date     = isset( $row['date'] ) ? sanitize_text_field( $row['date'] ) : '';
				$state    = isset( $row['state'] ) ? sanitize_text_field( $row['state'] ) : 'available';
				$capacity = isset( $row['capacity'] ) ? absint( $row['capacity'] ) : 0;

				if ( empty( $date ) ) {
					continue;
				}

				$rule_title = sprintf( '%s - %s', $date, ucfirst( $state ) );

				if ( $existing_id ) {
					wp_update_post( array(
						'ID'         => $existing_id,
						'post_title' => $rule_title,
					) );
					$child_id = $existing_id;
				} else {
					$child_id = wp_insert_post( array(
						'post_type'   => 'availability_rule',
						'post_title'  => $rule_title,
						'post_status' => 'publish',
					) );
					update_post_meta( $child_id, 'tbc_tour_id', $post_id );
				}

				update_post_meta( $child_id, 'tbc_date', $date );
				update_post_meta( $child_id, 'tbc_state', $state );
				update_post_meta( $child_id, 'tbc_capacity', $capacity );
			}
		}
	}
}
