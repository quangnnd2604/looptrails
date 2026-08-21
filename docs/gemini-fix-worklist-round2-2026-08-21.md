# Work Order Vòng 2 — Các vấn đề còn lại sau fix wave đầu tiên

**Dành cho AI agent (Gemini) tiếp tục sửa.** Đọc file này đầy đủ trước khi sửa. Đây là kết quả kiểm tra ĐỘC LẬP (chạy test thật, gọi API thật, đọc DB thật, chụp ảnh thật) sau khi fix wave đầu tiên (`docs/gemini-fix-worklist-2026-08-21.md`) đã hoàn thành — 8 lỗi CRITICAL và hầu hết IMPORTANT đã sửa **đúng, xác nhận thật** (không phải bịa). Còn 4 vấn đề thật sự chưa xong, liệt kê dưới đây.

**Quy tắc giống vòng 1:** sửa từng mục, chạy kiểm chứng, chỉ qua mục tiếp theo khi đúng. Không hardcode dữ liệu giả để qua bài.

**Cập nhật 2026-08-21 (sau khi F1-F4 được viết):** người dùng báo "bố cục trang chủ và các trang khác bị lệch sang phải, giống giao diện mobile" khi xem bằng trình duyệt thật. Đã điều tra và tìm ra nguyên nhân THẬT, khác hẳn suy đoán ban đầu (không phải lỗi CSS/grid). Thêm mục **F0** bên dưới — làm mục này **TRƯỚC TIÊN**, trước cả F1-F4, vì nó phá vỡ bố cục nhiều trang cùng lúc.

---

## F0 — ƯU TIÊN CAO NHẤT: WordPress tự động phá hỏng thuộc tính `alt=""` trong ảnh SVG inline, làm rối toàn bộ HTML phía sau

**Đây là nguyên nhân thật của việc "bố cục lệch sang phải, giống mobile" mà người dùng báo cáo — đã xác minh bằng cách đọc trực tiếp DOM thật trong trình duyệt (Playwright `page.content()`), không phải đoán qua CSS.**

**Cơ chế lỗi (đã xác nhận chắc chắn):** WordPress có bộ lọc `wptexturize()` tự động chạy trên nội dung block (kể cả nội dung pattern render qua `do_blocks()`), chuyển các dấu ngoặc kép thẳng `"` thành dấu ngoặc kép kiểu chữ (`"` và `"`, cong) để đẹp về mặt in ấn. Bộ lọc này áp dụng nhầm lên các thẻ `<img>` viết inline dạng:
```html
<img src="data:image/svg+xml;utf8,<svg xmlns='...'>...</svg>" alt="Highland Trail" />
```
Cụ thể tại vị trí `</svg>" alt="Highland Trail"` — `wptexturize` biến 2 dấu `"` bao quanh `alt=` thành dấu cong `"…"`. Vì dấu ngoặc kép cong **không phải ký tự hợp lệ để đóng thuộc tính HTML**, trình duyệt không nhận diện được điểm kết thúc thuộc tính `src="..."` nữa, và tiếp tục "nuốt" toàn bộ HTML phía sau (kể cả các thẻ `<div class="...">` thật) vào bên trong giá trị của `src` cho đến khi gặp dấu `"` thẳng tiếp theo — làm mất hết `class` (nên mất luôn CSS căn giữa/lưới) của rất nhiều phần tử sau đó. Đã xác minh trực tiếp: trên trang chủ, `<div id="destinations">` (đúng ra phải rộng ~1160px, căn giữa) bị nuốt vào text và render sai vị trí/kích thước — khớp chính xác với hiện tượng "lệch phải, hẹp như mobile" người dùng mô tả.

**Vị trí lỗi — đã đếm chính xác bằng grep, tất cả đều theo mẫu `</svg>" alt=`:**
- `wp-content/themes/tour-reference-theme/patterns/top-destinations-essentials.php` — 8 chỗ (8 ảnh destination card)
- `wp-content/themes/tour-reference-theme/patterns/rental-bikes.php` — 4 chỗ (dùng ở trang Thuê xe máy — giải thích vì sao trang đó cũng bị lỗi)
- `wp-content/themes/tour-reference-theme/patterns/blog-teaser.php` — 3 chỗ
- `wp-content/themes/tour-reference-theme/patterns/brand-narrative.php` — 1 chỗ
- `wp-content/themes/tour-reference-theme/includes/tour-card.php` — 2 chỗ (**file này AN TOÀN, không cần sửa** — được gọi trực tiếp bằng PHP `echo tour_theme_render_featured_tours()`, không đi qua `do_blocks()`/`wptexturize` — nhưng sửa luôn cho nhất quán nếu có thời gian)

**Cách sửa — áp dụng cho TẤT CẢ các chỗ trong 4 file có lỗi (không chỉ ví dụ dưới đây):** không viết SVG thô trực tiếp trong `src="data:image/svg+xml;utf8,<svg...`. Thay bằng cách encode đúng chuẩn qua `rawurlencode()` trong PHP — cách này loại bỏ hoàn toàn mọi dấu `<`, `>`, `'`, `"` thô khỏi thuộc tính `src`, nên `wptexturize` không còn gì để phá hỏng.

**Ví dụ cụ thể — `patterns/brand-narrative.php` dòng 70, trước:**
```php
<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='450' viewBox='0 0 600 450'><rect fill='%23d0cac0' width='600' height='450'/><text fill='%23444' font-family='sans-serif' font-size='22' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Highland Trail Landscape</text></svg>" alt="Highland Trail" />
```
**Sau (viết lại bằng PHP, dùng `rawurlencode()`):**
```php
<?php
$narrative_svg = "<svg xmlns='http://www.w3.org/2000/svg' width='600' height='450' viewBox='0 0 600 450'><rect fill='#d0cac0' width='600' height='450'/><text fill='#444' font-family='sans-serif' font-size='22' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Highland Trail Landscape</text></svg>";
?>
<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $narrative_svg ) ); ?>" alt="<?php esc_attr_e( 'Highland Trail', 'tour-reference-theme' ); ?>" />
```
**Lưu ý khi viết lại:** trong `$narrative_svg`, đổi `%23` (mã hex cũ dùng để thoát ký tự `#` thủ công) trở lại thành `#` thường — vì giờ `rawurlencode()` sẽ tự động encode đúng toàn bộ chuỗi, không cần tự thoát `#` bằng tay nữa (nếu giữ `%23` cũ, `rawurlencode` sẽ encode luôn dấu `%` thành `%2523`, làm sai màu hiển thị).

Áp dụng đúng khuôn mẫu này cho **cả 8 chỗ** trong `top-destinations-essentials.php`, **cả 4 chỗ** trong `rental-bikes.php`, và **cả 3 chỗ** trong `blog-teaser.php` — mỗi ảnh có nội dung SVG khác nhau (màu nền, text khác nhau), giữ nguyên nội dung, chỉ đổi CÁCH ENCODE.

**Kiểm chứng bắt buộc — chạy sau khi sửa hết cả 4 file:**
```
# Không còn chỗ nào dùng cú pháp cũ dễ vỡ
grep -rn "svg+xml;utf8,<svg" wp-content/themes/tour-reference-theme/patterns/ wp-content/themes/tour-reference-theme/includes/

# Đọc DOM thật qua trình duyệt, không còn dấu ngoặc kép cong nào trong HTML
```
Chạy script sau bằng `node` (đặt trong `tools/local-audit/`, đã có sẵn `node_modules/playwright`):
```js
import { chromium } from 'playwright';
const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
await page.goto('http://localhost/looptrails/', { waitUntil: 'networkidle' });
const html = await page.content();
console.log('curly-quote corruption count:', (html.match(/&lt;div/g) || []).length);
const box = await page.locator('#destinations').boundingBox();
console.log('destinations section box:', JSON.stringify(box));
await browser.close();
```
**Kết quả mong đợi:** lệnh grep đầu tiên trả về rỗng (0 kết quả). Script node in ra `curly-quote corruption count: 0` và `destinations section box` phải có `"x"` gần 120 (không phải 750) và `"width"` gần 1160-1200 (không phải 570) ở viewport 1440px.

**Sau khi sửa xong F0, kiểm tra lại bằng mắt trên CẢ các trang khác** (không chỉ trang chủ) — đặc biệt trang Thuê xe máy (`http://localhost/looptrails/motorbike-rental/`, dùng `rental-bikes.php`) — chụp lại ảnh và xác nhận bố cục không còn bị lệch/hẹp bất thường ở bất kỳ trang nào.

**Yêu cầu bổ sung của người dùng — ảnh placeholder:** ở những chỗ chưa có ảnh thật (chưa upload media thật cho tour/destination/blog/rental), giữ nguyên cách dùng placeholder (SVG màu + chữ) như hiện tại — đây là chủ đích, đúng quy tắc bản quyền của dự án (không lấy ảnh thật từ looptrails.com khi chưa có ảnh gốc hợp lệ). Chỉ sửa CÁCH ENCODE placeholder đó (theo hướng dẫn trên), không cần thay bằng ảnh thật.

---

## F1 — 6/12 tour thật (bản tiếng Việt) không có giá, trả lỗi khi đặt

**Xác nhận bằng dữ liệu thật:** mỗi tour trong 6 cặp EN/VI demo có 1 bản tiếng Anh (ví dụ tour ID 155 "Northern Highlands Loop") và 1 bản tiếng Việt liên kết qua field `tbc_translation_group` (ví dụ tour ID 173 "Vòng Cung Cao Nguyên Bắc", cùng group với 155). Đây là **có chủ đích từ M3/M4** (tránh trùng lặp dữ liệu vận hành giữa 2 ngôn ngữ theo spec §6) — nhưng hệ quả là **bản tiếng Việt không có `vehicle_option`/`accommodation`/`transfer_option`/`addon` riêng**, chỉ bản tiếng Anh mới có. Việc "resolve giá qua bản anh chị em cùng nhóm" **chưa từng được code** — kể cả sau fix wave 1.

Hậu quả thật: gọi quote cho 6 tour này (173, 197, 221, 245, 269, 293) trả về lỗi `no_pricing_available_for_tour`; trên trang chủ/archive, 6 thẻ tour này hiện "Contact for pricing" thay vì giá thật.

**Cách sửa — File `wp-content/plugins/tour-booking-core/includes/class-pricing-engine.php`:**

Sửa `get_cheapest_vehicle_for_tour( $tour_id )` — nếu tour này không có `vehicle_option` riêng, tự động tra `tbc_translation_group` để tìm tour anh/chị em (khác `tbc_lang`, cùng group) và tra giá từ tour đó thay vì trả `null` ngay:

```php
	public static function get_cheapest_vehicle_for_tour( $tour_id ) {
		$cheapest = self::query_cheapest_vehicle_direct( $tour_id );
		if ( $cheapest ) {
			return $cheapest;
		}

		// This tour has no vehicle_option of its own (e.g. a VI-language demo
		// tour whose pricing lives on its EN sibling, per the M3/M4 dedup rule).
		// Resolve via tbc_translation_group and retry once on the sibling.
		$group = get_post_meta( $tour_id, 'tbc_translation_group', true );
		if ( ! $group ) {
			return null;
		}
		$siblings = get_posts(
			array(
				'post_type'      => 'tour',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $tour_id ),
				'meta_key'       => 'tbc_translation_group',
				'meta_value'     => $group,
			)
		);
		foreach ( $siblings as $sibling ) {
			$sibling_cheapest = self::query_cheapest_vehicle_direct( $sibling->ID );
			if ( $sibling_cheapest ) {
				return $sibling_cheapest;
			}
		}
		return null;
	}

	/**
	 * The original direct-lookup logic, unchanged, extracted into its own method.
	 */
	private static function query_cheapest_vehicle_direct( $tour_id ) {
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

Cũng cập nhật hàm sync cache (`sync_tour_starting_price` hoặc tên tương đương đã thêm ở fix wave 1) — khi 1 `vehicle_option` được lưu cho tour EN, sau khi cache giá lên tour EN đó, **cũng cache CÙNG giá đó lên tour VI anh em** (dùng cùng cơ chế `tbc_translation_group`) để `tbc_price_from_vnd` đúng ở cả 2 bên (phục vụ hiển thị card + bộ lọc giá).

**Tương tự sửa `tour-card.php`** — phần lấy danh sách `$vehicles` theo `tbc_tour_id = $post_id`: nếu rỗng, thử tìm qua `tbc_translation_group` giống logic trên trước khi hiện "Contact for pricing".

**Chạy backfill lại sau khi sửa xong** (giống bước 2.3 vòng 1):
```
C:\xampp\wp-cli.bat eval "
foreach ( get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1, 'post_status' => 'any' ) ) as \$t ) {
    \$cheapest = Tbc_Pricing_Engine::get_cheapest_vehicle_for_tour( \$t->ID );
    if ( \$cheapest ) { update_post_meta( \$t->ID, 'tbc_price_from_vnd', \$cheapest['price_vnd'] ); echo \$t->ID . ': ' . \$cheapest['price_vnd'] . \"\n\"; }
}
"
```

**Kiểm chứng bắt buộc:**
```
# Cả 12 tour phải có giá cache, không tour nào rỗng
C:\xampp\wp-cli.bat eval "
foreach ( get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1 ) ) as \$t ) {
    echo \$t->ID . ' ' . get_the_title(\$t->ID) . ': ' . get_post_meta(\$t->ID, 'tbc_price_from_vnd', true) . \"\n\";
}
"

# Quote cho 1 tour VI trước đây lỗi (ví dụ 293) phải trả 200 với total_usd > 0
curl -s -w "\nHTTP:%{http_code}\n" -X POST http://localhost/looptrails/wp-json/tour-booking/v1/quote -H "Content-Type: application/json" -d "{\"tour_id\":293,\"party_size\":2}"

# Trang chủ không còn "Contact for pricing"
curl -s http://localhost/looptrails/ | grep -c "Contact for pricing"
```
**Kết quả mong đợi:** tất cả 12 tour có `tbc_price_from_vnd` > 0; quote cho tour 293 trả HTTP 200 với `total_usd` > 0; dòng cuối trả về 0.

---

## F2 — Thông tin thật (tên site "looptrails", email cá nhân thật) vẫn lộ ra Schema.org

**Fix wave 1 đã làm ĐÚNG về mặt kiến trúc** (thêm trang cài đặt, code đọc từ `get_option()` thay vì hardcode) — nhưng 2 vấn đề vẫn còn:

1. **`wp option get blogname` vẫn trả về `looptrails`** — tên site WordPress thật chưa từng được đổi, nên `get_bloginfo('name')` (giá trị mặc định của `tbc_site_business_name` khi chưa cấu hình) vẫn in ra đúng tên miền thật của website tham chiếu trong Schema.org.
2. **Giá trị mặc định của `tbc_site_email` là `get_option('admin_email')`** — hiện tại là email cá nhân thật của admin (`nnduyquang@gmail.com`), bị in thẳng ra `<script type="application/ld+json">` công khai trên mọi trang. Đây là rò rỉ thông tin cá nhân thật, nghiêm trọng hơn cả việc hardcode email giả trước đây.

**Cách sửa:**

1. Đổi tên site: `C:\xampp\wp-cli.bat option update blogname "<tên gốc do bạn tự đặt, không giống looptrails>"` — ví dụ tạm "Northbound Trails" hoặc tên khác hoàn toàn không gần giống "Loop Trails"/"looptrails".
2. Sửa `class-seo.php` và `class-admin-page.php`: đổi giá trị mặc định (fallback) của `tbc_site_email` từ `get_option('admin_email')` thành **không có giá trị mặc định là email thật** — nếu `tbc_site_email` chưa được cấu hình, **không xuất field `email` trong JSON-LD** (bỏ hẳn key đó khỏi mảng thay vì in email cá nhân). Tương tự cân nhắc `tbc_site_phone` (số điện thoại giả `+84 123 456 789` hiện tại không phải PII thật nên có thể giữ làm placeholder, nhưng nên đổi thành rõ ràng là placeholder).
3. Vào trang admin `wp-admin/admin.php?page=tour-booking-core`, điền thật các trường Business Name/Email/Phone/Address bằng nội dung gốc (không phải thông tin thật của looptrails.com, không phải email cá nhân thật) — hoặc set qua WP-CLI:
```
C:\xampp\wp-cli.bat option update tbc_site_business_name "<ten goc>"
C:\xampp\wp-cli.bat option update tbc_site_email "<email khong phai ca nhan that, vd contact@example.com hoac domain gia dinh cua site>"
```

**Kiểm chứng bắt buộc:**
```
curl -s http://localhost/looptrails/ | grep -ic "looptrails"
curl -s "http://localhost/looptrails/tours/vong-cung-mao-hiem-ha-giang/" | grep -o '"email":"[^"]*"'
curl -s "http://localhost/looptrails/tours/vong-cung-mao-hiem-ha-giang/" | grep -o '"name":"[^"]*"'
```
**Kết quả mong đợi:** lệnh đầu trả về 0; lệnh 2 không còn `nnduyquang@gmail.com` (hoặc field `email` không xuất hiện nếu chưa cấu hình); lệnh 3 không còn `looptrails`.

---

## F3 — Trang chi tiết tour vẫn hardcode lịch trình/giá giống hệt mọi tour (Important I6 chưa làm)

**Chưa có bất kỳ thay đổi nào** ở `templates/single-tour.html` từ fix wave 1 — vẫn 3 khối `<div class="itinerary-day">` tĩnh giống hệt nhau cho mọi tour, không truy vấn CPT `itinerary_day` thật (field `tbc_tour_id`, `tbc_day_number`, `tbc_included`, `tbc_excluded` — xem `class-meta-fields.php`).

**Cách sửa:** chuyển phần lịch trình trong `single-tour.html` thành gọi 1 pattern PHP mới (theo đúng cách đã sửa C3/C4 ở fix wave 1 — tạo hàm PHP, KHÔNG dùng shortcode qua `wp:pattern`, gọi thẳng bằng `<?php echo ...; ?>` trong 1 file `.php` mới ở `includes/` hoặc `patterns/`), truy vấn:
```php
$days = get_posts( array(
    'post_type'      => 'itinerary_day',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'tbc_tour_id',
    'meta_value'     => get_the_ID(), // hoặc ID tour hiện tại truyền vào
    'meta_query'     => array( array( 'key' => 'tbc_day_number', 'compare' => 'EXISTS' ) ),
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'tbc_day_number', // lưu ý PHP array chỉ giữ 1 meta_key nếu khai 2 lần — dùng orderby=meta_value_num với meta_query đúng cách, kiểm tra kỹ cú pháp WP_Query thật khi viết
) );
```
Nếu tour hiện tại không có `itinerary_day` riêng (tour tiếng Việt — áp dụng đúng logic resolve-qua-`tbc_translation_group` như F1), fallback sang tour anh em. Nếu vẫn không có ngày nào, hiện thông báo "Itinerary details coming soon" thay vì hardcode nội dung giả.

Bảng giá bên cạnh: dùng lại đúng logic đã viết ở `tour-card.php` (fix wave 1 + F1) để lấy các mức giá thật theo `vehicle_option`, không hardcode $140/$208/$290.

**Kiểm chứng bắt buộc:**
```
# Lấy 2 tour khác nhau, nội dung lịch trình phải KHÁC nhau (không còn giống hệt)
curl -s "http://localhost/looptrails/tours/vong-cung-mao-hiem-ha-giang/" | grep -o 'itinerary-day__title">[^<]*' 
curl -s "http://localhost/looptrails/tours/<slug-tour-khac>/" | grep -o 'itinerary-day__title">[^<]*'
```
**Kết quả mong đợi:** 2 danh sách tiêu đề ngày khác nhau giữa 2 tour (trừ khi đúng thật 2 tour đó có cùng lịch trình do cùng nhóm dịch — kiểm tra bằng mắt hợp lý).

---

## F4 — Giao diện trang chủ: hero vẫn không có ảnh nền, nền be vẫn phủ gần hết trang

**Đã re-check bằng ảnh chụp thật sau fix wave 1 — 2 vấn đề trong 3 vấn đề đã nêu ở vòng 1 (mục 3) vẫn CHƯA sửa:**

1. Hero (banner đầu trang) vẫn là **nền đen phẳng, không có ảnh núi nào** — đúng y hệt trước fix wave 1.
2. Nền be/tan (`#e4e0da` hoặc tương tự) **vẫn phủ gần như toàn bộ phần thân trang** (Top Destinations, Why Ride, Testimonials, Brand Narrative, Booking form, Blog) — ảnh tham chiếu chỉ dùng nền trắng cho hầu hết các phần này, màu chỉ xuất hiện ở 1-2 dải (ví dụ dải thống kê tối màu). Kiểm tra `theme.json` và các block group đang gán `has-surface-header-footer-background-color`/màu nền tương tự cho quá nhiều section liên tiếp.

**(Điểm thứ 3 đã nêu ở vòng 1 — lưới "Top Destinations" — có vẻ đã cải thiện thành nhiều cột, không cần sửa thêm trừ khi so ảnh vẫn thấy sai.)**

**Cách làm:**
1. Sửa `patterns/hero-home.php`: thêm ảnh nền phong cảnh núi thật (dùng ảnh có sẵn trong theme nếu có, hoặc tải 1 ảnh từ Wikimedia Commons đúng license — **không lấy ảnh từ looptrails.com**), phủ gradient tối để chữ trắng dễ đọc, giống bố cục ảnh tham chiếu `docs/reference-screenshots/home/desktop.png`.
2. Rà từng pattern (`brand-narrative.php`, `top-destinations-essentials.php`, `why-choose-us.php`, `testimonials.php`, `editorial-cta.php`, `booking-section` pattern, `blog-teaser.php`, `faq-accordion.php`) — chỉ giữ nền màu be/tối cho ĐÚNG 1-2 section giống ảnh tham chiếu (ví dụ dải thống kê tối màu "99.8% / 10,000+ / 4.9★"), phần còn lại đổi về nền trắng/mặc định.
3. Sau mỗi thay đổi, chạy lại:
```
cd tools/local-audit && node capture-local.mjs
```
rồi so `docs/reference-screenshots/local-m5/desktop-full.png` với `docs/reference-screenshots/home/desktop.png` — lặp lại đến khi khớp về tổng thể (ảnh nền hero có, nền trắng chiếm phần lớn thân trang).

---

## Sau khi xong — cập nhật báo cáo

Bổ sung vào `docs/fix-report-2026-08-21.md` (không tạo file mới, thêm section "Vòng 2" vào cuối) — liệt kê F1-F4 đã làm gì, kết quả kiểm chứng thật (dán output lệnh thật). Nếu có mục nào không kịp làm, ghi rõ "CHƯA XONG" thay vì báo đã xong.
