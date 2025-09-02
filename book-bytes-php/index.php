<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';
$books = getAllBooks();
?>

<?php include 'includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <div class="mb-8">
            <img src="https://placehold.co/800x400?text=Book+Bytes+-+Your+Gateway+to+Knowledge" 
                 alt="Book Bytes - Your Gateway to Knowledge" 
                 class="mx-auto rounded-lg shadow-lg max-w-full h-auto">
        </div>
        
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">
            Book Bytes
        </h1>
        <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
            Discover the essence of great books through our comprehensive summaries. 
            Learn faster, retain more, and expand your knowledge with our interactive reading experience.
        </p>
        
        <?php if (!isLoggedIn()): ?>
        <div class="space-x-4">
            <a href="register.php" class="bg-black text-white px-8 py-3 rounded-lg hover:bg-gray-800 transition-colors">
                Get Started
            </a>
            <a href="login.php" class="border border-black text-black px-8 py-3 rounded-lg hover:bg-black hover:text-white transition-colors">
                Sign In
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-8 mb-16">
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                📚
            </div>
            <h3 class="text-xl font-semibold mb-2">Curated Summaries</h3>
            <p class="text-gray-600">Hand-picked book summaries that capture the key insights and takeaways from bestselling books.</p>
        </div>
        
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                🎧
            </div>
            <h3 class="text-xl font-semibold mb-2">Audio Experience</h3>
            <p class="text-gray-600">Listen to summaries with our advanced text-to-speech feature with voice selection and speed control.</p>
        </div>
        
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                💡
            </div>
            <h3 class="text-xl font-semibold mb-2">Key Takeaways</h3>
            <p class="text-gray-600">Get actionable insights and practical examples that you can apply immediately in your life.</p>
        </div>
    </div>

    <!-- Book Collection -->
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8">Our Book Collection</h2>
        
        <?php if (empty($books)): ?>
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No books available at the moment. Please check back later!</p>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($books as $book): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="aspect-w-16 aspect-h-9">
                    <img src="<?php echo $book['image'] ? 'uploads/' . htmlspecialchars($book['image']) : 'https://placehold.co/400x300?text=' . urlencode($book['title']); ?>" 
                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                         class="w-full h-48 object-cover">
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2 text-gray-900">
                        <?php echo htmlspecialchars($book['title']); ?>
                    </h3>
                    
                    <p class="text-gray-600 mb-3">
                        by <span class="font-medium"><?php echo htmlspecialchars($book['author']); ?></span>
                    </p>
                    
                    <p class="text-gray-700 mb-4 line-clamp-3">
                        <?php echo htmlspecialchars(truncateText($book['description'], 120)); ?>
                    </p>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            <?php echo formatDate($book['created_at']); ?>
                        </span>
                        
                        <a href="book-summary.php?id=<?php echo $book['id']; ?>" 
                           class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">
                            Read Summary
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Call to Action -->
    <?php if (!isLoggedIn()): ?>
    <div class="bg-gray-900 text-white rounded-lg p-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Start Learning?</h2>
        <p class="text-xl mb-6 text-gray-300">
            Join thousands of readers who are accelerating their learning with Book Bytes.
        </p>
        <a href="register.php" class="bg-white text-gray-900 px-8 py-3 rounded-lg hover:bg-gray-100 transition-colors font-semibold">
            Create Free Account
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
