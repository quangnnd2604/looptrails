# Work Order Vòng 6 — 3 việc: nâng cấp trang chi tiết tour, sửa nghiệp vụ thuê xe máy, cho phép chỉnh sửa mọi trang trong wp-admin

**Dành cho AI agent (Gemini) thực hiện.** Đọc file này đầy đủ trước khi sửa bất kỳ dòng nào. File dài vì có 3 phần độc lập (A, B, C) — làm lần lượt, kiểm chứng xong phần nào mới sang phần đó, không trộn lẫn.

---

# PHẦN A — Trang chi tiết tour còn thô sơ, cần thêm placeholder + thiết kế lại cột phải

## A.0 Căn cứ — đã đo đạc thật từ site tham chiếu, không phải đoán

Toàn bộ số liệu dưới đây lấy từ `docs/reference-audit/02-tour-detail.md` (đã đo bằng Playwright thật từ site gốc ở Milestone 2, không phải suy đoán). Đọc file đó nếu cần thêm chi tiết trước khi code.

**Cấu trúc cột phải (Booking Widget) ở bản gốc — hiện tại theme của mình chỉ có 1 danh sách giá dạng text phẳng (`[tour_single_pricing]` trong `includes/tour-card.php` hàm `tour_theme_render_single_pricing()`), thiếu hẳn:**
1. Các mức giá phải hiện dạng **thẻ chọn được** (card, có thể click chọn, thẻ đang chọn có viền/nền nổi bật), không phải text phẳng.
2. **Bộ đếm số người** (Travelers stepper): nút `−` / số người / nút `+`.
3. Nút CTA chính đúng theo số đo thật: **cao 59px, bo góc 7px, nền `#ff6602`, đổ bóng lệch cứng `rgb(54,52,59) 2px 3px 0px 0px`, hover nền `#e4e0da`**.
4. 1 dòng chữ nhỏ (12px) ghi chú tin cậy (ví dụ "Free cancellation · Instant confirmation · No hidden fees") ngay dưới nút CTA.
5. **Thanh điều hướng nhanh trong trang** (sticky sub-nav) với các link nhảy tới từng section: Overview / Itinerary / What's Included / FAQs (bản gốc có thêm Route Map/Reviews — bỏ qua 2 mục này vì site mình chưa có 2 section đó).

**Hero:** bản gốc có ảnh nền chụp núi/xe máy full-bleed. Hero hiện tại của mình (`tour-detail-hero` trong `templates/single-tour.html`) chỉ có màu nền cam phẳng (`backgroundColor:"primary"`), không có ảnh — đây là điểm "thô sơ" rõ nhất so với bản gốc.

**Mỗi ngày lịch trình:** bản gốc có 1 hàng 3 ảnh nhỏ (213×160px, bo góc 10px) cho mỗi ngày. `itinerary_day` CPT hiện KHÔNG có field ảnh nào — thêm ảnh placeholder (không phải ảnh thật, không lấy từ trang gốc) cho mỗi ngày để bố cục đỡ trống, không cần build hẳn 1 hệ thống upload ảnh mới cho việc này (quá lớn so với yêu cầu "thêm placeholder cho giống").

## A.1 Việc cần làm

1. **Hero có ảnh nền:** sửa khối `tour-detail-hero` trong `templates/single-tour.html` — thêm `background-image` (dùng lại kiểu đã làm đúng cho hero trang chủ ở Vòng 2/F4: `linear-gradient(...), url(...)`, dùng ảnh có sẵn trong theme hoặc 1 ảnh Wikimedia Commons hợp lệ theo đúng quy tắc bản quyền dự án — **không lấy ảnh từ looptrails.com**).

2. **Cột phải — viết lại `tour_theme_render_single_pricing()`** trong `includes/tour-card.php`:
   - Mỗi mức giá render thành `<label class="booking-price-tier">` bọc 1 `<input type="radio" name="vehicle_choice" value="{vehicle_option ID}">` ẩn + nội dung hiển thị — để chọn được bằng click, thẻ đang chọn có class `is-selected` (dùng CSS `:has()` nếu hỗ trợ, hoặc JS đơn giản gắn class khi radio thay đổi — Gemini tự chọn cách chắc ăn nhất, JS thuần không cần thư viện).
   - Thêm 1 khối travelers stepper phía trên hoặc dưới danh sách giá: nút `−`, số hiển thị (mặc định 1, min 1, max 8 theo đúng "Small Group Max 8" đã ghi trên hero), nút `+`. Giá trị này lưu vào 1 `<input type="hidden" name="party_size">` để dùng khi bấm CTA.
   - Style nút "Instant Booking" đúng số đo ở mục A.0 (thêm CSS class mới hoặc style trực tiếp trong `theme.css`, đừng lẫn với style nút "Book Now" khác đang dùng nếu số đo khác).
   - Thêm dòng chữ tin cậy nhỏ dưới nút (có thể giữ nguyên "🔒 Free cancellation up to 48h before departure" đã có, chỉ cần đúng vị trí/kiểu chữ).

3. **Thêm sticky sub-nav:** 1 thanh ngang nhỏ ngay dưới hero, các link `<a href="#overview">Overview</a> <a href="#itinerary">Itinerary</a> <a href="#included">What's Included</a> <a href="#faq">FAQs</a>` (thêm `id` tương ứng vào các section đã có trong `single-tour.html`: `id="overview"` cho khối Tour Overview, `id="itinerary"` cho `.tour-itinerary-section`, `id="included"` cho `.tour-inclusions-box`; phần FAQ thì kiểm tra xem trang tour hiện có section FAQ chưa — nếu chưa có, thêm 1 khối FAQ đơn giản dùng lại pattern `faq-accordion` đã có sẵn, hoặc bỏ link "FAQs" khỏi sub-nav nếu không muốn thêm phần mới). CSS: `position: sticky; top: [chiều cao header];` để thanh này dính khi cuộn, giống hành vi "sticky secondary nav" đã đo ở bản gốc.

4. **Ảnh placeholder cho mỗi ngày lịch trình:** sửa `tour_theme_render_single_itinerary()` — thêm 1 hàng ảnh placeholder (3 ảnh SVG màu + chữ, style giống các placeholder khác đã có trong theme — **bắt buộc dùng `rawurlencode()` để encode, đúng bài học Vòng 2/F0, tuyệt đối không viết SVG thô có dấu ngoặc kép trực tiếp trong chuỗi PHP nối vào HTML nếu đoạn đó chạy qua `do_blocks()`** — nhưng vì hàm này chạy qua shortcode nhúng trực tiếp trong `single-tour.html`, không qua `wp:pattern`, nên rủi ro thấp hơn C3 cũ; vẫn nên dùng `rawurlencode()` cho an toàn và nhất quán) ngay dưới mỗi `.itinerary-day__desc`.

5. **Related Tours "You May Also Like":** đã có sẵn (`[tour_featured_grid postsPerPage="3"]`) — không cần sửa, chỉ xác nhận vẫn hiển thị đúng sau khi các thay đổi trên xong.

## A.2 Kiểm chứng bắt buộc

```
curl -s "http://localhost/looptrails/tours/northern-highlands-loop/" | grep -c "background-image"
curl -s "http://localhost/looptrails/tours/northern-highlands-loop/" | grep -c "svg+xml;utf8,<svg"
```
Lệnh 1 phải > 0 (hero có ảnh nền). Lệnh 2 phải = 0 (không dùng cú pháp SVG thô dễ vỡ như C3 cũ).

Chạy lại capture ảnh + so sánh bằng mắt:
```
cd tools/local-audit
node -e "
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
  await page.goto('http://localhost/looptrails/tours/northern-highlands-loop/', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tour-detail-round6.png', fullPage: true });
  await browser.close();
})();
"
```
Mở `tour-detail-round6.png` so với `docs/reference-screenshots/tours-ha-giang-loop-4-days-3-nights/desktop.png` — xác nhận cột phải giờ có thẻ giá chọn được + travelers stepper + nút CTA đúng kiểu, không còn là danh sách text phẳng.

Chạy PHPUnit theme — 100% pass, cập nhật test nếu có test nào so chuỗi cứng bị ảnh hưởng bởi thay đổi markup.

---

# PHẦN B — Nghiệp vụ đặt thuê xe máy sai: nút "Rent This Bike" đẩy tới form đặt TOUR

## B.0 Căn cứ — đã nghiên cứu đúng nghiệp vụ site gốc

Từ `docs/reference-audit/03-secondary-pages.md`, mục "Motorbike Rental" (đo thật từ site gốc, không suy đoán): site gốc có **1 form đặt thuê xe RIÊNG BIỆT**, khác hẳn form đặt tour, với các trường: *bike picker (chọn xe), rental dates/days/count (ngày bắt đầu thuê + số ngày), personal info (tên/email/phone), cost summary (tổng tiền), payment type/method, terms, nút "Book Now & Pay"*. Form này **không có** trường chọn lịch trình tour ("Select Your Tour Itinerary") — vì thuê xe không liên quan gì tới việc chọn tour.

**Xác nhận hiện trạng sai:** hiện tại nút "Rent This Bike" ở `patterns/rental-bikes.php` trỏ tới `href="#book"`, và `id="book"` (thêm ở Vòng 3/F6) chính là section `booking-section` — **đúng là form đặt TOUR** (`patterns/booking-section.php`, có trường "Select Your Tour Itinerary"), không phải form thuê xe. Đây là lỗi nghiệp vụ thật, không phải lỗi hiển thị.

**Xác nhận hiện trạng backend:** `Tbc_Pricing_Engine::calculate_quote()` (đã sửa ở Vòng 2/C1) **bắt buộc phải có `tour_id`** — nếu không có, trả lỗi `tour_id_required` ngay. Nghĩa là API hiện tại **không hỗ trợ được** 1 lượt đặt "chỉ thuê xe, không đi tour" — đây là lỗ hổng thật cần vá ở backend, không chỉ là vấn đề giao diện.

## B.1 Việc cần làm

### B.1.1 Backend — cho phép quote/book KHÔNG cần `tour_id` khi có `rental_bike` + `rental_days`

Sửa `wp-content/plugins/tour-booking-core/includes/class-pricing-engine.php`, hàm `calculate_quote()` — tìm đoạn hiện tại kiểu:
```php
} else {
    return array( 'error' => 'tour_id_required' );
}
```
(đoạn else khi không có `$tour_id` và không có `$vehicle_id`) — sửa thành: chỉ trả lỗi `tour_id_required` nếu **KHÔNG có `tour_id` VÀ KHÔNG có (`rental_bike` hợp lệ trong `RENTAL_BIKES` VÀ `rental_days` > 0)`**. Nếu là đơn thuê-xe-thuần (không tour), bỏ qua hẳn phần tính `tour_unit_price`/`tour_subtotal` (set về 0), chỉ tính `rental_subtotal` (logic tính rental đã có sẵn từ Vòng 1/C1, giữ nguyên) + `transfer_subtotal` nếu có. Đảm bảo response trả về `success` bình thường, không lỗi, khi request có dạng `{"rental_bike":"wave_alpha","rental_days":3,"party_size":1}` (không có `tour_id`).

Sửa tương tự trong `class-booking-handler.php`, hàm `handle_book()` — hiện dòng `if ( empty( $customer_name ) || empty( $customer_email ) )` là điều kiện bắt buộc duy nhất, việc còn lại tự động đi qua `calculate_quote()` nên không cần sửa gì thêm ở đây NGOÀI việc kiểm tra: khi tạo `booking` post cho đơn thuê-xe-thuần, đặt `tbc_tour_id = 0` hợp lý (không gán tour giả), và tiêu đề booking nên phản ánh đúng là đơn thuê xe (ví dụ đổi `$tour_title` fallback từ `'Custom Northern Loop Tour'` thành logic: nếu có `rental_bike` mà không có `tour_id`, dùng tên xe làm tiêu đề thay vì tên tour giả).

### B.1.2 Frontend — thêm 1 form đặt thuê xe riêng trên trang Motorbike Rental

Thêm 1 pattern mới `patterns/rental-booking-form.php` (đặt tên khác `booking-section` để không nhầm với form đặt tour), với các trường đúng tinh thần đo được ở site gốc (mục B.0):
- Chọn xe (dropdown `<select name="rental_bike">`, giá trị khớp đúng 4 key trong `Tbc_Pricing_Engine::RENTAL_BIKES` đã có từ Vòng 1: `wave_alpha`, `blade_fi`, `xr150l`, `cb500x`).
- Ngày bắt đầu thuê (`<input type="date" name="start_date">`).
- Số ngày thuê (`<input type="number" name="rental_days" min="1">`).
- Họ tên / Email / Số điện thoại (giống các trường đã có ở form đặt tour, tái dùng đúng tên field `customer_name`/`customer_email`/`customer_phone` để không phải sửa gì ở backend).
- 1 khối tóm tắt chi phí (JS tính `rental_days × giá/ngày theo xe đã chọn`, hiển thị realtime khi đổi lựa chọn — có thể gọi thẳng `/wp-json/tour-booking/v1/quote` qua `fetch()` để lấy số chính xác từ server thay vì tính tay ở JS, tránh sai lệch giữa hiển thị và số thật server tính — cách này AN TOÀN HƠN, khuyến nghị dùng).
- Nút submit gọi `/wp-json/tour-booking/v1/book` (dùng đúng logic JS đã có cho form đặt tour làm mẫu — tìm file JS xử lý submit form đặt tour hiện tại, ví dụ trong `assets/js/` hoặc inline script trong `booking-section.php`, sao chép logic gọi API, chỉ đổi field nào gửi lên).

Thêm pattern này vào `templates/page-motorbike-rental.html`, đặt `id="rental-book"` cho section này (KHÔNG dùng lại `id="book"` để tránh nhầm với form đặt tour nếu sau này 2 form cùng tồn tại trên 1 trang).

### B.1.3 Nối nút "Rent This Bike" với form mới

Sửa `patterns/rental-bikes.php` — đổi `href="#book"` thành `href="#rental-book"` cho cả 4 nút, VÀ thêm `data-bike="wave_alpha"` (hoặc đúng key tương ứng mỗi xe) vào từng nút. Thêm đoạn JS nhỏ: khi click 1 nút "Rent This Bike", set giá trị `<select name="rental_bike">` trong form ở B.1.2 thành đúng xe vừa bấm, rồi mới cuộn xuống (dùng `href="#rental-book"` mặc định của trình duyệt là đủ để cuộn, chỉ cần JS set giá trị select trước khi cuộn).

## B.2 Kiểm chứng bắt buộc

```
# Backend: quote thuê-xe-thuần (không tour_id) phải thành công
curl -s -w "\nHTTP:%{http_code}\n" -X POST http://localhost/looptrails/wp-json/tour-booking/v1/quote -H "Content-Type: application/json" -d '{"rental_bike":"wave_alpha","rental_days":3,"party_size":1}'
```
**Kết quả mong đợi:** HTTP 200, `success:true`, `rental_subtotal` = 3 × $10 = 30, `tour_subtotal` = 0.

```
curl -s http://localhost/looptrails/motorbike-rental/ | grep -c 'id="rental-book"'
curl -s http://localhost/looptrails/motorbike-rental/ | grep -o 'href="#rental-book"' | wc -l
```
Lệnh 1 phải ≥ 1, lệnh 2 phải = 4 (đúng 4 nút "Rent This Bike").

Thử thao tác thật trên trình duyệt: mở trang thuê xe, bấm "Rent This Bike" ở 1 xe bất kỳ, xác nhận cuộn xuống đúng form thuê xe (không phải form có "Select Your Tour Itinerary"), xe đã chọn đúng sẵn trong dropdown, điền thử số ngày + thông tin, bấm submit, xác nhận nhận được phản hồi thành công (không lỗi 400).

Chạy PHPUnit cả 2 package — 100% pass. Thêm test PHPUnit mới cho `calculate_quote()` với input chỉ có `rental_bike`+`rental_days` (không `tour_id`) — assert `success`/không lỗi, dùng dữ liệu thật đi qua hàm, không mock.

---

# PHẦN C — Cho phép chỉnh sửa mọi phần của website trong wp-admin (kiểu Flatsome, không cần page builder ngoài)

## C.0 Chẩn đoán — đã xác minh chắc chắn, không phải đoán

**Đã kiểm tra trực tiếp database:** các trang `About` (ID 300), `Contact` (ID 301), `Motorbike Rental` (ID 302) đều có `post_content` **RỖNG HOÀN TOÀN** (`content_length=0`). Toàn bộ nội dung hiển thị thật trên các trang này đến từ các file pattern `.php` trong code (`patterns/*.php`) — được gọi qua `<!-- wp:pattern {"slug":"..."} /-->` trong file template `.html`. Đây chính là lý do khi bạn bấm "Sửa" trang About/Contact trong wp-admin, khung soạn thảo hiện trống trơn — vì đúng là **không có nội dung nào lưu trong bài viết đó cả**, nội dung nằm cứng trong code.

**Trang chủ còn tệ hơn:** không hề có 1 bài `Page` nào đại diện cho trang chủ trong database (`Cài đặt > Đọc` hiện `show_on_front=posts`, `page_on_front=0` — chưa từng được set). Toàn bộ nội dung trang chủ đến từ `templates/front-page.html` (WordPress tự động ưu tiên file này làm trang chủ bất kể cấu hình Đọc) — nghĩa là **không có nơi nào trong wp-admin để bấm vào sửa trang chủ cả**, kể cả khung trống.

**Vì sao dùng Site Editor (Appearance > Editor) cũng không sửa được các pattern này:** các pattern trong `patterns/*.php` được đăng ký bằng code (file PHP), Site Editor coi đây là **"Chưa được đồng bộ" (unsynced, code-owned)** — có thể xem/chèn thêm vào 1 trang khác, nhưng KHÔNG có nút "Lưu" nào ghi ngược thay đổi vào file `.php` gốc. Đây là giới hạn thật của cơ chế theo đúng thiết kế của WordPress, không phải lỗi.

**Hướng sửa đúng — không cần cài Elementor/page builder nào khác (dự án đã có quyết định KHÔNG dùng page builder ngoài từ đầu, giữ nguyên quyết định đó):** WordPress bản thân đã có sẵn khả năng "kéo thả, click để sửa chữ/ảnh trực tiếp" y hệt Flatsome UX Builder — đó chính là **Trình soạn thảo khối (Block Editor)** khi bài viết có NỘI DUNG THẬT (block thật) trong `post_content`, thay vì rỗng. Cách làm: **chuyển nội dung từ các file pattern `.php` (code-owned) thành nội dung THẬT lưu trong `post_content` của từng trang (admin-owned)** — khi đó mở "Sửa" trang About/Contact/Trang chủ sẽ thấy đúng các block thật (tiêu đề, đoạn văn, ảnh, nút...), kéo thả sắp xếp lại được, click vào sửa chữ trực tiếp được, y như Flatsome — nhưng hoàn toàn bằng công cụ WordPress gốc, không cài thêm gì.

## C.1 Việc cần làm

**Nguyên tắc quan trọng:** việc này CHỈ áp dụng cho các trang **tĩnh, ít lặp lại** (Trang chủ, About, Contact, Motorbike Rental) — **KHÔNG áp dụng cho `single-tour.html`/`archive-tour.html`** (các trang này hiển thị dữ liệu ĐỘNG theo từng tour, không phải nội dung tĩnh — phần dữ liệu động của Tour đã có cách sửa riêng, đúng đắn, từ Vòng 4/5 (meta box + Classic Editor) — **không đụng vào phần đó**).

### C.1.1 Trang chủ — tạo 1 Page thật, đặt làm trang chủ

1. Tạo 1 Page mới: `wp_insert_post(['post_type'=>'page', 'post_title'=>'Trang chủ', 'post_name'=>'home', 'post_status'=>'publish'])`.
2. **Nội dung của Page này = đúng nội dung thật hiện đang render** trên trang chủ — lấy bằng cách: với mỗi pattern hiện đang được `front-page.html` tham chiếu qua `wp:pattern` (`hero-home`, `featured-tours`, `brand-narrative`, `top-destinations-essentials`, `why-choose-us`, `testimonials`, `editorial-cta`, `booking-section`, `blog-teaser`, `faq-accordion`), lấy **nội dung block THẬT đã render của pattern đó** (gọi `WP_Block_Patterns_Registry::get_instance()->get_registered('tour-reference-theme/<slug>')['content']` cho từng pattern, hoặc đơn giản hơn: mở từng file `patterns/*.php`, copy đúng phần markup block bên trong — bỏ qua các đoạn PHP thuần tuý như vòng lặp/shortcode động không convert được thành block tĩnh, xem lưu ý bên dưới) — nối các đoạn này lại làm `post_content` của Page "Trang chủ".
3. Vào **Cài đặt > Đọc**, đặt "Trang chủ hiển thị" = "Một trang tĩnh", "Trang chủ" = Page "Trang chủ" vừa tạo.
4. **Rút gọn `templates/front-page.html` và `templates/home.html`** xuống còn cấu trúc tối thiểu (giống `templates/page.html` đã có sẵn dạng tối giản): header + `<!-- wp:post-content /-->` + footer — để nội dung hiển thị đúng là nội dung THẬT của Page (không còn hardcode pattern trong template nữa).

**Lưu ý về phần KHÔNG THỂ chuyển thành block tĩnh đơn giản** — 2 pattern sau có logic PHP động thật sự (không phải chỉ markup tĩnh), xử lý riêng:
- `featured-tours.php`: gọi hàm PHP `tour_theme_render_featured_tours()` (đã sửa ở Vòng 2/C3) — **giữ nguyên cách gọi PHP này**, không cố convert thành block tĩnh. Cách làm: dùng 1 **Shortcode block thật** (`<!-- wp:shortcode -->[tour_featured_grid]<!-- /wp:shortcode -->`) đặt TRỰC TIẾP trong `post_content` của Page — **đã xác nhận qua Vòng 2/C3 rằng shortcode nhúng trực tiếp (không qua `wp:pattern`) chạy đúng, không bị lỗi wptexturize** — nên đặt thẳng trong `post_content` (không qua `wp:pattern`) là AN TOÀN.
- `booking-section.php`: có form + JS tương tác thật — copy nguyên khối markup (kể cả script nếu có) vào `post_content` là được, không có phần PHP-loop động nào ở đây cần giữ riêng (kiểm tra lại file thật để chắc chắn trước khi copy).

### C.1.2 About / Contact / Motorbike Rental — chuyển nội dung pattern thật vào `post_content` của từng Page

Áp dụng đúng nguyên tắc như C.1.1 bước 2, cho từng trang:
- Page "About" (ID 300) ← nội dung từ template `page-about.html` (kiểm tra file này hiện đang tham chiếu pattern nào hoặc có markup trực tiếp, copy đúng nội dung thật).
- Page "Contact" (ID 301) ← nội dung từ `page-contact.html`.
- Page "Motorbike Rental" (ID 302) ← nội dung từ `page-motorbike-rental.html`, **bao gồm cả phần form thuê xe mới ở Phần B** — form đặt thuê xe (B.1.2) nên đặt là 1 **Custom HTML block** (`<!-- wp:html -->...<!-- /wp:html -->`) trong `post_content` (vì có form + JS, không nên cố tách thành block chuẩn) thay vì để trong file pattern code-owned như đã làm ở Phần B — **cập nhật lại hướng dẫn Phần B**: viết `patterns/rental-booking-form.php` như đã mô tả, nhưng sau khi xong, LẤY nội dung render ra và đặt vào `post_content` của Page Motorbike Rental dưới dạng `wp:html`, thay vì giữ nó là 1 pattern riêng được tham chiếu qua `wp:pattern` trong template — để admin cũng sửa được phần này nếu cần (ví dụ đổi text nhãn trường).
- Sau khi copy xong, **rút gọn `page-about.html`, `page-contact.html`, `page-motorbike-rental.html`** xuống cấu trúc tối thiểu giống `page.html` (header + `wp:post-content` + footer) — **những file `page-{slug}.html` này thật ra có thể XOÁ HẲN sau khi rút gọn**, vì `page.html` (template mặc định) đã đủ để hiển thị `post_content` của bất kỳ Page nào — giữ lại nếu muốn có style riêng theo từng trang, xoá nếu muốn đơn giản hoá (Gemini tự quyết định, không bắt buộc xoá).

### C.1.3 Cập nhật test đã bị ảnh hưởng

`tests/test-home-template.php` hiện `assertStringContainsString` các slug pattern trực tiếp trên **nội dung file** `front-page.html`/`home.html` — sau khi rút gọn 2 file này, các assertion đó sẽ sai (đúng theo thiết kế mới, không phải lỗi). **Sửa lại test này để kiểm tra đúng ĐIỀU THẬT SỰ QUAN TRỌNG**: dựng 1 request thật tới trang chủ (`http://localhost/looptrails/` qua HTTP thật trong test, hoặc dùng `WP_UnitTestCase` cách chuẩn dựng `$this->go_to('/')` rồi `get_echo('the_content')`/kiểm tra `$post->post_content` của Page trang chủ), assert nội dung RENDER RA THẬT có chứa các phần tử mong đợi (ví dụ class `hero-home-section`, `tour-card`, v.v.) — **đây chính là bài học I9 đã rút ra ở các vòng trước: test string-match trên file nguồn chưa thực thi không phát hiện được lỗi thật, phải test trên nội dung đã render**. Áp dụng tương tự cho bất kỳ test nào khác đang so chuỗi cứng trên `page-about.html`/`page-contact.html`/`page-motorbike-rental.html`.

## C.2 Kiểm chứng bắt buộc

```
C:\xampp\wp-cli.bat option get show_on_front
C:\xampp\wp-cli.bat option get page_on_front
C:\xampp\wp-cli.bat post list --post_type=page --fields=ID,post_title,post_name --format=table
```
Xác nhận `show_on_front=page`, `page_on_front` là ID thật của Page "Trang chủ" vừa tạo, và danh sách Page có đủ Trang chủ/About/Contact/Motorbike Rental.

```
curl -s -o /dev/null -w "home: %{http_code}\n" http://localhost/looptrails/
curl -s http://localhost/looptrails/ | grep -c "tour-card lt-tour-card"
curl -s -o /dev/null -w "about: %{http_code}\n" http://localhost/looptrails/about/
curl -s -o /dev/null -w "contact: %{http_code}\n" http://localhost/looptrails/contact/
curl -s -o /dev/null -w "rental: %{http_code}\n" http://localhost/looptrails/motorbike-rental/
```
Tất cả phải 200, trang chủ vẫn có tour card thật (không bị mất nội dung sau khi chuyển đổi).

**Thao tác thật bắt buộc trên wp-admin** (đây là mục đích chính của cả Phần C, không được bỏ qua):
1. Vào `Trang > Tất cả trang`, bấm sửa "About" — xác nhận khung soạn thảo giờ có NỘI DUNG THẬT (không còn trống), thử sửa 1 đoạn chữ bất kỳ, Cập nhật, xác nhận thay đổi hiện đúng ở `http://localhost/looptrails/about/`.
2. Làm tương tự với "Trang chủ" — thử kéo 1 block xuống dưới 1 block khác (đổi thứ tự), Cập nhật, xác nhận thứ tự thay đổi hiện đúng ở trang chủ thật.
3. Chụp ảnh màn hình cả 2 thao tác trên làm bằng chứng.

Chạy PHPUnit cả 2 package — 100% pass (bao gồm test đã sửa ở C.1.3).

---

## Báo cáo lại

Thêm section "Vòng 6" vào `docs/fix-report-2026-08-21.md`, chia rõ 3 phần A/B/C, dán kết quả kiểm chứng thật của từng phần. Phần C đặc biệt quan trọng — phải có ảnh chụp màn hình thao tác sửa nội dung thật trong wp-admin làm bằng chứng, không chỉ mô tả bằng lời. Nếu phần nào chưa xong hết, ghi rõ CHƯA XONG, không báo hoàn tất sai sự thật.
