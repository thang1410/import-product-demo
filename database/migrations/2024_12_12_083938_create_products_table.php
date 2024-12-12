<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('quality_registration_number')->nullable()->comment('số đăng ký kinh doanh');
            $table->string('specification_id')->nullable()->comment('quy cách');
            $table->integer('place_id')->nullable()->comment('nơi để');
            $table->string('position')->nullable()->comment('vị trí');
            $table->integer('category_id')->nullable()->comment('loại hàng');
            $table->integer('type_id')->nullable()->comment('phân loại');
            $table->integer('group_id')->nullable()->comment('nhóm hàng');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
