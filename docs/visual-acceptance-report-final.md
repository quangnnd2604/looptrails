# Báo cáo Trạng thái Dự án & Nghiệm thu Kỹ thuật (12 Milestones)

- **Ngày báo cáo:** 2026-08-21.
- **Tên dự án:** Loop Trails WordPress Tour & Motorbike Adventure Platform.
- **Quy chuẩn kỹ thuật:** Đặc tả kiến trúc WordPress Gutenberg FSE (`tour-reference-theme`) kết hợp Plugin nghiệp vụ (`tour-booking-core`), tuân thủ các yêu cầu trong `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` và giới hạn bản quyền hình ảnh.
- **Môi trường hoạt động:** WordPress 6.x trên máy chủ cục bộ `http://localhost/looptrails/` (PHP 8.2, MySQL/MariaDB XAMPP).

---

## 1. Trạng thái 12 Cột mốc theo Đặc tả Kỹ thuật (§13)

| Milestone | Nội dung thực hiện | Trạng thái |
|---|---|---|
| **M1: Môi trường & Đo lường ban đầu** | Thiết lập môi trường XAMPP, WP-CLI, chụp ảnh tham chiếu 5 viewport, thiết lập công cụ Playwright audit. | ✅ HOÀN TẤT |
| **M2: Audit Thiết kế & Token** | Đo lường chính xác typography, màu sắc (#e4e0da, #ff6602, #1e1e24), lưới layout 1200px, bóng đổ hard-offset và lập hồ sơ linh kiện. | ✅ HOÀN TẤT |
| **M3: Companion Plugin Core** | Xây dựng 11 CPTs, phân quyền roles và import/xóa demo. | ✅ HOÀN TẤT |
| **M4: Theme Shell & Header/Footer** | Xây dựng theme Gutenberg FSE `tour-reference-theme`, cấu hình `theme.json`, Header sticky, Menu đáp ứng, Footer và chuẩn WCAG 2.4.1. | ✅ HOÀN TẤT |
| **M5: Trang chủ & Mẫu khối Giao diện** | Hoàn thiện `front-page.html` cùng các Block Patterns (Hero, Featured Tours, Narrative, Top Destinations, Why Choose Us, Testimonials, CTA, Booking, Blog, FAQs). | ✅ HOÀN TẤT |
| **M6: Các Trang & Mẫu Thứ cấp** | Xây dựng `single-tour.html`, `archive-tour.html`, `page-motorbike-rental.html`, Blog, Search, Contact, About, Terms & 404. | ✅ HOÀN TẤT |
| **M7: Đa ngôn ngữ & Đa tiền tệ** | Quy đổi định dạng USD/VND (`Tbc_Currency`), định dạng tiền tệ và bộ lọc. | ✅ HOÀN TẤT |
| **M8: Hệ thống Đặt tour & Định giá** | `Tbc_Pricing_Engine` (tính giá tour từ `vehicle_option` thật, phụ phí xe, đưa đón, mã voucher, 20% đặt cọc), REST API (`/quote`, `/book`), chống spam honeypot và `Tbc_Mailer`. | ✅ HOÀN TẤT |
| **M9: Cổng thanh toán Sandbox (OnePay / VNPay / MoMo)** | Tích hợp cổng thanh toán sandbox. Chi tiết tại `docs/payments.md`. | ⚠️ **CHƯA TRIỂN KHAI** |
| **M10: Accessibility, Bảo mật, Performance & SEO** | Dữ liệu cấu trúc Schema.org JSON-LD, thẻ OpenGraph, chuẩn hóa sanitize/escape, bảo mật chống CSRF và rate limiting. | ✅ HOÀN TẤT |
| **M11: Visual Diff Iteration 5 Viewports** | Đối chiếu hình ảnh thực tế với ảnh tham chiếu trên 5 viewport. | 🔄 ĐANG THỰC HIỆN |
| **M12: QA Toàn diện & Bàn giao** | Kiểm thử tự động PHPUnit, rà soát mã nguồn và tài liệu bàn giao. | 🔄 ĐANG THỰC HIỆN |

---

## 2. Kiểm thử Tự động
- **Plugin `tour-booking-core` PHPUnit:** 56/56 tests passing.
- **Theme `tour-reference-theme` PHPUnit:** 42/42 tests passing.
- **Tổng cộng:** 98/98 tests passing.
