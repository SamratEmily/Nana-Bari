# Backend Setup Guide - নানার বাড়ি

## Method 1: Using MySQL Command Line (Recommended)

### Step 1: Open Terminal/Command Prompt

### Step 2: Login to MySQL
```bash
mysql -u root -p
```
Enter password: `root`

### Step 3: Run the SQL Script
```bash
source /Users/samrathossen/Herd/Nana-bari/backend/database.sql
```

### Step 4: Verify Tables Created
```sql
USE nanabari;
SHOW TABLES;
```

You should see:
- awards
- deyalikha
- gallery
- kobita

---

## Method 2: Using phpMyAdmin

### Step 1: Open phpMyAdmin
Navigate to: `http://localhost/phpmyadmin`

### Step 2: Import SQL File
1. Click on **"Import"** tab
2. Click **"Choose File"**
3. Select: `/Users/samrathossen/Herd/Nana-bari/backend/database.sql`
4. Click **"Go"** button at the bottom

### Step 3: Verify
Click on `nanabari` database in the left sidebar and verify all 4 tables exist.

---

## Method 3: Using TablePlus or Similar GUI Tool

### Step 1: Create New Connection
- Host: `localhost`
- User: `root`
- Password: `root`

### Step 2: Run SQL Script
1. Open a new query window
2. Copy the contents of `backend/database.sql`
3. Paste and execute

---

## Method 4: Manual PHP Setup (Alternative)

If the above methods don't work, you can run the setup through your browser:

### Step 1: Access Setup Script
Navigate to: `http://localhost/Nana-bari/backend/setup.php`

**Note:** Make sure the database `nanabari` exists first. If not, create it manually:

```sql
CREATE DATABASE nanabari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Troubleshooting

### Error: "Access denied for user 'root'"
**Solution:** Check your MySQL credentials in `/backend/config.php`

### Error: "Unknown database 'nanabari'"
**Solution:** The database doesn't exist. Run this first:
```sql
CREATE DATABASE nanabari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Error: "Can't connect to MySQL server"
**Solution:** Make sure MySQL is running. If using:
- **XAMPP:** Start MySQL from XAMPP Control Panel
- **MAMP:** Start servers from MAMP
- **Herd:** MySQL should be running automatically
- **Laragon:** Start All from Laragon

---

## Verify Installation

After creating tables, verify by running:

```sql
USE nanabari;
SELECT * FROM gallery;
SELECT * FROM kobita;
SELECT * FROM awards;
SELECT * FROM deyalikha;
```

All queries should return empty results (0 rows) if tables are created successfully.

---

## Next Steps

1. ✅ Database and tables created
2. ➡️ Navigate to: `http://localhost/Nana-bari/login.html`
3. ➡️ Login with:
   - Email: `shahnoormaymuna@gmail.com`
   - Password: `shahnoormaymuna@gmail.com`
4. ➡️ Start using the application!

---

## Quick Command Reference

**Login to MySQL:**
```bash
mysql -u root -p
```

**Show all databases:**
```sql
SHOW DATABASES;
```

**Use nanabari database:**
```sql
USE nanabari;
```

**Show all tables:**
```sql
SHOW TABLES;
```

**Describe a table structure:**
```sql
DESCRIBE gallery;
DESCRIBE kobita;
DESCRIBE awards;
DESCRIBE deyalikha;
```

**Drop database (if you need to start fresh):**
```sql
DROP DATABASE nanabari;
```
Then run the database.sql script again.
