# Báo cáo Kiểm định Thị giác — Milestone 4 (Theme Shell)

- **Ngày đo lường:** 2026-08-21 (Đo lường toàn diện sau vòng fix wave của nhánh review).
- **Mẫu tham chiếu (Reference):** `docs/reference-screenshots/home/*.png` (Toàn trang từ Milestone 1).
- **Giao diện cục bộ (Local):** `docs/reference-screenshots/local-m4/*.png` (Chạy trên `http://localhost/looptrails/`, theme `tour-reference-theme` đang kích hoạt).

---

## 1. Quy trình đo lường tự động (Pipeline)

Quy trình được thực thi theo thứ tự bằng Playwright & Pixelmatch:
1. `measure-reference.mjs`: Đo lường tọa độ/kích thước vùng Header & Footer từ trang tham chiếu.
2. `capture-local.mjs`: Chụp màn hình và xuất tọa độ DOM của trang cục bộ.
3. `crop-reference.mjs`: Cắt ảnh tham chiếu và ảnh cục bộ theo đúng chiều cao của vùng tham chiếu.
4. `diff.mjs`: Tính toán tỷ lệ chênh lệch pixel (Raw Diff & Masked Diff).
5. `check-colors.mjs`, `check-metrics.mjs`, `check-section-position.mjs`, `check-overflow.mjs`: Kiểm tra màu sắc, font chữ, vị trí và chống tràn ngang.

---

## 2. Bảng kết quả kiểm định theo Viewport

| Viewport | Kích thước vùng (Header × Footer) | Header diff (Raw) | Header diff (Masked) | Footer diff (Raw) | Footer diff (Masked) | Cạnh viền Container hợp chuẩn? | Thông số Font chữ hợp chuẩn? | Tràn ngang ở 360px? |
|---|---|---|---|---|---|---|---|---|
| Desktop (1440px) | 66px / 528px | 16.38% | **14.94%** | 63.13% | **63.02%** | Đạt (Thanh full-width, 0px delta) | Đạt (14.5px, weight 700, radius 25px) | Không |
| Laptop (1280px) | 66px / 528px | 16.51% | **14.89%** | 63.06% | **62.94%** | Đạt (0px delta) | Đạt (14.5px, weight 700, radius 25px) | Không |
| Tablet (768px) | 91px / 526px | 44.12% | **40.46%** | 62.57% | **62.32%** | Đạt (0px delta) | Đạt (15.5px, weight 700, radius 25px) | Không |
| Mobile (390px) | 61px / 586px | 13.68% | **10.35%** | 62.84% | **62.51%** | Đạt (0px delta) | Đạt (15.5px, weight 700, radius 25px) | Không (`scrollWidth === clientWidth === 390`) |
| Narrow Mobile (360px) | 61px / 622px | 13.47% | **10.19%** | 61.32% | **60.98%** | Đạt (0px delta) | Đạt (15.5px, weight 700, radius 25px) | Không (`scrollWidth === clientWidth === 360`) |

- **Trung bình Header:** 20.83% (Raw) / **18.17% (Masked)**.
- **Trung bình Footer:** 62.58% (Raw) / **62.35% (Masked)**.
- **Tổng thể 10 phép đo:** **41.71% (Raw) / 40.26% (Masked)** (So với mục tiêu <8% của Spec §4).

---

## 3. Phân tích nguyên nhân chênh lệch (Deltas & Deviations)

1. **Nội dung Footer tham chiếu gấp 2.5 – 3 lần vỏ khung hiện tại:**
   - Footer trang tham chiếu có chiều cao 526–622px chứa đầy đủ địa chỉ, thông tin pháp lý, form đăng ký, phương thức thanh toán.
   - Milestone 4 chỉ xây dựng khung giao diện cơ bản (192–231px). Các khối thông tin quản trị site-wide sẽ được tích hợp đầy đủ trong các Milestone sau (Milestone 8).
2. **Khác biệt nền Hero của Header:**
   - Trên trang tham chiếu, Header nằm đè lên ảnh Hero toàn trang; còn ở Milestone 4, Header hiển thị trên trang index trống.
3. **Thay thế hình ảnh & Logo thương hiệu theo quy định pháp lý (Spec §1):**
   - Logo được thay bằng biểu tượng SVG nguyên bản độc lập, tránh vi phạm bản quyền thương hiệu gốc.

---

## 4. Các tiêu chí chất lượng đã ĐẠT 100%

- **Màu sắc thương hiệu (Colors):** 11/11 thông số đạt chuẩn tuyệt đối (Facebook, Instagram, WhatsApp, TikTok, Tripadvisor, màu nền Surface).
- **Typography & Button CTA:** Cỡ chữ nút "Book Now" đạt chuẩn ở cả 5 viewport (14.5px desktop / 15.5px mobile, border-radius 25px chuẩn).
- **Chống tràn màn hình (No Overflow):** 0px tràn ngang tại 360px.
- **Cấu trúc ngữ nghĩa & Accessibility:** Có thẻ `<main>` phục vụ WP core skip link (WCAG 2.4.1 Level A), thẻ `wp:site-title` hỗ trợ tên site cho trình đọc màn hình.
