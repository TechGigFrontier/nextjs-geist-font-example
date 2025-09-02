<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$error = '';
$success = '';

// Handle actions
if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $bookId = (int)($_POST['book_id'] ?? 0);
        
        $pdo = getDBConnection();
        
        if ($action === 'toggle_status' && $bookId) {
            $stmt = $pdo->prepare("UPDATE books SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
            if ($stmt->execute([$bookId])) {
                $success = 'Book status updated successfully.';
            } else {
                $error = 'Failed to update book status.';
            }
        } elseif ($action === 'delete' && $bookId) {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
            if ($stmt->execute([$bookId])) {
                $success = 'Book deleted successfully.';
            } else {
                $error = 'Failed to delete book.';
            }
        }
    }
}

// Get all books with pagination
$page = (int)($_GET['page'] ?? 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;

$pdo = getDBConnection();

// Count total books
$stmt = $pdo->query("SELECT COUNT(*) as total FROM books");
$totalBooks = $stmt->fetch()['total'];
$totalPages = ceil($totalBooks / $perPage);

// Get books for current page
$stmt = $pdo->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Books';
?>

<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Books</h1>
            <p class="text-gray-600">Total: <?php echo $totalBooks; ?> books</p>
        </div>
        <div class="space-x-4">
            <a href="add-book.php" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                📖 Add New Book
            </a>
            <a href="index.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <!-- Books Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (empty($books)): ?>
        <div class="p-8 text-center">
            <p class="text-gray-500 text-lg mb-4">No books found.</p>
            <a href="add-book.php" class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors">
                Add Your First Book
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Book Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Author
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($books as $book): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img class="h-16 w-16 rounded-lg object-cover" 
                                         src="<?php echo $book['image'] ? '../uploads/' . htmlspecialchars($book['image']) : 'https://placehold.co/64x64?text=' . urlencode(substr($book['title'], 0, 2)); ?>" 
                                         alt="<?php echo htmlspecialchars($book['title']); ?>">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars(truncateText($book['description'], 60)); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($book['author']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $book['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($book['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo formatDate($book['created_at']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="../book-summary.php?id=<?php echo $book['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900" 
                               target="_blank">
                                View
                            </a>
                            <a href="edit-book.php?id=<?php echo $book['id']; ?>" 
                               class="text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>
                            
                            <!-- Toggle Status Form -->
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to change the status of this book?')">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                    <?php echo $book['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                                </button>
                            </form>
                            
                            <!-- Delete Form -->
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
                <?php endif; ?>
            </div>
            
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                        <span class="font-medium"><?php echo min($offset + $perPage, $totalBooks); ?></span> of 
                        <span class="font-medium"><?php echo $totalBooks; ?></span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-black bg-gray-100' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            Next
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
