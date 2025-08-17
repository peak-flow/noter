<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'note_count'];
    
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    
    public function processedNotes()
    {
        return $this->hasMany(ProcessedNote::class);
    }
}
