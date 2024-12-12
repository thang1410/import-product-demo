<?php
namespace App\Services\ImportProduct;

use Illuminate\Support\Facades\DB;

class TypeService
{
    public function handleType($types)
    {
        $now = now();

        $types = array_filter(array_unique($types), function ($type) {
            return $type !== '';
        });

        $existingTypes = DB::table('types')
            ->whereIn('name', $types)
            ->pluck('id', 'name')
            ->toArray();

        $newTypes = array_diff($types, array_keys($existingTypes));

        if (!empty($newTypes)) {
            $insertData = [];
            foreach ($newTypes as $type) {
                $insertData[] = [
                    'name' => $type,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('types')->insert($insertData);

            $newInsertedTypes = DB::table('types')
                ->whereIn('name', $newTypes)
                ->pluck('id', 'name')
                ->toArray();

            $existingTypes = array_merge($existingTypes, $newInsertedTypes);
        }

        return $existingTypes;
    }
}
