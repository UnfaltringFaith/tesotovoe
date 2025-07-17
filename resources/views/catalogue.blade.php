<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background: white;
            border-radius: 8px;
            position: sticky;
            top: 20px;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-lg-3 mb-3">
                <div class="sidebar p-3">
                    <h5>Категории</h5>
                    <x-recursive-menu :categories="$menuCategories" />
                </div>
            </div>

            <div class="col-lg-9">
                <!-- Хлебные крошки -->
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

                @if ($group)
                    <h1>{{ $group->name }}</h1>
                @else
                    <h1>Все товары</h1>
                @endif

                @if (isset($products) && $products->count() > 0)
                    <div class="bg-white p-3 rounded mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="btn-group me-3" role="group">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc']) }}"
                                        class="btn btn-sm {{ $sortBy === 'name' && $sortDirection === 'asc' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        А-Я
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'desc']) }}"
                                        class="btn btn-sm {{ $sortBy === 'name' && $sortDirection === 'desc' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        Я-А
                                    </a>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'asc']) }}"
                                        class="btn btn-sm {{ $sortBy === 'price' && $sortDirection === 'asc' ? 'btn-success' : 'btn-outline-success' }}">
                                        Дешевле
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'desc']) }}"
                                        class="btn btn-sm {{ $sortBy === 'price' && $sortDirection === 'desc' ? 'btn-success' : 'btn-outline-success' }}">
                                        Дороже
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @foreach ([6, 12, 18] as $count)
                                    <a href="{{ request()->fullUrlWithQuery(['per_page' => $count, 'page' => 1]) }}"
                                        class="btn btn-sm {{ $perPage == $count ? 'btn-secondary' : 'btn-outline-secondary' }} me-1">
                                        {{ $count }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Товары -->
                    <div class="row g-3">
                        @foreach ($products as $product)
                            <div class="col-md-4">
                                <div class="card product-card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        @if ($product->price)
                                            <p class="text-danger fw-bold mb-2">
                                                {{ number_format($product->price->price, 0, ',', ' ') }} ₽</p>
                                        @else
                                            <p class="text-muted mb-2">Цена не указана</p>
                                        @endif
                                        <a href="{{ route('product.show', $product) }}" class="btn btn-primary btn-sm">
                                            Подробнее
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Пагинация -->
                    @if ($products->hasPages())
                        <div class="mt-4">
                            <p class="mb-2">
                                Страница: 
                                @for ($i = 1; $i <= $products->lastPage(); $i++)
                                    @if ($i == $products->currentPage())
                                        <strong>{{ $i }}</strong>
                                    @else
                                        <a href="{{ $products->url($i) }}">{{ $i }}</a>
                                    @endif
                                    @if ($i < $products->lastPage())
                                        {{ ' ' }}
                                    @endif
                                @endfor
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <h4 class="text-muted">Товары не найдены</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
