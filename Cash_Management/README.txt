Barmageyat Cash Flow Control System v1.3
========================================

Purpose
-------
A simple PHP + MySQL system for shared hosting to track Barmageyat company cash flow between partners.

Included Modules
----------------
1. Partner login
2. Expense registration with receipt upload
3. Income / revenue registration with attachment upload
4. Admin approval / rejection
5. Dashboard KPIs: income, expenses, net cash, pending approvals
6. Partner balance calculation
7. Reports by expense category and income customer
8. CSV export
9. User access levels: admin, partner, viewer
10. Category management
11. Barmageyat logo included

Hosting Requirements
--------------------
- PHP 7.4 or above
- MySQL / MariaDB
- Shared hosting with file upload permission

Installation Steps on Mocha Shared Hosting
------------------------------------------
1. Upload all files to your domain/subdomain folder.
   Example: cashflow.barmageyat.com

2. Create a MySQL database from hosting control panel.
   Suggested database name: barmageyat_cashflow

3. Import database.sql using phpMyAdmin.

4. Edit config.php and update:
   $db_name
   $db_user
   $db_pass

5. Ensure uploads folder has write permission.
   Usually 755 works. If upload fails, try 775.

Default Login
-------------
Admin:
Email: admin@barmageyat.com
Password: admin123

Partner:
Email: mahmoud@barmageyat.com
Password: admin123

Important Security Notes
------------------------
1. Change the default passwords immediately after installation.
2. Do not keep database.sql publicly accessible after setup. Delete it from the hosting folder.
3. Use HTTPS.
4. This is a lightweight startup cash tracker, not a full accounting/ERP system.

Version Scope
-------------
v1.0: Expenses and partner registration
v1.1: Income/revenue module and net cash dashboard
v1.2: Partner balance calculation
v1.3: Reports, CSV export, invoice/receipt upload, multi-currency field
