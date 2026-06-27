# Mtaa‑Connect – Security Design

## 1. Authentication & Session Management

- **Registration**: Passwords are hashed using `password_hash()` with `PASSWORD_DEFAULT` (bcrypt).
- **Login**: Passwords are verified using `password_verify()`.
- **Sessions**: PHP sessions are used to maintain login state.
- **Session Protection**: 
  - `session_start()` on all protected pages.
  - Redirect to `login.php` if `$_SESSION['user_id']` is not set.
  - `session_destroy()` on logout.

## 2. Authorization (Role‑Based Access)

- **User types**: `landlord`, `tenant` (and future `admin`).
- **Protected pages** check `$_SESSION['user_type']` before allowing access.
- Example: `if ($_SESSION['user_type'] !== 'landlord') { header('Location: login.php'); exit(); }`

## 3. Input Validation & Sanitization

- **Client‑side**: JavaScript validation (email format, password length, required fields).
- **Server‑side**:
  - `mysqli_real_escape_string()` to sanitize user inputs before SQL queries.
  - `filter_var($email, FILTER_VALIDATE_EMAIL)` for email validation.
  - Empty field checks before processing.
  - Password length minimum (6 characters).

## 4. SQL Injection Prevention

- All SQL queries use `mysqli_real_escape_string()` to escape special characters.
- Prepared statements will be implemented in later versions (Week 11+).

## 5. File Upload Security

- File type validation (MIME type check with `finfo_file()`).
- Allowed types: JPG, PNG, GIF, WEBP (images) / MP4, WEBM, OGG (videos).
- Files are stored outside the web root (optional, but we store in `/srv/http/mtaa/uploads/`).
- File names are randomized to prevent overwriting and guessing.

## 6. Password Storage

- Passwords are never stored in plain text.
- `password_hash()` with automatic salt generation.
- `password_verify()` for comparison.

## 7. Error Handling

- Errors are displayed to the user for debugging (development).
- Production will log errors instead of displaying them (`display_errors = Off`).

## 8. Future Security Enhancements

- HTTPS (SSL/TLS) for encrypted communication.
- Prepared statements (PDO) for all queries.
- Rate limiting on login attempts.
- CSRF tokens on forms.
- Two‑factor authentication (2FA).
- Regular security audits.
