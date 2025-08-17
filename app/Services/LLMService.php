<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Category;
use App\Models\Subcategory;

class LLMService
{
    /**
     * Analyze note content and generate categories
     */
    public function analyzeAndCategorize(string $content): array
    {
        $prompt = "Analyze this note and suggest a category and subcategory. 
                  Respond in JSON format with fields: 
                  'category' (single word or short phrase), 
                  'subcategory' (optional, single word or short phrase),
                  'category_description' (brief description),
                  'subcategory_description' (brief description if subcategory exists).
                  
                  Note content: " . substr($content, 0, 3000);
        
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that categorizes notes. Always respond in valid JSON format.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 500,
            ]);
            
            $result = json_decode($response->choices[0]->message->content, true);
            
            if (!$result) {
                throw new \Exception('Invalid JSON response from LLM');
            }
            
            return $result;
        } catch (\Exception $e) {
            // Fallback categorization
            return [
                'category' => 'Uncategorized',
                'subcategory' => null,
                'category_description' => 'Notes that need manual categorization',
                'subcategory_description' => null,
            ];
        }
    }
    
    /**
     * Summarize and rewrite note content
     */
    public function summarizeNote(string $content): array
    {
        $prompt = "Rewrite this note in a concise, easy-to-understand manner. 
                  Provide:
                  1. A clear title
                  2. A summary (2-3 paragraphs max)
                  3. Key points (bullet points)
                  
                  Respond in JSON format with fields:
                  'title' (string),
                  'summary' (string),
                  'key_points' (array of strings).
                  
                  Note content: " . substr($content, 0, 4000);
        
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes and organizes notes clearly. Always respond in valid JSON format.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
                'max_tokens' => 1000,
            ]);
            
            $result = json_decode($response->choices[0]->message->content, true);
            
            if (!$result) {
                throw new \Exception('Invalid JSON response from LLM');
            }
            
            // Ensure key_points is properly formatted
            if (isset($result['key_points']) && is_array($result['key_points'])) {
                $result['key_points'] = json_encode($result['key_points']);
            } else {
                $result['key_points'] = json_encode([]);
            }
            
            return $result;
        } catch (\Exception $e) {
            // Fallback summary
            return [
                'title' => 'Untitled Note',
                'summary' => substr($content, 0, 500) . '...',
                'key_points' => json_encode(['Original content preserved']),
            ];
        }
    }
    
    /**
     * Find or create category
     */
    public function findOrCreateCategory(string $name, string $description = null): Category
    {
        return Category::firstOrCreate(
            ['name' => $name],
            ['description' => $description ?? 'Auto-generated category']
        );
    }
    
    /**
     * Find or create subcategory
     */
    public function findOrCreateSubcategory(int $categoryId, string $name, string $description = null): Subcategory
    {
        return Subcategory::firstOrCreate(
            [
                'category_id' => $categoryId,
                'name' => $name
            ],
            ['description' => $description ?? 'Auto-generated subcategory']
        );
    }
}