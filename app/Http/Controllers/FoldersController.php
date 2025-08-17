<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FolderImportService;
use App\Models\Folder;
use Illuminate\Support\Facades\Validator;

class FoldersController extends Controller
{
    protected FolderImportService $importService;
    
    public function __construct(FolderImportService $importService)
    {
        $this->importService = $importService;
    }
    
    /**
     * Display imported folders
     */
    public function index()
    {
        $folders = $this->importService->getImportedFolders();
        return view('folders.index', compact('folders'));
    }
    
    /**
     * Show import form
     */
    public function create()
    {
        return view('folders.create');
    }
    
    /**
     * Import a new folder
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_path' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $sourcePath = $request->input('source_path');
        $name = $request->input('name');
        
        try {
            // Check if path exists and is readable
            if (!is_dir($sourcePath) || !is_readable($sourcePath)) {
                return back()->withErrors(['source_path' => 'Directory does not exist or is not readable.'])->withInput();
            }
            
            $folder = $this->importService->importFolder($sourcePath, $name);
            
            return redirect()->route('folders.show', $folder->id)
                ->with('success', "Successfully imported {$folder->total_files} files from {$folder->name}");
                
        } catch (\Exception $e) {
            return back()->withErrors(['import' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Show folder details
     */
    public function show(Folder $folder)
    {
        $folder->load(['notes' => function($query) {
            $query->latest();
        }]);
        
        return view('folders.show', compact('folder'));
    }
    
    /**
     * Delete imported folder
     */
    public function destroy(Folder $folder)
    {
        try {
            $this->importService->deleteImportedFolder($folder);
            return redirect()->route('folders.index')
                ->with('success', 'Folder and all its files have been deleted.');
        } catch (\Exception $e) {
            return back()->withErrors(['delete' => $e->getMessage()]);
        }
    }
}
