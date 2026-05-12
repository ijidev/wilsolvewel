# Wilsolvewel Engineering Portal

Modern technical infrastructure for Wilsolvewel Engineering Nigeria Limited.

## Overview
This project is an industrial engineering portal designed to manage machinery specifications, procurement requests, and technical support inquiries. It has been transformed from a static site into a dynamic PHP/MySQL-backed application.

## Core Features
- **Dynamic Inquiry System**: All forms (Technical Specs, Contact) are saved to a central database.
- **Admin Terminal**: Secure dashboard for managing inquiries and system health.
- **SMTP Gateway**: Admin-configurable mail server settings for automated notifications.
- **Industrial Design**: Unified high-precision UI themed with Yellow/Black industrial aesthetics.

## Technology Stack
- **Backend**: PHP 8.x, MySQL
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Components**: Modular JS-based header, footer, and admin navigation.

## Installation
1. Upload files to your PHP-enabled web server.
2. Create a `.env` file in the root directory (refer to `.env.example` or create from scratch).
3. Configure database credentials and SMTP defaults in `.env`.
4. Run `setup_db.php` once to initialize the database schema.
5. Log in to the Admin Terminal to refine SMTP settings.
