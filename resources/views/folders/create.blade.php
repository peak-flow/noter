@extends('layouts.app')

@section('title', 'Import Folder')

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
                        <span class="ml-1 text-gray-700">Import Folder</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <h1 class="text-3xl font-bold text-gray-900">Import Folder</h1>
        <p class="text-gray-600 mt-2">Import notes from a local folder. Files will be copied to preserve originals.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('folders.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="source_path" class="block text-sm font-medium text-gray-700 mb-2">
                    Folder Path *
                </label>
                <input type="text" 
                       id="source_path" 
                       name="source_path" 
                       value="{{ old('source_path') }}"
                       placeholder="/path/to/your/notes/folder"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                <p class="mt-1 text-sm text-gray-500">
                    Enter the full path to the folder containing your notes (supports TXT, PDF, Markdown, RTF)
                </p>
            </div>
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Display Name (Optional)
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       placeholder="My Important Notes"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">
                    Optional custom name for this folder. If not provided, the folder name will be used.
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">What happens during import?</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>All supported files are copied to the application storage</li>
                                <li>Original files remain completely unchanged</li>
                                <li>File creation and modification dates are preserved</li>
                                <li>Files are ready for AI processing after import</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <a href="{{ route('folders.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    Import Folder
                </button>
            </div>
        </form>
    </div>
@endsection