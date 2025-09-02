// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('hidden');
        menuToggle.classList.toggle('hamburger-active');
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.add('hidden');
        menuToggle.classList.remove('hamburger-active');
    }

    // Event listeners
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Auto-hide sidebar on larger screens
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Remove error styling on input
                    field.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                    });
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('opacity-0');
                img.classList.add('opacity-100');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // Reading progress indicator
    function updateReadingProgress() {
        const content = document.querySelector('.reading-content');
        if (!content) return;

        const scrollTop = window.pageYOffset;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;

        let progressBar = document.querySelector('.reading-progress');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.className = 'reading-progress fixed top-0 left-0 h-1 bg-blue-500 z-50 transition-all duration-300';
            document.body.appendChild(progressBar);
        }

        progressBar.style.width = scrollPercent + '%';
    }

    // Update reading progress on scroll
    if (document.querySelector('.reading-content')) {
        window.addEventListener('scroll', updateReadingProgress);
        updateReadingProgress(); // Initial call
    }

    // Auto-scroll to center content
    function scrollToCenter(element) {
        const elementRect = element.getBoundingClientRect();
        const absoluteElementTop = elementRect.top + window.pageYOffset;
        const middle = absoluteElementTop - (window.innerHeight / 2);
        
        window.scrollTo({
            top: middle,
            behavior: 'smooth'
        });
    }

    // Expose functions globally for TTS
    window.scrollToCenter = scrollToCenter;

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-black text-white px-2 py-1 rounded text-sm z-50 pointer-events-none';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.top = '-30px';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.absolute');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
});
