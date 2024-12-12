<?php
namespace App\Services\ImportProduct;

use Illuminate\Support\Facades\DB;

class PlaceService
{
    public function handlePlace($places)
    {
        $now = now();

        $places = array_filter($places, function ($place) {
            return !empty($place['code']) && !empty($place['name']);
        });

        $places = array_unique($places, SORT_REGULAR);

        $codes = array_column($places, 'code');

        $existingPlaces = DB::table('places')
            ->whereIn('code', $codes)
            ->pluck('id', 'code')
            ->toArray();

        $newPlaces = array_filter($places, function ($place) use ($existingPlaces) {
            return !isset($existingPlaces[$place['code']]);
        });

        if (!empty($newPlaces)) {
            $insertData = [];
            foreach ($newPlaces as $place) {
                $insertData[] = [
                    'code' => $place['code'],
                    'name' => $place['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('places')->insert($insertData);

            $newInsertedPlaces = DB::table('places')
                ->whereIn('code', array_column($newPlaces, 'code'))
                ->pluck('id', 'code')
                ->toArray();

            $existingPlaces = array_merge($existingPlaces, $newInsertedPlaces);
        }

        return $existingPlaces;
    }
}
