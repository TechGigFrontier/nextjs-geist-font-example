// Wave Animation for Text-to-Speech
class WaveAnimation {
    constructor() {
        this.waveBars = document.querySelectorAll('.wave-bar');
        this.isAnimating = false;
        this.animationInterval = null;
    }

    start() {
        if (this.isAnimating) return;
        
        this.isAnimating = true;
        this.waveBars.forEach(bar => {
            bar.classList.add('active');
        });

        // Create random wave animation
        this.animationInterval = setInterval(() => {
            this.waveBars.forEach((bar, index) => {
                const height = Math.random() * 20 + 10; // Random height between 10-30px
                const delay = index * 50; // Stagger the animation
                
                setTimeout(() => {
                    bar.style.height = height + 'px';
                }, delay);
            });
        }, 200);
    }

    stop() {
        if (!this.isAnimating) return;
        
        this.isAnimating = false;
        
        if (this.animationInterval) {
            clearInterval(this.animationInterval);
            this.animationInterval = null;
        }

        // Reset all bars to default height
        this.waveBars.forEach(bar => {
            bar.classList.remove('active');
            bar.style.height = '10px';
        });
    }

    pulse() {
        // Single pulse animation for button clicks
        this.waveBars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.height = '25px';
                setTimeout(() => {
                    bar.style.height = '10px';
                }, 100);
            }, index * 20);
        });
    }
}

// Initialize wave animation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.waveAnimation = new WaveAnimation();
    
    // Add pulse effect to control buttons
    const controlButtons = document.querySelectorAll('#playBtn, #pauseBtn, #stopBtn');
    controlButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (window.waveAnimation) {
                window.waveAnimation.pulse();
            }
        });
    });
});
