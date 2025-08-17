@extends('layouts.app')

@section('title', $folder->name)

@section('content')
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('notes.index') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <a href="{{ route('folders.index') }}" class="ml-1 text-gray-500 hover:text-gray-700">Folders</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-1 text-gray-700">{{ Str::limit($folder->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $folder->name }}</h1>
                <p class="text-gray-600 mt-2">Source: {{ $folder->source_path }}</p>
                <p class="text-gray-500 text-sm">Imported on {{ $folder->imported_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex space-x-3">
                @if($folder->processed_files < $folder->total_files)
                    <button onclick="processFolder({{ $folder->id }})" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        Process Notes
                    </button>
                @endif
                <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this folder and all its files?')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                        Delete Folder
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Files</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $folder->total_files }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Processed</h3>
            <p class="text-3xl font-bold text-green-600">{{ $folder->processed_files }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Progress</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $folder->progress_percentage }}%</p>
        </div>
    </div>

    @if($folder->total_files > 0)
        <div class="mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Processing Progress</span>
                    <span>{{ $folder->processed_files }}/{{ $folder->total_files }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all" 
                         style="width: {{ $folder->progress_percentage }}%"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Imported Files</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($folder->notes as $note)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $note->file_name }}</h3>
                            <div class="mt-1 text-sm text-gray-500">
                                <span>{{ ucfirst($note->format) }} file</span>
                                <span class="mx-2">•</span>
                                <span>{{ number_format($note->file_size / 1024, 1) }} KB</span>
                                <span class="mx-2">•</span>
                                <span>Modified {{ $note->file_modified_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if($note->is_processed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Processed
                                </span>
                                @if($note->processedNote)
                                    <a href="{{ route('notes.show', $note->processedNote->id) }}" 
                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                        View Summary
                                    </a>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    No files found in this folder.
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function processFolder(folderId) {
            // This could trigger an AJAX call to process notes for this folder
            alert('Processing feature coming soon! Use the command line: php artisan notes:process --folder=' + folderId);
        }
    </script>
@endsection