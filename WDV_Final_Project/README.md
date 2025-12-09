# 🍰 Mimi's Bakery - PHP Web Application

A full-featured bakery e-commerce website built with PHP, implementing MVC architecture, secure authentication, contact forms with email notifications, and product management.

**Live Demo**: [http://kickshunter.com/WDV341/index_v1.php](http://kickshunter.com/WDV341/index_v1.php)

---

## 📋 Table of Contents

- [Features](#-features)
- [Application Flow](#-application-flow)
- [Architecture](#-architecture)
- [File Structure](#-file-structure)
- [Page Relationships](#-page-relationships)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Security Features](#-security-features)
- [Technologies Used](#-technologies-used)
- [Documentation](#-documentation)

---

## 👤 Author

**Hunter Lovan**
- Portfolio: [http://kickshunter.com](http://kickshunter.com)
- Course: WDV341 - Intro PHP
- Last updated date: 12.09.2025

---

## 🙏 Acknowledgments

- Thank you Eric Burkheimer, DMACC Intro to PHP class
- Mimi (inspiration for bakery theme)
- PHP and MySQL communities

- I used Claude Sonnet 4.5 to help with this README document, where documentation is the specialty of this tool. 
- I plan to enhance this application with better user experiences, features, and lean architecture.
- I hoope 
---

## ✨ Features

### Customer Features
- 🏠 **Landing Page** - Welcome page with featured content and navigation
- 🛍️ **Product Catalog** - Browse bakery products with images, prices, and stock info
- 🛒 **Shopping Cart** - Add items to cart with quantity controls
- 📬 **Contact Form** - Send inquiries with email notifications
- 📧 **Email Notifications** - Automatic email confirmations to customers
- 🔒 **Security** - Honeypot protection against bots

### Admin Features
- 🔐 **Secure Login** - Session-based authentication
- ➕ **Add Products** - Create new products with 8+ fields
- ✏️ **Edit Products** - Update existing product information
- 🗑️ **Delete Products** - Remove products from catalog
- 📊 **Product Management** - View all products in a table

### Technical Features
- 🏗️ **MVC Architecture** - Separated Model, View, Controller layers
- 🛡️ **SQL Injection Protection** - PDO prepared statements
- 📱 **Responsive Design** - Mobile-friendly interface
- 🎨 **Shared Styling** - Consistent design across pages
- 📂 **Modular Code** - Reusable includes (header, footer, navigation)

---

## 🌊 Application Flow

### Starting Point: `index_v1.php`

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USER ENTRY POINT                             │
│                       index_v1.php (Home)                            │
├─────────────────────────────────────────────────────────────────────┤
│  • Landing page for all users                                        │
│  • Displays welcome message and navigation                           │
│  • Shows login status                                                │
│  • Displays contact form success messages                            │
│  • Links to all major sections                                       │
└─────────────────────────────────────────────────────────────────────┘
                              ↓
                    ┌─────────┴─────────┐
                    │                   │
        ┌───────────▼────────┐  ┌───────▼──────────┐
        │   CUSTOMER FLOW    │  │   ADMIN FLOW     │
        └───────────┬────────┘  └───────┬──────────┘
                    │                   │
                    ▼                   ▼
```

---

## 📊 Complete Application Flow Diagram

```
┌──────────────────────────────────────────────────────────────────────────┐
│                            CUSTOMER JOURNEY                               │
└──────────────────────────────────────────────────────────────────────────┘

    index_v1.php (Home Page)
         │
         ├──→ [Browse Bakery] ──→ baked-products.php
         │                              │
         │                              ├──→ View Products
         │                              ├──→ Add to Cart
         │                              └──→ Place Order ──→ process-order.php
         │                                                         │
         │                                                         └──→ order-confirmation.php
         │
         ├──→ [Contact Us] ──→ contactForm.php
         │                         │
         │                         └──→ Submit Form ──→ processEmailForm.php
         │                                                   │
         │                                                   ├──→ [Success] ──→ index_v1.php?contact_success=1
         │                                                   ├──→ [Bot] ──→ Access Denied Page
         │                                                   └──→ [Error] ──→ Back to contactForm.php
         │
         └──→ [Order Now] ──→ orderProducts.php


┌──────────────────────────────────────────────────────────────────────────┐
│                            ADMIN JOURNEY                                  │
└──────────────────────────────────────────────────────────────────────────┘

    index_v1.php (Home Page)
         │
         └──→ [Admin Login] ──→ login_v1.php
                                    │
                                    ├──→ [Valid Login] ──→ Admin Dashboard
                                    │                           │
                                    │                           ├──→ [Add Products] ──→ productInputForm.html
                                    │                           │                            │
                                    │                           │                            └──→ insertProduct.php
                                    │                           │                                      │
                                    │                           │                                      └──→ updateProducts.php
                                    │                           │
                                    │                           ├──→ [Manage Products] ──→ updateProducts.php
                                    │                           │                               │
                                    │                           │                               ├──→ [Edit] ──→ editProduct.php
                                    │                           │                               │                   │
                                    │                           │                               │                   └──→ productUpdate.php
                                    │                           │                               │
                                    │                           │                               └──→ [Delete] ──→ Product deleted
                                    │                           │
                                    │                           └──→ [Logout] ──→ logout_v1.php ──→ index_v1.php
                                    │
                                    └──→ [Invalid Login] ──→ Error message shown
```

---

## 🏗️ Architecture

### MVC Pattern Implementation

```
┌─────────────────────────────────────────────────────────────────┐
│                        MVC ARCHITECTURE                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  MODEL LAYER (models/)                                           │
│  • ProductModel.php - Database operations for products          │
│    - getAllProducts()                                            │
│    - getProductById($id)                                         │
│    - hasStock($id, $quantity)                                    │
│    - getProductsByCategory($category)                            │
└─────────────────────────────────────────────────────────────────┘
                              ↓ (Raw data)
┌─────────────────────────────────────────────────────────────────┐
│  CONTROLLER LAYER (controllers/)                                 │
│  • ContactController.php - Contact form logic                    │
│    - processContactForm($data)                                   │
│    - validateForm($data)                                         │
│    - checkHoneypot($data)                                        │
│                                                                  │
│  • ProductController.php - Product business logic                │
│    - getProductsForDisplay()                                     │
│    - validateOrder($orderData)                                   │
│    - calculateOrderTotal($items)                                 │
│    - processOrder($orderData)                                    │
└─────────────────────────────────────────────────────────────────┘
                              ↓ (Formatted data)
┌─────────────────────────────────────────────────────────────────┐
│  VIEW LAYER (*.php, views/)                                      │
│  • index_v1.php - Home page                                      │
│  • baked-products.php - Product catalog                          │
│  • contactForm.php - Contact form                                │
│  • contact-result-view.php - Form results                        │
│  • login_v1.php - Admin login                                    │
│  • updateProducts.php - Product management                       │
│                                                                  │
│  INCLUDES (includes/)                                            │
│  • header.php - HTML head and page header                        │
│  • nav-header.php - Navigation menu                              │
│  • footer.php - Page footer                                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📁 File Structure

```
Mimi's Bakery/
│
├── 📄 index_v1.php                 # Home page (ENTRY POINT)
│
├── 📁 models/                      # Data Access Layer
│   └── ProductModel.php            # Product database operations
│
├── 📁 controllers/                 # Business Logic Layer
│   ├── ContactController.php      # Contact form processing
│   └── ProductController.php      # Product business logic
│
├── 📁 views/                       # Presentation Components
│   └── contact-result-view.php    # Contact form results
│
├── 📁 includes/                    # Shared Components
│   ├── header.php                 # Page header
│   ├── nav-header.php             # Navigation menu
│   └── footer.php                 # Page footer
│
├── 🛍️ CUSTOMER PAGES
│   ├── baked-products.php         # Product catalog
│   ├── contactForm.php            # Contact form
│   ├── processEmailForm.php       # Contact form processor
│   ├── orderProducts.php          # Order page
│   ├── process-order.php          # Order processor
│   └── order-confirmation.php     # Order confirmation
│
├── 🔐 ADMIN PAGES
│   ├── login_v1.php               # Admin login
│   ├── logout_v1.php              # Admin logout
│   ├── productInputForm.html      # Add product form
│   ├── insertProduct.php          # Insert product handler
│   ├── updateProducts.php         # Manage products page
│   ├── editProduct.php            # Edit product form
│   └── productUpdate.php          # Update product handler
│
├── 🔧 UTILITIES
│   ├── EmailHelper.php            # Email sending wrapper
│   ├── email_config.php           # Email configuration
│   ├── dbConnect1                 # Database connection
│
├── 🎨 STYLING
│   └── shared-styles.css          # Global styles
│
├── 📊 DATABASE
│   ├── schema.sql   # all tables used by this application
│
└── 📚 DOCUMENTATION
    ├── README.md                   # This file
    ├── MVC-IMPLEMENTATION.md       # MVC architecture guide
    ├── MVC-FLOW-DIAGRAM.md         # Visual flow diagrams
    ├── EMAIL_SETUP_GUIDE.md        # Email configuration
    ├── SETUP_GUIDE.md              # Installation guide
    └── INCLUDES-GUIDE.md           # Shared components guide
```

---

## 🔗 Page Relationships

### Navigation Flow from `index_v1.php`

```
index_v1.php
│
├─── NAVIGATION MENU (Always Visible)
│    ├── 🏠 Home → index_v1.php
│    ├── 🛍️ View Bakery → baked-products.php
│    ├── 📬 Contact Us → contactForm.php
│    ├── 🔐 Admin Login → login_v1.php (if not logged in)
│    ├── ⚙️ Admin Panel → login_v1.php (if logged in)
│    └── 🔓 Logout → logout_v1.php (if logged in)
│
├─── CALL-TO-ACTION BUTTONS
│    ├── "Browse Our Bakery Items" → baked-products.php
│    └── "Order Now" → orderProducts.php
│
└─── SESSION-BASED DISPLAYS
     ├── Login Status Display (if authenticated)
     └── Contact Success Message (if redirected from contact form)
```

### Product Catalog Flow

```
baked-products.php
│
├─── LOADS DATA (MVC Pattern)
│    ├── Requires: models/ProductModel.php
│    ├── Requires: controllers/ProductController.php
│    ├── Creates: ProductModel($pdo)
│    ├── Creates: ProductController($productModel)
│    └── Gets: $productController->getProductsForDisplay()
│
├─── DISPLAYS
│    ├── Product Grid (6 columns)
│    ├── Product Images
│    ├── Prices and Stock Info
│    └── Add to Order Controls
│
└─── USER ACTIONS
     ├── Adjust Quantities (JavaScript)
     ├── Add to Order (JavaScript cart)
     └── Place Order → Opens customer info modal
                       └── Submit → process-order.php
```

### Contact Form Flow

```
contactForm.php (Form Display)
     │
     ├─── FORM FIELDS
     │    ├── Full Name (required)
     │    ├── Email (required)
     │    ├── Phone (optional)
     │    ├── Subject (required)
     │    ├── Message (required, textarea)
     │    └── Honeypot Field (hidden)
     │
     └─── SUBMIT → processEmailForm.php
                       │
                       ├─── LOADS CONTROLLER
                       │    ├── Requires: EmailHelper.php
                       │    ├── Requires: controllers/ContactController.php
                       │    └── Calls: $controller->processContactForm($_POST)
                       │
                       ├─── VALIDATION RESULTS
                       │    │
                       │    ├─── ✅ SUCCESS
                       │    │    ├── Sends email to admin
                       │    │    ├── Sends copy to submitter
                       │    │    ├── Saves to session
                       │    │    └── Redirects to: index_v1.php?contact_success=1
                       │    │
                       │    ├─── 🚫 BOT DETECTED (honeypot)
                       │    │    ├── Logs security alert
                       │    │    ├── Sends alert email
                       │    │    └── Shows: Access Denied page (views/contact-result-view.php)
                       │    │
                       │    └─── ❌ VALIDATION ERROR
                       │         ├── Saves error to session
                       │         └── Redirects to: contactForm.php (shows error)
                       │
                       └─── 📭 DIRECT ACCESS (no POST data)
                            └── Shows: "No Form Data" message
```

### Admin Flow

```
login_v1.php
     │
     ├─── LOGIN FORM
     │    ├── Username Field
     │    └── Password Field
     │
     ├─── AUTHENTICATION
     │    ├── Checks against database
     │    ├── Password verification
     │    └── Sets $_SESSION['validUser'] = true
     │
     └─── LOGGED IN VIEW
          │
          ├─── QUICK ACTIONS
          │    ├── "Add New Product" → productInputForm.html
          │    ├── "Manage Products" → updateProducts.php
          │    └── "Logout" → logout_v1.php
          │
          └─── PROTECTED PAGES (require login)
               │
               ├── productInputForm.html
               │        └── Submit → insertProduct.php
               │                      └── Success → updateProducts.php
               │
               ├── updateProducts.php
               │        ├── Lists all products in table
               │        ├── [Edit] → editProduct.php
               │        │             └── Submit → productUpdate.php
               │        │                          └── Success → updateProducts.php
               │        │
               │        └── [Delete] → Confirms and deletes product
               │
               └── logout_v1.php
                        ├── Destroys session
                        └── Redirects to: index_v1.php
```

---

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for PHPMailer)

### Step 1: Clone Repository
```bash
git clone https://github.com/yourusername/mimis-bakery.git
cd mimis-bakery
```

### Step 2: Database Setup
```bash
# Import database schema
mysql -u your_username -p your_database < create_orders_tables.sql
```

### Step 3: Configure Database Connection
Edit `dbConnect1`:
```php
<?php
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
```

### Step 4: Configure Email (Optional)
Edit `email_config.php`:
```php
<?php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'your-email@gmail.com',
    'smtp_password' => 'your-app-password',
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'Mimi\'s Bakery',
    'admin_email' => 'admin@example.com'
];
```

### Step 5: Set Permissions
```bash
chmod 755 *.php
chmod 644 email_config.php
mkdir email_submissions
chmod 777 email_submissions
```

### Step 6: Access Application
Open browser: `http://localhost/index_v1.php`

---

## ⚙️ Configuration

### Email Configuration
- **With PHPMailer**: Install via Composer, configure `email_config.php`
- **Fallback Mode**: Saves emails to `email_submissions/` folder
- See `EMAIL_SETUP_GUIDE.md` for detailed instructions

### Database Tables

#### Products Table (`wdv341_products`)
- `product_id` - Primary key
- `product_name` - Product name
- `product_description` - Description
- `product_price` - Decimal price
- `product_image` - Image filename
- `product_inStock` - Inventory count
- `product_status` - Status text
- `product_category` - Category
- `ingredients` - Ingredients list
- `update_date` - Last updated
- `expired_date` - Expiration date

#### Users Table (for admin login)
- `username` - Admin username
- `password` - Hashed password

---

## 📖 Usage

### For Customers

1. **Browse Products**
   - Visit homepage → Click "Browse Our Bakery Items"
   - View products with images, prices, and stock levels
   - Add items to cart using quantity controls

2. **Place Orders**
   - Add items to cart
   - Click "Place Order"
   - Fill in customer information (name, phone)
   - Confirm order

3. **Contact Us**
   - Click "Contact Us" in navigation
   - Fill out form (all fields required except phone)
   - Submit to receive confirmation email

### For Administrators

1. **Login**
   - Click "Admin Login"
   - Enter username and password
   - Access admin dashboard

2. **Add Products**
   - Click "Add New Product"
   - Fill in all product details
   - Upload product image
   - Submit form

3. **Manage Products**
   - Click "Manage Products"
   - View all products in table
   - Edit or delete products as needed

4. **Logout**
   - Click "Logout" to end session

---

## 🔒 Security Features

### Implemented Security Measures

1. **SQL Injection Protection**
   - PDO prepared statements throughout
   - Parameter binding for all queries
   - No direct SQL concatenation

2. **Session Security**
   - Session-based authentication
   - Session validation on protected pages
   - Proper session destruction on logout

3. **Bot Protection**
   - Honeypot field in contact form
   - Hidden from users, visible to bots
   - Triggers access denied page

4. **Input Validation**
   - Server-side validation for all forms
   - Required field checks
   - Email format validation
   - XSS prevention with `htmlspecialchars()`

5. **Password Security**
   - Passwords hashed in database
   - Secure password verification

6. **Error Handling**
   - Try-catch blocks for database operations
   - Graceful error messages
   - Error logging without exposing details

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL** - Database management
- **PDO** - Database abstraction layer
- **PHPMailer** - Email sending (optional)

### Frontend
- **HTML5** - Markup
- **CSS3** - Styling
- **JavaScript** - Client-side interactivity
- **Responsive Design** - Mobile-friendly

### Architecture
- **MVC Pattern** - Separation of concerns
- **Session Management** - User authentication
- **Modular Includes** - Reusable components

---

## 📚 Documentation

### Available Documentation Files

- **`README.md`** (this file) - Complete project overview
---

## 🎯 Key Features Breakdown

### MVC Architecture
✅ **Separation of Concerns**
- Models handle database operations
- Controllers handle business logic
- Views handle presentation

✅ **Files Implementing MVC**
- `models/ProductModel.php` - Product data access
- `controllers/ContactController.php` - Contact form logic
- `controllers/ProductController.php` - Product business logic
- `baked-products.php` - View using MVC
- `processEmailForm.php` - Controller entry point

### Email System
✅ **Dual Email Sending**
- Sends email to admin
- Sends confirmation copy to customer

✅ **Graceful Fallback**
- Works with or without PHPMailer
- Saves to file if SMTP fails

### Product Management
✅ **Complete CRUD Operations**
- Create: `productInputForm.html` → `insertProduct.php`
- Read: `baked-products.php`, `updateProducts.php`
- Update: `editProduct.php` → `productUpdate.php`
- Delete: `updateProducts.php` (delete button)

✅ **8+ Product Fields**
- Name, Description, Price, Image
- Category, Ingredients, Stock Level
- Status, Update Date, Expired Date

---

## 🌟 Highlights

### Customer Experience
- Clean, modern interface
- Easy navigation
- Real-time cart updates
- Email confirmations
- Mobile-responsive design

### Admin Experience
- Simple authentication
- Intuitive product management
- Quick access to all functions
- Success/error feedback

### Code Quality
- MVC architecture
- DRY principle (Don't Repeat Yourself)
- Consistent naming conventions
- Comprehensive comments
- Security best practices

---

## 🤝 Contributing

This is an educational project for WDV341 course. Not currently accepting contributions.

---

## 📝 License

Educational project - no license specified.

---

## 📞 Support

For questions about this educational project, please refer to the documentation files listed above.

---

**Built with ❤️ for WDV341 Final Project**


