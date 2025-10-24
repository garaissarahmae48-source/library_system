Library System (PHP + MySQL) - Quick setup

1. Start XAMPP Apache and MySQL.
2. Import 'library_sql.sql' in phpMyAdmin to create database and tables.
3. Place the 'library_system' folder in your XAMPP htdocs (e.g., C:\xampp\htdocs\library_system)
4. Open http://localhost/library_system/ in your browser.
5. Create Members and Books first, then add Loans.

Notes:
- DB credentials are in db.php (default root with no password).
- 'Mark as Returned' button available in Loans list to set returned_at to today's date.
