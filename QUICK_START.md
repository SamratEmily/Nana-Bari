# 🎯 Quick Start Guide - নানার বাড়ি

## ✅ Database Setup Complete!

All 5 tables have been created successfully:
- ✅ `gallery` - For images
- ✅ `kobita` - For poems
- ✅ `awards` - For awards  
- ✅ `deyalikha` - For guestbook
- ✅ `users` - For authentication (New!)

---

## 🔐 Login Credentials

**Email:** `shahnoormaymuna@gmail.com`  
**Password:** `shahnoormaymuna@gmail.com`

---

## 🌐 Your URLs (Laravel Herd)

Since you're using Laravel Herd, use these URLs:

1. **Test Login Page (Debug):**  
   👉 http://nana-bari.test/backend/test-login.php
   
2. **Main Login Page:**  
   👉 http://nana-bari.test/login.html
   
3. **Main App (after login):**  
   👉 http://nana-bari.test/index.html

---

## 🚀 How to Login

### Method 1: Use the Test Page (Recommended for debugging)

1. Open: **http://nana-bari.test/backend/test-login.php**
2. You'll see:
   - Configuration tests ✅
   - Database connection status ✅
   - Two login buttons to test
3. Click "**Test Login (AJAX/JSON)**" - This tests the same login method as your app

### Method 2: Use the Main Login Page

1. Open: **http://nana-bari.test/login.html**
2. Enter credentials:
   - Email: `shahnoormaymuna@gmail.com`
   - Password: `shahnoormaymuna@gmail.com`
3. Click "লগইন করুন"

---

## 🔧 If Login Still Doesn't Work

### Check 1: Browser Console

1. Open browser DevTools (F12)
2. Go to Console tab
3. Try to login
4. Check for errors (Red text)

### Check 2: Network Tab

1. Open browser DevTools (F12)
2. Go to Network tab  
3. Try to login
4. Click on "login.php" request
5. Check:
   - **Request Payload** - Should show email and password
   - **Response** - Should show `{"success":true,...}` or error message

### Check 3: PHP Errors

Check your PHP error logs for any issues.

---

## 📝 Common Issues & Solutions

### Issue: "Invalid email or password"
**Solution:** Make sure you're copying the exact credentials (no extra spaces):
```
shahnoormaymuna@gmail.com
shahnoormaymuna@gmail.com
```

### Issue: Page shows blank or PHP errors
**Solution:** Check PHP is running:
```bash
php -v
```

### Issue: Login seems to work but redirects to login again
**Solution:** This is a session issue. The test page will help diagnose this.

### Issue: CORS errors in console
**Solution:** The updated config.php now has CORS headers. Refresh the page.

---

## 🎨 Features Ready to Use

Once logged in, you can:

1. **Gallery (ছবি)** 
   - Upload images
   - View in 2-column layout  
   - Click to view full size (lightbox)
   - Delete images

2. **Poems (কবিতা/স্মৃতিকথা)**
   - Add poems with title
   - View all poems
   - Delete poems

3. **Awards (পুরস্কার বিতরণ)**
   - Add awards with description
   - View all awards
   - Delete awards

4. **Guestbook (দেয়ালিকা)**
   - Add messages
   - View all messages
   - Delete messages

---

## 📂 File Structure

```
Nana-bari/
├── login.html              ← Start here
├── index.html              ← Main app (requires login)
├── backend/
│   ├── config.php          ← Database & auth config
│   ├── login.php           ← Login endpoint
│   ├── test-login.php      ← Debug page (use this!)
│   ├── database.sql        ← SQL script (already run ✅)
│   └── api/
│       ├── gallery.php
│       ├── kobita.php
│       ├── awards.php
│       └── deyalikha.php
```

---

## 🎯 Next Steps

1. Open: **http://nana-bari.test/backend/test-login.php**
2. Check all tests pass ✅
3. Click "Test Login (AJAX/JSON)" button
4. Should redirect to main app
5. Start using! 🎉

---

## 💡 Need Help?

If you see any errors:
1. Check the test page first
2. Look at browser console (F12)
3. Check PHP error logs
4. Verify database credentials in `backend/config.php`
