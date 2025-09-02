// Text-to-Speech functionality
class TextToSpeech {
    constructor() {
        this.synth = window.speechSynthesis;
        this.utterance = null;
        this.voices = [];
        this.currentWordIndex = 0;
        this.words = [];
        this.isPlaying = false;
        this.isPaused = false;
        this.selectedVoice = null;
        this.speed = 1.0;
        
        this.init();
    }

    init() {
        // Wait for voices to be loaded
        if (this.synth.getVoices().length === 0) {
            this.synth.addEventListener('voiceschanged', () => {
                this.loadVoices();
            });
        } else {
            this.loadVoices();
        }

        this.setupEventListeners();
        this.showTTSControl();
    }

    loadVoices() {
        this.voices = this.synth.getVoices();
        this.populateVoiceSelect();
    }

    populateVoiceSelect() {
        const voiceSelect = document.getElementById('voiceSelect');
        if (!voiceSelect) return;

        voiceSelect.innerHTML = '<option value="">Select Voice</option>';
        
        this.voices.forEach((voice, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = `${voice.name} (${voice.lang})`;
            voiceSelect.appendChild(option);
        });

        // Auto-select first English voice
        const englishVoice = this.voices.findIndex(voice => voice.lang.startsWith('en'));
        if (englishVoice !== -1) {
            voiceSelect.value = englishVoice;
            this.selectedVoice = this.voices[englishVoice];
            this.updateSelectedVoiceDisplay();
        }
    }

    setupEventListeners() {
        const playBtn = document.getElementById('playBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const stopBtn = document.getElementById('stopBtn');
        const voiceSelect = document.getElementById('voiceSelect');
        const speedSelect = document.getElementById('speedSelect');
        const speedUp = document.getElementById('speedUp');
        const speedDown = document.getElementById('speedDown');

        if (playBtn) {
            playBtn.addEventListener('click', () => this.play());
        }

        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => this.pause());
        }

        if (stopBtn) {
            stopBtn.addEventListener('click', () => this.stop());
        }

        if (voiceSelect) {
            voiceSelect.addEventListener('change', (e) => {
                if (e.target.value) {
                    this.selectedVoice = this.voices[e.target.value];
                    this.updateSelectedVoiceDisplay();
                    this.hideVoiceSelection();
                }
            });
        }

        if (speedSelect) {
            speedSelect.addEventListener('change', (e) => {
                this.speed = parseFloat(e.target.value);
            });
        }

        if (speedUp) {
            speedUp.addEventListener('click', () => this.adjustSpeed(0.5));
        }

        if (speedDown) {
            speedDown.addEventListener('click', () => this.adjustSpeed(-0.5));
        }
    }

    showTTSControl() {
        const ttsControl = document.getElementById('ttsControl');
        const readingContent = document.querySelector('.reading-content');
        
        if (ttsControl && readingContent) {
            ttsControl.classList.remove('hidden');
        }
    }

    hideVoiceSelection() {
        const voiceSelection = document.getElementById('voiceSelection');
        if (voiceSelection && this.selectedVoice) {
            voiceSelection.style.display = 'none';
        }
    }

    updateSelectedVoiceDisplay() {
        const selectedVoiceDiv = document.getElementById('selectedVoice');
        const voiceName = document.getElementById('voiceName');
        
        if (selectedVoiceDiv && voiceName && this.selectedVoice) {
            voiceName.textContent = this.selectedVoice.name;
            selectedVoiceDiv.classList.remove('hidden');
        }
    }

    prepareText() {
        const readingContent = document.querySelector('.reading-content');
        if (!readingContent) return;

        // Get all text content and split into words
        const textContent = readingContent.innerText || readingContent.textContent;
        this.words = textContent.split(/\s+/).filter(word => word.length > 0);
        
        // Wrap each word in a span for highlighting
        this.wrapWordsInSpans(readingContent);
    }

    wrapWordsInSpans(element) {
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            if (node.nodeValue.trim()) {
                textNodes.push(node);
            }
        }

        textNodes.forEach(textNode => {
            const words = textNode.nodeValue.split(/(\s+)/);
            const fragment = document.createDocumentFragment();
            
            words.forEach(word => {
                if (word.trim()) {
                    const span = document.createElement('span');
                    span.textContent = word;
                    span.className = 'tts-word';
                    fragment.appendChild(span);
                } else {
                    fragment.appendChild(document.createTextNode(word));
                }
            });
            
            textNode.parentNode.replaceChild(fragment, textNode);
        });
    }

    play() {
        if (!this.selectedVoice) {
            alert('Please select a voice first.');
            return;
        }

        if (this.isPaused && this.utterance) {
            this.synth.resume();
            this.isPaused = false;
            this.isPlaying = true;
            this.startWaveAnimation();
            return;
        }

        this.prepareText();
        const readingContent = document.querySelector('.reading-content');
        if (!readingContent) return;

        const text = readingContent.innerText || readingContent.textContent;
        this.utterance = new SpeechSynthesisUtterance(text);
        this.utterance.voice = this.selectedVoice;
        this.utterance.rate = this.speed;

        this.currentWordIndex = 0;
        this.isPlaying = true;
        this.isPaused = false;

        // Set up word boundary event
        this.utterance.addEventListener('boundary', (event) => {
            if (event.name === 'word') {
                this.highlightCurrentWord(event.charIndex, text);
            }
        });

        this.utterance.addEventListener('end', () => {
            this.stop();
        });

        this.utterance.addEventListener('error', () => {
            this.stop();
        });

        this.startWaveAnimation();
        this.synth.speak(this.utterance);
    }

    pause() {
        if (this.isPlaying && !this.isPaused) {
            this.synth.pause();
            this.isPaused = true;
            this.isPlaying = false;
            this.stopWaveAnimation();
        }
    }

    stop() {
        this.synth.cancel();
        this.isPlaying = false;
        this.isPaused = false;
        this.currentWordIndex = 0;
        this.clearHighlights();
        this.stopWaveAnimation();
    }

    adjustSpeed(delta) {
        const speedSelect = document.getElementById('speedSelect');
        const speeds = [0.5, 1.0, 1.5, 2.0, 2.5, 3.0];
        const currentIndex = speeds.indexOf(this.speed);
        let newIndex = currentIndex;

        if (delta > 0 && currentIndex < speeds.length - 1) {
            newIndex = currentIndex + 1;
        } else if (delta < 0 && currentIndex > 0) {
            newIndex = currentIndex - 1;
        }

        this.speed = speeds[newIndex];
        speedSelect.value = this.speed;

        // If currently playing, restart with new speed
        if (this.isPlaying) {
            this.stop();
            setTimeout(() => this.play(), 100);
        }
    }

    highlightCurrentWord(charIndex, fullText) {
        // Clear previous highlights
        this.clearHighlights();

        // Find the word at the character index
        const beforeText = fullText.substring(0, charIndex);
        const wordIndex = beforeText.split(/\s+/).length - 1;

        const wordSpans = document.querySelectorAll('.tts-word');
        if (wordSpans[wordIndex]) {
            wordSpans[wordIndex].classList.add('highlight-word');
            
            // Auto-scroll to keep the highlighted word in view
            if (window.scrollToCenter) {
                window.scrollToCenter(wordSpans[wordIndex]);
            }
        }
    }

    clearHighlights() {
        const highlightedWords = document.querySelectorAll('.highlight-word');
        highlightedWords.forEach(word => {
            word.classList.remove('highlight-word');
        });
    }

    startWaveAnimation() {
        const waveAnimation = window.waveAnimation;
        if (waveAnimation) {
            waveAnimation.start();
        }
    }

    stopWaveAnimation() {
        const waveAnimation = window.waveAnimation;
        if (waveAnimation) {
            waveAnimation.stop();
        }
    }
}

// Initialize TTS when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.reading-content')) {
        window.textToSpeech = new TextToSpeech();
    }
});
