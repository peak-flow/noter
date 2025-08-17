@extends('layouts.app')

@section('title', 'Imported Folders')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Imported Folders</h1>
                <p class="text-gray-600 mt-2">Manage your imported note folders</p>
            </div>
            <a href="{{ route('folders.create') }}" 
               class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                Import New Folder
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        @forelse($folders as $folder)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <a href="{{ route('folders.show', $folder->id) }}" class="hover:text-blue-600">
                                {{ $folder->name }}
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $folder->source_path }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                {{ $folder->notes_count }} files imported
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $folder->imported_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this folder and all its files?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-700 p-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($folder->total_files > 0)
                    <div class="mb-3">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Processing Progress</span>
                            <span>{{ $folder->processed_files }}/{{ $folder->total_files }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ $folder->progress_percentage }}%"></div>
                        </div>
                    </div>
                @endif
                
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                        </svg>
                        {{ $folder->total_files }} files
                    </span>
                    @if($folder->processed_files > 0)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $folder->processed_files }} processed
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No folders imported</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by importing your first folder of notes.</p>
                <div class="mt-6">
                    <a href="{{ route('folders.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Import Folder
                    </a>
                </div>
            </div>
        @endforelse
    </div>
@endsection