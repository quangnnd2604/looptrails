# Work Order For Fixing Agent — Bám sát từng bước, không được bỏ qua

**Đây là tài liệu dành cho AI agent (Gemini) thực hiện sửa lỗi. Nếu bạn là agent đang đọc file này: đọc toàn bộ tài liệu này VÀ toàn bộ `docs/audit-report-gemini-handoff-2026-08-21.md` trước khi sửa bất kỳ dòng code nào. Không bắt đầu sửa nếu chưa đọc hết cả hai file.**

---

## 0. Quy tắc bắt buộc — đọc trước

1. **Sửa đúng 1 task một lần.** Sau mỗi task, chạy đủ các lệnh kiểm chứng ghi trong task đó. Chỉ chuyển sang task tiếp theo khi TẤT CẢ lệnh kiểm chứng của task hiện tại cho kết quả đúng như mô tả "Kết quả mong đợi". Nếu sai, sửa tiếp cho đến khi đúng — không được bỏ qua hoặc chuyển task tiếp theo với lỗi còn tồn đọng.
2. **Không tự ý sửa file ngoài phạm vi task đang làm**, trừ khi task đó yêu cầu rõ.
3. **Dùng đúng công cụ môi trường:**
   - WP-CLI: luôn dùng `C:\xampp\wp-cli.bat`, **không bao giờ dùng lệnh `wp` hoặc `php` trần** — máy này có 2 bản PHP cài song song, chỉ có `wp-cli.bat` được cấu hình đúng.
   - Chạy PHPUnit: `cd wp-content/plugins/tour-booking-core && C:/xampp/php/php.exe vendor/bin/phpunit` và tương tự cho `wp-content/themes/tour-reference-theme`.
   - Site chạy tại `http://localhost/looptrails/`.
4. **Sau khi sửa xong TẤT CẢ các task Mức CRITICAL** (mục 1), chạy lại toàn bộ PHPUnit của cả plugin và theme — phải 100% pass. Nếu một fix làm vỡ test cũ, sửa lại test đó để phản ánh đúng hành vi ĐÚNG mới (không được sửa test để che giấu lỗi — nếu không chắc test nào đúng, dừng lại và hỏi).
5. **Cấm tuyệt đối:** không hardcode lại bất kỳ dữ liệu giả nào mới (giá giả, rating giả, tên thương hiệu thật của looptrails.com) để "cho qua" một kiểm tra. Nếu không có dữ liệu thật, phải hiển thị trạng thái rỗng/thông báo phù hợp thay vì bịa số liệu.
6. **Sau khi xong toàn bộ mục 1 và mục 2**, làm bước kiểm tra hình ảnh ở mục 3 và lặp lại cho đến khi trang chủ nhìn giống ảnh tham chiếu về bố cục tổng thể (không cần giống 100% pixel).

---

## 1. CRITICAL — bắt buộc sửa trước, theo đúng thứ tự

### C1 — Chặn lỗ hổng đặt tour giá $0 + kiểm tra tour_id hợp lệ

**File:** `wp-content/plugins/tour-booking-core/includes/class-pricing-engine.php`

**Vấn đề:** `rental_rate` lấy thẳng từ client (`floatval($args['rental_rate'])`) không kiểm tra, cho phép gửi số âm khiến `total_usd` về 0. Không có bước nào kiểm tra `tour_id` có phải tour thật đang publish hay không.

**Sửa `calculate_quote()` như sau** (thay toàn bộ method, giữ nguyên phần ký tự đầu/cuối class):

```php
	const RENTAL_BIKES = array(
		'wave_alpha' => array( 'label' => 'Honda Wave Alpha 110cc', 'rate_usd' => 10.0 ),
		'blade_fi'   => array( 'label' => 'Honda Blade FI 110cc', 'rate_usd' => 12.0 ),
		'xr150l'     => array( 'label' => 'Honda XR 150L', 'rate_usd' => 22.0 ),
		'cb500x'     => array( 'label' => 'Adventure Honda CB500X', 'rate_usd' => 48.0 ),
	);

	public static function calculate_quote( $args ) {
		$tour_id      = isset( $args['tour_id'] ) ? absint( $args['tour_id'] ) : 0;
		$party_size   = isset( $args['party_size'] ) ? max( 1, absint( $args['party_size'] ) ) : 1;
		$vehicle_id   = isset( $args['vehicle_id'] ) ? absint( $args['vehicle_id'] ) : 0;
		$transfer_in  = isset( $args['transfer_in'] ) ? absint( $args['transfer_in'] ) : 0;
		$transfer_out = isset( $args['transfer_out'] ) ? absint( $args['transfer_out'] ) : 0;
		$rental_days  = isset( $args['rental_days'] ) ? absint( $args['rental_days'] ) : 0;
		$rental_bike  = isset( $args['rental_bike'] ) ? sanitize_key( $args['rental_bike'] ) : '';
		$voucher_code = isset( $args['voucher_code'] ) ? sanitize_text_field( $args['voucher_code'] ) : '';

		// --- Validate tour_id refers to a real, published tour ---
		if ( $tour_id ) {
			if ( 'tour' !== get_post_type( $tour_id ) || 'publish' !== get_post_status( $tour_id ) ) {
				return array( 'error' => 'invalid_tour_id' );
			}
		}

		// --- Validate vehicle_id belongs to this tour ---
		$tour_unit_price = 0.0;
		$vehicle_name    = '';
		if ( $vehicle_id ) {
			if ( 'vehicle_option' !== get_post_type( $vehicle_id ) ) {
				return array( 'error' => 'invalid_vehicle_id' );
			}
			$linked_tour = absint( get_post_meta( $vehicle_id, 'tbc_tour_id', true ) );
			if ( $tour_id && $linked_tour !== $tour_id ) {
				return array( 'error' => 'vehicle_does_not_belong_to_tour' );
			}
			$price_vnd = intval( get_post_meta( $vehicle_id, 'tbc_price_vnd', true ) );
			if ( $price_vnd <= 0 ) {
				return array( 'error' => 'vehicle_has_no_price' );
			}
			$tour_unit_price = Tbc_Currency::vnd_to_usd( $price_vnd );
			$vehicle_name    = get_the_title( $vehicle_id );
		} elseif ( $tour_id ) {
			// No specific vehicle chosen — use the cheapest published vehicle_option for this tour.
			$cheapest = self::get_cheapest_vehicle_for_tour( $tour_id );
			if ( ! $cheapest ) {
				return array( 'error' => 'no_pricing_available_for_tour' );
			}
			$tour_unit_price = Tbc_Currency::vnd_to_usd( $cheapest['price_vnd'] );
			$vehicle_name    = $cheapest['label'];
		} else {
			return array( 'error' => 'tour_id_required' );
		}

		$tour_subtotal = $tour_unit_price * $party_size;

		// --- Transfers: real price looked up server-side, tbc_price_vnd is the real field ---
		$transfer_subtotal = 0.0;
		foreach ( array( $transfer_in, $transfer_out ) as $transfer_post_id ) {
			if ( ! $transfer_post_id ) {
				continue;
			}
			if ( 'transfer_option' !== get_post_type( $transfer_post_id ) ) {
				return array( 'error' => 'invalid_transfer_id' );
			}
			$t_price_vnd = intval( get_post_meta( $transfer_post_id, 'tbc_price_vnd', true ) );
			if ( $t_price_vnd > 0 ) {
				$transfer_subtotal += Tbc_Currency::vnd_to_usd( $t_price_vnd ) * $party_size;
			}
		}

		// --- Motorbike rental add-on: rate comes from a fixed server-side catalog, never from the client ---
		$rental_subtotal = 0.0;
		if ( $rental_days > 0 && isset( self::RENTAL_BIKES[ $rental_bike ] ) ) {
			$rental_subtotal = $rental_days * self::RENTAL_BIKES[ $rental_bike ]['rate_usd'];
		}

		$subtotal_usd = $tour_subtotal + $transfer_subtotal + $rental_subtotal;

		// --- Voucher / Discount ---
		$discount_usd      = 0.0;
		$discount_applied  = false;
		if ( ! empty( $voucher_code ) ) {
			$voucher_data = self::validate_voucher( $voucher_code, $tour_id, $subtotal_usd );
			if ( $voucher_data['valid'] ) {
				$discount_usd     = $voucher_data['discount_amount'];
				$discount_applied = true;
			}
		}

		$total_usd = max( 0.0, $subtotal_usd - $discount_usd );

		// A tour_subtotal > 0 must never result in total_usd of 0 unless the discount legitimately covers it.
		if ( $tour_subtotal > 0 && $total_usd <= 0 && ! $discount_applied ) {
			return array( 'error' => 'price_calculation_error' );
		}

		$deposit_percent = self::DEFAULT_DEPOSIT_PERCENT;
		$deposit_usd     = round( ( $total_usd * $deposit_percent ) / 100, 2 );
		$balance_due_usd = max( 0.0, $total_usd - $deposit_usd );

		$rate_vnd    = self::get_exchange_rate();
		$total_vnd   = intval( round( $total_usd * $rate_vnd ) );
		$deposit_vnd = intval( round( $deposit_usd * $rate_vnd ) );

		$quote_payload = array(
			'tour_id'           => $tour_id,
			'party_size'        => $party_size,
			'vehicle_name'      => $vehicle_name,
			'tour_unit_price'   => $tour_unit_price,
			'tour_subtotal'     => $tour_subtotal,
			'transfer_subtotal' => $transfer_subtotal,
			'rental_subtotal'   => $rental_subtotal,
			'subtotal_usd'      => $subtotal_usd,
			'discount_usd'      => $discount_usd,
			'discount_applied'  => $discount_applied,
			'total_usd'         => $total_usd,
			'total_vnd'         => $total_vnd,
			'deposit_percent'   => $deposit_percent,
			'deposit_usd'       => $deposit_usd,
			'deposit_vnd'       => $deposit_vnd,
			'balance_due_usd'   => $balance_due_usd,
			'exchange_rate'     => $rate_vnd,
			'timestamp'         => time(),
		);

		$quote_payload['signature'] = self::sign_quote( $quote_payload );

		return $quote_payload;
	}

	/**
	 * Find the cheapest published vehicle_option linked to a tour.
	 */
	public static function get_cheapest_vehicle_for_tour( $tour_id ) {
		$vehicles = get_posts(
			array(
				'post_type'      => 'vehicle_option',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => 'tbc_tour_id',
				'meta_value'     => $tour_id,
				'orderby'        => 'meta_value_num',
				'meta_query'     => array(
					array(
						'key'     => 'tbc_price_vnd',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
			)
		);
		if ( empty( $vehicles ) ) {
			return null;
		}
		$cheapest = null;
		foreach ( $vehicles as $v ) {
			$price = intval( get_post_meta( $v->ID, 'tbc_price_vnd', true ) );
			if ( null === $cheapest || $price < $cheapest['price_vnd'] ) {
				$cheapest = array(
					'id'        => $v->ID,
					'price_vnd' => $price,
					'label'     => get_the_title( $v->ID ),
				);
			}
		}
		return $cheapest;
	}
```

**Lưu ý quan trọng:** `deposit_percent` ở trên tạm dùng thẳng hằng số `self::DEFAULT_DEPOSIT_PERCENT` (không gọi `get_deposit_percent()` — hàm đó CHƯA tồn tại ở bước này, chỉ được thêm sau ở task I4). Khi làm xong I4 (thêm cấu hình admin cho % đặt cọc), quay lại đổi dòng `$deposit_percent = self::DEFAULT_DEPOSIT_PERCENT;` thành gọi hàm mới đó — không làm trước ở bước C1.

**Cũng sửa `handle_quote` và `handle_book`** trong `class-booking-handler.php` — sau dòng `$quote = Tbc_Pricing_Engine::calculate_quote( $params );`, thêm ngay:

```php
if ( isset( $quote['error'] ) ) {
    return new WP_Error( $quote['error'], 'Unable to calculate a valid quote for the given parameters.', array( 'status' => 400 ) );
}
```

(áp dụng cho cả `handle_quote` và `handle_book`, ngay sau dòng gọi `calculate_quote`).

**Kiểm chứng bắt buộc:**
```
# 1. Exploit cũ phải bị chặn — HTTP 400, không phải HTTP 200 với total_usd:0
curl -s -X POST http://localhost/looptrails/wp-json/tour-booking/v1/quote -H "Content-Type: application/json" -d "{\"tour_id\":1,\"party_size\":1,\"rental_days\":1,\"rental_rate\":-1000}"

# 2. tour_id không tồn tại phải bị chặn
curl -s -X POST http://localhost/looptrails/wp-json/tour-booking/v1/quote -H "Content-Type: application/json" -d "{\"tour_id\":9999999,\"party_size\":1}"

# 3. Quote hợp lệ với 1 tour thật phải trả về total_usd > 0 và khớp đúng giá vehicle_option thật
C:\xampp\wp-cli.bat post list --post_type=tour --post_status=publish --field=ID
# lấy 1 ID thật ở trên, ví dụ 275, rồi:
curl -s -X POST http://localhost/looptrails/wp-json/tour-booking/v1/quote -H "Content-Type: application/json" -d "{\"tour_id\":275,\"party_size\":2}"
```
**Kết quả mong đợi:** lệnh 1 và 2 trả về HTTP 400 với `code` là `price_calculation_error`/`invalid_tour_id`. Lệnh 3 trả về HTTP 200, `total_usd` khớp với giá vehicle_option rẻ nhất của tour đó nhân 2 (kiểm tra chéo bằng `C:\xampp\wp-cli.bat post meta list <vehicle_option_id đúng tour đó>`).

---

### C2 — Giá thật của tour/xe/transfer bị "chết" do sai tên field

**Vấn đề gốc:** field `tbc_price_from_usd`/`tbc_price_usd` được code đọc ở nhiều nơi **không tồn tại trong schema thật**. Field đúng là `tbc_price_vnd` (số nguyên, đơn vị VND) trên các post type `vehicle_option`/`accommodation`/`transfer_option`/`addon` — xem `wp-content/plugins/tour-booking-core/includes/class-meta-fields.php` dòng 36-58. **Post type `tour` không có field giá riêng** — giá của một tour được tính từ các `vehicle_option` con của nó (liên kết qua field `tbc_tour_id`).

`class-pricing-engine.php` đã được sửa đúng ở task C1 (đọc `tbc_price_vnd` qua `Tbc_Currency::vnd_to_usd()`). Task này sửa **2 file còn lại**: `tour-card.php` (hiển thị) và `class-search-filter.php` (bộ lọc). Cũng cần thêm 1 cơ chế đồng bộ giá "từ X" lên tour cha.

**Bước 2.1 — Thêm field cache giá "từ X" trên tour**

File `wp-content/plugins/tour-booking-core/includes/class-meta-fields.php`, trong mảng `'tour' => array(...)`, thêm dòng:
```php
'tbc_price_from_vnd' => array( 'type' => 'integer', 'is_price' => true ),
```

**Bước 2.2 — Đồng bộ tự động khi vehicle_option được lưu**

Thêm method mới vào `class-pricing-engine.php`:
```php
	/**
	 * Recompute and cache the tour's "starting from" price whenever one of
	 * its vehicle_option children is saved. Hooked to save_post_vehicle_option.
	 */
	public static function sync_tour_starting_price( $vehicle_post_id ) {
		$tour_id = absint( get_post_meta( $vehicle_post_id, 'tbc_tour_id', true ) );
		if ( ! $tour_id ) {
			return;
		}
		$cheapest = self::get_cheapest_vehicle_for_tour( $tour_id );
		if ( $cheapest ) {
			update_post_meta( $tour_id, 'tbc_price_from_vnd', $cheapest['price_vnd'] );
		}
	}
```

Đăng ký hook — mở `wp-content/plugins/tour-booking-core/tour-booking-core.php`, tìm nơi các hook khác được đăng ký (ví dụ gần `add_action` khác), thêm:
```php
add_action( 'save_post_vehicle_option', array( 'Tbc_Pricing_Engine', 'sync_tour_starting_price' ) );
```

**Bước 2.3 — Backfill dữ liệu demo đã có sẵn** (chạy 1 lần, không phải code thường trực):
```
C:\xampp\wp-cli.bat eval "
foreach ( get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1, 'post_status' => 'any' ) ) as \$t ) {
    \$cheapest = Tbc_Pricing_Engine::get_cheapest_vehicle_for_tour( \$t->ID );
    if ( \$cheapest ) { update_post_meta( \$t->ID, 'tbc_price_from_vnd', \$cheapest['price_vnd'] ); echo \$t->ID . ': ' . \$cheapest['price_vnd'] . \"\n\"; }
}
"
```

**Bước 2.4 — Sửa `tour-card.php`**

Thay đoạn (dòng ~29, ~33-35, ~67-75):
```php
$price_from    = get_post_meta( $post_id, 'tbc_price_from_usd', true );
...
if ( ! $price_from ) {
    $price_from = 140;
}
...
$price_self_usd = intval( $price_from );
$price_self_vnd = number_format( $price_self_usd * 25400, 0, ',', '.' );
$price_easy_usd = intval( round( $price_self_usd * 1.48 ) );
...
$price_jeep_usd = intval( round( $price_self_usd * 2.07 ) );
...
```
bằng logic đọc **thật** từ các `vehicle_option` con của tour này (bỏ hẳn tỉ lệ 1.48/2.07 bịa ra):
```php
$vehicles = get_posts( array(
    'post_type'      => 'vehicle_option',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'tbc_tour_id',
    'meta_value'     => $post_id,
) );
$price_rows = array();
foreach ( $vehicles as $v ) {
    $vnd = intval( get_post_meta( $v->ID, 'tbc_price_vnd', true ) );
    if ( $vnd <= 0 ) {
        continue;
    }
    $price_rows[] = array(
        'label' => get_the_title( $v->ID ),
        'vnd'   => $vnd,
        'usd'   => Tbc_Currency::vnd_to_usd( $vnd ),
    );
}
usort( $price_rows, function( $a, $b ) { return $a['vnd'] <=> $b['vnd']; } );
```
Sau đó trong phần HTML render (dòng ~111-134), thay 3 khối `<div class="lt-price-row">` cứng bằng vòng lặp qua `$price_rows` (nếu `$price_rows` rỗng thì hiển thị 1 dòng "Contact for pricing" thay vì bịa số — không được để trống trắng và không được viết cứng lại $140):
```php
<div class="tour-card__prices lt-price-rows">
	<?php if ( empty( $price_rows ) ) : ?>
		<div class="lt-price-row"><span class="lt-price-row__label"><?php esc_html_e( 'Contact for pricing', 'tour-reference-theme' ); ?></span></div>
	<?php else : ?>
		<?php foreach ( $price_rows as $i => $row ) : ?>
			<div class="lt-price-row<?php echo 1 === $i ? ' is-featured-tier' : ''; ?>">
				<span class="lt-price-row__label"><?php echo esc_html( $row['label'] ); ?></span>
				<div class="lt-price-row__amount">
					<span class="lt-price-row__value"><?php echo esc_html( number_format( $row['vnd'], 0, ',', '.' ) ); ?> ₫</span>
					<span class="lt-price-row__usd">· $<?php echo esc_html( $row['usd'] ); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
```

**Bước 2.5 — Sửa `class-search-filter.php`**

Thay:
```php
if ( ! empty( $args['max_price_usd'] ) ) {
    $meta_query[] = array(
        'key'     => 'tbc_price_from_usd',
        'value'   => floatval( $args['max_price_usd'] ),
        'compare' => '<=',
        'type'    => 'DECIMAL(10,2)',
    );
}
```
bằng:
```php
if ( ! empty( $args['max_price_usd'] ) ) {
    $max_vnd = Tbc_Currency::usd_to_vnd( floatval( $args['max_price_usd'] ) );
    $meta_query[] = array(
        'key'     => 'tbc_price_from_vnd',
        'value'   => $max_vnd,
        'compare' => '<=',
        'type'    => 'NUMERIC',
    );
}
```

**Bước 2.6 — Sửa `class-seo.php`** dòng có `tbc_price_from_usd`:
```php
$price_usd = floatval( get_post_meta( $post->ID, 'tbc_price_from_usd', true ) );
if ( $price_usd <= 0 ) {
    $price_usd = 140.0;
}
```
thay bằng:
```php
$price_vnd = intval( get_post_meta( $post->ID, 'tbc_price_from_vnd', true ) );
$price_usd = $price_vnd > 0 ? Tbc_Currency::vnd_to_usd( $price_vnd ) : 0;
```
và trong khối `'offers'` phía dưới, nếu `$price_usd` là 0 thì **không xuất khối `offers` này** (bỏ hẳn `'offers' => ...` khỏi mảng $schema thay vì in ra giá 0 hoặc giá bịa).

**Kiểm chứng bắt buộc:**
```
# Trước hết chạy discovery để chắc chắn có dữ liệu demo thật:
C:\xampp\wp-cli.bat post list --post_type=vehicle_option --post_status=publish --fields=ID,post_title,post_status --format=table

# Lấy 1 tour thật, xem giá đã cache đúng chưa:
C:\xampp\wp-cli.bat post meta get <tour_id> tbc_price_from_vnd

# Trang chủ + trang archive-tour phải hiển thị giá KHÁC NHAU giữa các tour (không còn 140/207/290 giống hệt mọi nơi)
curl -s http://localhost/looptrails/ | grep -o 'lt-price-row__usd">[^<]*' | sort -u
curl -s "http://localhost/looptrails/?post_type=tour" | grep -o 'lt-price-row__usd">[^<]*' | sort -u

# Bộ lọc giá phải trả về > 0 kết quả với mức giá hợp lý
C:\xampp\wp-cli.bat eval "var_dump( Tbc_Search_Filter::filter_tours(['max_price_usd'=>500])->found_posts );"
```
**Kết quả mong đợi:** các dòng giá hiển thị khác nhau theo từng tour thật (không phải toàn bộ đều là cùng 3 con số), và bộ lọc `max_price_usd:500` trả về found_posts > 0.

---

### C3 — Trang chủ: lưới tour không render (hiện chữ thô `[tour_featured_grid]`)

**Nguyên nhân đã xác định chắc chắn:** WordPress core's `render_block_core_shortcode()` (file `wp-includes/blocks/shortcode.php`, không sửa file này — đây là core, không phải file của dự án) chỉ gọi `wpautop( $content )`, không tự gọi `do_shortcode()`. Khi shortcode được nhúng trực tiếp trong template `.html` (như `archive-tour.html`, `single-tour.html`) thì `do_shortcode()` vẫn chạy đúng qua tầng render khác — nhưng khi nhúng qua `<!-- wp:pattern {"slug":"..."} /-->` (cách `front-page.html` dùng cho `patterns/featured-tours.php`), tầng render lại KHÔNG chạy `do_shortcode()`.

**Cách sửa an toàn nhất — không dùng shortcode nữa, gọi thẳng hàm PHP trong pattern:**

File `wp-content/themes/tour-reference-theme/patterns/featured-tours.php`, thay khối:
```php
	<!-- wp:shortcode -->
	[tour_featured_grid]
	<!-- /wp:shortcode -->
```
bằng (gọi trực tiếp hàm render, không qua shortcode nữa — hàm `tour_theme_render_featured_tours()` đã có sẵn trong `includes/tour-card.php`):
```php
	<?php echo tour_theme_render_featured_tours(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
```

**Kiểm chứng bắt buộc:**
```
curl -s http://localhost/looptrails/ | grep -c "tour-card lt-tour-card"
curl -s http://localhost/looptrails/ | grep -c "tour_featured_grid"
```
**Kết quả mong đợi:** dòng đầu trả về số > 0 (có thẻ tour thật), dòng hai trả về 0 (không còn chữ thô nào của shortcode sót lại).

**Sau khi sửa xong, kiểm tra thêm:** `single-tour.html` và `archive-tour.html` vẫn dùng `[tour_featured_grid ...]` qua `<!-- wp:shortcode -->` trực tiếp trong template — đã xác nhận CHẠY ĐÚNG (không qua `wp:pattern`), **không cần sửa 2 file này**. Chỉ xác nhận lại bằng:
```
curl -s "http://localhost/looptrails/?post_type=tour" | grep -c "tour-card lt-tour-card"
```
phải > 0.

---

### C4 — Code PHP hiện ra thành chữ thô trên 8 template `.html`

**Nguyên nhân:** file `.html` trong block-theme KHÔNG được WordPress `include` như PHP — chỉ có file `.php` trong `patterns/` mới được thực thi PHP. Mọi `<?php esc_html_e(...); ?>` viết trực tiếp trong `.html` sẽ hiện nguyên văn.

**Danh sách file cần sửa (đã xác nhận có lỗi qua render thật):**
`templates/404.html`, `templates/archive-tour.html`, `templates/archive.html`, `templates/page-about.html`, `templates/page-contact.html`, `templates/page-motorbike-rental.html`, `templates/search.html`, `templates/single-tour.html`

**Cách sửa — cho MỖI file trên:**
1. Tìm mọi đoạn `<?php esc_html_e( 'Text tiếng Anh', 'tour-reference-theme' ); ?>` hoặc `<?php echo ...; ?>` trong file.
2. Nếu là văn bản tĩnh (`esc_html_e`), thay trực tiếp bằng chính chuỗi tiếng Anh đó dạng plain text (bỏ toàn bộ tag PHP), ví dụ:
   - Trước: `<h2><?php esc_html_e( 'Off The Beaten Track', 'tour-reference-theme' ); ?></h2>`
   - Sau: `<h2>Off The Beaten Track</h2>`
3. Nếu đoạn PHP có logic động thật sự cần thực thi (vòng lặp, gọi hàm lấy dữ liệu tour thật — kiểm tra kỹ từng trường hợp, đừng giả định), **không thể sửa bằng cách xóa tag PHP** — phải chuyển toàn bộ khối đó thành một pattern `.php` riêng trong `patterns/` (theo đúng cách `featured-tours.php` đã làm đúng) và gọi vào template bằng `<!-- wp:pattern {"slug":"tour-reference-theme/ten-pattern-moi"} /-->`, y hệt cách C3 vừa sửa.

**Kiểm chứng bắt buộc — chạy sau khi sửa xong TẤT CẢ 8 file:**
```
for path in "this-page-does-not-exist-xyz/" "?post_type=tour" "" ; do
  echo "=== $path ==="
  curl -s "http://localhost/looptrails/${path}" | grep -c "<?php"
done
C:\xampp\wp-cli.bat post list --post_type=tour --field=post_name | head -1
# lấy slug thật ở trên rồi:
curl -s "http://localhost/looptrails/tours/<slug-that-vua-lay>/" | grep -c "<?php"
curl -s "http://localhost/looptrails/?s=zzzz_khong_ton_tai_zzzz" | grep -c "<?php"
```
**Kết quả mong đợi:** MỌI lệnh `grep -c "<?php"` ở trên đều trả về **0**.

---

### C5 — 3 trang bắt buộc theo spec chưa được tạo trên site thật (Giới thiệu, Liên hệ, Thuê xe máy)

**Vấn đề:** file template `page-about.html`, `page-contact.html`, `page-motorbike-rental.html` đã tồn tại, nhưng không có WordPress Page nào dùng chúng — cần tạo Page thật với đúng slug để WordPress tự động khớp template theo quy ước `page-{slug}.html`.

**Chạy đúng các lệnh sau (sau khi đã sửa xong C4 cho 3 file này, vì nếu chưa sửa C4 thì trang mới tạo cũng sẽ lộ code PHP):**
```
C:\xampp\wp-cli.bat post create --post_type=page --post_title="About" --post_name="about" --post_status=publish
C:\xampp\wp-cli.bat post create --post_type=page --post_title="Contact" --post_name="contact" --post_status=publish
C:\xampp\wp-cli.bat post create --post_type=page --post_title="Motorbike Rental" --post_name="motorbike-rental" --post_status=publish
```
Sau đó thêm các trang này vào menu điều hướng nếu theme có menu (kiểm tra `wp-cli.bat menu list`; nếu có menu chính, thêm 3 mục này vào).

**Kiểm chứng bắt buộc:**
```
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/looptrails/about/
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/looptrails/contact/
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/looptrails/motorbike-rental/
```
**Kết quả mong đợi:** cả 3 lệnh trả về **200**, không phải 404.

---

### C6 — Milestone thanh toán (OnePay/VNPay/MoMo) bị bỏ hoàn toàn, không công bố

**Đây là hạng mục lớn nhất, không nên vội làm ẩu.** Không tự ý implement 3 cổng thanh toán thật trong 1 lần nếu không chắc chắn về API sandbox thật của từng bên — làm sai phần động vào tiền còn nguy hiểm hơn không làm.

**Yêu cầu tối thiểu bắt buộc cho task này:**
1. Tạo file `docs/payments.md` mô tả rõ: trạng thái hiện tại là **CHƯA triển khai** cổng thanh toán nào, đây là sai lệch đã biết so với spec §10, lý do (thời gian/độ phức tạp), và kế hoạch dự kiến triển khai sau.
2. Sửa `docs/visual-acceptance-report-final.md` — bỏ dòng "Hoàn tất Toàn diện Dự án" nếu thanh toán chưa xong; thêm rõ dòng "Milestone Thanh toán (Payment Gateway sandbox): CHƯA TRIỂN KHAI" vào bảng milestone.
3. Trong `class-booking-handler.php`, đảm bảo `tbc_payment_status` được set là `'pending'` (đã đúng, giữ nguyên) và **không** để `tbc_booking_status` tự động là `'confirmed'` ngay khi submit — đổi thành `'pending_payment'` (khớp đúng vòng đời spec §9: draft → pending-payment → paid → confirmed) cho đến khi có cổng thanh toán thật xác nhận.
4. Nếu có thời gian, implement tối thiểu 1 cổng (khuyến nghị VNPay vì tài liệu sandbox công khai dễ tiếp cận nhất) ở **chế độ sandbox**, có chữ ký request/callback được verify đúng, ghi log. Nếu không kịp, để trống nhưng đã disclosure rõ theo bước 1-2.

**Kiểm chứng bắt buộc:**
```
type docs\payments.md
C:\xampp\wp-cli.bat post meta get <booking_id_vua_tao_qua_test_C1> tbc_booking_status
```
**Kết quả mong đợi:** file `payments.md` tồn tại và nói rõ trạng thái thật; `tbc_booking_status` của booking mới không còn tự động là `confirmed`.

---

### C7 — Dữ liệu SEO giả mạo (fake rating) + hardcode thông tin thật của looptrails.com

**Vấn đề:** `class-seo.php` hardcode `aggregateRating: 4.9/1200` và `4.9/120` không dựa trên dữ liệu thật; nhiều file hardcode tên "Loop Trails Vietnam", email `booking@looptrails.com`, số điện thoại, địa chỉ Hà Giang — đây là thông tin thật của website tham chiếu, **cấm sử dụng** theo ranh giới bản quyền nội dung của dự án.

**Bước 7.1 — Thêm trang cài đặt admin cho thông tin site**

File `wp-content/plugins/tour-booking-core/includes/class-admin-page.php`, thêm vào `render()` (trước 2 form import/remove hiện có) một form cài đặt cơ bản, dùng `register_setting`/`get_option`/`update_option` chuẩn WordPress Settings API cho các field: `tbc_site_business_name`, `tbc_site_email`, `tbc_site_phone`, `tbc_site_address`. Đăng ký các option này trong `register()` bằng `register_setting( 'tbc_settings', 'tbc_site_business_name' )` (và tương tự cho 3 field còn lại), thêm `settings_fields('tbc_settings')` + `do_settings_sections('tbc_settings')` hoặc tự vẽ input thủ công đơn giản (không bắt buộc dùng Settings API đầy đủ, miễn là lưu được qua `admin-post.php` giống 2 form đã có, dùng `update_option` trong 1 handler mới `handle_save_settings`).

**Bước 7.2 — Sửa `class-seo.php`:** thay toàn bộ giá trị hardcode bằng `get_option('tbc_site_business_name', get_bloginfo('name'))`, `get_option('tbc_site_email', get_option('admin_email'))`, v.v. **Xóa hẳn 2 khối `'aggregateRating' => [...]`** (cả site-wide và per-tour) — không thay bằng số khác, xóa hẳn key này khỏi mảng cho đến khi có review thật để hiển thị (nếu muốn giữ chỗ, chỉ output `aggregateRating` khi `tbc_rating_count` thật > 0 trên post đó, lấy đúng `tbc_rating_value`/`tbc_rating_count` thật từ post meta thay vì số cứng).

**Bước 7.3 — Sửa các file còn lại có hardcode brand/liên hệ thật:** `parts/footer.html`, `templates/page-contact.html`, `patterns/why-choose-us.php`, `patterns/testimonials.php`, `templates/page-about.html` — thay `booking@looptrails.com`, "Loop Trails", số điện thoại `+84987654321`/`+84 987 654 321`, địa chỉ "Nguyen Trai Street, Ha Giang City" bằng giá trị đọc từ `get_bloginfo('name')` / `get_option('admin_email')` / các option mới ở bước 7.1, hoặc placeholder trung tính rõ ràng là placeholder (ví dụ "Your Business Name", "contact@example.com") — **không được thay bằng một tên thương hiệu khác nghe giống looptrails.com**.

**Kiểm chứng bắt buộc:**
```
curl -s http://localhost/looptrails/ | grep -c "looptrails.com\|Loop Trails Vietnam"
curl -s "http://localhost/looptrails/tours/<slug-that>/" > /tmp/tour.html
grep -o '"aggregateRating"[^}]*}' /tmp/tour.html
```
**Kết quả mong đợi:** lệnh đầu trả về 0. Lệnh sau không tìm thấy `aggregateRating` giả (hoặc nếu có, phải khớp đúng số thật từ post meta, không phải 4.9/120 cứng).

---

### C8 — M11/M12 được đánh dấu "hoàn thành" giả

**Sửa đơn giản, chỉ là sự trung thực tài liệu:**
1. Mở `docs/superpowers/plans/2026-08-21-milestone-11-visual-diff-iteration.md` và `2026-08-21-milestone-12-final-qa-handover.md` — bỏ tick `[x]` ở mọi checkbox chưa thực sự có công việc/commit tương ứng, đổi lại thành `[ ]`.
2. Xóa mọi câu khẳng định milestone này "đã hoàn thành" trong `docs/visual-acceptance-report-final.md` nếu M11/M12 chưa xong — liệt kê rõ trạng thái thật của cả 12 milestone (không phải 10).
3. Không tạo file `walkthrough.md` giả — nếu làm task này thật (mục 3 bên dưới), hãy tạo file đó thật với nội dung thật.

---

## 2. IMPORTANT — làm sau khi toàn bộ mục 1 đã pass hết kiểm chứng

Với mỗi mục dưới đây: sửa xong → chạy lại PHPUnit cả 2 package → phải vẫn 100% pass → mới sang mục tiếp theo.

- **I1 — Rate limit & idempotency cho `/quote`, `/book`:** thêm kiểm tra đơn giản dựa trên IP + transient (ví dụ tối đa 10 request/phút/IP dùng `get_transient`/`set_transient`), và thêm field `idempotency_key` (string do client gửi lên) — nếu đã có booking với cùng key trong 24h gần nhất, trả về booking cũ thay vì tạo mới.
- **I2 — Email báo sai sự thật khi gửi thất bại:** trong `class-mailer.php`, bắt giá trị trả về của `wp_mail()` (`$sent = wp_mail(...)`), nếu `false` thì `error_log()` lại và trả `false` từ `send_booking_emails()`. Trong `class-booking-handler.php`, dùng giá trị trả về đó để đổi message trả về client — nếu gửi thất bại, nói "Booking submitted — we will contact you to confirm" thay vì khẳng định "Confirmation email has been sent" khi không chắc.
- **I3 — Voucher hardcode dùng vô hạn:** đổi `validate_voucher()` để tra cứu Voucher CPT thật (post type `voucher`, field `tbc_code`/`tbc_amount`/`tbc_usage_limit`/`tbc_used_count`/`tbc_min_spend_vnd`/`tbc_valid_from`/`tbc_valid_to` — xem `class-meta-fields.php` để lấy đúng tên field) thay vì 3 mã hardcode; kiểm tra hết hạn, đã dùng hết lượt, min spend trước khi cho áp dụng; tăng `tbc_used_count` khi voucher thật sự được dùng trong `handle_book`.
- **I4 — Tỷ giá & % đặt cọc không cấu hình được:** thêm 2 field vào form admin đã tạo ở C7.1 (`tbc_exchange_rate`, `tbc_deposit_percent`), sửa `get_exchange_rate()` và deposit-percent trong `class-pricing-engine.php` đọc từ `get_option()` thay vì hằng số cứng (giữ hằng số làm giá trị mặc định nếu chưa cấu hình).
- **I6 — Trang chi tiết tour hardcode lịch trình/giá giống hệt mọi tour:** sửa `single-tour.html` để dùng pattern PHP (giống cách sửa C3/C4) truy vấn thật `itinerary_day` (lọc theo `tbc_tour_id` = tour hiện tại, sắp theo `tbc_day_number`) thay vì 3 khối tĩnh; bảng giá dùng lại đúng logic `vehicle_option` đã viết ở C2.
- **I7 — Tab Destinations/Itinerary/Transport/Accommodation ở trang chủ không hoạt động:** hoàn thiện nội dung 3 tab còn thiếu trong `patterns/top-destinations-essentials.php`, thêm 1 file JS nhỏ (`assets/js/tabs.js`, enqueue trong `functions.php`) để chuyển `.is-active` khi click.
- **I8 — Giao diện khác biệt so với ảnh tham chiếu:** xem mục 3 bên dưới — đây là việc cần lặp lại nhiều vòng bằng ảnh chụp thật, không sửa 1 lần là xong.
- **I9 — Test `.html` chỉ so chuỗi thô, không render thật:** sửa `tests/test-secondary-templates.php` để gọi `do_blocks()` (giống cách `test-home-patterns.php` đã làm đúng) trên nội dung thật của trang (dùng `wp_insert_post` tạo page test rồi `get_the_content()`/`the_content` filter, hoặc `render_block_core_template_part`) thay vì `file_get_contents()` + so chuỗi trên file chưa thực thi.

---

## 3. Kiểm tra giao diện bằng ảnh chụp thật — lặp lại đến khi đạt

**Đây là bước bắt buộc cuối cùng, không được bỏ qua chỉ vì code "chạy được".**

1. Chạy: `cd tools/local-audit && node capture-local.mjs` — script này chụp lại toàn bộ trang chủ ở 5 kích thước màn hình, lưu vào `docs/reference-screenshots/local-m5/*-full.png` (đè lên ảnh cũ).
2. Mở song song 2 ảnh sau và so sánh trực tiếp bằng mắt (hoặc bằng công cụ đọc ảnh nếu có):
   - Ảnh tham chiếu (site gốc thật): `docs/reference-screenshots/home/desktop.png`
   - Ảnh vừa chụp: `docs/reference-screenshots/local-m5/desktop-full.png`
3. **Các điểm ĐÃ XÁC NHẬN sai và phải sửa cho khớp:**
   - Hero (banner đầu trang) phải có **ảnh nền phong cảnh núi** (full-bleed, phủ gradient tối để chữ dễ đọc) — hiện tại là nền đen phẳng không có ảnh. Sửa `patterns/hero-home.php`: thêm `background-image` (dùng ảnh có sẵn trong `assets/images/` của theme, hoặc ảnh Wikimedia Commons license hợp lệ theo đúng quy tắc bản quyền của dự án — **tuyệt đối không tải/hotlink ảnh từ looptrails.com**).
   - Nền của các section phía dưới hero hiện đang bị phủ **màu be/tan gần như toàn trang** — kiểm tra `theme.json` và các class `has-surface-header-footer-background-color`/tương tự đang bị áp dụng sai phạm vi (đang áp cho quá nhiều section thay vì chỉ 1-2 section như bản gốc dùng màu, phần lớn còn lại phải là nền trắng).
   - Lưới "Top Destinations & Essentials" hiện render thành 1 cột hẹp, thẻ cao — phải là lưới nhiều cột (4 cột ở desktop) như ảnh gốc. Kiểm tra CSS grid/flex trong `patterns/top-destinations-essentials.php` — khả năng cao là container cha thiếu `layout:{type:"grid"}` hoặc CSS `grid-template-columns` bị thiếu/sai ở breakpoint desktop.
4. **Sau khi sửa từng điểm, LẶP LẠI bước 1-2** (chụp lại, so lại) — không dừng lại sau 1 lần sửa. Tiếp tục cho đến khi bố cục tổng thể (thứ tự section, có ảnh nền hero, tỷ lệ lưới đúng số cột) khớp với ảnh tham chiếu. Không cần khớp tuyệt đối từng pixel màu/khoảng cách.
5. Nếu phát hiện thêm bất kỳ điểm khác biệt nào ngoài 3 điểm đã liệt kê ở bước 3 trong lúc so sánh, ghi lại vào cuối file `docs/audit-report-gemini-handoff-2026-08-21.md` (thêm 1 mục mới "Bổ sung sau fix wave 2026-08-21", không sửa/xóa nội dung cũ trong file đó) rồi sửa luôn nếu đủ rõ ràng.

---

## 4. Sau khi xong hết — báo cáo lại

Viết 1 file mới `docs/fix-report-2026-08-21.md` (tiếng Việt) liệt kê: từng task ở mục 1/2/3 đã làm gì, kết quả kiểm chứng thật (dán output lệnh thật, không tự bịa số liệu), và bất kỳ sai lệch/khó khăn nào gặp phải mà chưa giải quyết được — **trung thực, kể cả nếu có phần chưa xong**. Đây là điều quan trọng nhất: báo cáo trước đó (`visual-acceptance-report-final.md`) đã mất uy tín vì báo "hoàn tất" sai sự thật — không lặp lại lỗi đó.
