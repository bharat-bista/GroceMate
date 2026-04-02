<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Categories in Database ===\n";

try {
    $categories = \App\Models\Category::all(['id', 'name']);
    
    if ($categories->count() === 0) {
        echo "❌ No categories found in database!\n";
    } else {
        echo "✅ Found " . $categories->count() . " categories:\n";
        foreach ($categories as $category) {
            echo "  ID: {$category->id} | Name: '{$category->name}' | Length: " . strlen($category->name) . "\n";
        }
    }
    
    echo "\n=== Testing Search ===\n";
    $searchTerm = 'juic';
    echo "Searching for: '{$searchTerm}'\n";
    
    $results = \App\Models\Category::where('name', 'LIKE', '%' . $searchTerm . '%')->get(['id', 'name']);
    echo "Results: " . $results->count() . " items\n";
    
    foreach ($results as $result) {
        echo "  Found: ID {$result->id} - '{$result->name}'\n";
    }
    
    // Test case insensitive search
    echo "\n=== Case Insensitive Search ===\n";
    $results2 = \App\Models\Category::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])->get(['id', 'name']);
    echo "Case insensitive results: " . $results2->count() . " items\n";
    
    foreach ($results2 as $result) {
        echo "  Found: ID {$result->id} - '{$result->name}'\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";