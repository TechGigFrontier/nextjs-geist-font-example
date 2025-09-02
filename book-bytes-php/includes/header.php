<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hamburger-line {
            transition: all 0.3s ease;
        }
        .hamburger-active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .hamburger-active .hamburger-line:nth-child(2) {
            opacity: 0;
        }
        .hamburger-active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .highlight-word {
            background-color: #fbbf24;
            color: #000;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .tts-control {
            backdrop-filter: blur(10px);
            background: rgba(0, 0, 0, 0.8);
        }
        .wave-container {
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wave-bar {
            width: 3px;
            height: 10px;
            background: #10b981;
            margin: 0 1px;
            border-radius: 2px;
            transition: height 0.1s ease;
        }
        .wave-bar.active {
            animation: wave 0.5s ease-in-out infinite alternate;
        }
        @keyframes wave {
            0% { height: 10px; }
            100% { height: 30px; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Mobile Menu Button -->
    <button id="menuToggle" class="fixed top-4 left-4 z-50 p-2 bg-black text-white rounded-lg md:hidden">
        <div class="w-6 h-6 flex flex-col justify-center items-center">
            <span class="hamburger-line w-6 h-0.5 bg-white block mb-1"></span>
            <span class="hamburger-line w-6 h-0.5 bg-white block mb-1"></span>
            <span class="hamburger-line w-6 h-0.5 bg-white block"></span>
        </div>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed left-0 top-0 w-80 h-full bg-white shadow-lg z-40 overflow-y-auto">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-6">Book Bytes</h2>
            
            <!-- Navigation Menu -->
            <nav class="space-y-2">
                <a href="index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    🏠 Home
                </a>
                
                <?php
                $menuItems = getMenuItems();
                foreach ($menuItems as $item):
                ?>
                <a href="<?php echo $item['url']; ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    📖 <?php echo htmlspecialchars($item['title']); ?>
                    <div class="text-sm text-gray-500 ml-6">by <?php echo htmlspecialchars($item['author']); ?></div>
                </a>
                <?php endforeach; ?>
            </nav>
            
            <!-- Bottom Menu -->
            <div class="absolute bottom-6 left-6 right-6 space-y-2 border-t pt-4">
                <?php if (isLoggedIn()): ?>
                    <div class="px-4 py-2 text-sm text-gray-600">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </div>
                    <?php if (isAdmin()): ?>
                    <a href="admin/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        ⚙️ Admin Panel
                    </a>
                    <?php endif; ?>
                    <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                        🚪 Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        🔑 Login
                    </a>
                    <a href="register.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        📝 Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 md:ml-0 min-h-screen">
