# DukaStock Kenya — SME Inventory & M-Pesa Sales Tracker

A lightweight, offline-first inventory and point-of-sale web application designed for small retail shops (*dukas*) in Kenya.

## The Problem
Many micro and small retailers in Kenya rely on paper notebooks to track daily inventory and sales, leading to unrecorded M-Pesa vs. cash transactions, frequent stockouts, and inaccurate profit tracking.

## Key Features
- **Real-Time Inventory Dashboard:** Tracks total products, estimated stock value in KES, and automatically flags items below their reorder threshold.
- **Atomic Sales Recording:** Logs customer purchases via **M-Pesa** or **Cash** using PDO database transactions while automatically deducting sold quantities from stock.
- **Revenue Breakdown:** Separates M-Pesa mobile money revenue from physical cash sales using SQL `JOIN` queries.
- **Offline-First Design:** Built with zero external CSS/JS dependencies so shops can run it locally without constant internet access.

## Tech Stack
- **Backend:** PHP 8 (PDO)
- **Database:** MySQL
- **Frontend:** HTML5, Custom Responsive CSS

## How to Run Locally
1. Clone this repository into your XAMPP `htdocs` folder (`C:\xampp\htdocs\duka-stock-tracker`).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`, create a database named `dukastock_db`, and import `database.sql`.
4. Visit `http://localhost/duka-stock-tracker/` in your browser.
5.