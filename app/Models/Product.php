<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'id_group',
        'name'
    ];

    public $timestamps = false;

    /**
     * Связь с группой товаров
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    /**
     * Связь с ценой товара
     */
    public function price()
    {
        return $this->hasOne(Price::class, 'id_product');
    }
}
