<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\Note;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FolderImportService
{
    protected FileReaderService $fileReader;
    
    public function __construct(FileReaderService $fileReader)
    {
        $this->fileReader = $fileReader;
    }
    
    /**
     * Import a folder by copying all supported files
     */
    public function importFolder(string $sourcePath, string $name = null): Folder
    {
        if (!File::isDirectory($sourcePath)) {
            throw new \Exception("Source path is not a valid directory: {$sourcePath}");
        }
        
        $folderName = $name ?: basename($sourcePath);
        $storageSubPath = 'imported-notes/' . $this->generateSafeDirectoryName($folderName);
        $storagePath = storage_path('app/' . $storageSubPath);
        
        // Create storage directory
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }
        
        // Scan for supported files
        $supportedFiles = $this->fileReader->scanDirectory($sourcePath);
        
        if (empty($supportedFiles)) {
            throw new \Exception('No supported files found in the directory');
        }
        
        // Create folder record
        $folder = Folder::create([
            'name' => $folderName,
            'source_path' => $sourcePath,
            'storage_path' => $storageSubPath,
            'total_files' => count($supportedFiles),
            'processed_files' => 0,
            'imported_at' => now(),
        ]);
        
        // Copy files and create note records
        foreach ($supportedFiles as $fileInfo) {
            $this->copyFileWithMetadata($fileInfo, $storagePath, $folder);
        }
        
        return $folder;
    }
    
    /**
     * Copy a single file preserving its metadata
     */
    protected function copyFileWithMetadata(array $fileInfo, string $destinationDir, Folder $folder): void
    {
        $sourceFile = $fileInfo['path'];
        $fileName = $fileInfo['name'];
        $destinationFile = $destinationDir . '/' . $fileName;
        
        // Handle duplicate filenames
        $counter = 1;
        $originalFileName = $fileName;
        while (File::exists($destinationFile)) {
            $pathInfo = pathinfo($originalFileName);
            $fileName = $pathInfo['filename'] . "_{$counter}." . $pathInfo['extension'];
            $destinationFile = $destinationDir . '/' . $fileName;
            $counter++;
        }
        
        // Copy the file
        if (!File::copy($sourceFile, $destinationFile)) {
            throw new \Exception("Failed to copy file: {$sourceFile}");
        }
        
        // Preserve timestamps
        touch($destinationFile, $fileInfo['modified_at']->timestamp, $fileInfo['created_at']->timestamp);
        
        // Read content from the copied file
        $content = $this->fileReader->readFile($destinationFile);
        
        // Create note record
        Note::create([
            'folder_id' => $folder->id,
            'file_path' => $destinationFile,
            'file_name' => $fileName,
            'format' => $fileInfo['format'],
            'original_content' => $content,
            'file_size' => $fileInfo['size'],
            'file_created_at' => $fileInfo['created_at'],
            'file_modified_at' => $fileInfo['modified_at'],
            'is_processed' => false,
        ]);
    }
    
    /**
     * Generate a safe directory name for storage
     */
    protected function generateSafeDirectoryName(string $name): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $safeName = preg_replace('/_+/', '_', $safeName);
        $safeName = trim($safeName, '_');
        
        // Add timestamp to ensure uniqueness
        return $safeName . '_' . now()->format('Y-m-d_H-i-s');
    }
    
    /**
     * Get all imported folders with statistics
     */
    public function getImportedFolders()
    {
        return Folder::withCount('notes')
            ->orderBy('imported_at', 'desc')
            ->get();
    }
    
    /**
     * Delete imported folder and all its files
     */
    public function deleteImportedFolder(Folder $folder): void
    {
        $storagePath = storage_path('app/' . $folder->storage_path);
        
        // Delete physical files
        if (File::exists($storagePath)) {
            File::deleteDirectory($storagePath);
        }
        
        // Delete database records (notes will be cascade deleted)
        $folder->delete();
    }
}