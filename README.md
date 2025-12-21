# MiniDo - Task Manager Application

A minimalistic PHP-based todo list and task manager application.

## Project Structure

```
Todo-College-Project/
├── index.php                 # Main entry point (redirects to app/index.php)
├── app/
│   ├── config.php           # Database configuration
│   ├── index.php            # Landing page
│   ├── auth.php             # Login & Registration page
│   ├── dashboard.php        # Main task dashboard
│   ├── add_task.php         # Add task endpoint
│   ├── complete_task.php    # Mark task as complete endpoint
│   ├── delete_task.php      # Delete task endpoint
│   ├── logout.php           # Logout endpoint
│   └── y.sql                # Database schema
├── styles/                   # CSS files (optional - using Tailwind CSS)
└── assets/                   # Images, fonts, etc.
```

## Features

- **User Authentication**: Register and login system
- **Task Management**: Create, complete, and delete tasks
- **Responsive Design**: Modern UI with Tailwind CSS
- **Session Management**: Secure user sessions
- **Database**: MySQL/MariaDB with prepared statements for SQL injection prevention

## Requirements

- PHP 7.2+
- MySQL 5.7+ or MariaDB 10.1+
- Web server (Apache, Nginx, etc.)

## Setup Instructions

### 1. Database Setup

1. Open your MySQL client (phpMyAdmin, MySQL Command Line, etc.)
2. Create the database and tables by running the SQL script:

```sql
CREATE DATABASE IF NOT EXISTS todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE todo_app;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(50) NOT NULL,
  lastname VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  is_completed TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Update Database Credentials

Edit `app/config.php` and update the database credentials if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'ss44mm55');    // Change this
define('DB_NAME', 'todo_app');
```

### 3. Place Project in Web Root

Place the `Todo-College-Project` folder in your web server's root directory:

- Apache: `htdocs/`
- Nginx: `/var/www/html/`
- XAMPP/WAMP: `www/` or `htdocs/`

### 4. Access the Application

Open your browser and navigate to:

```
http://localhost/Todo-College-Project/
```

## Usage

1. **Create Account**: Click "Get Started" and sign up with your information
2. **Add Tasks**: Enter a task title and click "Add Task"
3. **Complete Tasks**: Click "Complete" to mark a task as done
4. **Delete Tasks**: Click "Delete" to remove a task
5. **Logout**: Click the "Logout" button in the sidebar

## Key Improvements from Original

✅ **PHP-Only**: All pages are now PHP files with embedded HTML (no separate HTML files)
✅ **Better Structure**: Proper separation of concerns with `config.php`, API endpoints
✅ **Enhanced Security**: Password hashing, SQL injection prevention with prepared statements
✅ **Improved UI**: Modern, responsive design with better styling
✅ **Better Error Handling**: Proper session validation and error messages
✅ **Fixed Redirects**: Consistent redirect logic across all pages
✅ **Proper Form Handling**: Server-side form processing instead of AJAX complications

## Security Notes

- All database queries use prepared statements
- Passwords are hashed using `PASSWORD_DEFAULT`
- Session-based authentication for protected pages
- CSRF protection recommended for production use
- Input sanitization with `htmlspecialchars()` in output

## File Descriptions

| File                | Purpose                                        |
| ------------------- | ---------------------------------------------- |
| `config.php`        | Database connection and session initialization |
| `index.php`         | Landing page with welcome message              |
| `auth.php`          | Combined login and registration page           |
| `dashboard.php`     | Main task management interface                 |
| `add_task.php`      | Handles new task creation                      |
| `complete_task.php` | Marks a task as completed                      |
| `delete_task.php`   | Deletes a task                                 |
| `logout.php`        | Destroys user session                          |

## Troubleshooting

**Database Connection Error**

- Check `config.php` credentials
- Ensure MySQL server is running
- Verify database and tables exist

**Can't Login**

- Ensure you've registered first
- Check that email is exactly correct (case-sensitive)
- Reset by deleting user record from database

**Tasks Not Showing**

- Check browser cookies are enabled
- Clear browser cache
- Verify user is logged in (check session)

## Production Deployment

For production use, consider:

1. Using environment variables for database credentials
2. Implementing CSRF tokens
3. Adding input validation with regex patterns
4. Setting up HTTPS
5. Implementing rate limiting for login attempts
6. Adding backup and recovery features
