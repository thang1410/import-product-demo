<?php
namespace App\Services\ImportProduct;

use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function handleCategories($categories)
    {
        $now = now();

        $categories = array_filter(array_unique($categories), function ($cate) {
            return $cate !== '';
        });

        $existingCategories = DB::table('categories')
            ->whereIn('name', $categories)
            ->pluck('id', 'name')
            ->toArray();

        $newCategories = array_diff($categories, array_keys($existingCategories));

        if (!empty($newCategories)) {
            $insertData = [];
            foreach ($newCategories as $category) {
                $insertData[] = [
                    'name' => $category,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('categories')->insert($insertData);

            $newInsertedCategories = DB::table('categories')
                ->whereIn('name', $newCategories)
                ->pluck('id', 'name')
                ->toArray();

            $existingCategories = array_merge($existingCategories, $newInsertedCategories);
        }

        return $existingCategories;
    }
}
