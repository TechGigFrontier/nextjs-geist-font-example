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

if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error = 'Please fill in all fields.';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters long.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            if (registerUser($username, $email, $password)) {
                $success = 'Account created successfully! You can now log in.';
                // Clear form data
                $_POST = [];
            } else {
                $error = 'Username or email already exists. Please choose different ones.';
            }
        }
    }
}

$pageTitle = 'Register';
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="login.php" class="font-medium text-black hover:underline">
                    sign in to your existing account
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($success); ?>
                <div class="mt-2">
                    <a href="login.php" class="font-medium underline">Click here to login</a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Username
                    </label>
                    <input id="username" 
                           name="username" 
                           type="text" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Choose a username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    <p class="mt-1 text-xs text-gray-500">At least 3 characters</p>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Enter your email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Create a password">
                    <p class="mt-1 text-xs text-gray-500">At least 6 characters</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <input id="confirm_password" 
                           name="confirm_password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-black focus:border-black focus:z-10 sm:text-sm" 
                           placeholder="Confirm your password">
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" 
                       name="terms" 
                       type="checkbox" 
                       required
                       class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900">
                    I agree to the 
                    <a href="#" class="text-black hover:underline">Terms of Service</a> 
                    and 
                    <a href="#" class="text-black hover:underline">Privacy Policy</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    Create Account
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="login.php" class="font-medium text-black hover:underline">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
        
        <!-- Benefits -->
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Why join Book Bytes?</h3>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>• Access to curated book summaries</li>
                <li>• Interactive text-to-speech reading</li>
                <li>• Personalized reading experience</li>
                <li>• Track your learning progress</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = getPasswordStrength(password);
    
    // Remove existing indicator
    const existingIndicator = document.querySelector('.password-strength');
    if (existingIndicator) {
        existingIndicator.remove();
    }
    
    if (password.length > 0) {
        const indicator = document.createElement('div');
        indicator.className = 'password-strength mt-1 text-xs';
        
        switch (strength) {
            case 'weak':
                indicator.innerHTML = '<span class="text-red-600">Weak password</span>';
                break;
            case 'medium':
                indicator.innerHTML = '<span class="text-yellow-600">Medium strength</span>';
                break;
            case 'strong':
                indicator.innerHTML = '<span class="text-green-600">Strong password</span>';
                break;
        }
        
        this.parentNode.appendChild(indicator);
    }
});

function getPasswordStrength(password) {
    if (password.length < 6) return 'weak';
    
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    if (score < 3) return 'weak';
    if (score < 5) return 'medium';
    return 'strong';
}

// Confirm password validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
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

<?php include 'includes/footer.php'; ?>
