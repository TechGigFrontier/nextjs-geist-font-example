<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';
$step = 'email'; // email, token, or success

// Handle password reset token
if (isset($_GET['token'])) {
    $token = sanitize($_GET['token']);
    $user = verifyResetToken($token);
    
    if ($user) {
        $step = 'reset';
    } else {
        $error = 'Invalid or expired reset token.';
    }
}

if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        if (isset($_POST['email'])) {
            // Step 1: Send reset email
            $email = sanitize($_POST['email']);
            
            if (empty($email)) {
                $error = 'Please enter your email address.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
                // Check if user exists
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND status = 'active'");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $token = generateResetToken($email);
                    $resetLink = SITE_URL . '/forgot-password.php?token=' . $token;
                    
                    $subject = 'Password Reset - ' . SITE_NAME;
                    $message = "
                    <html>
                    <body>
                        <h2>Password Reset Request</h2>
                        <p>You have requested to reset your password for " . SITE_NAME . ".</p>
                        <p>Click the link below to reset your password:</p>
                        <p><a href='{$resetLink}' style='background-color: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                        <p>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p>{$resetLink}</p>
                        <p>This link will expire in 1 hour.</p>
                        <p>If you didn't request this reset, please ignore this email.</p>
                        <br>
                        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
                    </body>
                    </html>
                    ";
                    
                    if (sendEmail($email, $subject, $message)) {
                        $success = 'Password reset instructions have been sent to your email address.';
                        $step = 'sent';
                    } else {
                        $error = 'Failed to send email. Please try again later.';
                    }
                } else {
                    // Don't reveal if email exists or not for security
                    $success = 'If an account with that email exists, password reset instructions have been sent.';
                    $step = 'sent';
                }
            }
        } elseif (isset($_POST['new_password'])) {
            // Step 2: Reset password
            $token = sanitize($_POST['token']);
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if (empty($newPassword) || empty($confirmPassword)) {
                $error = 'Please fill in all fields.';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Password must be at least 6 characters long.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } else {
                if (resetPassword($token, $newPassword)) {
                    $success = 'Your password has been reset successfully. You can now log in with your new password.';
                    $step = 'success';
                } else {
                    $error = 'Failed to reset password. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Forgot Password';
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <?php if ($step === 'email' || $step === 'sent'): ?>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Forgot your password?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            <?php elseif ($step === 'reset'): ?>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your new password below.
            </p>
            <?php else: ?>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Password Reset Complete
            </h2>
            <?php endif; ?>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($step === 'email'): ?>
        <!-- Email Form -->
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                       placeholder="Enter your email address"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    Send Reset Instructions
                </button>
            </div>
        </form>
        
        <?php elseif ($step === 'reset'): ?>
        <!-- Password Reset Form -->
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="space-y-4">
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                        New Password
                    </label>
                    <input id="new_password" 
                           name="new_password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Enter new password">
                    <p class="mt-1 text-xs text-gray-500">At least 6 characters</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirm New Password
                    </label>
                    <input id="confirm_password" 
                           name="confirm_password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Confirm new password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    Reset Password
                </button>
            </div>
        </form>
        
        <?php endif; ?>
        
        <div class="text-center space-y-2">
            <a href="login.php" class="font-medium text-black hover:underline">
                Back to Login
            </a>
            <br>
            <a href="register.php" class="font-medium text-gray-600 hover:underline">
                Don't have an account? Sign up
            </a>
        </div>
        
        <?php if ($step === 'sent'): ?>
        <!-- Instructions -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-sm font-medium text-blue-900 mb-2">What's next?</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Check your email inbox (and spam folder)</li>
                <li>• Click the reset link in the email</li>
                <li>• The link will expire in 1 hour</li>
                <li>• If you don't receive the email, try again</li>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if ($step === 'success'): ?>
        <!-- Success Actions -->
        <div class="space-y-4">
            <a href="login.php" 
               class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                Go to Login
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($step === 'reset'): ?>
<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    // Remove existing indicator
    const existingIndicator = document.querySelector('.password-match');
    if (existingIndicator) {
        existingIndicator.remove();
    }
    
    if (confirmPassword.length > 0) {
        const indicator = document.createElement('div');
        indicator.className = 'password-match mt-1 text-xs';
        
        if (password === confirmPassword) {
            indicator.innerHTML = '<span class="text-green-600">Passwords match</span>';
        } else {
            indicator.innerHTML = '<span class="text-red-600">Passwords do not match</span>';
        }
        
        this.parentNode.appendChild(indicator);
    }
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
