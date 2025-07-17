<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Каталог товаров</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #f8f9fa; 
        }
        .product-image {
            background: #e9ecef;
            border-radius: 8px;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Хлебные крошки -->
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        
        <!-- Ссылка назад -->
        @if($product->group)
            <div class="mb-3">
                <a href="{{ route('catalogue.index', $product->group) }}" class="btn btn-outline-primary">
                    ← Назад к категории "{{ $product->group->name }}"
                </a>
            </div>
        @endif

        <!-- Карточка товара -->
        <div class="card">
            <div class="card-body p-4">
                <div class="row">
                    <!-- Изображение -->
                    <div class="col-lg-6 mb-4">
                        <div class="product-image d-flex align-items-center justify-content-center text-muted">
                            Изображение товара
                        </div>
                    </div>
                    
                    <!-- Информация -->
                    <div class="col-lg-6">
                        <h1 class="h2 mb-3">{{ $product->name }}</h1>
                        
                        @if($product->group)
                            <p class="text-muted mb-3">Категория: {{ $product->group->name }}</p>
                        @endif
                        
                        @if ($product->price)
                            <h3 class="text-danger mb-4">{{ number_format($product->price->price, 0, ',', ' ') }} ₽</h3>
                        @else
                            <h4 class="text-muted mb-4">Цена не указана</h4>
                        @endif
                        
                        <div class="d-grid gap-2 d-md-block">
                            <button type="button" class="btn btn-primary btn-lg">В корзину</button>
                            <button type="button" class="btn btn-outline-secondary btn-lg">В избранное</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
