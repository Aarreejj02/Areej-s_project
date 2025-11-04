🩷 AREEJ System

AREEJ System is a simple PHP-based web application built to manage users easily through an admin dashboard.
Admins can create, edit, and delete users, import users from Excel, and send automatic emails with passwords.

🚀 Features

🔐 Admin login system (secured with sessions)

👥 Manage users (Create / Edit / Delete)

📤 Import users from Excel file using PhpSpreadsheet

📧 Send users their passwords via email using PHPMailer

📊 Export dashboard data to Excel (via DataTables)

🕓 Track last login date

🎨 Clean responsive UI with HTML, CSS, and DataTables

🛠️ Technologies Used

PHP 8+

MySQL (via phpMyAdmin)

HTML5 / CSS3 / JavaScript

DataTables (for user management table)

PhpSpreadsheet (for reading Excel files)

PHPMailer (for sending emails)

⚙️ Installation

Install WAMP
 or XAMPP
.

Clone this repository inside your web root:

C:\wamp64\www\Areej


Create a database in phpMyAdmin (e.g., areej_db).

Import the provided SQL file (database.sql) if available.

Update your DB connection in:

includes/db.php


Install dependencies via Composer:

composer require phpoffice/phpspreadsheet
composer require phpmailer/phpmailer


Run your local server:

http://localhost/Areej/back_end/admin_login.php

📁 Project Structure
Areej/
│
├── back_end/
│   ├── admin_dashboard.php
│   ├── admin_create_dump.php
│   ├── admin_login.php
│___ front_end/  └── ...
│
├── includes/
│   ├── db.php
│   └── functions.php
│
├── css/
│   └── style.css
│
├── uploads/
│   └── users_dump.xlsx
│
├── vendor/
│   └── (composer libraries)
│
└── README.md

📨 Email Configuration

Inside admin_create_dump.php, update your Gmail credentials:

$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';


⚠️ Make sure you use an App Password from Google (not your real Gmail password).

💡 Notes

The uploaded Excel file should contain the following columns:

Name | User Number | Email


The system automatically generates random passwords for new users and emails them.

👩‍💻 Author

Areej Abu Kishk
📧 abukishkareej2@gmail.com
