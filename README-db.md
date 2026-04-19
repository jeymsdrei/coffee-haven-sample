# Coffee Haven — Database setup

This file explains how to create/import the MySQL database used by the site.

Files added:
- `database.sql` — schema + sample data

Import using phpMyAdmin
- Open `http://localhost/phpmyadmin` (XAMPP) and import `database.sql` from the Import tab.

Import using MySQL CLI (PowerShell)
- If your XAMPP MySQL user is `root` with no password (default), run:

```powershell
mysql -u root < "c:\xampp\htdocs\coffee_haven\database.sql"
```

- If your root has a password, use:

```powershell
mysql -u root -p < "c:\xampp\htdocs\coffee_haven\database.sql"
```

Security notes
- The sample `database.sql` uses `SHA2(...,256)` only for demo inserts. For production, create user accounts via your application and use a strong hashing function (e.g. PHP's `password_hash`).

Next steps
- After importing, you can use `login/db_connect.php` (example) to connect from PHP.
