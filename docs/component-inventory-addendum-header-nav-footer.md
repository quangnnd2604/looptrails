# Phụ lục Kiểm định Tương tác Header/Nav/Footer

Thu thập ngày 2026-08-20 qua `tools/reference-audit/interaction-audit.mjs` trực tiếp từ trang web tham chiếu (https://looptrails.com/, trang chủ). Bổ sung cho mục "Primary Navigation (Header)" và "Footer Block" trong `docs/component-inventory.md`. Dữ liệu gốc: `docs/reference-screenshots/interaction-audit/findings.json`.

---

## 1. Cấu trúc Điều hướng (Nav Structure)

Nút bật menu hamburger `.elementor-menu-toggle` xuất hiện (`toggleVisible: true`) ở **tất cả 5 viewport được kiểm định** — Desktop (1440px), Laptop (1280px), Tablet (768px), Mobile (390px) và Narrow Mobile (360px). Kết quả này được xác nhận qua ảnh chụp màn hình: ở độ phân giải 1440px chỉ hiển thị logo, nút "Book Now" và biểu tượng hamburger 3 gạch — không có thanh menu dàn ngang ở bất kỳ độ phân giải nào.

Khi nhấp vào biểu tượng toggle, danh sách liên kết trả về giống hệt nhau ở cả 5 viewport (theo thứ tự DOM):
- Home, Tours, Ha Giang Loop 2D1N, Ha Giang Loop 3D2N, Ha Giang Loop 4D3N, Ha Giang Cao Bang 5D4N, Ha Giang Cao Bang 6D5N, Cao Bang Loop 3D2N, Motorbike Rental, Blog, Contact, About, Terms & Privacy.

**Kết luận:** Trang web tham chiếu áp dụng đồng nhất mô hình hamburger menu trên mọi kích thước màn hình (bao gồm cả desktop). Menu mở ra dạng danh sách dọc 1 cột.

---

## 2. Hành vi Cuộn trang (Sticky Behavior)

Tại tất cả 5 viewport, giá trị computed style của thẻ `<header>` trước và sau khi cuộn trang (`window.scrollTo(0, 900)`) là **hoàn toàn giống nhau**: `position: static`, `top: auto`, `backgroundColor: rgba(0, 0, 0, 0)`.

**Kết luận:** Header là dạng tĩnh (`position: static`), không phải sticky/fixed và không thay đổi màu nền khi cuộn trang.

---

## 3. Cấu trúc Footer

Thẻ `<footer>` có computed style `display: block`. Toàn bộ nội dung footer hiển thị dưới dạng **một khối cột đơn căn giữa** (stacked centered block): logo chữ, địa chỉ, hotline, email, liên kết website, thông tin pháp lý, chứng nhận và hàng biểu tượng mạng xã hội (Facebook, Instagram, WhatsApp, TikTok, Tripadvisor) xếp dọc căn giữa, không chia theo nhiều cột dạng lưới (grid).

**Kết luận:** Footer là một khối cột đơn căn giữa, không phải hệ thống lưới nhiều cột.

---

## 4. Bộ chuyển đổi Ngôn ngữ / Tiền tệ (Language / Currency Switcher)

Trên giao diện tham chiếu thực tế, không có bộ chọn ngôn ngữ hoặc tiền tệ hiển thị trong header (`findings.langSwitcher` trả về `null`).

**Quy chuẩn thực hiện theo Đặc tả Spec:**
- Mục §5.1 của đặc tả yêu cầu bắt buộc phải có tính năng chuyển đổi ngôn ngữ/tiền tệ (EN/USD và VI/VND).
- Tính năng này được phân bổ thực hiện chính thức trong **Milestone 7** ("Hành vi EN/VI và USD/VND").
