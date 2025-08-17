<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileReaderService;
use App\Services\LLMService;
use App\Models\Note;
use App\Models\ProcessedNote;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;

class ProcessNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notes:process {--path= : Directory path to scan} {--folder= : Folder ID to process} {--limit=10 : Number of notes to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process imported notes or scan directory for notes and process them with LLM';
    
    protected FileReaderService $fileReader;
    protected LLMService $llmService;

    /**
     * Create a new command instance
     */
    public function __construct(FileReaderService $fileReader, LLMService $llmService)
    {
        parent::__construct();
        $this->fileReader = $fileReader;
        $this->llmService = $llmService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $folderId = $this->option('folder');
        $path = $this->option('path');
        $limit = (int) $this->option('limit');
        
        if ($folderId) {
            return $this->processImportedFolder($folderId, $limit);
        } elseif ($path) {
            return $this->processDirectoryPath($path, $limit);
        } else {
            return $this->processAllUnprocessedNotes($limit);
        }
    }
    
    /**
     * Process notes from an imported folder
     */
    protected function processImportedFolder(int $folderId, int $limit): int
    {
        $folder = Folder::find($folderId);
        
        if (!$folder) {
            $this->error("Folder with ID {$folderId} not found");
            return 1;
        }
        
        $this->info("Processing folder: {$folder->name}");
        
        $notes = $folder->notes()
            ->where('is_processed', false)
            ->limit($limit)
            ->get();
            
        if ($notes->isEmpty()) {
            $this->warn('No unprocessed notes found in this folder');
            return 0;
        }
        
        $processed = 0;
        $progressBar = $this->output->createProgressBar($notes->count());
        $progressBar->start();
        
        foreach ($notes as $note) {
            if ($this->processNote($note, $folder)) {
                $processed++;
                $folder->increment('processed_files');
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully processed {$processed} notes from folder: {$folder->name}");
        
        return 0;
    }
    
    /**
     * Process notes from a directory path (legacy method)
     */
    protected function processDirectoryPath(string $path, int $limit): int
    {
        $this->info("Scanning directory: {$path}");
        
        try {
            // Scan directory for files
            $files = $this->fileReader->scanDirectory($path);
            $this->info("Found " . count($files) . " supported files");
            
            if (empty($files)) {
                $this->warn('No supported files found in the directory');
                return 0;
            }
            
            $processed = 0;
            $progressBar = $this->output->createProgressBar(min($limit, count($files)));
            $progressBar->start();
            
            foreach ($files as $file) {
                if ($processed >= $limit) {
                    break;
                }
                
                // Check if file already processed
                $existingNote = Note::where('file_path', $file['path'])->first();
                
                if ($existingNote && $existingNote->is_processed) {
                    continue;
                }
                
                if ($this->processFileFromPath($file)) {
                    $processed++;
                }
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
            $this->info("Successfully processed {$processed} notes");
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Process all unprocessed notes from all folders
     */
    protected function processAllUnprocessedNotes(int $limit): int
    {
        $this->info("Processing unprocessed notes from all imported folders");
        
        $notes = Note::where('is_processed', false)
            ->whereNotNull('folder_id')
            ->with('folder')
            ->limit($limit)
            ->get();
            
        if ($notes->isEmpty()) {
            $this->warn('No unprocessed notes found. Import a folder first or use --path option.');
            return 0;
        }
        
        $processed = 0;
        $progressBar = $this->output->createProgressBar($notes->count());
        $progressBar->start();
        
        foreach ($notes as $note) {
            if ($this->processNote($note, $note->folder)) {
                $processed++;
                $note->folder->increment('processed_files');
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully processed {$processed} notes");
        
        return 0;
    }
    
    /**
     * Process a single note
     */
    protected function processNote(Note $note, ?Folder $folder = null): bool
    {
        DB::beginTransaction();
        try {
            // Analyze and categorize
            $categoryData = $this->llmService->analyzeAndCategorize($note->original_content);
            
            // Create or find category
            $category = $this->llmService->findOrCreateCategory(
                $categoryData['category'],
                $categoryData['category_description'] ?? null
            );
            
            // Create or find subcategory if provided
            $subcategory = null;
            if (!empty($categoryData['subcategory'])) {
                $subcategory = $this->llmService->findOrCreateSubcategory(
                    $category->id,
                    $categoryData['subcategory'],
                    $categoryData['subcategory_description'] ?? null
                );
            }
            
            // Summarize note
            $summary = $this->llmService->summarizeNote($note->original_content);
            
            // Create processed note
            ProcessedNote::updateOrCreate(
                ['note_id' => $note->id],
                [
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory?->id,
                    'title' => $summary['title'],
                    'summary' => $summary['summary'],
                    'key_points' => $summary['key_points'],
                    'metadata' => [
                        'original_file' => $note->file_name,
                        'format' => $note->format,
                        'folder' => $folder?->name,
                        'processed_at' => now(),
                    ],
                ]
            );
            
            // Mark note as processed
            $note->update(['is_processed' => true]);
            
            // Update category counts
            $category->increment('note_count');
            if ($subcategory) {
                $subcategory->increment('note_count');
            }
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError processing {$note->file_name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process a file from path (legacy method)
     */
    protected function processFileFromPath(array $file): bool
    {
        DB::beginTransaction();
        try {
            // Read file content
            $content = $this->fileReader->readFile($file['path']);
            
            // Store original note
            $note = Note::updateOrCreate(
                ['file_path' => $file['path']],
                [
                    'file_name' => $file['name'],
                    'format' => $file['format'],
                    'original_content' => $content,
                    'file_size' => $file['size'],
                    'file_created_at' => $file['created_at'],
                    'file_modified_at' => $file['modified_at'],
                ]
            );
            
            return $this->processNote($note);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError processing {$file['name']}: " . $e->getMessage());
            return false;
        }
    }
}
