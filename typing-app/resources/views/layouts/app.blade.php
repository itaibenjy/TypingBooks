<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TypingBooks')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <style>
        .typing-text {
            font-family: 'Georgia', serif;
            line-height: 1.8;
            font-size: 18px;
        }
        
        .typing-input {
            font-family: 'Georgia', serif;
            line-height: 1.8;
            font-size: 18px;
        }
        
        .correct {
            color: #059669;
        }
        
        .incorrect {
            color: #dc2626;
            text-decoration: underline;
        }
        
        .current {
            background-color: #fbbf24;
            color: #000;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    @auth
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900">
                        TypingBooks
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('books.index') }}" class="text-gray-700 hover:text-gray-900">Books</a>
                    <a href="{{ route('themes.index') }}" class="text-gray-700 hover:text-gray-900">Themes</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Scripts -->
    @yield('scripts')
</body>
</html> 