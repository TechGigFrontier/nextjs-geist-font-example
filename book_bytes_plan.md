# Book Bytes Website - Comprehensive Project Plan

## Project Overview
Create a responsive book summaries website using PHP 7.4 (procedural), MySQL, HTML, JavaScript, and Tailwind CSS with advanced text-to-speech functionality.

## Technical Requirements
- PHP 7.4 (Procedural)
- MySQL Database
- HTML5, CSS3, JavaScript
- Tailwind CSS for styling
- Text-to-Speech Web API
- Responsive design

## Database Configuration
- Host: localhost
- Username: root
- Password: (empty/default)
- Database: book_bytes_db

## Project Structure
```
book-bytes-php/
├── index.php (Home page)
├── book-summary.php (Book summary page)
├── login.php
├── register.php
├── forgot-password.php
├── admin/
│   ├── index.php (Admin dashboard)
│   ├── manage-books.php
│   ├── manage-users.php
│   └── add-book.php
├── includes/
│   ├── config.php (Database connection)
│   ├── functions.php (Common functions)
│   └── header.php, footer.php
├── assets/
│   ├── css/
│   ├── js/
│   │   ├── text-to-speech.js
│   │   ├── wave-animation.js
│   │   └── main.js
│   └── images/
├── uploads/ (Book images)
└── sql/
    └── database.sql (Database schema)
```

## Phase 1: Database Setup
1. Create MySQL database and tables:
   - users (id, username, email, password, role, created_at, status)
   - books (id, title, author, description, image, content, created_at, status)
   - book_sections (id, book_id, section_title, content, order_num)
   - takeaways (id, section_id, takeaway_text, example_text)

## Phase 2: Core PHP Development
1. Database connection and configuration
2. User authentication system (login, register, password reset)
3. Session management
4. Admin panel with CRUD operations
5. Book display and management functions

## Phase 3: Frontend Development
1. Responsive HTML structure with Tailwind CSS
2. Left-side hamburger menu (auto-hide)
3. Home page with book cards
4. Book summary pages with sections
5. Admin interface

## Phase 4: Text-to-Speech Implementation
1. JavaScript TTS integration with Web Speech API
2. Voice selection dropdown
3. Speed control (0.5x to 3.0x with +/- buttons)
4. Play, pause, stop controls
5. Wave visualization during playbook
6. Word highlighting with auto-scroll
7. Floating control panel (right side)

## Phase 5: Advanced Features
1. Auto-scrolling to keep reading text centered
2. Wave animation (active during speech, flat during pause)
3. Voice persistence (remember selected voice)
4. Reading progress tracking
5. Responsive design optimization

## Phase 6: Sample Data
Include sample books:
1. "The Habit of Winning" by Prakash Iyer
2. "Atomic Habits" by James Clear
3. "Think and Grow Rich" by Napoleon Hill
4. "The 7 Habits of Highly Effective People" by Stephen Covey
5. "Rich Dad Poor Dad" by Robert Kiyosaki

## Phase 7: Testing & Optimization
1. Cross-browser compatibility
2. Mobile responsiveness
3. Text-to-speech functionality across devices
4. Database operations testing
5. Security testing (SQL injection, XSS prevention)

## Key Features Implementation Details

### Text-to-Speech Control Panel
- Position: Fixed right side, floating
- Components: Voice selector, play/pause/stop, speed controls, wave display
- Auto-hide after voice selection
- Speed range: 0.5x, 1.0x, 1.5x, 2.0x, 2.5x, 3.0x

### Word Highlighting
- Real-time highlighting during speech
- Auto-scroll to keep current word in viewport center
- Smooth scrolling animation

### Admin Panel Features
- User management (view, disable/enable, delete)
- Book management (add, edit, delete, enable/disable)
- Section and takeaway management
- Image upload for books

### Security Measures
- Password hashing (password_hash/password_verify)
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars)
- Session security
- Admin role verification

## File Assets Required
- favicon.ico
- home.png (homepage intro image)
- Book thumbnail images
- Default placeholder images

## Email Configuration
- Use PHP mail() function for password reset
- Simple email template for reset links
- Basic validation and security measures
