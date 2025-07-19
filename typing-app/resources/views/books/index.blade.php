@extends('layouts.app')

@section('title', 'My Books - TypingBooks')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">My Books</h1>
        <button onclick="openImportModal()" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Import from Drive
        </button>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $book->title }}</h3>
                            @if($book->author)
                                <p class="text-sm text-gray-600">{{ $book->author }}</p>
                            @endif
                        </div>
                        <div class="ml-4">
                            <form method="POST" action="{{ route('books.destroy', $book) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to remove this book?')"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @php
                        $progress = $book->progress->where('user_id', auth()->id())->first();
                        $percentage = $progress ? round(($progress->character_offset / max($progress->total_characters, 1)) * 100) : 0;
                    @endphp

                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Progress</span>
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('typing.show', $book) }}" 
                           class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $progress ? 'Continue' : 'Start' }} Typing
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No books yet</h3>
            <p class="mt-1 text-sm text-gray-500">Import your first book from Google Drive to get started.</p>
            <div class="mt-6">
                <button onclick="openImportModal()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Import from Drive
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Import Books from Google Drive</h3>
            
            <div id="loadingFiles" class="text-center py-4">
                <svg class="animate-spin h-5 w-5 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-600">Loading your EPUB files...</p>
            </div>
            
            <div id="filesList" class="space-y-2 max-h-64 overflow-y-auto hidden">
                <!-- Files will be loaded here -->
            </div>
            
            <div id="noFiles" class="text-center py-4 hidden">
                <p class="text-sm text-gray-600">No EPUB files found in your Google Drive.</p>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeImportModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    loadDriveFiles();
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

function loadDriveFiles() {
    fetch('/books/import-from-drive')
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingFiles').classList.add('hidden');
            
            if (data.error) {
                console.error('Error loading files:', data.error);
                document.getElementById('noFiles').classList.remove('hidden');
                return;
            }
            
            const filesList = document.getElementById('filesList');
            filesList.innerHTML = '';
            
            if (data.files && data.files.length > 0) {
                data.files.forEach(file => {
                    const fileElement = document.createElement('div');
                    fileElement.className = 'flex items-center justify-between p-3 border rounded hover:bg-gray-50';
                    fileElement.innerHTML = `
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                        </div>
                        <button onclick="importBook('${file.id}', '${file.name.replace(/'/g, "\\'")}')" 
                                class="ml-3 px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800">
                            Import
                        </button>
                    `;
                    filesList.appendChild(fileElement);
                });
                filesList.classList.remove('hidden');
            } else {
                document.getElementById('noFiles').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingFiles').classList.add('hidden');
            document.getElementById('noFiles').classList.remove('hidden');
        });
}

function importBook(fileId, fileName) {
    // Create a form to submit the book import
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("books.store") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const fileIdInput = document.createElement('input');
    fileIdInput.type = 'hidden';
    fileIdInput.name = 'drive_file_id';
    fileIdInput.value = fileId;
    
    const titleInput = document.createElement('input');
    titleInput.type = 'hidden';
    titleInput.name = 'title';
    titleInput.value = fileName.replace(/\.epub$/i, '');
    
    form.appendChild(csrfToken);
    form.appendChild(fileIdInput);
    form.appendChild(titleInput);
    
    document.body.appendChild(form);
    form.submit();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection 