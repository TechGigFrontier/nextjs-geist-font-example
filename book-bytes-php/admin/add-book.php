<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$error = '';
$success = '';

if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title = sanitize($_POST['title'] ?? '');
        $author = sanitize($_POST['author'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        
        // Validation
        if (empty($title) || empty($author) || empty($description)) {
            $error = 'Please fill in all required fields.';
        } else {
            $pdo = getDBConnection();
            
            // Handle image upload
            $imageName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = uploadFile($_FILES['image'], ['jpg', 'jpeg', 'png', 'gif']);
                if (!$imageName) {
                    $error = 'Failed to upload image. Please use JPG, PNG, or GIF format.';
                }
            }
            
            if (!$error) {
                try {
                    // Insert book
                    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, image, status) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $author, $description, $imageName, $status]);
                    $bookId = $pdo->lastInsertId();
                    
                    // Process sections if provided
                    if (!empty($_POST['sections'])) {
                        foreach ($_POST['sections'] as $index => $sectionData) {
                            $sectionTitle = sanitize($sectionData['title'] ?? '');
                            $sectionContent = sanitize($sectionData['content'] ?? '');
                            
                            if (!empty($sectionTitle) && !empty($sectionContent)) {
                                $stmt = $pdo->prepare("INSERT INTO book_sections (book_id, section_title, content, order_num) VALUES (?, ?, ?, ?)");
                                $stmt->execute([$bookId, $sectionTitle, $sectionContent, $index + 1]);
                                $sectionId = $pdo->lastInsertId();
                                
                                // Process takeaways for this section
                                if (!empty($sectionData['takeaways'])) {
                                    foreach ($sectionData['takeaways'] as $takeawayIndex => $takeawayData) {
                                        $takeawayText = sanitize($takeawayData['text'] ?? '');
                                        $exampleText = sanitize($takeawayData['example'] ?? '');
                                        
                                        if (!empty($takeawayText)) {
                                            $stmt = $pdo->prepare("INSERT INTO takeaways (section_id, takeaway_text, example_text, order_num) VALUES (?, ?, ?, ?)");
                                            $stmt->execute([$sectionId, $takeawayText, $exampleText, $takeawayIndex + 1]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $success = 'Book added successfully!';
                    // Clear form data
                    $_POST = [];
                } catch (Exception $e) {
                    $error = 'Failed to add book. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Add New Book';
?>

<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Book</h1>
            <p class="text-gray-600">Create a new book summary with sections and takeaways</p>
        </div>
        <a href="manage-books.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
            ← Back to Books
        </a>
    </div>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($success); ?>
        <div class="mt-2">
            <a href="manage-books.php" class="font-medium underline">View all books</a> or 
            <a href="add-book.php" class="font-medium underline">add another book</a>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Book Title *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black"
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                           placeholder="Enter book title">
                </div>
                
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                        Author *
                    </label>
                    <input type="text" 
                           id="author" 
                           name="author" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black"
                           value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>"
                           placeholder="Enter author name">
                </div>
            </div>
            
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description *
                </label>
                <textarea id="description" 
                          name="description" 
                          required 
                          rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black"
                          placeholder="Enter book description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Book Cover Image
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, or GIF format. Max 5MB.</p>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black">
                        <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sections -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Book Sections</h2>
                <button type="button" 
                        onclick="addSection()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    + Add Section
                </button>
            </div>
            
            <div id="sections-container">
                <!-- Sections will be added here dynamically -->
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="manage-books.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors">
                Add Book
            </button>
        </div>
    </form>
</div>

<script>
let sectionCount = 0;

function addSection() {
    const container = document.getElementById('sections-container');
    const sectionDiv = document.createElement('div');
    sectionDiv.className = 'border border-gray-200 rounded-lg p-4 mb-4';
    sectionDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Section ${sectionCount + 1}</h3>
            <button type="button" onclick="removeSection(this)" class="text-red-600 hover:text-red-800 text-sm">
                Remove Section
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                <input type="text" 
                       name="sections[${sectionCount}][title]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black"
                       placeholder="Enter section title">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Section Content</label>
                <textarea name="sections[${sectionCount}][content]" 
                          rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black"
                          placeholder="Enter section content"></textarea>
            </div>
            
            <div class="takeaways-container">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Takeaways</label>
                    <button type="button" onclick="addTakeaway(this, ${sectionCount})" class="text-blue-600 hover:text-blue-800 text-sm">
                        + Add Takeaway
                    </button>
                </div>
                <div class="takeaways-list"></div>
            </div>
        </div>
    `;
    
    container.appendChild(sectionDiv);
    sectionCount++;
}

function removeSection(button) {
    button.closest('.border').remove();
}

function addTakeaway(button, sectionIndex) {
    const takeawaysList = button.closest('.takeaways-container').querySelector('.takeaways-list');
    const takeawayCount = takeawaysList.children.length;
    
    const takeawayDiv = document.createElement('div');
    takeawayDiv.className = 'border border-gray-100 rounded p-3 mb-3';
    takeawayDiv.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Takeaway ${takeawayCount + 1}</span>
            <button type="button" onclick="removeTakeaway(this)" class="text-red-600 hover:text-red-800 text-xs">
                Remove
            </button>
        </div>
        
        <div class="space-y-2">
            <input type="text" 
                   name="sections[${sectionIndex}][takeaways][${takeawayCount}][text]" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black text-sm"
                   placeholder="Takeaway title">
            
            <textarea name="sections[${sectionIndex}][takeaways][${takeawayCount}][example]" 
                      rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-black focus:border-black text-sm"
                      placeholder="Example or explanation (optional)"></textarea>
        </div>
    `;
    
    takeawaysList.appendChild(takeawayDiv);
}

function removeTakeaway(button) {
    button.closest('.border-gray-100').remove();
}

// Add initial section
document.addEventListener('DOMContentLoaded', function() {
    addSection();
});
</script>

<?php include '../includes/footer.php'; ?>
