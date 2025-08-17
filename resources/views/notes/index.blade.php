@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Notes</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $totalNotes }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Processed</h3>
                <p class="text-3xl font-bold text-green-600">{{ $processedNotes }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Unprocessed</h3>
                <p class="text-3xl font-bold text-orange-600">{{ $unprocessedNotes }}</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Categories</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
                <a href="{{ route('notes.category', $category->id) }}" 
                   class="block bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h-.5a1 1 0 000-2H8a2 2 0 012-2h2a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                        </svg>
                        {{ $category->processed_notes_count }} notes
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    No categories found. Run <code class="bg-gray-100 px-2 py-1 rounded">php artisan notes:process</code> to process notes.
                </div>
            @endforelse
        </div>
    </div>
@endsection