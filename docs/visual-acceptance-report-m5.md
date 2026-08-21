# Báo cáo Kiểm định Thị giác & Nghiệm thu — Milestone 5 (Trang chủ & Các thành phần Giao diện)

- **Ngày thực hiện:** 2026-08-21.
- **Mục tiêu:** Xây dựng hoàn chỉnh Trang chủ (`templates/front-page.html` / `templates/home.html`) và hệ thống các thành phần giao diện / Block Patterns tái sử dụng theo đúng đặc tả Spec §5.2, §5.3, §13.5 và số liệu đo lường thực tế.
- **Môi trường thử nghiệm:** `http://localhost/looptrails/` (WordPress 7.0.4, theme `tour-reference-theme` đang kích hoạt).

---

## 1. Danh mục các thành phần đã xây dựng trong Milestone 5

1. **Thành phần Thẻ Tour tái sử dụng (`tour-card` & `[tour_featured_grid]`):**
   - **Tệp nguồn:** `wp-content/themes/tour-reference-theme/includes/tour-card.php`.
   - **Cấu trúc:** Ảnh thumbnail tỉ lệ chuẩn, huy hiệu nổi bật (`tbc_badge`), nhãn thời lượng (`tbc_duration_days/nights`), số sao & lượt đánh giá (`tbc_rating_value/count`), tiêu đề, bảng giá chi tiết theo từng loại phương tiện (Self-Ride, Easy Rider/Pillion, Jeep), nút CTA "Book Now" và liên kết "Details →".
   - **Quy chuẩn kích thước (CSS):** Lưới 3 cột Desktop (336px width, 22px gap, 14px border-radius, box-shadow mờ), 2 cột Tablet (768px), 1 cột Mobile (390px/360px).

2. **Khối Hero Trang chủ (`patterns/hero-home.php`):**
   - Banner toàn màn hình (full-bleed) với nền media phủ lớp gradient tối, tiêu đề chính H1 nổi bật, phụ đề mô tả ngắn và nút CTA "Book Your Tour".

3. **Khối Giới thiệu & Thống kê Thương hiệu (`patterns/brand-narrative.php`):**
   - Bố cục 2 cột: Cột trái chứa thông điệp thương hiệu, số liệu thống kê (10k+ Riders, 100% Local Guides, 4.9/5 Rating) và nút bấm; Cột phải chứa hình ảnh minh họa phong cảnh núi rừng.

4. **Khối Điểm đến & Cẩm nang (`patterns/top-destinations-essentials.php`):**
   - Lưới hiển thị các địa danh nổi tiếng (Mã Pí Lèng, Đồng Văn Geopark, Bản Giốc, Hẻm Tu Sản/Sông Nho Quế) kèm thẻ phân loại và mô tả chi tiết.

5. **Khối Lý do Lựa chọn & Thanh Thống kê Nổi bật (`patterns/why-choose-us.php`):**
   - Lưới 3×2 gồm 6 thẻ tính năng an toàn & chất lượng (Mũ bảo hiểm full-face, Hướng dẫn viên bản địa, Nhóm nhỏ tối đa 8 người, Homestay sạch sẽ, Xe bảo dưỡng định kỳ, Hỗ trợ 24/7).
   - Thanh thông số nền tối (Dark stats bar): 99.8% An toàn, 10,000+ Vòng loop, 4.9★ Đánh giá, 65+ Làng bản đối tác.

6. **Khối Đánh giá Khách hàng (`patterns/testimonials.php`):**
   - Lưới 3 đánh giá mẫu từ khách quốc tế kèm số sao 5★, trích dẫn trải nghiệm thực tế và thông tin người đánh giá.

7. **Khối Kêu gọi Hành động Nổi bật (`patterns/editorial-cta.php`):**
   - Banner CTA lớn toàn trang với nền gradient bo góc 20px, kích thích chuyển đổi đặt tour.

8. **Khối Câu hỏi Thường gặp (`patterns/faq-accordion.php`):**
   - Accordion sử dụng thẻ HTML `<details><summary>` chuẩn ngữ nghĩa và hỗ trợ tốt cho trình đọc màn hình / bàn phím (Accessibility).

9. **Template Trang chủ Hoàn chỉnh (`templates/front-page.html` & `templates/home.html`):**
   - Tích hợp Header, toàn bộ 8 Section Patterns theo đúng thứ tự giao diện tham chiếu, bọc trong thẻ ngữ nghĩa `<main>` (WCAG 2.4.1 Level A) và Footer.

---

## 2. Kết quả Kiểm thử Tự động (Automated Verification)

| Hạng mục kiểm tra | Công cụ thực hiện | Kết quả | Trạng thái |
|---|---|---|---|
| **Theme Unit Tests** | PHPUnit 9.6 (`tests/`) | **40 / 40 tests passed** (140 assertions, 0 failures) | ✅ ĐẠT 100% |
| **Plugin Core Unit Tests** | PHPUnit 9.6 (`tour-booking-core`) | **47 / 47 tests passed** (323 assertions, 0 failures) | ✅ ĐẠT 100% |
| **Độ chuẩn Màu sắc (Colors)** | Playwright (`check-colors.mjs`) | **11 / 11 thông số đạt chuẩn tuyệt đối** (max channel diff = 0) | ✅ ĐẠT |
| **Kiểu chữ & Nút bấm (Metrics)** | Playwright (`check-metrics.mjs`) | 14.5px (Desktop) / 15.5px (Mobile/Tablet), weight 700, radius 25px | ✅ ĐẠT |
| **Chống tràn màn hình (Overflow)**| Playwright (`check-overflow.mjs`) | **0px tràn ngang tại tất cả 5 viewport** (Desktop, Laptop, Tablet, Mobile, 360px) | ✅ ĐẠT |

---

## 3. Ảnh chụp màn hình nghiệm thu
Dữ liệu ảnh chụp toàn bộ trang chủ tại 5 viewport đã được lưu trữ đầy đủ tại:
`docs/reference-screenshots/local-m5/` (`desktop-full.png`, `laptop-full.png`, `tablet-full.png`, `mobile-full.png`, `narrow-mobile-full.png`).

---

## 4. Kết luận
Milestone 5 đã hoàn thành toàn diện, đáp ứng đầy đủ tiêu chí kỹ thuật và quy chuẩn thiết kế. Hệ thống sẵn sàng chuyển sang **Milestone 6** (Archives, Tour Detail, Motorbike Rental, Blog, Information/Legal and Error Templates).
