</div>

    <!-- Text-to-Speech Control Panel -->
    <div id="ttsControl" class="fixed right-4 top-1/2 transform -translate-y-1/2 tts-control text-white p-4 rounded-lg shadow-lg z-50 hidden">
        <div class="space-y-3 w-64">
            <!-- Voice Selection -->
            <div id="voiceSelection" class="space-y-2">
                <label class="block text-sm font-medium">Voice:</label>
                <select id="voiceSelect" class="w-full p-2 bg-gray-700 text-white rounded border-none">
                    <option value="">Select Voice</option>
                </select>
            </div>

            <!-- Control Buttons -->
            <div class="flex space-x-2">
                <button id="playBtn" class="flex-1 bg-green-600 hover:bg-green-700 px-3 py-2 rounded text-sm">
                    ▶️ Play
                </button>
                <button id="pauseBtn" class="flex-1 bg-yellow-600 hover:bg-yellow-700 px-3 py-2 rounded text-sm">
                    ⏸️ Pause
                </button>
                <button id="stopBtn" class="flex-1 bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    ⏹️ Stop
                </button>
            </div>

            <!-- Speed Control -->
            <div class="space-y-2">
                <label class="block text-sm font-medium">Speed:</label>
                <div class="flex items-center space-x-2">
                    <button id="speedDown" class="bg-gray-600 hover:bg-gray-700 px-2 py-1 rounded text-sm">-</button>
                    <select id="speedSelect" class="flex-1 p-2 bg-gray-700 text-white rounded border-none">
                        <option value="0.5">0.5x</option>
                        <option value="1.0" selected>1.0x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2.0">2.0x</option>
                        <option value="2.5">2.5x</option>
                        <option value="3.0">3.0x</option>
                    </select>
                    <button id="speedUp" class="bg-gray-600 hover:bg-gray-700 px-2 py-1 rounded text-sm">+</button>
                </div>
            </div>

            <!-- Wave Visualization -->
            <div class="wave-container bg-gray-800 rounded p-2">
                <div id="waveDisplay" class="flex justify-center items-center space-x-1">
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                </div>
            </div>

            <!-- Selected Voice Display -->
            <div id="selectedVoice" class="text-xs text-gray-300 hidden">
                <span id="voiceName"></span>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/text-to-speech.js"></script>
    <script src="assets/js/wave-animation.js"></script>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
