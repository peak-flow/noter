<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Note Processor')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('notes.index') }}" class="text-xl font-semibold text-gray-900">
                        Note Processor
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('notes.index') }}" 
                           class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('notes.*') ? 'font-medium' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('folders.index') }}" 
                           class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('folders.*') ? 'font-medium' : '' }}">
                            Folders
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <form action="{{ route('notes.search') }}" method="GET" class="flex">
                        <input type="text" name="q" placeholder="Search notes..." 
                               value="{{ request('q') }}"
                               class="px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
</body>
</html>