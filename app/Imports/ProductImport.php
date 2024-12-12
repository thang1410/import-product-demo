<?php
namespace App\Imports;
use App\Services\ImportProduct\CategoryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductImport implements ToCollection, WithStartRow, WithChunkReading
{
    private $import_rows = 0;
    private $skip_rows = 0;
    private $invalid_rows = [];

    private $categoryService;
    private $groupService;
    private $typeService;
    private $placeService;
    private $unitService;

    public function __construct($categoryService, $groupService, $typeService, $placeService, $unitService)
    {
        $this->categoryService = $categoryService;
        $this->groupService = $groupService;
        $this->typeService = $typeService;
        $this->placeService = $placeService;
        $this->unitService = $unitService;
    }

    public function collection(Collection $rows)
    {
        $now = now();
        $data = [];
        $categories = [];
        $groups = [];
        $types = [];
        $places = [];
        $units = [];
        $productUnits = [];
        $productUnitsTemp = [];

        foreach ($rows as $row) {
            $data[] = [
                'name' => $row[1],
                'code' => $row[2],
                'quality_registration_number' => $row[18],
                'position' => $row[22],
                'category_name' => trim($row[23]),
                'group_name' => trim($row[25]),
                'type_name' => trim($row[24]),
                'place_code' => trim($row[20]),
                'place_name' => trim($row[21]),
                'unit_name_1' => trim($row[3]),
                'unit_name_2' => trim($row[4]),
                'unit_name_3' => trim($row[6]),
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ];

            $categories[] = trim($row[23]);
            $groups[] = trim($row[25]);
            $types[] = trim($row[24]);
            $units = array_merge($units, array_filter([
                trim($row[3]),
                trim($row[4]),
                trim($row[6]),
            ]));
            $places[] = ['code' => trim($row[20]), 'name' => trim($row[21])];

        }

        $productExists = DB::table('products')
            ->whereIn('code', array_column($data, 'code'))
            ->pluck('code')
            ->toArray();

        $data = array_filter($data, function ($item) use ($productExists) {
            return !in_array($item['code'], $productExists);
        });

        $categoryIds = $this->categoryService->handleCategories($categories);
        $groupIds = $this->groupService->handleGroup($groups);
        $typeIds = $this->typeService->handleType($types);
        $placeIds = $this->placeService->handlePlace($places);
        $unitIds = $this->unitService->handleUnit($units);

        foreach ($data as $index => &$item) {
            $item['category_id'] = $categoryIds[$item['category_name']] ?? null;
            $item['group_id'] = $groupIds[$item['group_name']] ?? null;
            $item['type_id'] = $typeIds[$item['type_name']] ?? null;
            $item['place_id'] = $placeIds[$item['place_code']] ?? null;

            $productUnitsTemp[$index] = [
                'unit_name_1' => $item['unit_name_1'],
                'unit_name_2' => $item['unit_name_2'],
                'unit_name_3' => $item['unit_name_3'],
            ];

            unset($item['category_name'], $item['group_name'], $item['type_name'], $item['place_code'],
                $item['place_name'], $item['unit_name_1'], $item['unit_name_2'], $item['unit_name_3']);
        }

        $data = array_filter($data, function ($item) {
            return !empty($item['code']);
        });

        DB::table('products')->insert($data);

        $productIds = DB::table('products')
            ->where('created_at', $now)
            ->pluck('id')
            ->toArray();

        // xử lý units
        foreach ($data as $index => $product) {
            $productId = $productIds[$index] ?? null;

            if ($productId) {
                foreach (['unit_name_1', 'unit_name_2', 'unit_name_3'] as $unitField) {
                    $unitName = $productUnitsTemp[$index][$unitField] ?? null;

                    if (!empty($unitName) && isset($unitIds[$unitName])) {
                        $productUnits[] = [
                            'product_id' => $productId,
                            'unit_id' => $unitIds[$unitName],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        DB::table('product_units')->insert($productUnits);

        // xử lý price
        $priceTypeIds = DB::table('price_types')
            ->pluck('id', 'name')
            ->toArray();

        $priceTypes = [
            'giá nhập' => 8,
            'giá bán' => 9,
            'giá kê khai' => 10,
            'giá nhập giá Vốn' => 11,
            'giá niêm yết' => 12,
            'giá vốn đích danh' => 13,
            'giá hapu' => 14
        ];

        $productPrices = [];

        foreach ($rows as $index => $row) {
            $productId = $productIds[$index] ?? null;

            if (!$productId) {
                continue;
            }

            foreach ($priceTypes as $priceTypeName => $columnIndex) {
                $price = trim($row[$columnIndex]);

                if (!empty($price)) {
                    $priceTypeId = $priceTypeIds[$priceTypeName] ?? null;
                    if ($priceTypeId) {
                        $productPrices[] = [
                            'product_id' => $productId,
                            'price_type' => $priceTypeId,
                            'price' => $price,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        DB::table('product_prices')->insert($productPrices);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getImportCount(): int
    {
        return $this->import_rows;
    }

    public function getSkipCount(): int
    {
        return $this->skip_rows;
    }

    public function getInvalidRow()
    {
        return $this->invalid_rows;
    }


}
