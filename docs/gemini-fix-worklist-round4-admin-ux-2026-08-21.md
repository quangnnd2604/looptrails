# Work Order Vòng 4 — Gom quản lý dữ liệu con của Tour vào 1 màn hình Admin duy nhất

**Dành cho AI agent (Gemini) thực hiện.** Đọc file này đầy đủ trước khi sửa. Đây là một tính năng UI/UX admin mới (không phải sửa bug), nhưng phải làm cẩn thận vì đụng vào dữ liệu mà toàn bộ frontend/pricing engine đã build ở các vòng trước đang phụ thuộc vào.

---

## 0. Bối cảnh & Mục tiêu

**Vấn đề người dùng (chủ site) phản ánh:** Hiện tại trong wp-admin, mỗi loại dữ liệu con của 1 tour là **1 Custom Post Type (CPT) riêng, có menu riêng ở sidebar admin**: "Itinerary Day", "Vehicle Option", "Accommodation", "Transfer Option", "Add-on", "Availability Rule" — tổng cộng 6 menu, mỗi menu là 1 danh sách bài viết KHÔNG hiển thị rõ nó thuộc tour nào. Khi tạo 1 tour mới, người quản trị phải nhảy qua lại giữa 7 màn hình khác nhau (Tour + 6 menu con) và tự gõ đúng ID tour vào từng bài viết con để liên kết — rất khó dùng, dễ nhầm.

**Mục tiêu:** Khi soạn/sửa **1 bài Tour**, người quản trị thao tác **TẤT CẢ** trong cùng 1 màn hình edit-post của Tour đó — chia thành các phần (meta box) rõ ràng: Lịch trình (Itinerary), Phương tiện & Giá (Vehicles & Pricing), Chỗ ở (Accommodation), Đưa đón (Transfer), Dịch vụ thêm (Add-ons), Lịch trống (Availability). Mỗi phần cho phép thêm/sửa/xóa nhiều dòng (repeater) ngay tại chỗ, không cần rời màn hình.

**Nguyên tắc bắt buộc — KHÔNG được phá vỡ những gì đã hoạt động:**
- **KHÔNG xóa hay đổi cấu trúc dữ liệu (CPT, tên các trường meta `tbc_*`) đang có.** Toàn bộ pricing engine (`class-pricing-engine.php`), theme (`tour-card.php`, `single-tour.html`...), và 98+ test đã build và fix qua 3 vòng đều dựa vào các CPT `itinerary_day`/`vehicle_option`/`accommodation`/`transfer_option`/`addon`/`availability_rule` với field `tbc_tour_id` liên kết tới tour — **giữ nguyên 100%** cấu trúc này.
- Đây **CHỈ LÀ THAY ĐỔI LỚP GIAO DIỆN ADMIN (cách người dùng NHẬP dữ liệu)** — không phải đổi cách dữ liệu được lưu/truy vấn. Cách làm đúng: xây 1 màn hình nhập liệu hợp nhất (meta box trên tour), khi lưu thì **tự động đồng bộ** thành các bài viết CPT con y hệt cấu trúc hiện tại (tạo mới/cập nhật/xóa bài con cho khớp với những gì admin nhập) — để mọi code cũ (pricing, frontend, test) không cần sửa gì cả và vẫn chạy đúng.
- **Sau khi hoàn thành, ẩn 6 menu admin con** (`Itinerary Day`, `Vehicle Option`, `Accommodation`, `Transfer Option`, `Add-on`, `Availability Rule`) khỏi sidebar — không xóa CPT, chỉ ẩn menu để người dùng không còn bối rối, không thao tác nhầm ở đó nữa.

**Phạm vi — CHỈ gom 6 CPT sau (đúng những cái người dùng phàn nàn), KHÔNG đụng vào các CPT khác:**
| CPT gom vào Tour | Field liên kết | Field dữ liệu (theo đúng schema thật, xem `class-meta-fields.php`) |
|---|---|---|
| `itinerary_day` | `tbc_tour_id` | `tbc_day_number` (số), `tbc_included` (text), `tbc_excluded` (text), + `post_title` (tên lộ trình ngày đó), `post_content` (mô tả) |
| `vehicle_option` | `tbc_tour_id` | `tbc_vehicle_type` (text), `tbc_price_vnd` (số nguyên), `tbc_capacity` (số), + `post_title` |
| `accommodation` | `tbc_tour_id` | `tbc_price_vnd` (số nguyên), `tbc_upgrade` (checkbox), + `post_title`, `post_content` |
| `transfer_option` | `tbc_tour_id` | `tbc_direction` (text), `tbc_price_vnd` (số nguyên), + `post_title` |
| `addon` | `tbc_tour_id` | `tbc_price_vnd` (số nguyên), + `post_title`, `post_content` |
| `availability_rule` | `tbc_tour_id` | `tbc_date` (ngày), `tbc_state` (available/blocked), `tbc_capacity` (số) |

**KHÔNG gom** (giữ nguyên menu riêng như hiện tại, không đụng vào): `destination`, `testimonial`, `faq`, `booking`, `voucher` — đây là các CPT dùng chung/toàn site (không phải "con" của riêng 1 tour theo cách người quản trị nghĩ, hoặc là dữ liệu vận hành cần xem dạng danh sách toàn site như booking/voucher). Nếu sau này người dùng muốn gom thêm `faq`/`testimonial` vào tour luôn, đó là việc của 1 work order khác — **không tự ý mở rộng phạm vi ở vòng này.**

---

## 1. Kiến trúc kỹthuật

### 1.1 Meta box mới trên màn hình edit Tour

Tạo 1 file mới `wp-content/plugins/tour-booking-core/includes/class-tour-editor.php`, đăng ký qua `add_action('add_meta_boxes', ...)` — thêm **6 meta box riêng biệt** (không gộp làm 1 box duy nhất, để mỗi phần rõ ràng, thu gọn/mở rộng độc lập) vào màn hình edit `tour`:
1. `tbc_itinerary_metabox` — "Lịch trình theo ngày" (context: `normal`, priority: `high`, ngay dưới nội dung mô tả tour)
2. `tbc_vehicles_metabox` — "Phương tiện & Giá"
3. `tbc_accommodation_metabox` — "Chỗ ở"
4. `tbc_transfer_metabox` — "Đưa đón"
5. `tbc_addons_metabox` — "Dịch vụ thêm"
6. `tbc_availability_metabox` — "Lịch trống" (có thể đặt context `side` vì thường đơn giản hơn — tuỳ Gemini quyết định bố cục hợp lý)

Đăng ký các meta box này trong `tour-booking-core.php` (nơi các class khác của plugin được `init()`/hook), tương tự cách các class khác (`Tbc_Admin_Page::register()`, v.v.) đã được gọi.

### 1.2 Mỗi meta box hiển thị "repeater" — bảng nhiều dòng, thêm/xóa được bằng JS thuần (không cần thư viện ngoài)

**Khi load màn hình edit (tour đã có sẵn dữ liệu):** truy vấn `get_posts()` theo đúng CPT + `tbc_tour_id = ID tour hiện tại`, đổ dữ liệu có sẵn thành các dòng trong bảng — **PHẢI hiển thị đúng dữ liệu cũ đang có** (ví dụ mở tour "Northern Highlands Loop" ID 155 phải thấy sẵn 2 dòng lịch trình, 2 dòng phương tiện Motorbike/Jeep đã có, không phải trống trơn).

**Khi bấm "+ Thêm dòng":** JS thêm 1 hàng `<tr>`/khối input trống vào bảng (dùng `<template>` HTML clone hoặc nối chuỗi JS đơn giản — không cần framework).

**Khi bấm nút xóa trên 1 dòng:** JS ẩn dòng đó đi VÀ đánh dấu 1 input ẩn `_delete=1` cho dòng đó (không xoá khỏi DOM để form vẫn submit đúng field name index) — để lúc lưu, server biết dòng nào cần xoá thật trong DB.

**Đặt tên input theo mảng PHP có index**, ví dụ cho Itinerary:
```html
<input type="hidden" name="tbc_itinerary[0][post_id]" value="168" />
<input type="text" name="tbc_itinerary[0][title]" value="Ha Giang City → Quan Ba Heaven Gate → Yen Minh" />
<textarea name="tbc_itinerary[0][description]">...</textarea>
<input type="text" name="tbc_itinerary[0][included]" value="Breakfast, guide, fuel" />
<input type="text" name="tbc_itinerary[0][excluded]" value="Personal expenses" />
<input type="checkbox" name="tbc_itinerary[0][delete]" value="1" /> <!-- ẩn, chỉ set khi bấm xoá -->
```
`post_id` để trống (hoặc 0) với dòng MỚI thêm — server sẽ biết đó là bài mới cần tạo (`wp_insert_post`) thay vì cập nhật (`wp_update_post`).

### 1.3 Đồng bộ khi lưu Tour (`save_post_tour` hook)

Viết 1 method dùng chung, ví dụ `Tbc_Tour_Editor::sync_child_posts( $tour_id, $post_type, $submitted_rows, $field_map )`, logic:
1. Với mỗi dòng trong `$submitted_rows` (mảng từ `$_POST['tbc_itinerary']` v.v., **phải qua `wp_unslash()` + sanitize đúng loại từng field** — text dùng `sanitize_text_field`, số dùng `absint`/`intval`, mô tả dài dùng `sanitize_textarea_field`, giá tiền `is_price` dùng đúng logic kiểm tra `edit_tbc_prices` capability như các field giá khác trong `class-meta-fields.php` đã làm):
   - Nếu có `delete=1` VÀ có `post_id` → `wp_delete_post( $post_id, true )`.
   - Nếu có `post_id` (khác 0) và KHÔNG xoá → `wp_update_post()` + `update_post_meta()` cho từng field.
   - Nếu KHÔNG có `post_id` (dòng mới) và KHÔNG xoá → `wp_insert_post()` với `post_type` tương ứng + `update_post_meta( $new_id, 'tbc_tour_id', $tour_id )` + các field khác.
2. **Bắt buộc dùng nonce riêng** (`wp_nonce_field` trong mỗi meta box, `check_admin_referer` khi lưu) — không tin dữ liệu `$_POST` nếu thiếu nonce hợp lệ.
3. **Bắt buộc kiểm tra quyền** trước khi lưu: `current_user_can( 'edit_post', $tour_id )` tối thiểu; với các field giá (`tbc_price_vnd`), chỉ ghi nếu `current_user_can( 'edit_tbc_prices' )` — giữ đúng nguyên tắc phân quyền đã có từ Milestone 3 (đọc lại `class-meta-fields.php` để hiểu cách kiểm tra `is_price` hiện tại và áp dụng tương tự ở đây).
4. Bỏ qua auto-save (`if ( wp_is_doing_autosave() ) return;` ở đầu hàm xử lý save) — tránh tạo/xoá dữ liệu nhầm khi WordPress tự động lưu nháp.

Áp dụng ĐÚNG pattern này cho cả 6 phần (Itinerary, Vehicles, Accommodation, Transfer, Add-ons, Availability) — field cụ thể theo đúng bảng ở mục 0.

### 1.4 Ẩn 6 menu admin con

Sau khi phần trên hoạt động ổn, thêm vào `class-post-types.php` (hoặc 1 hook riêng trong `class-tour-editor.php`) — với đúng 6 CPT trong bảng mục 0, đổi `show_in_menu` thành `false` khi đăng ký (không đổi `show_ui` — vẫn cần `show_ui=true` để `register_post_type` hoạt động và edit-link nội bộ không lỗi, chỉ ẩn khỏi sidebar chính). Cách chuẩn WordPress:
```php
// trong build_args() của class-post-types.php, hoặc override riêng cho 6 CPT này:
'show_in_menu' => false,
```
**Kiểm tra kỹ:** sau khi ẩn, các trang admin khác (ví dụ trang sửa 1 bài `itinerary_day` trực tiếp qua URL `post.php?post=168&action=edit`, nếu admin lỡ có link cũ) **vẫn phải mở được bình thường** (không 404, không lỗi quyền) — chỉ là không còn xuất hiện ở menu sidebar. Đây là hành vi chuẩn của `show_in_menu=false`.

---

## 2. Ví dụ đầy đủ — Meta box "Lịch trình theo ngày" (Itinerary)

**Dùng làm mẫu cho 5 phần còn lại — chép đúng cấu trúc, chỉ đổi tên field/CPT.**

```php
<?php
// wp-content/plugins/tour-booking-core/includes/class-tour-editor.php

defined( 'ABSPATH' ) || exit;

class Tbc_Tour_Editor {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
		add_action( 'save_post_tour', array( __CLASS__, 'save' ) );
	}

	public static function register_meta_boxes() {
		add_meta_box( 'tbc_itinerary_metabox', __( 'Lịch trình theo ngày', 'tour-booking-core' ), array( __CLASS__, 'render_itinerary' ), 'tour', 'normal', 'high' );
		// ... tương tự cho 5 meta box còn lại, gọi render_vehicles / render_accommodation / render_transfer / render_addons / render_availability
	}

	public static function render_itinerary( $post ) {
		wp_nonce_field( 'tbc_save_itinerary', 'tbc_itinerary_nonce' );
		$days = get_posts( array(
			'post_type'      => 'itinerary_day',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $post->ID,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'tbc_day_number',
		) );
		?>
		<table class="widefat tbc-repeater" id="tbc-itinerary-repeater">
			<thead>
				<tr><th>Ngày</th><th>Tiêu đề lộ trình</th><th>Mô tả</th><th>Bao gồm</th><th>Không bao gồm</th><th></th></tr>
			</thead>
			<tbody>
				<?php foreach ( $days as $i => $day ) : ?>
				<tr class="tbc-repeater-row">
					<td>
						<input type="hidden" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][post_id]" value="<?php echo esc_attr( $day->ID ); ?>" />
						<input type="number" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][day_number]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_day_number', true ) ); ?>" style="width:60px" />
					</td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $day->post_title ); ?>" style="width:100%" /></td>
					<td><textarea name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][description]" rows="2" style="width:100%"><?php echo esc_textarea( $day->post_content ); ?></textarea></td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][included]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_included', true ) ); ?>" /></td>
					<td><input type="text" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][excluded]" value="<?php echo esc_attr( get_post_meta( $day->ID, 'tbc_excluded', true ) ); ?>" /></td>
					<td>
						<label><input type="checkbox" name="tbc_itinerary[<?php echo esc_attr( $i ); ?>][delete]" value="1" /> Xoá</label>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<button type="button" class="button tbc-add-row" data-repeater="tbc-itinerary-repeater" data-prefix="tbc_itinerary">+ Thêm ngày</button>
		<?php
	}

	public static function save( $post_id ) {
		if ( wp_is_doing_autosave() ) {
			return;
		}
		if ( ! isset( $_POST['tbc_itinerary_nonce'] ) || ! wp_verify_nonce( $_POST['tbc_itinerary_nonce'], 'tbc_save_itinerary' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$rows = isset( $_POST['tbc_itinerary'] ) ? wp_unslash( $_POST['tbc_itinerary'] ) : array();
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
			$day_number  = isset( $row['day_number'] ) ? absint( $row['day_number'] ) : 0;
			$included    = isset( $row['included'] ) ? sanitize_text_field( $row['included'] ) : '';
			$excluded    = isset( $row['excluded'] ) ? sanitize_text_field( $row['excluded'] ) : '';

			if ( $existing_id ) {
				wp_update_post( array( 'ID' => $existing_id, 'post_title' => $title, 'post_content' => $description ) );
				$day_id = $existing_id;
			} else {
				$day_id = wp_insert_post( array(
					'post_type'   => 'itinerary_day',
					'post_title'  => $title,
					'post_content'=> $description,
					'post_status' => 'publish',
				) );
				update_post_meta( $day_id, 'tbc_tour_id', $post_id );
			}

			update_post_meta( $day_id, 'tbc_day_number', $day_number );
			update_post_meta( $day_id, 'tbc_included', $included );
			update_post_meta( $day_id, 'tbc_excluded', $excluded );
		}
	}
}
```

**JS dùng chung cho mọi repeater** (1 file `assets/js/admin-repeater.js`, enqueue chỉ trên màn hình edit `tour` qua `admin_enqueue_scripts` + kiểm tra `get_current_screen()->post_type === 'tour'`):
```js
document.addEventListener('click', function (e) {
	if (!e.target.classList.contains('tbc-add-row')) return;
	const table = document.getElementById(e.target.dataset.repeater);
	const tbody = table.querySelector('tbody');
	const newIndex = tbody.querySelectorAll('tr').length;
	const lastRow = tbody.querySelector('tr:last-child');
	const newRow = lastRow ? lastRow.cloneNode(true) : null;
	if (!newRow) return; // nếu bảng đang rỗng hoàn toàn, cân nhắc có sẵn 1 template row ẩn để clone
	newRow.querySelectorAll('input, textarea').forEach((el) => {
		el.name = el.name.replace(/\[\d+\]/, '[' + newIndex + ']');
		if (el.type === 'checkbox') { el.checked = false; }
		else { el.value = ''; }
		if (el.name.includes('[post_id]')) { el.value = '0'; }
	});
	tbody.appendChild(newRow);
});
```
(Gemini tự hoàn thiện chi tiết — ví dụ cần xử lý trường hợp bảng rỗng ban đầu bằng cách luôn render sẵn 1 dòng ẩn `display:none` làm template để clone, tránh lỗi khi `tbody` trống.)

**Áp dụng ĐÚNG cấu trúc trên cho 5 phần còn lại** (Vehicles & Pricing, Accommodation, Transfer, Add-ons, Availability) — field theo đúng bảng ở mục 0. Với field `tbc_price_vnd` (giá tiền), thêm kiểm tra: chỉ `update_post_meta` field đó nếu `current_user_can( 'edit_tbc_prices' )`, else bỏ qua field đó khi lưu (giữ giá trị cũ, không cho user không đủ quyền sửa giá).

---

## 3. Kiểm chứng bắt buộc

**Test tự động — thêm mới, không thay test cũ:** viết `wp-content/plugins/tour-booking-core/tests/test-tour-editor.php` — giả lập `$_POST` với dữ liệu itinerary/vehicle mẫu, gọi `Tbc_Tour_Editor::save( $tour_id )`, assert đúng số bài `itinerary_day`/`vehicle_option` được tạo với đúng `tbc_tour_id` và đúng giá trị field — **PHẢI dùng dữ liệu thật đi qua toàn bộ hàm `save()`, không mock**, để không lặp lại lớp lỗi "test giả không phát hiện được lỗi thật" đã gặp nhiều lần ở các vòng trước.

**Kiểm chứng thủ công bắt buộc — thao tác thật trên wp-admin, không chỉ chạy code:**
1. Đăng nhập admin, mở sửa tour "Northern Highlands Loop" (ID 155): xác nhận 2 meta box Itinerary/Vehicles hiện ĐÚNG dữ liệu có sẵn (2 ngày, Motorbike 350.000đ, Jeep 900.000đ) — không trống trơn.
2. Thêm 1 dòng lịch trình mới ("Day 3", tiêu đề bất kỳ), Lưu (Update) bài Tour.
3. Chạy: `C:\xampp\wp-cli.bat post list --post_type=itinerary_day --meta_key=tbc_tour_id --meta_value=155 --fields=ID,post_title` — phải thấy 3 bài, bài mới có đúng tiêu đề vừa nhập.
4. Xoá dòng "Day 3" vừa thêm (tick Xoá, Lưu lại) — chạy lại lệnh trên, phải còn đúng 2 bài như ban đầu.
5. Kiểm tra menu sidebar admin: "Itinerary Day", "Vehicle Option", "Accommodation", "Transfer Option", "Add-on", "Availability Rule" **không còn xuất hiện** trong menu chính.
6. Chạy lại PHPUnit cả 2 package — phải 100% pass (bao gồm cả test mới thêm).
7. Kiểm tra trang chủ + trang chi tiết tour Northern Highlands Loop vẫn hiển thị đúng giá/lịch trình như trước (không bị ảnh hưởng bởi thay đổi ở admin) — vì code frontend không đổi, chỉ cần xác nhận không có tác dụng phụ.

---

## 4. Báo cáo lại

Thêm section "Vòng 4 — Admin UX" vào `docs/fix-report-2026-08-21.md`, mô tả rõ: đã tạo file nào, meta box nào, kết quả từng bước kiểm chứng ở mục 3 (dán output lệnh thật + mô tả thao tác admin đã thử thật). Nếu gặp khó khăn kỹ thuật không giải quyết được (ví dụ JS repeater phức tạp hơn dự kiến), dừng lại và ghi rõ đã làm tới đâu, vướng ở đâu — không tự ý bỏ qua bước kiểm chứng.
