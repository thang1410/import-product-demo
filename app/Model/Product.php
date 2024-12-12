<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function group() {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function type() {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function place() {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function specification() {
        return $this->belongsTo(Specification::class, 'specification');
    }

    public function prices() {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function units() {
        return $this->hasMany(ProductUnit::class, 'product_id');
    }

}
