# নানার বাড়ি (Nana-Bari) - Setup Guide

## Project Overview
নানার বাড়ি is a Bengali language web application for sharing photos, poems, memories, awards, and guestbook entries. It now includes a PHP backend with MySQL database and authentication.

## Backend Features
- ✅ **Authentication System** - Login with email and password
- ✅ **4 Database Tables** - Gallery, Kobita (Poems), Awards, Deyalikha (Guestbook)
- ✅ **REST API** - Full CRUD operations for all sections
- ✅ **Session Management** - Secure authentication checking
- ✅ **Bengali Language Support** - UTF-8 encoding throughout

## Setup Instructions

### 1. Database Setup

**Create the Database:**
```sql
CREATE DATABASE nanabari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Run the Setup Script:**
Navigate to `http://localhost/Nana-bari/backend/setup.php` in your browser. This will automatically create all 4 tables:
- `gallery` - For storing images (as base64 data)
- `kobita` - For poems and memories
- `awards` - For award distribution
- `deyalikha` - For guestbook entries

### 2. Login Credentials

**Default Admin Account:**
- Email: `shahnoormaymuna@gmail.com`
- Password: `shahnoormaymuna@gmail.com`

### 3. Project Structure

```
Nana-bari/
├── index.html              # Main application page (requires authentication)
├── login.html              # Login page
├── css/
│   └── style.css          # Enhanced modern styles with animations
├── js/
│   └── script.js          # JavaScript with PHP backend integration
├── assets/
│   └── bridge.jpeg        # Hero background image
└── backend/
    ├── config.php         # Database configuration
    ├── setup.php          # Database table creation script
    ├── login.php          # Login authentication endpoint
    ├── logout.php         # Logout endpoint
    ├── check-auth.php     # Authentication verification
    └── api/
        ├── gallery.php    # Gallery CRUD operations
        ├── kobita.php     # Poems CRUD operations
        ├── awards.php     # Awards CRUD operations
        └── deyalikha.php  # Guestbook CRUD operations
```

### 4. Database Configuration

If you need to change database credentials, edit `/backend/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'nanabari');
```

### 5. API Endpoints

All API endpoints are protected and require authentication.

**Gallery API** (`/backend/api/gallery.php`)
- `GET` - Fetch all images
- `POST` - Add new image (requires `image_data` as base64)
- `DELETE` - Remove image (requires `id`)

**Kobita API** (`/backend/api/kobita.php`)
- `GET` - Fetch all poems
- `POST` - Add new poem (requires `title`, `body`)
- `DELETE` - Remove poem (requires `id`)

**Awards API** (`/backend/api/awards.php`)
- `GET` - Fetch all awards
- `POST` - Add new award (requires `name`, `desc`)
- `DELETE` - Remove award (requires `id`)

**Deyalikha API** (`/backend/api/deyalikha.php`)
- `GET` - Fetch all guestbook entries
- `POST` - Add new entry (requires `name`, `message`)
- `DELETE` - Remove entry (requires `id`)

## UI Features

### Gallery
- **2 images per row** layout
- **Lightbox view** - Click any image to view in full screen
- **Delete functionality** - Trash icon appears on hover

### Enhanced Design
- 🎨 Modern color palette with teal and amber accents
- ✨ Smooth animations and transitions
- 🌟 Glassmorphism effects on cards
- 💫 Hover effects on buttons and cards
- 📱 Fully responsive design

## Running the Application

1. **Start your PHP server** (XAMPP, MAMP, Laragon, or Laravel Herd)
2. **Navigate to** `http://localhost/Nana-bari/login.html`
3. **Login** with the credentials above
4. **Start using** the application!

## Security Notes

⚠️ **Important**: This is a simple authentication system for demonstration. For production use:
- Hash passwords using `password_hash()` and `password_verify()`
- Store admin credentials in a separate users table
- Implement CSRF protection
- Use prepared statements for all queries (already implemented)
- Add rate limiting for login attempts

## Browser Support

- ✅ Chrome, Firefox, Safari, Edge (latest versions)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Credits

Built with ❤️ for preserving memories and sharing Bengali culture.
