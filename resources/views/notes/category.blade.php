@extends('layouts.app')

@section('title', $category->name)

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
                        <span class="ml-1 text-gray-700">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $category->description }}</p>
    </div>

    @if($category->subcategories->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Subcategories</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($category->subcategories as $subcategory)
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                        {{ $subcategory->name }} ({{ $subcategory->note_count }})
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($notes as $note)
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <a href="{{ route('notes.show', $note->id) }}" class="block">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $note->title }}</h3>
                    <p class="text-gray-600 mb-3">{{ Str::limit($note->summary, 200) }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <span>{{ $note->note->file_name }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $note->created_at->format('M d, Y') }}</span>
                        @if($note->subcategory)
                            <span class="mx-2">•</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">{{ $note->subcategory->name }}</span>
                        @endif
                    </div>
                </a>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                No notes found in this category.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notes->links() }}
    </div>
@endsection