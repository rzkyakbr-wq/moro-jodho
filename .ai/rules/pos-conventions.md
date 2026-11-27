---
paths:
  - "**/*"
---

# Moro Jodho POS Project Conventions

## 1. Project Overview & Architecture
- **Application**: Multi-role Point of Sale (POS) & Inventory Management System for Toko Tembakau Moro Jodho.
- **Tech Stack**: Laravel (Backend API/Web), SQLite/PostgreSQL (Database), Tailwind CSS (Frontend UI).
- **Core Roles**:
  - **Owner**: Authenticated dashboard, restock management, profit & sales analytics.
  - **Cashier**: Shift tracking, POS transaction screen, and physical receipt printing.
  - **Customer**: Public catalog display with direct WhatsApp ordering integration.

## 2. Coding & Laravel Best Practices
- **Controllers**: Keep controllers lean. Offload complex business logic to Service classes or Action objects.
- **Validation**: Always use `FormRequest` classes for input validation (e.g., `StoreTransactionRequest`, `UpdateStockRequest`). Never validate directly inside controller methods.
- **Models & Eloquent**:
  - Explicitly define `$fillable` or `$guarded` on all models.
  - Define type-hinted Eloquent relationships (`BelongsTo`, `HasMany`, etc.).
  - Use database transactions (`DB::transaction`) for all multi-step inventory/sales operations.
- **Database Migrations**: Always specify foreign key constraints and proper indexing for high-query fields like `product_id`, `category_id`, and `created_at`.

## 3. UI/UX & Tailwind Guidelines
- Ensure all cashier interfaces are optimized for quick navigation and responsive touch/keyboard inputs.
- Maintain consistent color branding matching Toko Tembakau Moro Jodho guidelines.
- Use Blade components or Alpine.js for interactive dynamic UI elements.

## 4. Testing & Code Quality
- Write Feature tests (`Pest` or `PHPUnit`) for critical POS operations: stock deduction, shift opening/closing, and order calculations.
- Follow PSR-12 coding standards and format code using Laravel Pint before committing.