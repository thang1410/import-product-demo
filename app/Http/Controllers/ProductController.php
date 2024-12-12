<?php

namespace App\Http\Controllers;


use App\Http\Requests\ImportFileRequest;
use App\Imports\ProductImport;
use App\Services\ImportProduct\CategoryService;
use App\Services\ImportProduct\GroupService;
use App\Services\ImportProduct\PlaceService;
use App\Services\ImportProduct\TypeService;
use App\Services\ImportProduct\UnitService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends BaseController
{
    public function showImportProduct() {
        return view('products.import');
    }

    public function postImportFile(ImportFileRequest $request,
                                   CategoryService $categoryService,
                                   GroupService  $groupService,
                                   TypeService $typeService,
                                   PlaceService $placeService,
                                   UnitService $unitService
    ) {
        $file = $request->file('file');

        $import = new ProductImport($categoryService, $groupService, $typeService, $placeService, $unitService);
        Excel::import($import, $file);

        return back()->with('success', 'Import dữ liệu hàng hóa thành công');

    }

}
