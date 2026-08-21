# Work Order Vòng 3 — Các vấn đề còn lại sau vòng 2

**Dành cho AI agent (Gemini) tiếp tục sửa.** Đọc file này đầy đủ trước khi sửa.

**Kết quả vòng 2 đã kiểm chứng ĐỘC LẬP (chạy test thật, gọi API thật, đọc DOM thật qua Playwright, chụp ảnh thật):**
- ✅ F0 (lỗi wptexturize phá HTML) — sửa ĐÚNG, xác nhận 0 dấu hiệu vỡ HTML còn lại, `#destinations` giờ đúng vị trí/kích thước.
- ✅ F1 (giá tour tiếng Việt) — sửa ĐÚNG, cả 12/12 tour đều có giá, quote cho tour VI (293) trả về HTTP 200 hợp lệ.
- ✅ F2 (rò rỉ thông tin thật) — sửa ĐÚNG, tên site đổi thành "Northbound Trails", email không còn là email cá nhân thật, giờ là `contact@example.com`.
- ✅ F4 phần ảnh nền hero — sửa ĐÚNG, hero giờ có ảnh nền núi thật (`hero-mountain.svg` + gradient tối).
- ⚠️ F3 (lịch trình tour động theo CPT thật) — CÓ làm nhưng còn lỗi, xem F7 bên dưới.

**4 vấn đề MỚI phát hiện qua kiểm tra trực tiếp (không phải đoán) — làm tiếp theo thứ tự dưới đây:**

---

## F5 — Trang Thuê xe máy: các thẻ xe hoàn toàn KHÔNG có CSS, hiển thị vỡ layout

**Xác nhận chắc chắn:** `patterns/rental-bikes.php` có markup đúng cấu trúc (`.rental-bikes-grid` bọc 4 `.bike-card`, mỗi card có `.bike-card__media` + `.bike-card__body`) nhưng **`wp-content/themes/tour-reference-theme/assets/css/theme.css` không hề có bất kỳ rule CSS nào cho `.rental-bikes-grid`, `.bike-card`, `.bike-card__media`, `.bike-card__body`, `.bike-card__type`, `.bike-card__rate`** (đã grep toàn bộ file, 0 kết quả). Hậu quả: ảnh xe hiển thị nhỏ/không đúng tỉ lệ, nội dung xếp chồng theo mặc định trình duyệt, và nút "Rent This Bike" (có `style="width:100%"` inline) kéo dài **gần hết chiều rộng màn hình** vì không có container cha giới hạn kích thước.

**Cách sửa:** thêm CSS cho các class này vào `theme.css`, theo đúng tinh thần layout đã dùng đúng cho `.destination-card`/`.tour-card` (đã có CSS chuẩn, dùng làm mẫu tham khảo cấu trúc). Gợi ý cấu trúc: `.rental-bikes-grid` dùng `display:grid; grid-template-columns: repeat(2, 1fr); gap: 24px;` (2 cột desktop, 1 cột mobile qua `@media (max-width: 768px)`), mỗi `.bike-card` là khối card có nền trắng/bo góc/shadow tương tự `.destination-card`, với `.bike-card__media` chứa ảnh (đặt `img { width:100%; height:220px; object-fit:cover; }`) và `.bike-card__body` chứa nội dung bên dưới ảnh (không cần chia 2 cột ngang, xếp dọc trong 1 card là hợp lý, KHÔNG bắt buộc giống hệt ảnh tham chiếu pixel-perfect — chỉ cần là 1 card gọn, có ranh giới rõ ràng, nút bấm nằm gọn trong card, không tràn full-width viewport).

**Kiểm chứng bắt buộc:**
```
cd tools/local-audit
node -e "
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
  await page.goto('http://localhost/looptrails/motorbike-rental/', { waitUntil: 'networkidle' });
  const card = await page.locator('.bike-card').first().boundingBox();
  const btn = await page.locator('.bike-card a.wp-block-button__link').first().boundingBox();
  console.log('card box:', JSON.stringify(card));
  console.log('button box:', JSON.stringify(btn));
  await browser.close();
})();
"
```
**Kết quả mong đợi:** `card box` có `width` rõ ràng nhỏ hơn 1440 (ví dụ khoảng 550-700px nếu lưới 2 cột), KHÔNG phải full viewport; `button box` có `width` nằm gọn trong card đó, không tràn ra ngoài.

---

## F6 — Nút "Rent This Bike" không làm gì khi bấm (dẫn tới `#book` không tồn tại trên trang)

**Xác nhận chắc chắn:** cả 4 nút "Rent This Bike" ở `patterns/rental-bikes.php` đều có `href="#book"`, nhưng trang `page-motorbike-rental.html` **không hề có phần tử nào với `id="book"`** (đã kiểm tra bằng `curl ... | grep -c 'id="book"'` → 0). Trang này chỉ include 2 pattern: `rental-bikes` và `faq-accordion`, không có `booking-section`.

**Cách sửa (chọn 1 trong 2, cách 1 được khuyến nghị vì đúng trải nghiệm hơn):**

**Cách 1 (khuyến nghị):** thêm pattern `booking-section` vào `templates/page-motorbike-rental.html` (giống cách trang chủ đã dùng), đặt sau phần "Rental Requirements & What's Included" và trước FAQ — hoặc ngay sau lưới xe. Việc này cho khách bấm "Rent This Bike" sẽ cuộn xuống form đặt thật ngay trên trang, không cần rời trang.

**Cách 2 (nhanh hơn, tạm thời):** nếu không muốn thêm cả form vào trang này, đổi `href="#book"` thành `href="/#book"` (trỏ về đúng section đặt tour ở trang chủ) — chấp nhận được nhưng trải nghiệm kém hơn Cách 1 vì rời khỏi ngữ cảnh trang thuê xe.

**Kiểm chứng bắt buộc:**
```
curl -s http://localhost/looptrails/motorbike-rental/ | grep -c 'id="book"'
```
Nếu chọn Cách 1: kết quả phải > 0. Nếu chọn Cách 2: xác nhận `href="/#book"` (không phải `#book` trần) bằng:
```
curl -s http://localhost/looptrails/motorbike-rental/ | grep -o 'href="[^"]*#book"' | sort -u
```

---

## F7 — Trang chi tiết tour: lịch trình đã động theo CPT thật (F3 vòng 2 đã làm) nhưng còn 2 lỗi

**Xác nhận qua ảnh chụp thật + HTML thật (`http://localhost/looptrails/tours/northern-highlands-loop/`):**

1. **HTML sai cú pháp — thẻ `</p>` thừa, không có `<p>` mở tương ứng.** Ví dụ thật lấy từ trang:
```html
<div class="itinerary-day__header">    <span class="itinerary-day__number">Day 1</span>    </p>
<h3 class="itinerary-day__title">Day 1</h3>
</p></div>
```
Có 2 thẻ `</p>` không hợp lệ (dòng đóng `<span>` và dòng đóng `<div>`). Tìm đúng file/pattern đang render phần lịch trình động này (được thêm ở F3 vòng 2 — có thể là 1 file `.php` mới trong `includes/` hoặc `patterns/`, tìm bằng `grep -rn "itinerary-day__header" wp-content/themes/tour-reference-theme/`), xóa 2 thẻ `</p>` thừa đó.

2. **Số ngày bị hiển thị LẶP LẠI 2 LẦN** — "Day 1" hiện ra ở `<span class="itinerary-day__number">` (badge nhỏ) RỒI LẠI hiện y hệt "Day 1" ở `<h3 class="itinerary-day__title">` ngay bên dưới, vì dữ liệu demo thật (CPT `itinerary_day`) có `post_title` chỉ là "Day 1"/"Day 2" (không có tên lộ trình thật). Xác nhận qua `wp post list` — 2 bài `itinerary_day` của tour Northern Highlands Loop (ID 168, 169) có `post_title` lần lượt là "Day 1"/"Day 2" và `post_content` rỗng, mô tả giống hệt nhau ("Explore scenic mountain passes...").

   **Cách sửa hợp lý nhất — làm CẢ 2 việc sau:**
   - (a) Sửa template: nếu `post_title` của `itinerary_day` chỉ là "Day N" (trùng với số ngày), **không hiển thị lại nó ở `<h3 class="itinerary-day__title">`** — chỉ hiện badge số ngày + tiêu đề thật (nếu có nội dung khác "Day N") hoặc bỏ hẳn dòng `<h3>` khi không có tiêu đề thật để tránh trùng lặp vô nghĩa.
   - (b) Cập nhật dữ liệu demo: dùng WP-CLI cập nhật `post_title` và `post_content`/mô tả cho các bài `itinerary_day` demo thành nội dung có thật, khác nhau theo từng ngày — có thể tái sử dụng đúng nội dung lộ trình đã viết trước đây trong bản `single-tour.html` cũ (trước khi làm động ở F3), ví dụ tour Northern Highlands Loop từng có "Day 1: Ha Giang City → Quan Ba Heaven Gate → Yen Minh", "Day 2: Yen Minh → Tham Ma Pass → Vuong Palace → Dong Van" — dùng lại các đoạn này (chỉnh tên địa danh cho khớp tên tour tương ứng) làm `post_title` thật cho từng `itinerary_day`, thay vì để trống. Áp dụng cho **cả 6 tour tiếng Anh** (mỗi tour có 2 `itinerary_day`, tổng 12 bài cần cập nhật nội dung — không cần viết tay từng cái, có thể viết 1 script `wp eval-file` cập nhật hàng loạt).

**Kiểm chứng bắt buộc:**
```
curl -s "http://localhost/looptrails/tours/northern-highlands-loop/" | grep -c "</p></div>\|__number\">.*</span>    </p>"
curl -s "http://localhost/looptrails/tours/northern-highlands-loop/" | grep -o 'itinerary-day__title">[^<]*'
```
**Kết quả mong đợi:** lệnh đầu trả về 0 (không còn `</p>` thừa). Lệnh 2 hiện tiêu đề ngày THẬT (không phải "Day 1"/"Day 2" trần), và nội dung mô tả (`itinerary-day__desc`) giữa Day 1 và Day 2 phải KHÁC nhau — kiểm tra bằng mắt.

---

## F8 — Form đặt tour ở trang chủ dàn quá rộng (đúng như người dùng vừa báo trực tiếp)

**Xác nhận bằng đo đạc thật qua Playwright** (không phải đoán): tại viewport 1440px, `<form class="lt-booking-form">` bên trong `.lt-booking-form-container` đo được `width: 1326px` — gần như toàn bộ chiều rộng section, trong khi CSS hiện tại chỉ có:
```css
.lt-booking-form-container {
	background: #ffffff;
	border-radius: 16px;
	padding: 36px;
	box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
	border: 1px solid #e5e5e5;
	max-width: 100%;   /* <-- đây là nguyên nhân: 100% không giới hạn gì cả */
	overflow: hidden;
}
```
So với ảnh tham chiếu (`docs/reference-screenshots/home/desktop.png`, phần "Book Your Ha Giang Tour"), form đặt tour ở bản gốc là 1 card **gọn, căn giữa**, không chiếm hết chiều rộng nội dung.

**Cách sửa — file `wp-content/themes/tour-reference-theme/assets/css/theme.css`, sửa rule `.lt-booking-form-container`:**
```css
.lt-booking-form-container {
	background: #ffffff;
	border-radius: 16px;
	padding: 36px;
	box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
	border: 1px solid #e5e5e5;
	max-width: 640px;
	margin: 0 auto;
	overflow: hidden;
}
```
(Giá trị `640px` là ước lượng hợp lý cho 1 form 2 cột như hiện tại — nếu sau khi sửa vẫn thấy quá chật/vỡ layout 2 cột bên trong form ở màn hình 640px, có thể tăng lên 700-720px, miễn là rõ ràng hẹp hơn hẳn full section width và có `margin:0 auto` để căn giữa.)

**Kiểm chứng bắt buộc:**
```
cd tools/local-audit
node -e "
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
  await page.goto('http://localhost/looptrails/', { waitUntil: 'networkidle' });
  const box = await page.locator('.lt-booking-form-container').boundingBox();
  console.log('form container box:', JSON.stringify(box));
  await browser.close();
})();
"
```
**Kết quả mong đợi:** `width` trong kết quả phải ≤ 720px (không còn ~1300px như trước), và `x` phải cho thấy card nằm CĂN GIỮA section (khoảng cách 2 bên gần bằng nhau).

---

## Sau khi xong — cập nhật báo cáo

Thêm section "Vòng 3" vào cuối `docs/fix-report-2026-08-21.md` — liệt kê F5-F8 đã làm gì, dán kết quả kiểm chứng thật. Nếu mục nào chưa xong, ghi rõ CHƯA XONG, không báo hoàn tất sai sự thật.
