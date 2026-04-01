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
* **Member 2 (Name|@username):** User Authentication (Login, Signup, Logout & Session Management)
* **Member 3 (Name|@username):** Catalog & UI Lead (Book Search, Discovery, Global CSS/Styling)
* **Member 4 (Name|@username):** Admin Panel (Inventory Management: Add, Edit, Delete Books)
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
│
├── index.php               ← (Main App Router)
└── README.md
```

## 🚀 Setup Instructions
1. **Install** XAMPP (or MAMP).
2. **Place project** inside the `htdocs/` folder (or `www/`).
3. **Open** `http://localhost/phpmyadmin` in your browser.
4. **Create database** named `olms`.
5. **Import** the `database/olms.sql` file.
6. **Run:** Open `http://localhost/olms-web-app/` in your browser.

## 🤝 Team Workflow (Fork & Pull)
1. **Fork** this main repository to your personal GitHub account.
2. Clone your personal fork to your computer.
3. Write your code and test it locally.
4. Push the changes to your personal fork.
5. Submit a **Pull Request (PR)** to this main repository.
