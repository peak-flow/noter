@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
        <p class="text-gray-600 mt-2">
            @if($query)
                Found {{ $notes->total() }} results for "{{ $query }}"
            @else
                Showing all notes
            @endif
        </p>
    </div>

    <div class="space-y-4">
        @forelse($notes as $note)
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <a href="{{ route('notes.show', $note->id) }}" class="block">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $note->title }}</h3>
                    <p class="text-gray-600 mb-3">{{ Str::limit($note->summary, 200) }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ $note->category->name }}</span>
                        @if($note->subcategory)
                            <span class="ml-2 px-2 py-1 bg-gray-100 rounded">{{ $note->subcategory->name }}</span>
                        @endif
                        <span class="mx-2">•</span>
                        <span>{{ $note->created_at->format('M d, Y') }}</span>
                    </div>
                </a>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                No notes found matching your search.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notes->appends(['q' => $query])->links() }}
    </div>
@endsection