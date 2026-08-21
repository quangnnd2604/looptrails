# Payment Gateways Implementation Status & Architecture Plan

- **Status:** **NOT YET IMPLEMENTED (CHƯA TRIỂN KHAI)**
- **Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §10, §15.
- **Document Date:** 2026-08-21.

---

## 1. Current State Disclosure

As of Milestone 10 audit, automated payment gateway adapters for **OnePay**, **VNPay**, and **MoMo** in sandbox mode have **not yet been implemented**.

### Current Booking Flow Behavior:
1. Customer submits the booking form via REST API (`POST /wp-json/tour-booking/v1/book`).
2. Server recalculates authoritative quote, saves booking record in database.
3. `tbc_payment_status` is saved as `'pending'`.
4. `tbc_booking_status` is saved as `'pending_payment'`.
5. Customer and Admin receive automated notifications for manual bank transfer or on-arrival deposit handling until payment gateway adapters are integrated.

---

## 2. Planned Architecture for Payment Gateway Adapters

When implementing payment gateway integrations, the following architecture must be followed:

### 2.1 VNPay Sandbox Adapter (`Tbc_Gateway_VNPay`)
- **Protocol:** SHA512 hash-based signature generation and verification.
- **Endpoints:**
  - Payment redirect generation with `vnp_TxnRef`, `vnp_Amount`, `vnp_OrderInfo`, `vnp_ReturnUrl`, `vnp_IpnUrl`.
  - IPN (Instant Payment Notification) listener verifying `vnp_SecureHash` and transitioning booking from `pending_payment` → `paid` / `confirmed`.

### 2.2 OnePay International Gateway (`Tbc_Gateway_OnePay`)
- **Protocol:** HMAC-SHA256 signature for Visa/Mastercard processing.
- **Handling:** Currency conversion snapshots (USD charging with VND internal settlement).

### 2.3 MoMo Wallet Adapter (`Tbc_Gateway_MoMo`)
- **Protocol:** HMAC-SHA256 signature, QR code and deep-link payload generation for mobile checkout.

---

## 3. Security Requirements for Implementation
- Zero real customer charges / zero live credentials during staging (sandbox only).
- All secret keys (`vnp_HashSecret`, `onepay_secret`, `momo_secret`) must be stored in `wp-config.php` constants or secured options, never committed to git.
- Strict IPN idempotency checks to prevent double-charging or replay attacks.
