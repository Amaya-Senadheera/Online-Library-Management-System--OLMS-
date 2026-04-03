# Online Library Management System (OLMS)

Welcome to the repository for our Level 1 Semester 2 Web Technologies Module Final Assignment! 

The Online Library Management System (OLMS) is a web-based application designed to allow students to view, borrow, and return books, while administrators can manage the library's inventory. 

## 🛠️ Technology Stack
* **Frontend:** HTML5, CSS3, JavaScript, Bootstrap (Responsive UI)
* **Backend:** PHP
* **Database:** MySQL (Relational Database)

## ✨ Features
* **User Login & Registration:** Secure authentication for students and admins.
* **Book Search & View:** Browse the catalog and find books easily.
* **Borrow & Return Books:** Real-time tracking of active transactions.
* **Admin Book Management:** Full CRUD operations for library inventory.

## 👥 Team & Modules
This project is divided into 5 core modules, ensuring a balanced workload across the team:

* **Member 1 (Mithesha Nuwanjana|@MNuwanjana):** Core User Hub (Homepage, User Dashboard & Profile Settings)
* **Member 2 Thiseni Nudara|@Thiseni-Nuda):** User Authentication (Login, Signup, Logout & Session Management)
* **Member 3 (Name|@username):** Catalog & UI Lead (Book Search, Discovery, Global CSS/Styling)
* **Member 4 (Amaya Senadheera|@Amaya-Senadheera):** Admin Panel (Inventory Management: Add, Edit, Delete Books)
* **Member 5 (Name|@username):** Library Operations (Borrowing Logic, Return Actions, & Reviews)

## 📁 Project Structure
To prevent merge conflicts and keep our code organized, we are using a modular folder structure. Please ensure your files are placed in their respective directories:

```text
olms-web-app/
│
├── includes/               ← (Shared UI & DB Connection)
│   ├── db.php
│   ├── header.php
│   └── footer.php
│
├── core/                   ← Member 1 (Dashboard & Profile)
│   ├── dashboard.php
│   └── profile.php
│
├── auth/                   ← Member 2 (Authentication)
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── catalog/                ← Member 3 (Search & Discovery)
│   ├── books.php
│   └── book_details.php
│
├── admin/                  ← Member 4 (Inventory Management)
│   ├── admin_index.php
│   ├── add_book.php
│   ├── manage_books.php
│   └── edit_book.php
│
├── operations/             ← Member 5 (Transactions & Logic)
│   ├── borrow_action.php
│   ├── return_action.php
│   └── submit_review.php
│
├── assets/                 ← (Shared Styling & Scripts)
│   ├── css/style.css       <--(Member 3: UI / Frontend Lead)
│   ├── js/script.js        <--(Member 3: UI / Frontend Lead)
│   └── images/
│
├── database/
│   └── olms.sql            ← (The single MySQL Database file)
│   └── seeds/              ← (The dummy data databases)
│       ├── 01_users_seed.sql       
│       ├── 02_books_seed.sql        
│       ├── 03_transactions_seed.sql      
│       └── 04_reviews_seed.sql        
│
├── index.php               ← (Main App Router)
└── README.md
```

### 🚀 Setup Instructions

1. **Install** XAMPP (or MAMP) on your computer.
2. **Clone the repository** directly into your XAMPP `htdocs/` folder (or `www/` for MAMP). 
   *(Make sure the folder is named exactly: `Online-Library-Management-System--OLMS-`)*
3. **Start** both Apache and MySQL from your XAMPP Control Panel.
4. **Open** `http://localhost/phpmyadmin` in your web browser.
5. **Import the Database:** Go to the "Import" tab and upload the `database/olms.sql` file. 
   *(Note: You do not need to create a database first! The script will automatically build the `olms` database and insert the default Admin account).*
6. **Run the App:** Open `http://localhost/Online-Library-Management-System--OLMS-/` in your browser.

## 🤝 Team Workflow (Fork & Pull)
1. **Fork** this main repository to your personal GitHub account.
2. Clone your personal fork to your computer.
3. Write your code and test it locally.
4. Push the changes to your personal fork.
5. Submit a **Pull Request (PR)** to this main repository.

## Page Template
Copy and paste this into the top and bottom of your PHP files.

Note on Paths: If your file is inside a folder (like auth/login.php), you must use ../ to find the includes folder. If your file is in the main root (like index.php), you do not need the ../.

For files inside folders (auth/, core/, catalog/, admin/, operations/):

```text
<?php 
// 1. Include the header (This handles DB connection, Session, Navbar and CSS)
include '../includes/header.php'; 
?>

//your code goes here

<?php 
// 2. Include the footer (This handles JavaScript and closes tags)
include '../includes/footer.php'; 
?>
```
