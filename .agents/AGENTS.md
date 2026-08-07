# Fleet Maintenance System Project Rules

This project is a **Fleet Maintenance System (Sistem Manajemen Perawatan Armada)** built using Laravel, with a multi-user architecture and Indonesian-language domain logic.

## 1. Language & Terminology Guidelines
- **User Interface & Logs**: Use Indonesian (Bahasa Indonesia) for user-facing strings, buttons, menus, notifications, error messages, and logs.
- **Database & Code Naming**: Code naming is a mix of Indonesian models/tables (e.g., `Kendaraan`, `Pembayaran`) and Laravel standard conventions. Follow the existing patterns in the codebase when modifying or adding database columns.

## 2. Role-Based Access Control (RBAC)
Strictly respect and implement authorization boundaries for the following 3 roles:
- **Admin**: Full access. Manages vehicles, users, reports, approvals, and budgets.
- **Teknisi (Technician)**: Access to daily checklists, odometer updates, maintenance status changes, and repair expense input.
- **User (Driver/Staff)**: Access to viewing available vehicles, reporting issues/complaints, and tracking document expiration dates.

## 3. Maintenance & Checklist Invariants
- **Daily Checklist Parameters**: Must track:
  - **Cairan**: Oli mesin, air radiator (coolant), minyak rem, air wiper.
  - **Kaki-kaki**: Tekanan angin ban, keausan ban, fungsi rem.
  - **Kelistrikan**: Lampu utama, sein, lampu rem, klakson, AC.
  - **Kebersihan**: Interior dan eksterior.
- **Color Indicators for Expiry/Due Dates**:
  - 🟡 **Kuning (Yellow)**: Approaching due date/threshold.
  - 🔴 **Merah (Red)**: Overdue (exceeded KM limit or calendar limit).
