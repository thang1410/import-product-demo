<?php
namespace App\Services\ImportProduct;

use Illuminate\Support\Facades\DB;

class GroupService
{
    public function handleGroup($groups)
    {
        $now = now();

        $groups = array_filter(array_unique($groups), function ($group) {
            return $group !== '';
        });

        $existingGroups = DB::table('groups')
            ->whereIn('name', $groups)
            ->pluck('id', 'name')
            ->toArray();

        $newGroups = array_diff($groups, array_keys($existingGroups));

        if (!empty($newGroups)) {
            $insertData = [];
            foreach ($newGroups as $group) {
                $insertData[] = [
                    'name' => $group,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('groups')->insert($insertData);

            $newInsertedGroups = DB::table('groups')
                ->whereIn('name', $newGroups)
                ->pluck('id', 'name')
                ->toArray();

            $existingGroups = array_merge($existingGroups, $newInsertedGroups);
        }

        return $existingGroups;
    }
}
