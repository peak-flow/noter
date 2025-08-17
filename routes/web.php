<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\FoldersController;

Route::get('/', [NotesController::class, 'index'])->name('notes.index');
Route::get('/category/{id}', [NotesController::class, 'category'])->name('notes.category');
Route::get('/note/{id}', [NotesController::class, 'show'])->name('notes.show');
Route::get('/search', [NotesController::class, 'search'])->name('notes.search');

// Folder management routes
Route::get('/folders', [FoldersController::class, 'index'])->name('folders.index');
Route::get('/folders/create', [FoldersController::class, 'create'])->name('folders.create');
Route::post('/folders', [FoldersController::class, 'store'])->name('folders.store');
Route::get('/folders/{folder}', [FoldersController::class, 'show'])->name('folders.show');
Route::delete('/folders/{folder}', [FoldersController::class, 'destroy'])->name('folders.destroy');
