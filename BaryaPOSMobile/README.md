# BaryaPOS Mobile & Tablet React Native App

Cross-platform enterprise Point of Sale (POS) & CRM application for Android, iOS, and handheld POS devices (Sunmi/Pax).

## Features Included
1. **Authentication**:
   - Username/Email & Password Login & Store Account Registration.
   - Google OAuth Sign-in support.
   - Biometrics (FaceID / Fingerprint) Login.
   - Dynamic Server IP / URL configuration (Cloud or Local Wi-Fi network).

2. **Full Customer CRM & Utang Management**:
   - Suki customer directory with Search, VIP member tiers, and Loyalty Points.
   - Customer profile & credit limit editor.
   - Utang Ledger statement & Receive/Pay Utang collection payment.

3. **POS Terminal**:
   - Responsive design for Tablet (Dual-Pane Master-Detail) and Phone (Catalog + Floating Cart Drawer).
   - Table View vs Card Grid View toggle.
   - Retail vs Wholesale pricing switcher.
   - Pack size & variant options modal.

4. **Checkout & Receipts**:
   - Touch Numpad with quick tender buttons (`₱20`, `₱50`, `₱100`, `₱200`, `₱500`, `₱1,000`, `Exact`).
   - Payment methods: Cash, GCash, Maya, Utang/Credit.
   - ESC/POS 58mm/80mm Bluetooth thermal receipt printing.
   - BIR X-Reading & Z-Reading sales report summaries.

## Database & API Integration
100% synchronized with the Laravel Web Backend (`/api/v1/auth`, `/api/v1/pos/products`, `/api/v1/pos/customers`, `/api/v1/pos/sales`, `/api/v1/pos/reports`). Both Web and Mobile apps share the exact same database tables.

## Setup & Running the Project

```bash
# 1. Install dependencies
npm install

# 2. Run on Android / Expo
npx expo start --android

# 3. Run on iOS
npx expo start --ios
```
