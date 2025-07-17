<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogueController extends Controller
{
    public function index(Request $request, $group = null)
    {
        // Если передан ID группы, находим её
        if ($group) {
            $group = Group::find($group);
            if (!$group) {
                abort(404, 'Категория не найдена');
            }
        }
        
        // Простое получение корневых категорий для меню
        $menuCategories = Group::getRootCategories();

        // Получаем параметры сортировки и пагинации
        $sortBy = $request->get('sort', 'name'); // по умолчанию по названию
        $sortDirection = $request->get('direction', 'asc'); // по умолчанию по возрастанию
        $perPage = $request->get('per_page', 12); // по умолчанию 12 товаров на страницу

        // Валидация количества элементов на странице
        $allowedPerPage = [6, 12, 24, 48];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 12;
        }

        if (!$group) {
            // Показываем все товары если категория не выбрана
            $query = \App\Models\Product::with('price');
            
            // Применяем сортировку
            if ($sortBy === 'price') {
                // Сортировка по цене через join с таблицей prices
                $query->leftJoin('prices', 'products.id', '=', 'prices.id_product')
                      ->orderBy('prices.price', $sortDirection)
                      ->select('products.*'); // Выбираем только поля товаров
            } else {
                // Сортировка по названию
                $query->orderBy('name', $sortDirection);
            }
            
            // Применяем пагинацию
            $products = $query->paginate($perPage);
            
            // Добавляем параметры в ссылки пагинации
            $products->appends($request->query());
            
            $breadcrumbs = collect();
        } else {
            // Загружаем группу с родительскими категориями для хлебных крошек
            $group->load('parent.parent.parent');
            
            // Получаем все товары из выбранной категории и всех её подкатегорий
            $categoryIds = $this->getAllCategoryIds($group);
            $query = \App\Models\Product::whereIn('id_group', $categoryIds)
                ->with('price');
                
            // Применяем сортировку
            if ($sortBy === 'price') {
                // Сортировка по цене через join с таблицей prices
                $query->leftJoin('prices', 'products.id', '=', 'prices.id_product')
                      ->orderBy('prices.price', $sortDirection)
                      ->select('products.*'); // Выбираем только поля товаров
            } else {
                // Сортировка по названию
                $query->orderBy('name', $sortDirection);
            }

            // Применяем пагинацию
            $products = $query->paginate($perPage);

            // Добавляем параметры в ссылки пагинации
            $products->appends($request->query());

            $breadcrumbs = $group->getSimpleBreadcrumbs();
        }
        
        return view('catalogue', compact('menuCategories', 'group', 'breadcrumbs', 'products', 'sortBy', 'sortDirection', 'perPage'));
    }

    /**
     * Получить все ID категории и её подкатегорий рекурсивно
     */
    private function getAllCategoryIds($group)
    {
        $ids = [$group->id];

        // Загружаем подкатегории если они не загружены
        if (!$group->relationLoaded('children')) {
            $group->load('children.children.children');
        }

        foreach ($group->children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIds($child));
        }

        return $ids;
    }

    /**
     * Показать страницу отдельного товара
     */
    public function product(Product $product)
    {
        // Загружаем связанные данные включая родительские категории
        $product->load(['price', 'group.parent.parent.parent']);

        // Получаем хлебные крошки через группу товара
        $breadcrumbs = $product->group ? $product->group->getSimpleBreadcrumbs() : collect();

        // Добавляем сам товар в хлебные крошки как объект с нужными свойствами
        $productCrumb = new \stdClass();
        $productCrumb->name = $product->name;
        $productCrumb->id = $product->id;
        $breadcrumbs->push($productCrumb);

        // Получаем похожие товары из той же категории
        $relatedProducts = Product::where('id_group', $product->id_group)
            ->where('id', '!=', $product->id)
            ->with('price')
            ->take(6)
            ->get();

        return view('product', compact('product', 'breadcrumbs', 'relatedProducts'));
    }
}
