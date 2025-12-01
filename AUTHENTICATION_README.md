# Session-Based Authentication System

## Overview
A session-based authentication system has been implemented for the Parts Department application. All admin pages now require authentication before access.

## Login Credentials

Default Login:
- Username: admin
- Password: admin123

IMPORTANT: Change these credentials in production. Update them in php/session_handler.php:

```php
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
```

## How It Works

### Files Created/Modified

1. **php/session_handler.php** - Core authentication handler
   - Manages session variables
   - Validates credentials
   - Provides utility functions for checking login status

2. **login.php** - Login page
   - User login form
   - Credential validation
   - Redirects to Admin.php on successful login

3. **Admin.php** - Main admin menu (converted from Admin.html)
   - Requires authentication
   - Shows logged-in username
   - Includes logout button
   - Links to all admin functions

4. **logout.php** - Logout handler
   - Clears session data
   - Redirects to login page

5. **Protected Pages** (converted from HTML to PHP with auth checks):
   - Upload_Extracts.php
   - Materials_Admin.php
   - Upload_Scheduled_In.php
   - Upload_Vendors.php
   - Upload_Return_Form.php
   - Upload_Pending_Returns.php

6. **Backend Processing Files** (updated with auth checks):
   - php/Upload_Extracts.php
   - php/Upload_Scheduled_In.php
   - php/Upload_Vendors.php
   - php/Return_Forms.php
   - php/Upload_Pending_Returns.php

## Usage

1. **Initial Access:**
   - User navigates to login.php
   - Enters credentials (admin / admin123)
   - On successful login, redirects to Admin.php

2. **Protected Pages:**
   - Each protected page checks session with requireLogin()
   - If session invalid, redirects to login page
   - If valid, displays page content with navigation

3. **Logout:**
   - Click "Logout" button on any page
   - Session data cleared
   - Redirected to login page

## Database Note

No database table for authentication is used. Credentials are stored as PHP constants in memory. For a production system with multiple users, consider:
- Adding a users table to the database
- Using password hashing (password_hash/password_verify)
- Implementing role-based access control

## Session Security Features

- Session start on every protected page
- Automatic redirection for unauthenticated users
- Session variables cleared on logout
- Login timestamp recorded for audit purposes

## Accessing the Application

1. Start with: http://localhost/Parts/login.php
2. Or modify index.html to redirect to login.php for admin access

## File Redirects

All HTML files linked from Admin menu have been converted to PHP:
- Upload_Extracts.html -> Upload_Extracts.php
- Materials_Admin.html -> Materials_Admin.php
- Upload_Scheduled_In.html -> Upload_Scheduled_In.php
- Upload_Vendors.html -> Upload_Vendors.php
- Upload_Return_Form.html -> Upload_Return_Form.php
- Upload_Pending_Returns.html -> Upload_Pending_Returns.php

Original HTML files can be kept or removed as they are no longer referenced.
