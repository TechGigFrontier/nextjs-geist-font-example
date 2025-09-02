<?php
require_once 'config.php';

// Get all active books
function getAllBooks() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE status = 'active' ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get book by ID
function getBookById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get book sections
function getBookSections($bookId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM book_sections WHERE book_id = ? ORDER BY order_num ASC");
    $stmt->execute([$bookId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get section takeaways
function getSectionTakeaways($sectionId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM takeaways WHERE section_id = ? ORDER BY order_num ASC");
    $stmt->execute([$sectionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// User authentication
function authenticateUser($email, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Register new user
function registerUser($username, $email, $password) {
    $pdo = getDBConnection();
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        return false; // User already exists
    }
    
    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $email, $hashedPassword]);
}

// Generate password reset token
function generateResetToken($email) {
    $pdo = getDBConnection();
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);
    
    return $token;
}

// Verify reset token
function verifyResetToken($token) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Reset password
function resetPassword($token, $newPassword) {
    $pdo = getDBConnection();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    return $stmt->execute([$hashedPassword, $token]);
}

// Send email (simple PHP mail function)
function sendEmail($to, $subject, $message) {
    $headers = "From: noreply@bookbytes.com\r\n";
    $headers .= "Reply-To: noreply@bookbytes.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Upload file
function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return false;
    }
    
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = UPLOAD_PATH . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $fileName;
    }
    
    return false;
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Truncate text
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Get menu items for navigation
function getMenuItems() {
    $books = getAllBooks();
    $menuItems = [];
    
    foreach ($books as $book) {
        $menuItems[] = [
            'title' => $book['title'],
            'author' => $book['author'],
            'url' => 'book-summary.php?id=' . $book['id']
        ];
    }
    
    return $menuItems;
}
?>
