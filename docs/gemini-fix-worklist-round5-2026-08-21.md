# Work Order Vòng 5 — Sửa 1 vấn đề: Meta box quản lý Tour bị ẩn khuất

**Dành cho AI agent (Gemini) thực hiện.** File ngắn, chỉ 1 vấn đề, nhưng đọc kỹ phần chẩn đoán trước khi sửa.

---

## Đã kiểm tra kỹ — KHÔNG PHẢI lỗi code, mà là sai kiểu trình soạn thảo

**Xác nhận bằng cách đăng nhập thật vào wp-admin và render trang bằng trình duyệt thật (Playwright), không phải đoán:** Toàn bộ 6 meta box của Vòng 4 (`tbc_itinerary_metabox`, `tbc_vehicles_metabox`, v.v.) **hoạt động hoàn toàn đúng** — có đủ ô nhập liệu, hiển thị đúng dữ liệu thật đã lưu (đã tự kiểm tra: tour "Northern Highlands Loop" hiện đúng 2 dòng lịch trình với tên lộ trình thật, 2 dòng phương tiện Motorbike/Jeep với giá đúng, v.v.).

**Vấn đề thật:** Post type `tour` hiện dùng **trình soạn thảo khối (Block Editor / Gutenberg)**. WordPress có quy tắc: mọi meta box kiểu cũ (`add_meta_box()`, không phải block) khi ở trong Block Editor sẽ bị đẩy xuống **1 khung "Hộp Meta" cuộn xuống tận cuối trang, mặc định ĐÓNG LẠI** (accordion thu gọn) — không hiện ngay dưới khung soạn nội dung như cách người dùng quen thuộc với WordPress cổ điển mong đợi. Đây chính là lý do người dùng (chủ site) mở trang "Thêm Tour mới" chỉ thấy khung tiêu đề trống, không thấy ô nhập lịch trình/phương tiện nào — toàn bộ 6 meta box đang nằm ẩn bên dưới, phải cuộn hết trang và bấm mở "Hộp Meta" mới thấy.

## Cách sửa — chuyển post type `tour` sang dùng Trình soạn thảo Cổ điển (Classic Editor)

Đây là cách chuẩn, đáng tin cậy nhất cho 1 CPT chủ yếu dùng để **nhập dữ liệu có cấu trúc** (không phải viết nội dung dài dạng blog) — khi dùng Classic Editor, mọi meta box hiện NGAY BÊN DƯỚI khung mô tả tour, không bị giấu đi.

**File cần sửa:** `wp-content/plugins/tour-booking-core/includes/class-post-types.php` (hoặc thêm 1 hook riêng trong `class-tour-editor.php` — tuỳ vị trí hợp lý nhất theo cấu trúc code hiện tại), thêm:

```php
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( 'tour' === $post_type ) {
		return false;
	}
	return $use_block_editor;
}, 10, 2 );
```

Đăng ký hook này ở chỗ plugin đang bootstrap các class khác (ví dụ trong `tour-booking-core.php`, cạnh các `add_action`/`add_filter` khác đã có).

**Lưu ý — không ảnh hưởng gì tới phần đã build trước đó:**
- Trang chủ, trang chi tiết tour (`single-tour.html`) vẫn hiển thị đúng như cũ — nội dung mô tả tour (`the_content()`) render ra HTML giống nhau bất kể được soạn bằng Classic Editor hay Block Editor, template không cần sửa gì.
- Việc này **chỉ đổi CÁCH ADMIN SOẠN THẢO** bài Tour trong wp-admin, không đổi cách dữ liệu lưu hay cách frontend hiển thị.
- Các post type khác (`page`, `post`, v.v.) **không bị ảnh hưởng** — filter chỉ áp dụng cho đúng `'tour'`.

## Kiểm chứng bắt buộc

1. Vào `wp-admin/post-new.php?post_type=tour` — xác nhận màn hình soạn thảo giờ là giao diện Classic Editor (có thanh công cụ TinyMCE quen thuộc: B/I/U, danh sách, v.v., không phải khung block trống), và **6 meta box hiện NGAY trên trang, không cần cuộn xuống mở "Hộp Meta"**.
2. Mở sửa tour có sẵn (ví dụ "Northern Highlands Loop", post=155) — xác nhận vẫn thấy đúng dữ liệu cũ (2 ngày lịch trình, 2 phương tiện...) hiện sẵn ngay khi mở trang, không cần thao tác gì thêm.
3. Chạy lại PHPUnit cả 2 package — phải 100% pass (thay đổi này không đụng logic tính giá/lưu dữ liệu nên không nên ảnh hưởng tới test nào, nhưng vẫn phải xác nhận).
4. Kiểm tra nhanh 1 trang khác không phải `tour` (ví dụ `wp-admin/post-new.php?post_type=page`) vẫn dùng Block Editor bình thường — xác nhận filter không ảnh hưởng nhầm sang post type khác.

## Báo cáo lại

Thêm section "Vòng 5" vào `docs/fix-report-2026-08-21.md`, mô tả đã sửa gì, dán kết quả kiểm chứng thật (có thể chụp ảnh màn hình `wp-admin/post-new.php?post_type=tour` sau khi sửa để xác nhận meta box hiện rõ ràng ngay từ đầu).
