<?php

namespace App\Services;

use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class FileReaderService
{
    protected array $supportedFormats = ['txt', 'md', 'pdf', 'text', 'markdown', 'rtf'];
    
    /**
     * Scan directory for supported files
     */
    public function scanDirectory(string $path): array
    {
        if (!File::isDirectory($path)) {
            throw new \Exception("Path {$path} is not a valid directory");
        }
        
        $files = [];
        $allFiles = File::allFiles($path);
        
        foreach ($allFiles as $file) {
            $extension = strtolower($file->getExtension());
            
            if (in_array($extension, $this->supportedFormats)) {
                $files[] = [
                    'path' => $file->getPathname(),
                    'name' => $file->getFilename(),
                    'format' => $extension,
                    'size' => $file->getSize(),
                    'created_at' => Carbon::createFromTimestamp($file->getCTime()),
                    'modified_at' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            }
        }
        
        return $files;
    }
    
    /**
     * Read file content based on format
     */
    public function readFile(string $filePath): string
    {
        if (!File::exists($filePath)) {
            throw new \Exception("File {$filePath} does not exist");
        }
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return $this->readPdf($filePath);
            case 'txt':
            case 'text':
            case 'md':
            case 'markdown':
            case 'rtf':
                return File::get($filePath);
            default:
                throw new \Exception("Unsupported file format: {$extension}");
        }
    }
    
    /**
     * Read PDF file content
     */
    protected function readPdf(string $filePath): string
    {
        try {
            $pdf = new Pdf();
            return $pdf->setPdf($filePath)->text();
        } catch (\Exception $e) {
            // Fallback method if PDF reading fails
            return "PDF content could not be extracted. Error: " . $e->getMessage();
        }
    }
    
    /**
     * Get file metadata
     */
    public function getFileMetadata(string $filePath): array
    {
        if (!File::exists($filePath)) {
            throw new \Exception("File {$filePath} does not exist");
        }
        
        $file = new \SplFileInfo($filePath);
        
        return [
            'path' => $file->getPathname(),
            'name' => $file->getFilename(),
            'format' => strtolower($file->getExtension()),
            'size' => $file->getSize(),
            'created_at' => Carbon::createFromTimestamp($file->getCTime()),
            'modified_at' => Carbon::createFromTimestamp($file->getMTime()),
        ];
    }
}