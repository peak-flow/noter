<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'folder_id',
        'file_path', 
        'file_name', 
        'format', 
        'original_content', 
        'file_size',
        'file_created_at',
        'file_modified_at',
        'is_processed'
    ];
    
    protected $casts = [
        'is_processed' => 'boolean',
        'file_created_at' => 'datetime',
        'file_modified_at' => 'datetime',
    ];
    
    public function processedNote()
    {
        return $this->hasOne(ProcessedNote::class);
    }
    
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
