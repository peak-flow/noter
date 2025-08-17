<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ProcessedNote;
use App\Models\Note;

class NotesController extends Controller
{
    /**
     * Display dashboard with categories and statistics
     */
    public function index()
    {
        $categories = Category::withCount('processedNotes')->get();
        $totalNotes = Note::count();
        $processedNotes = Note::where('is_processed', true)->count();
        $unprocessedNotes = $totalNotes - $processedNotes;
        
        return view('notes.index', compact('categories', 'totalNotes', 'processedNotes', 'unprocessedNotes'));
    }
    
    /**
     * Display notes in a category
     */
    public function category($id)
    {
        $category = Category::with('subcategories')->findOrFail($id);
        $notes = ProcessedNote::with('note')
            ->where('category_id', $id)
            ->paginate(15);
        
        return view('notes.category', compact('category', 'notes'));
    }
    
    /**
     * Display a single processed note
     */
    public function show($id)
    {
        $processedNote = ProcessedNote::with(['note', 'category', 'subcategory'])->findOrFail($id);
        
        return view('notes.show', compact('processedNote'));
    }
    
    /**
     * Search notes
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $notes = ProcessedNote::with(['category', 'subcategory'])
            ->where('title', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
            ->paginate(15);
        
        return view('notes.search', compact('notes', 'query'));
    }
}
