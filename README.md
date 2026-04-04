# Online Library Management System (OLMS)

Welcome to the repository for our Level 1 Semester 2 Web Technologies Module Final Assignment! 

The Online Library Management System (OLMS) is a web-based application designed to allow students to view, borrow, and return books, while administrators can manage the library's inventory. 

## 🛠️ Technology Stack
* **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5.3.
* **Backend:** PHP 8.x 
* **Database:** MySQL (Relational)
* **UI/UX:** Custom Vintage Theme ("Casa Chromatica") with SweetAlert2 integrations.

## ✨ Features
* **User Login & Registration:** Secure authentication for students and admins.
* **Book Search & View:** Browse the catalog and find books easily.
* **Borrow & Return Books:** Real-time tracking of active transactions.
* **Admin Book Management:** Full CRUD operations for library inventory.

## 👥 Team & Modules
This project is divided into 5 core modules, ensuring a balanced workload across the team:

* **Member 1 (Mithesha Nuwanjana|@MNuwanjana):** Team Lead, Integration Architect & Core User Hub (Homepage, User Dashboard & Profile Settings)
* **Member 2 (Thiseni Nudara|@Thiseni-Nuda):** User Authentication Across all pages (Login, Signup, Logout & Session Management)
* **Member 3 (Name|@username):** Catalog & UI/UX Lead (Book Search, Discovery, Global CSS/Design System)
* **Member 4 (Amaya Senadheera|@Amaya-Senadheera):** Admin Panel & Inventory Management (CRUD operation on Book Inventory)
* **Member 5 (Shehara|@sanjanashehara0707):** Library Operations (Borrowing Logic, Return Actions, & Reviews)

## 📁 Project Structure
To prevent merge conflicts and keep our code organized, we are using a modular folder structure. Please ensure your files are placed in their respective directories:

```text
Online-Library-Management-System--OLMS-/
│
├── includes/               ← Member 1 (Shared UI & DB Connection)
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
│   ├── index.php
│   └── submit_review.php
│
├── assets/                 ← (Shared Styling & Scripts)
│   ├── css/style.css       <--(Member 3: UI / Frontend Lead)
│   ├── js/script.js        <--(Member 3: UI / Frontend Lead)
│   └── images/
│
├── database/
│   └── olms.sql            ← (The MySQL Database file)      
│
├── index.php               ← Member 1 (Main App Router)
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
6. **Run the App:** Open `http://localhost/Online-Library-Management-System--OLMS-/index.php` in your browser.

### 📌 Notes

* This project is built for academic purposes
* Designed using a modular monolithic architecture
* Focused on clean UI, usability, and structured backend logic

### Conclusion

The OLMS project demonstrates a complete full-stack web application integrating frontend design, backend logic, and relational database management into a cohesive system.
