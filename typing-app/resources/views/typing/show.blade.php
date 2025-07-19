@extends('layouts.app')

@section('title', $book->title . ' - TypingBooks')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                @if($book->author)
                    <p class="text-gray-600">{{ $book->author }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Chapter</p>
                    <p class="text-lg font-semibold">{{ $currentChapter + 1 }} of {{ count($epubData['chapters']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Progress</p>
                    <p class="text-lg font-semibold" id="progressPercentage">
                        {{ $progress ? round(($progress->character_offset / max($progress->total_characters, 1)) * 100) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Typing Interface -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <!-- Stats Bar -->
            <div class="flex items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">WPM</p>
                        <p class="text-xl font-bold text-blue-600" id="wpmDisplay">0</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Accuracy</p>
                        <p class="text-xl font-bold text-green-600" id="accuracyDisplay">0%</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Characters</p>
                        <p class="text-xl font-bold text-purple-600" id="charactersDisplay">0</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="resetTyping()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Reset
                    </button>
                    <button onclick="toggleTheme()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Theme
                    </button>
                </div>
            </div>

            <!-- Chapter Navigation -->
            <div class="flex items-center justify-between mb-4">
                <button onclick="previousChapter()" 
                        class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="prevChapterBtn">
                    ← Previous
                </button>
                <h3 class="text-lg font-medium text-gray-900" id="chapterTitle">
                    {{ $epubData['chapters'][$currentChapter]['title'] }}
                </h3>
                <button onclick="nextChapter()" 
                        class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="nextChapterBtn">
                    Next →
                </button>
            </div>

            <!-- Typing Area -->
            <div class="mb-6">
                <div id="typingText" class="typing-text p-6 bg-gray-50 rounded-lg leading-relaxed text-gray-800 min-h-[200px] whitespace-pre-wrap">
                    {{ $epubData['chapters'][$currentChapter]['content'] }}
                </div>
            </div>

            <!-- Input Area -->
            <div class="mb-6">
                <textarea id="typingInput" 
                          class="typing-input w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          rows="4"
                          placeholder="Start typing here..."
                          autocomplete="off"
                          spellcheck="false"></textarea>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Chapter Progress</span>
                    <span id="chapterProgress">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="chapterProgressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Theme Modal -->
<div id="themeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Choose Theme</h3>
            
            <div class="space-y-3">
                <button onclick="applyTheme('light')" class="w-full p-3 text-left border rounded-lg hover:bg-gray-50">
                    <div class="font-medium">Light</div>
                    <div class="text-sm text-gray-500">Clean white background with dark text</div>
                </button>
                
                <button onclick="applyTheme('dark')" class="w-full p-3 text-left border rounded-lg hover:bg-gray-50">
                    <div class="font-medium">Dark</div>
                    <div class="text-sm text-gray-500">Dark background with light text</div>
                </button>
                
                <button onclick="applyTheme('sepia')" class="w-full p-3 text-left border rounded-lg hover:bg-gray-50">
                    <div class="font-medium">Sepia</div>
                    <div class="text-sm text-gray-500">Warm sepia tones for easy reading</div>
                </button>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeThemeModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentChapter = {{ $currentChapter }};
let currentOffset = {{ $currentOffset }};
let totalChapters = {{ count($epubData['chapters']) }};
let chapters = @json($epubData['chapters']);
let bookId = {{ $book->id }};
let startTime = null;
let isTyping = false;
let typedCharacters = 0;
let correctCharacters = 0;
let currentTheme = 'light';

// Initialize typing interface
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('typingInput');
    const text = document.getElementById('typingText');
    
    input.addEventListener('input', handleTyping);
    input.addEventListener('keydown', handleKeyDown);
    
    // Load saved progress
    loadProgress();
    
    // Apply saved theme
    applyTheme(localStorage.getItem('typingTheme') || 'light');
});

function handleTyping(e) {
    if (!isTyping) {
        startTime = Date.now();
        isTyping = true;
    }
    
    const input = e.target;
    const text = document.getElementById('typingText');
    const inputText = input.value;
    const originalText = chapters[currentChapter].content;
    
    // Calculate progress
    typedCharacters = inputText.length;
    correctCharacters = 0;
    
    for (let i = 0; i < Math.min(inputText.length, originalText.length); i++) {
        if (inputText[i] === originalText[i]) {
            correctCharacters++;
        }
    }
    
    // Update displays
    updateStats();
    updateProgress();
    
    // Auto-save progress
    saveProgress();
}

function handleKeyDown(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        const input = e.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.substring(0, start) + '    ' + input.value.substring(end);
        input.selectionStart = input.selectionEnd = start + 4;
    }
}

function updateStats() {
    if (startTime && isTyping) {
        const elapsedMinutes = (Date.now() - startTime) / 60000;
        const wpm = elapsedMinutes > 0 ? Math.round((correctCharacters / 5) / elapsedMinutes) : 0;
        const accuracy = typedCharacters > 0 ? Math.round((correctCharacters / typedCharacters) * 100) : 0;
        
        document.getElementById('wpmDisplay').textContent = wpm;
        document.getElementById('accuracyDisplay').textContent = accuracy + '%';
        document.getElementById('charactersDisplay').textContent = typedCharacters;
    }
}

function updateProgress() {
    const totalChars = chapters[currentChapter].content.length;
    const percentage = totalChars > 0 ? Math.round((typedCharacters / totalChars) * 100) : 0;
    
    document.getElementById('chapterProgress').textContent = percentage + '%';
    document.getElementById('chapterProgressBar').style.width = percentage + '%';
}

function saveProgress() {
    const totalOffset = calculateTotalOffset();
    
    fetch('{{ route("typing.progress") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            book_id: bookId,
            chapter: currentChapter,
            character_offset: totalOffset,
            total_characters: calculateTotalCharacters(),
            wpm: parseInt(document.getElementById('wpmDisplay').textContent),
            accuracy: parseInt(document.getElementById('accuracyDisplay').textContent)
        })
    });
}

function calculateTotalOffset() {
    let offset = 0;
    for (let i = 0; i < currentChapter; i++) {
        offset += chapters[i].content.length;
    }
    offset += typedCharacters;
    return offset;
}

function calculateTotalCharacters() {
    let total = 0;
    for (let i = 0; i < chapters.length; i++) {
        total += chapters[i].content.length;
    }
    return total;
}

function loadProgress() {
    // Load progress from server (already done in controller)
    // This function can be used for additional client-side progress loading
}

function resetTyping() {
    if (confirm('Are you sure you want to reset your progress for this chapter?')) {
        document.getElementById('typingInput').value = '';
        typedCharacters = 0;
        correctCharacters = 0;
        startTime = null;
        isTyping = false;
        updateStats();
        updateProgress();
        saveProgress();
    }
}

function previousChapter() {
    if (currentChapter > 0) {
        currentChapter--;
        loadChapter();
    }
}

function nextChapter() {
    if (currentChapter < totalChapters - 1) {
        currentChapter++;
        loadChapter();
    }
}

function loadChapter() {
    const chapter = chapters[currentChapter];
    document.getElementById('chapterTitle').textContent = chapter.title;
    document.getElementById('typingText').textContent = chapter.content;
    document.getElementById('typingInput').value = '';
    
    // Update navigation buttons
    document.getElementById('prevChapterBtn').disabled = currentChapter === 0;
    document.getElementById('nextChapterBtn').disabled = currentChapter === totalChapters - 1;
    
    // Reset typing stats
    typedCharacters = 0;
    correctCharacters = 0;
    startTime = null;
    isTyping = false;
    updateStats();
    updateProgress();
    saveProgress();
}

function toggleTheme() {
    document.getElementById('themeModal').classList.remove('hidden');
}

function closeThemeModal() {
    document.getElementById('themeModal').classList.add('hidden');
}

function applyTheme(theme) {
    currentTheme = theme;
    localStorage.setItem('typingTheme', theme);
    
    const textElement = document.getElementById('typingText');
    const inputElement = document.getElementById('typingInput');
    
    // Remove existing theme classes
    textElement.className = textElement.className.replace(/theme-\w+/g, '');
    inputElement.className = inputElement.className.replace(/theme-\w+/g, '');
    
    // Apply new theme
    switch (theme) {
        case 'dark':
            textElement.classList.add('theme-dark');
            inputElement.classList.add('theme-dark');
            break;
        case 'sepia':
            textElement.classList.add('theme-sepia');
            inputElement.classList.add('theme-sepia');
            break;
        default: // light
            textElement.classList.add('theme-light');
            inputElement.classList.add('theme-light');
    }
    
    closeThemeModal();
}
</script>

<style>
.theme-dark {
    background-color: #1f2937 !important;
    color: #f9fafb !important;
}

.theme-sepia {
    background-color: #fef3c7 !important;
    color: #92400e !important;
}

.theme-light {
    background-color: #f9fafb !important;
    color: #1f2937 !important;
}
</style>
@endsection 