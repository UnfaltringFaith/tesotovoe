<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'id_parent',
        'name'
    ];

    public $timestamps = false;

    /**
     * Связь с родительской группой
     */
    public function parent()
    {
        return $this->belongsTo(Group::class, 'id_parent');
    }

    /**
     * Связь с дочерними группами
     */
    public function children()
    {
        return $this->hasMany(Group::class, 'id_parent');
    }

    /**
     * Связь с товарами
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'id_group');
    }

    /**
     * Получить корневые категории (с полной иерархией)
     */
    public static function getRootCategories()
    {
        return self::with([
            'products',
            'children.products',
            'children.children.products',
            'children.children.children.products'
        ])
        ->whereNull('id_parent')
        ->orWhere('id_parent', 0)
        ->get();
    }

    /**
     * Получить категории по родителю
     */
    public static function getByParent($parentId = null)
    {
        return self::with('products')
            ->where('id_parent', $parentId)
            ->get();
    }

    /**
     * Проверить, есть ли дочерние категории
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Получить простые хлебные крошки (без рекурсии)
     */
    public function getSimpleBreadcrumbs()
    {
        $breadcrumbs = [$this];
        $current = $this;
        
        // Максимум 5 уровней для безопасности
        for ($i = 0; $i < 5 && $current->parent; $i++) {
            $current = $current->parent;
            array_unshift($breadcrumbs, $current);
        }
        
        return collect($breadcrumbs);
    }

    /**
     * Получить количество товаров (только текущей категории)
     */
    public function getProductsCount()
    {
        return $this->products()->count();
    }

    /**
     * Получить общее количество товаров включая подкатегории
     */
    public function getTotalProductsCount()
    {
        // Считаем товары в текущей категории
        $count = $this->products()->count();
        
        // Добавляем товары из подкатегорий (рекурсивно для всех уровней)
        foreach ($this->children as $child) {
            $count += $child->getTotalProductsCount();
        }
        
        return $count;
    }

    /**
     * Работа с кодировкой
     */
    public function getNameAttribute($value)
    {
        if (mb_detect_encoding($value, 'UTF-8', true) === false) {
            return mb_convert_encoding($value, 'UTF-8', 'Windows-1251');
        }
        return $value;
    }
}
