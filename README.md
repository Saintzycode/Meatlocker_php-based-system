Meatlocker

A PHP-based inventory and order management system for meat products.

Overview

Meatlocker is a web application designed to help administrators and customers manage meat product transactions efficiently. It provides an admin dashboard for handling product inventory, order processing, stock updates, profit monitoring, and user account management. The system ensures that the platform remains updated, accurate, and ready for daily business operations.

Features
Administrator Features

Add, update, and remove meat products

Manage product inventory and stock levels

Process and review customer orders

Update stock after purchases

Monitor daily, weekly, and monthly profits

Manage user accounts and permissions

Access an admin dashboard displaying KPIs and system metrics

Customer Features

Register and log in

Browse available meat products

Add products to cart

Place orders and track order status

View order history

System Architecture
1. Technologies Used

Backend: PHP (Core + OOP)

Frontend: HTML, CSS, JavaScript

Database: MySQL

Server Environment: XAMPP / WAMP / LAMP

Version Control: Git
/meatlocker
│── /config
│     └── database.php
│── /classes
│     ├── Product.php
│     ├── User.php
│     ├── Order.php
│     └── Admin.php
│── /controllers
│     ├── productController.php
│     ├── orderController.php
│     └── userController.php
│── /views
│     ├── admin/
│     ├── customer/
│     └── shared/
│── /public
│     ├── css/
│     ├── js/
│     └── images/
│── index.php
└── README.md
Installation
Requirements
PHP 8+
MySQL 5.7+
Apache server (XAMPP/WAMP/LAMP)

Steps

Clone the repository

git clone https://github.com/yourusername/meatlocker.git


Import the SQL file into MySQL

Configure database settings in /config/database.php

Run the project via Apache server

Access the system through
http://localhost/meatlocker/
