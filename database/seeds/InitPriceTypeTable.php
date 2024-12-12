<?php

use Illuminate\Database\Seeder;

class InitPriceTypeTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataInsert = [
            [
                'name' => 'giá nhập',
            ],
            [
                'name' => 'giá bán',
            ],
            [
                'name' => 'giá kê khai',
            ],
            [
                'name' => 'giá nhập giá vốn',
            ],
            [
                'name' => 'giá niêm yết',
            ],
            [
                'name' => 'giá vốn đích danh',
            ],
            [
                'name' => 'giá hapu',
            ],
        ];

        \Illuminate\Support\Facades\DB::table('price_types')->insert($dataInsert);
    }
}
