# Multi-Factor Authentication System

### A Secure, Production-Ready MFA Web Application

**University of Messina
**Student:** Ali Muhammad Saif (ID: 546477)


---

## 1. Project Overview

This project is a fully functional Multi-Factor Authentication (MFA) web application designed to go beyond traditional username and password authentication.

The system implements two independent layers of security to verify user identity:

1. Email and password authentication
2. A time-limited One-Time Password (OTP) sent via email

In addition, users may authenticate using Google Sign-In (OAuth 2.0), which removes the need for local password storage entirely.

The application was developed from scratch using pure PHP 8 and a custom-built MVC architecture. No frameworks such as Laravel or Symfony were used. Every security mechanism is explicitly implemented, fully visible, and auditable.

This project was developed as the practical component of the System Security module at the Università degli Studi di Messina, with the objective of understanding authentication security by building it from the ground up.

---

## 2. Core Features

### Authentication Features

* Email and Password Login
  Credentials are verified against bcrypt-hashed passwords stored in MySQL.

* Email OTP (Second Factor)
  A fresh six-digit OTP is generated after successful password verification.

* Five-Minute OTP Expiry
  Expiration is enforced server-side using timestamp validation.

* Three-Attempt Lockout
  After three incorrect OTP submissions, the OTP record is deleted and the session is reset.

* Google Sign-In (OAuth 2.0)
  Full Google Single Sign-On integration. No local password is stored for these users.

---

### Security Protections

* Rate Limiting
  Maximum of three OTP requests per IP address within five minutes.
  Excess requests return HTTP 429 (Too Many Requests).

* Route Guards
  AuthGuard and GuestGuard middleware ensure protected routes cannot be accessed without proper authentication.

* Registration Rollback
  If OTP email delivery fails during registration, the user account is immediately deleted.

* Last Login Display
  The dashboard shows the previous login timestamp to help users detect suspicious activity.

* Generic Error Messages
  Login and registration responses never reveal whether an email address is registered.

---

## 3. Authentication Flows

### 3.1 Standard Login Flow

1. User enters email and password.
2. Password is verified using bcrypt.
3. If valid:

   * A cryptographically secure six-digit OTP is generated using `random_int()`.
   * The OTP is hashed using bcrypt.
   * The hashed OTP is stored in the database with a five-minute expiry timestamp.
   * The plaintext OTP is sent to the user's email.
4. User submits OTP.
5. The system performs four sequential security checks:

   * OTP record exists
   * OTP is within the five-minute validity window
   * Fewer than three failed attempts
   * Hash matches using bcrypt verification
6. If all checks pass:

   * OTP is deleted
   * Session is created
   * User is redirected to the dashboard

---

### 3.2 Google Sign-In Flow

1. User selects “Sign in with Google”.
2. User is redirected to Google’s authentication page.
3. Google returns a short-lived authorization code.
4. The server exchanges the code for:

   * Access token
   * Verified email address
   * Google ID
5. If user exists → login
   If new user → register automatically
6. Session is created immediately. No OTP required.

---

## 4. Technology Stack

| Layer            | Technology                      | Purpose                                   |
| ---------------- | ------------------------------- | ----------------------------------------- |
| Backend          | PHP 8                           | Built-in CSPRNG, bcrypt, PDO              |
| Architecture     | Custom MVC                      | Full control and transparency             |
| Database         | MySQL with PDO                  | Prepared statements prevent SQL injection |
| Password Hashing | bcrypt (`password_hash()`)      | Adaptive and secure password storage      |
| OTP Generation   | `random_int()` + bcrypt         | Cryptographically secure generation       |
| Email Transport  | Native SMTP over STARTTLS       | Encrypted email transport                 |
| SSO              | Google OAuth 2.0                | Passwordless authentication               |
| Frontend         | HTML5, CSS3, Vanilla JavaScript | Minimal attack surface                    |

---

## 5. Security Architecture

### OTP Verification Logic

Every OTP submission passes through four mandatory checks:

1. Record exists
   Prevents manipulation of non-existent sessions.

2. Within five-minute window
   Limits usefulness of intercepted OTPs.

3. Under three failed attempts
   Prevents brute-force attacks.
   Probability: 3 attempts out of 10,000,000 possible combinations.

4. Hash verification
   Even in case of database breach, plaintext OTP cannot be retrieved.

All four checks must pass for successful authentication.

---

## 6. Threat Model Summary

| Threat                 | Mitigation                        | Residual Risk               |
| ---------------------- | --------------------------------- | --------------------------- |
| OTP Brute Force        | 3 attempts + 5-minute expiry      | Negligible                  |
| OTP Replay             | OTP deleted after successful use  | None                        |
| Database Password Leak | bcrypt hashing                    | Low (weak passwords only)   |
| OTP Database Leak      | Only bcrypt hashes stored         | Negligible                  |
| SQL Injection          | PDO prepared statements           | None                        |
| XSS                    | `htmlspecialchars()` sanitization | Low                         |
| Username Enumeration   | Generic responses                 | Negligible                  |
| Email Spam             | IP-based rate limiting            | Low                         |
| Session Hijacking      | Route guards                      | Low (development HTTP only) |

---

## 7. Known Development Limitations

The following limitations are intentionally scoped for academic purposes:

* No HTTPS (development environment only)
* No CSRF tokens
* Session-based rate limiting (not distributed)
* No progressive login lockout for password failures

Each has a documented production solution.

---

## 8. Installation Guide

### Requirements

* Apache 2.4+ with mod_rewrite
* PHP 8.0+ (pdo, pdo_mysql, openssl, curl, sockets)
* MySQL 5.7+ or MariaDB 10.3+
* Mailtrap account (for development email testing)
* Google Cloud Console project with OAuth credentials

---

### Setup Steps

1. Clone repository

```
git clone https://github.com/your-username/system-social-media-mfa.git
cd system-social-media-mfa
```

2. Enable mod_rewrite

```
sudo a2enmod rewrite
sudo systemctl restart apache2
```

3. Create database

```
CREATE DATABASE system_social_media_mfa;
```

Import `database/schema.sql`.

4. Configure environment

```
cp .env.example .env
```

Fill in database, Google OAuth, and Mailtrap credentials.

5. Visit

```
http://localhost/system-social-media-mfa/
```

---

## 9. Project Structure

The application follows a structured MVC architecture:

* app/

  * controllers
  * models
  * services
  * middleware
  * config
  * utils

* database/

* public/

* routes/

* views/

* storage/

* index.php (entry point)

* .env (environment configuration)

All authentication logic is modular, layered, and separated by responsibility.

---

## 10. Testing

The system was tested against:

* 10 functional test cases
* 8 security test cases

All tests passed successfully, including:

* Expired OTP rejection
* OTP reuse prevention
* SQL injection attempts
* Rate limiting enforcement
* Unauthorized route access
* OTP database inspection

---

## 11. Future Enhancements

* TOTP (Google Authenticator / RFC 6238)
* WebAuthn / FIDO2 support
* Additional OAuth providers
* Redis-based distributed rate limiting
* CSRF token protection
* Progressive login lockout
* Structured audit logging
* Administrative dashboard
* HTTPS enforcement with HSTS
* MFA management interface

---

University: Università degli Studi di Messina
Department: MIFT
Module: System Security
Academic Year: 2024–2025
Student: Ali Muhammad Saif (ID: 546477)
Supervisors: Prof. Massimo Villari · Prof. Armando Ruggeri

---

