<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'name',
        'source_path',
        'storage_path',
        'total_files',
        'processed_files',
        'imported_at'
    ];
    
    protected $casts = [
        'imported_at' => 'datetime',
    ];
    
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    
    public function getProgressPercentageAttribute()
    {
        if ($this->total_files === 0) {
            return 0;
        }
        
        return round(($this->processed_files / $this->total_files) * 100);
    }
}
