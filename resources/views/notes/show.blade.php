@extends('layouts.app')

@section('title', $processedNote->title)

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
                        <a href="{{ route('notes.category', $processedNote->category->id) }}" class="ml-1 text-gray-500 hover:text-gray-700">
                            {{ $processedNote->category->name }}
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-1 text-gray-700">{{ Str::limit($processedNote->title, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $processedNote->title }}</h1>
        
        <div class="flex items-center space-x-4 mb-6 text-sm text-gray-600">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                {{ $processedNote->category->name }}
            </span>
            @if($processedNote->subcategory)
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                    {{ $processedNote->subcategory->name }}
                </span>
            @endif
            <span>{{ $processedNote->created_at->format('M d, Y h:i A') }}</span>
        </div>

        <div class="prose max-w-none mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-3">Summary</h2>
            <div class="text-gray-700 whitespace-pre-wrap">{{ $processedNote->summary }}</div>
        </div>

        @if($processedNote->key_points)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Key Points</h2>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    @foreach(json_decode($processedNote->key_points, true) as $point)
                        <li>{{ $point }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="border-t pt-6" x-data="{ showOriginal: false }">
            <button @click="showOriginal = !showOriginal" 
                    class="text-blue-600 hover:text-blue-700 font-medium">
                <span x-show="!showOriginal">Show Original Content</span>
                <span x-show="showOriginal">Hide Original Content</span>
            </button>
            
            <div x-show="showOriginal" x-transition class="mt-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-2">
                        Original File: {{ $processedNote->note->file_name }}
                    </h3>
                    <pre class="whitespace-pre-wrap text-sm text-gray-600 max-h-96 overflow-y-auto">{{ $processedNote->note->original_content }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection