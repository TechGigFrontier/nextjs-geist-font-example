<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

// Get statistics
$pdo = getDBConnection();

// Count total books
$stmt = $pdo->query("SELECT COUNT(*) as total FROM books");
$totalBooks = $stmt->fetch()['total'];

// Count active books
$stmt = $pdo->query("SELECT COUNT(*) as active FROM books WHERE status = 'active'");
$activeBooks = $stmt->fetch()['active'];

// Count total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

// Count active users
$stmt = $pdo->query("SELECT COUNT(*) as active FROM users WHERE status = 'active'");
$activeUsers = $stmt->fetch()['active'];

// Recent books
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 5");
$recentBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Admin Dashboard';
?>

<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                        📚
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Books</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $totalBooks; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">
                        ✅
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Books</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $activeBooks; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm">
                        👥
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm">
                        🟢
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $activeUsers; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="add-book.php" class="bg-black text-white px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors text-center">
                📖 Add New Book
            </a>
            <a href="manage-books.php" class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors text-center">
                📚 Manage Books
            </a>
            <a href="manage-users.php" class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors text-center">
                👥 Manage Users
            </a>
            <a href="../index.php" class="bg-gray-600 text-white px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-center">
                🏠 View Site
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Recent Books -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Books</h2>
            </div>
            <div class="p-6">
                <?php if (empty($recentBooks)): ?>
                <p class="text-gray-500 text-center py-4">No books found.</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentBooks as $book): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="text-sm text-gray-600">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo formatDate($book['created_at']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $book['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($book['status']); ?>
                            </span>
                            <a href="edit-book.php?id=<?php echo $book['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                Edit
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="manage-books.php" class="text-blue-600 hover:text-blue-800 text-sm">
                        View All Books →
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
            </div>
            <div class="p-6">
                <?php if (empty($recentUsers)): ?>
                <p class="text-gray-500 text-center py-4">No users found.</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo formatDate($user['created_at']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="manage-users.php" class="text-blue-600 hover:text-blue-800 text-sm">
                        View All Users →
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
