# Ledger Management System (Laravel API)

## Overview

This project is a comprehensive ledger and sales management backend built with Laravel. **It includes both a web version (admin panel) and a full-featured API, sharing the same backend codebase.**

You can use the web interface for admin/management tasks, or integrate with the API for mobile apps, automation, or third-party tools.

It provides APIs for:
- User authentication and management
- Category and product management
- Sales and expense ledgers
- Transactions and payments (Razorpay integration)
- Dashboard analytics

**Note:** There is a mobile app that uses this API. For the full mobile source code, check the corresponding repository [github.com/pplcallmesatz/finance-ledger-flutter](https://github.com/pplcallmesatz/finance-ledger-flutter).

---

## Project Setup

### Prerequisites
- PHP 8.1+
- Composer
- MySQL or MariaDB
- Node.js & npm (for frontend assets)

### Manual Setup
1. **Clone the repository and install dependencies:**
   ```sh
   git clone https://github.com/pplcallmesatz/finance-ledger-web-api-backend.git
   cd your-repo
   composer install
   npm install
   ```
2. **Copy and configure your environment:**
   ```sh
   cp .env.example .env
   # Edit .env for your DB, mail, etc.
   ```
3. **Generate app key and migrate database:**
   ```sh
   php artisan key:generate
   php artisan migrate --seed
   ```
4. **Build frontend assets:**
   ```sh
   npm run dev
   ```
5. **Serve the app:**
   ```sh
   php artisan serve
   ```
6. **Access the API:**
   - Web URL: `http://localhost:8000/` (or your configured host)

7. **Access the API:**
   - API base URL: `http://localhost:8000/api` (or your configured host)

---

## API Overview

Below is a summary of the main API endpoints and features available in this project:

### Authentication
- `POST /api/login` — Obtain an access token for API usage
- `POST /api/logout` — Invalidate the current token

### User Management
- `GET /api/users` — List users (paginated)
- `POST /api/users` — Create a new user
- `GET /api/users/{id}` — Get user details
- `PUT /api/users/{id}` — Update user
- `DELETE /api/users/{id}` — Delete user
- `GET /api/users/{id}/details` — Get user with sales summary (pending/paid)

### Category Management
- `GET /api/category-masters` — List categories
- `POST /api/category-masters` — Create category
- `GET /api/category-masters/{id}` — Get category details
- `PUT /api/category-masters/{id}` — Update category
- `DELETE /api/category-masters/{id}` — Delete category
- `GET /api/category-masters/{categoryMaster}/product-masters` — List product masters for a category
- `GET /api/category-masters/{categoryMaster}/products` — List products for a category

### Product Management
- `GET /api/products` — List products (with search, category, barcode filters)
- `POST /api/products` — Create product
- `GET /api/products/{id}` — Get product details
- `PUT /api/products/{id}` — Update product
- `DELETE /api/products/{id}` — Delete product
- `GET /api/products/search/barcode` — Search product by barcode
- `GET /api/products/{id}/inventory` — Get product inventory status
- `GET /api/products/{id}/pricing-history` — Get product pricing history
- `GET /api/products/{id}/performance` — Get product sales analytics
- `POST /api/products/bulk-update-prices` — Bulk update product prices
- `GET /api/products/low-stock` — List low stock products
- `POST /api/products/{id}/generate-barcode` — Generate barcode for product

### Product Master Management
- `GET /api/product-masters` — List product masters (with search, category filter)
- `POST /api/product-masters` — Create product master
- `GET /api/product-masters/{id}` — Get product master details
- `PUT /api/product-masters/{id}` — Update product master
- `DELETE /api/product-masters/{id}` — Delete product master

### Sales Ledger
- `GET /api/sales-ledgers` — List sales ledgers (with filters)
- `POST /api/sales-ledgers` — Create sales ledger entry
- `GET /api/sales-ledgers/{id}` — Get sales ledger details
- `PUT /api/sales-ledgers/{id}` — Update sales ledger
- `DELETE /api/sales-ledgers/{id}` — Delete sales ledger
- `GET /api/sales-ledgers/pending` — List all pending sales ledgers and total
- `PATCH /api/sales-ledgers/{id}/payment-info` — Update payment status/method
- `PATCH /api/sales-ledgers/{id}/payment-status` — Update payment status only
- `POST /api/sales-ledgers/{id}/payment-link` — Create payment link
- `GET /api/sales-ledgers/summary` — Get sales summary

### Expense Ledger
- `GET /api/expense-ledgers` — List expense ledgers
- `POST /api/expense-ledgers` — Create expense ledger
- `GET /api/expense-ledgers/{id}` — Get expense ledger details
- `PUT /api/expense-ledgers/{id}` — Update expense ledger
- `DELETE /api/expense-ledgers/{id}` — Delete expense ledger

### Transactions
- `GET /api/transactions` — List transactions
- `POST /api/transactions` — Create transaction
- `GET /api/transactions/{id}` — Get transaction details
- `PUT /api/transactions/{id}` — Update transaction
- `DELETE /api/transactions/{id}` — Delete transaction

### Payments (Razorpay)
- `POST /api/payments/create-order` — Create payment order
- `POST /api/payments/verify` — Verify payment
- `GET /api/payments/status/{salesLedgerId}` — Get payment status
- `GET /api/payments/history` — Get payment history
- `POST /api/payments/refund` — Process refund
- `POST /api/payments/webhook` — Razorpay webhook endpoint

### Dashboard & Analytics
- `GET /api/dashboard/overview` — Get business statistics
- `GET /api/dashboard/product-analysis` — Product/category profit/loss
- `GET /api/dashboard/inventory-status` — Inventory by category
- `GET /api/dashboard/sales-trends` — Sales trends (by period)
- `GET /api/dashboard/top-products` — Top products
- `GET /api/dashboard/customer-analytics` — Customer analytics

---

## Example: Using cURL with the API

You can interact with this API using cURL from the command line. Below are sample requests and typical JSON responses for the main modules.

### 1. Login (get token)

**Request:**
```sh
curl -X POST "http://localhost:8000/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```
**Expected Output:**
```json
{
  "token": "1|abc123..."
}
```

---

### 2. Users

**List Users**
```sh
curl -X GET "http://localhost:8000/api/users" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create User**
```sh
curl -X POST "http://localhost:8000/api/users" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "9876543210",
    "password": "your_password"
  }'
```
**Edit User**
```sh
curl -X PUT "http://localhost:8000/api/users/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated",
    "email": "john@example.com",
    "phone": "9876543210"
  }'
```
**Delete User**
```sh
curl -X DELETE "http://localhost:8000/api/users/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 3,
    "name": "John Updated",
    "email": "john@example.com",
    "phone": "9876543210"
    // ...other fields
  }
}
```

---

### 3. Category

**List Categories**
```sh
curl -X GET "http://localhost:8000/api/category-masters" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Category**
```sh
curl -X POST "http://localhost:8000/api/category-masters" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Electronics",
    "description": "Electronic products",
    "symbol": "ELEC",
    "self_life": "365"
  }'
```
**Edit Category**
```sh
curl -X PUT "http://localhost:8000/api/category-masters/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Electronics Updated",
    "description": "Updated description",
    "symbol": "ELEC",
    "self_life": "365"
  }'
```
**Delete Category**
```sh
curl -X DELETE "http://localhost:8000/api/category-masters/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 3,
    "name": "Electronics Updated",
    "description": "Updated description",
    "symbol": "ELEC",
    "self_life": "365"
    // ...other fields
  }
}
```

---

### 4. Product Master

**List Product Masters**
```sh
curl -X GET "http://localhost:8000/api/product-masters" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Product Master**
```sh
curl -X POST "http://localhost:8000/api/product-masters" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Bulk Laptops",
    "category_id": 1,
    "purchase_price": 50000,
    "purchase_date": "2024-01-01",
    "manufacturing_date": "2023-12-01",
    "transportation_cost": 1000,
    "invoice_number": "INV001",
    "vendor": "Tech Supplier",
    "quantity_purchased": 10,
    "expire_date": "2025-12-01",
    "total_piece": "10"
  }'
```
**Edit Product Master**
```sh
curl -X PUT "http://localhost:8000/api/product-masters/5" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Bulk Laptops Updated",
    "category_id": 1,
    "purchase_price": 51000,
    "purchase_date": "2024-01-01",
    "manufacturing_date": "2023-12-01",
    "transportation_cost": 1100,
    "invoice_number": "INV001",
    "vendor": "Tech Supplier",
    "quantity_purchased": 12,
    "expire_date": "2025-12-01",
    "total_piece": "12"
  }'
```
**Delete Product Master**
```sh
curl -X DELETE "http://localhost:8000/api/product-masters/5" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 5,
    "name": "Bulk Laptops Updated",
    "category_id": 1,
    "purchase_price": 51000,
    // ...other fields
  }
}
```

---

### 5. Products

**List Products**
```sh
curl -X GET "http://localhost:8000/api/products" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Product**
```sh
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop",
    "purchase_price": 50000,
    "packing_price": 500,
    "product_price": 50500,
    "selling_price": 60000,
    "description": "High-performance laptop",
    "category_master_id": 1,
    "barcode": "LAP001",
    "barcode_vendor": "Vendor A",
    "units": 1
  }'
```
**Edit Product**
```sh
curl -X PUT "http://localhost:8000/api/products/10" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Updated",
    "purchase_price": 51000,
    "packing_price": 600,
    "product_price": 51600,
    "selling_price": 61000,
    "description": "Updated laptop",
    "category_master_id": 1,
    "barcode": "LAP001",
    "barcode_vendor": "Vendor A",
    "units": 1
  }'
```
**Delete Product**
```sh
curl -X DELETE "http://localhost:8000/api/products/10" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 10,
    "name": "Laptop Updated",
    "category_master_id": 1,
    "product_price": 51600,
    // ...other fields
  }
}
```

---

### 6. Transactions

**List Transactions**
```sh
curl -X GET "http://localhost:8000/api/transactions" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Transaction**
```sh
curl -X POST "http://localhost:8000/api/transactions" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "bank_balance": 100000,
    "cash_in_hand": 50000,
    "sales_ledger_id": 1,
    "reason": "Sales transaction"
  }'
```
**Edit Transaction**
```sh
curl -X PUT "http://localhost:8000/api/transactions/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "bank_balance": 110000,
    "cash_in_hand": 60000,
    "sales_ledger_id": 1,
    "reason": "Updated reason"
  }'
```
**Delete Transaction**
```sh
curl -X DELETE "http://localhost:8000/api/transactions/3" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 3,
    "bank_balance": 110000,
    "cash_in_hand": 60000,
    "sales_ledger_id": 1,
    "reason": "Updated reason"
    // ...other fields
  }
}
```

---

### 7. Expense

**List Expense Ledgers**
```sh
curl -X GET "http://localhost:8000/api/expense-ledgers" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Expense Ledger**
```sh
curl -X POST "http://localhost:8000/api/expense-ledgers" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Office Rent",
    "description": "Monthly office rent",
    "invoice_number": "INV001",
    "purchase_price": 50000,
    "seller": "Landlord",
    "purchase_date": "2024-01-01",
    "payment_method": "bank_transfer",
    "expense_type": "rent",
    "deduct": true
  }'
```
**Edit Expense Ledger**
```sh
curl -X PUT "http://localhost:8000/api/expense-ledgers/5" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Office Rent Updated",
    "description": "Updated rent",
    "invoice_number": "INV001",
    "purchase_price": 51000,
    "seller": "Landlord",
    "purchase_date": "2024-01-01",
    "payment_method": "bank_transfer",
    "expense_type": "rent",
    "deduct": true
  }'
```
**Delete Expense Ledger**
```sh
curl -X DELETE "http://localhost:8000/api/expense-ledgers/5" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 5,
    "name": "Office Rent Updated",
    "purchase_price": 51000,
    // ...other fields
  }
}
```

---

### 8. Sales

**List Sales Ledgers**
```sh
curl -X GET "http://localhost:8000/api/sales-ledgers" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Create Sales Ledger**
```sh
curl -X POST "http://localhost:8000/api/sales-ledgers" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "sales_date": "2024-01-15",
    "payment_method": "cash",
    "payment_status": "pending",
    "remarks": "Customer order",
    "products": [
      {
        "selected": true,
        "product_id": 1,
        "product_name": "Laptop",
        "product_price": 50000,
        "selling_price": 60000,
        "quantity": 1,
        "customer_price": 60000,
        "product_master_id": 1
      }
    ]
  }'
```
**Edit Sales Ledger**
```sh
curl -X PUT "http://localhost:8000/api/sales-ledgers/7" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "sales_date": "2024-01-15",
    "payment_method": "bank_transfer",
    "payment_status": "paid",
    "remarks": "Updated order",
    "products": [
      {
        "selected": true,
        "product_id": 1,
        "product_name": "Laptop",
        "product_price": 50000,
        "selling_price": 60000,
        "quantity": 1,
        "customer_price": 60000,
        "product_master_id": 1
      }
    ]
  }'
```
**Delete Sales Ledger**
```sh
curl -X DELETE "http://localhost:8000/api/sales-ledgers/7" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```
**Expected Output (for create/edit):**
```json
{
  "data": {
    "id": 7,
    "user_id": 1,
    "total_customer_price": 60000,
    "payment_status": "paid",
    // ...other fields
  }
}
```

---

### Error Example

If you send invalid data or an invalid token, you may get:
```json
{
  "message": "Unauthenticated."
}
```
or
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["The field name field is required."]
  }
}
```

**Tip:**  
Replace `YOUR_ACCESS_TOKEN` with your actual Bearer token.

---

## Developer

**Name:** Satheesh Kumar S  
**Github Profile:** [github.com/pplcallmesatz](https://github.com/pplcallmesatz/)  
**Github Repo:** [github.com/pplcallmesatz/finance-ledger-web-api-backend](https://github.com/pplcallmesatz/finance-ledger-web-api-backend.git)  
**Email:** [satheeshssk@icloud.com](mailto:satheeshssk@icloud.com)  
**Instagram:** [instagram.com/pplcallmesatz](http://instagram.com/pplcallmesatz)

---

## Support

If you find this tool useful, consider supporting me:  
[![Buy Me a Coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/satheeshdesign)

---

## Disclaimer

> This tool is fully generated using AI tools. Issues may be expected.  
> Please report bugs or contribute via pull requests! 
