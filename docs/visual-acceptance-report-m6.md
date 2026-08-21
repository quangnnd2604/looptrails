# Báo cáo Kiểm định Thị giác & Nghiệm thu — Milestone 6 (Các Template Phụ & Mẫu Khối Tương Ứng)

- **Ngày thực hiện:** 2026-08-21.
- **Mục tiêu:** Xây dựng hoàn chỉnh toàn bộ các Block Templates và Block Patterns phụ cho theme `tour-reference-theme` theo đặc tả Spec (§5.4, §5.5, §13.6) và số liệu đo lường thực tế (`docs/reference-audit/02-tour-detail.md`, `03-secondary-pages.md`).
- **Môi trường thử nghiệm:** `http://localhost/looptrails/` (WordPress 7.0.4, theme `tour-reference-theme` và plugin `tour-booking-core` đang kích hoạt).

---

## 1. Danh mục Templates & Thành phần đã xây dựng trong Milestone 6

1. **Template Chi tiết Tour (`templates/single-tour.html`):**
   - **Hero Banner:** Tiêu đề tour lớn H1, các huy hiệu đánh giá sao (★ 4.9), lịch khởi hành hàng ngày và quy mô nhóm nhỏ (tối đa 8 người).
   - **Cột Nội dung chính (65%):** Ảnh đại diện 16:9 bo góc, Tổng quan tour (`post-content`), Lịch trình từng ngày dạng Timeline có điểm đánh dấu tròn (`itinerary-timeline`), Bảng phân chia quyền lợi Bao gồm (Included ✓) / Không bao gồm (Excluded ✗).
   - **Cột Đặt tour Sticky (35%):** Thẻ giá cố định khi cuộn trang, hiển thị 3 phân hạng phương tiện (Self-Ride, Easy Rider có tài xế, Xe Jeep 4x4), nút "Instant Booking" nổi bật và cam kết bảo đảm hoàn hủy.
   - **Khối Tour liên quan:** Lưới 3 tour gợi ý bên dưới (`[tour_featured_grid postsPerPage="3"]`).

2. **Template Danh mục Tour (`templates/archive-tour.html`):**
   - Banner tiêu đề "Northern Vietnam Loop Tours", lưới hiển thị toàn bộ 12 tour mẫu với đầy đủ phân hạng giá xe, khối tính năng vì sao chọn Loop Trails và CTA banner.

3. **Template Thuê xe máy (`templates/page-motorbike-rental.html` & `patterns/rental-bikes.php`):**
   - Lưới 4 loại xe máy đa dạng: Xe số Honda Wave Alpha 110cc ($10/ngày), Honda Blade FI 110cc ($12/ngày), Xe cào cào côn tay Honda XR 150L ($22/ngày), Xe phân khối lớn Adventure Honda CB500X ($48/ngày).
   - Thẻ quy định và điều kiện thuê xe (bằng lái quốc tế IDP, mũ bảo hiểm chuẩn DOT, hỗ trợ kỹ thuật 24/7) cùng FAQ accordion.

4. **Hệ thống Blog & Tìm kiếm (`templates/single.html`, `templates/archive.html`, `templates/search.html`):**
   - `single.html`: Bố cục bài viết chuyên nghiệp, hiển thị tác giả, ngày đăng, ảnh đại diện, nội dung và khối điều hướng bài trước/sau.
   - `archive.html`: Lưới bài viết 3 cột kèm phân trang tự động.
   - `search.html`: Khung tìm kiếm từ khóa và kết quả tìm kiếm với trạng thái rỗng thân thiện (Empty state fallback).

5. **Các Trang Thông tin & Pháp lý (`templates/page-contact.html`, `templates/page-about.html`, `templates/page.html`, `templates/404.html`):**
   - `page-contact.html`: Thẻ thông tin trụ sở Hà Giang (Địa chỉ, Hotline/WhatsApp, Email, Giờ làm việc) và Form gửi tin nhắn.
   - `page-about.html`: Lịch sử hình thành, sứ mệnh, thanh 4 chỉ số phát triển và lưới dịch vụ.
   - `page.html`: Kiểu chữ được tối ưu cho văn bản dài phục vụ Điều khoản dịch vụ & Chính sách quyền riêng tư.
   - `404.html`: Trang báo lỗi 404 thân thiện kèm thanh tìm kiếm và nút "Return To Homepage".

---

## 2. Kết quả Kiểm thử Tự động (Automated Verification)

| Hạng mục kiểm tra | Bộ công cụ | Kết quả | Trạng thái |
|---|---|---|---|
| **Theme Unit Tests** | PHPUnit 9.6 (`tests/`) | **42 / 42 tests passed** (225 assertions, 0 failures) | ✅ ĐẠT 100% |
| **Plugin Core Unit Tests** | PHPUnit 9.6 (`tour-booking-core`) | **47 / 47 tests passed** (323 assertions, 0 failures) | ✅ ĐẠT 100% |
| **Cấu trúc Ngữ nghĩa Semantic Landmark** | PHPUnit Assertion | 100% template có thẻ `<main>` và Header/Footer parts | ✅ ĐẠT (WCAG 2.4.1 Level A) |
| **Mẫu Khối Tương thích Gutenberg** | Gutenberg `do_blocks()` | 100% Patterns render chuẩn HTML không có lỗi cú pháp | ✅ ĐẠT |

---

## 3. Kết luận
Milestone 6 đã hoàn thành xuất sắc toàn bộ hệ thống các trang thứ cấp và template chi tiết của dự án, đảm bảo tính thẩm mỹ, nhất quán về thiết kế và độ tương thích tuyệt đối với WordPress Gutenberg Block Theme.
Hệ thống sẵn sàng chuyển sang **Milestone 7 (Booking Engine, Checkout Integration, Email & Notifications)**!
