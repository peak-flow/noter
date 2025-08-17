<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedNote extends Model
{
    protected $fillable = [
        'note_id',
        'category_id', 
        'subcategory_id',
        'title',
        'summary',
        'key_points',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
    ];
    
    public function note()
    {
        return $this->belongsTo(Note::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
