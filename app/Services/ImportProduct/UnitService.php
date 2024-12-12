<?php
namespace App\Services\ImportProduct;

use Illuminate\Support\Facades\DB;

class UnitService
{
    public function handleUnit($units)
    {
        $now = now();

        $units = array_filter(array_unique($units), function ($unit) {
            return $unit !== '';
        });

        $existingUnits = DB::table('units')
            ->whereIn('name', $units)
            ->pluck('id', 'name')
            ->toArray();

        $newUnits = array_diff($units, array_keys($existingUnits));

        if (!empty($newUnits)) {
            $insertData = [];
            foreach ($newUnits as $unit) {
                $insertData[] = [
                    'name' => $unit,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('units')->insert($insertData);

            $newInsertedUnits = DB::table('units')
                ->whereIn('name', $newUnits)
                ->pluck('id', 'name')
                ->toArray();

            $existingUnits = array_merge($existingUnits, $newInsertedUnits);
        }

        return $existingUnits;
    }
}
