<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$bookId) {
    header('Location: index.php');
    exit();
}

// Get book details
$book = getBookById($bookId);
if (!$book) {
    header('Location: index.php');
    exit();
}

// Get book sections and takeaways
$sections = getBookSections($bookId);

$pageTitle = $book['title'];
?>

<?php include 'includes/header.php'; ?>

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Book Header -->
    <div class="text-center mb-12">
        <div class="mb-6">
            <img src="<?php echo $book['image'] ? 'uploads/' . htmlspecialchars($book['image']) : 'https://placehold.co/600x400?text=' . urlencode($book['title'] . '+Book+Cover'); ?>" 
                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                 class="mx-auto rounded-lg shadow-lg max-w-md w-full h-auto">
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            <?php echo htmlspecialchars($book['title']); ?>
        </h1>
        
        <p class="text-xl text-gray-600 mb-6">
            By <span class="font-semibold"><?php echo htmlspecialchars($book['author']); ?></span>
        </p>
        
        <div class="bg-gray-100 rounded-lg p-6 max-w-3xl mx-auto">
            <p class="text-lg text-gray-700 leading-relaxed">
                <?php echo htmlspecialchars($book['description']); ?>
            </p>
        </div>
    </div>

    <!-- Reading Content -->
    <div class="reading-content prose prose-lg max-w-none">
        <?php if (empty($sections)): ?>
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No content available for this book yet.</p>
        </div>
        <?php else: ?>
        
        <?php foreach ($sections as $section): ?>
        <section class="mb-12" id="section-<?php echo $section['id']; ?>">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 border-b-2 border-gray-200 pb-3">
                <?php echo htmlspecialchars($section['section_title']); ?>
            </h2>
            
            <div class="mb-8">
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    <?php echo nl2br(htmlspecialchars($section['content'])); ?>
                </p>
            </div>

            <?php
            $takeaways = getSectionTakeaways($section['id']);
            if (!empty($takeaways)):
            ?>
            <div class="bg-blue-50 rounded-lg p-6 mb-8">
                <h3 class="text-2xl font-semibold text-blue-900 mb-4">Key Takeaways</h3>
                
                <?php foreach ($takeaways as $takeaway): ?>
                <div class="mb-6 last:mb-0">
                    <h4 class="text-lg font-semibold text-blue-800 mb-3">
                        <?php echo htmlspecialchars($takeaway['takeaway_text']); ?>
                    </h4>
                    
                    <?php if ($takeaway['example_text']): ?>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($takeaway['example_text'])); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="mt-12 pt-8 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                ← Back to Home
            </a>
            
            <div class="text-center">
                <p class="text-gray-600 mb-2">Enjoyed this summary?</p>
                <div class="space-x-4">
                    <button onclick="window.print()" class="text-gray-600 hover:text-gray-800">
                        🖨️ Print
                    </button>
                    <button onclick="shareBook()" class="text-gray-600 hover:text-gray-800">
                        📤 Share
                    </button>
                </div>
            </div>
            
            <?php
            // Get next book for navigation
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, title FROM books WHERE id > ? AND status = 'active' ORDER BY id ASC LIMIT 1");
            $stmt->execute([$bookId]);
            $nextBook = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <?php if ($nextBook): ?>
            <a href="book-summary.php?id=<?php echo $nextBook['id']; ?>" 
               class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors">
                Next Book →
            </a>
            <?php else: ?>
            <div class="w-24"></div> <!-- Spacer -->
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Share functionality
function shareBook() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($book['title']); ?> - Book Summary',
            text: 'Check out this book summary: <?php echo addslashes($book['description']); ?>',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// Add reading progress
document.addEventListener('DOMContentLoaded', function() {
    // Create table of contents
    const sections = document.querySelectorAll('section[id^="section-"]');
    if (sections.length > 1) {
        const toc = document.createElement('div');
        toc.className = 'bg-gray-100 rounded-lg p-6 mb-8';
        toc.innerHTML = '<h3 class="text-xl font-semibold mb-4">Table of Contents</h3>';
        
        const tocList = document.createElement('ul');
        tocList.className = 'space-y-2';
        
        sections.forEach((section, index) => {
            const title = section.querySelector('h2').textContent;
            const li = document.createElement('li');
            li.innerHTML = `<a href="#${section.id}" class="text-blue-600 hover:text-blue-800">${index + 1}. ${title}</a>`;
            tocList.appendChild(li);
        });
        
        toc.appendChild(tocList);
        
        // Insert TOC after book header
        const readingContent = document.querySelector('.reading-content');
        readingContent.insertBefore(toc, readingContent.firstChild);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
