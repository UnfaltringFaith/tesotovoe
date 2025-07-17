# Каталог товаров

Веб-приложение для управления каталогом товаров с иерархической системой категорий.

## Быстрый запуск

### Требования
- Docker
- Docker Compose

### Запуск проекта

1. **Клонируйте репозиторий:**
```bash
git clone <repository-url>
cd testovoe
```

2. **Запустите проект:**
```bash
docker-compose up -d
```

3. **Откройте приложение:**
- Веб-интерфейс: http://localhost:8080
- База данных: MySQL на порту 3306

### Структура Docker

```yaml
services:
  app:     # Laravel приложение (PHP 8.2 + FPM)
  nginx:   # Веб-сервер (порт 8080)
  mysql:   # База данных MySQL 8.0
```

## Архитектура сервиса

### Технологический стек
- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Blade templates + Bootstrap 5
- **База данных:** MySQL 8.0 с кодировкой UTF-8mb4

### Структура базы данных

#### Таблица `groups` (Категории)
```sql
- id (PRIMARY KEY)
- name (VARCHAR) - название категории
- id_parent (INT, NULLABLE) - ID родительской категории
- created_at, updated_at (TIMESTAMPS)
```

#### Таблица `products` (Товары)
```sql
- id (PRIMARY KEY)
- name (VARCHAR) - название товара
- id_group (INT, NULLABLE) - ID категории
- created_at, updated_at (TIMESTAMPS)
```

#### Таблица `prices` (Цены)
```sql
- id (PRIMARY KEY)
- id_product (INT) - ID товара
- price (DECIMAL) - цена товара
- created_at, updated_at (TIMESTAMPS)
```

### Модели и связи

#### Group (Категория)
```php
// Связи
- parent() - родительская категория
- children() - дочерние категории
- products() - товары в категории

// Методы
- getAllCategoryIds() - получение всех вложенных категорий
- getProductsCount() - подсчет товаров с учетом вложенности
```

#### Product (Товар)
```php
// Связи
- group() - категория товара
- price() - цена товара

// Scope методы
- sortByName() - сортировка по названию
- sortByPrice() - сортировка по цене
```

#### Price (Цена)
```php
// Связи
- product() - товар
```

### Архитектурные паттерны

1. **MVC Pattern**: Разделение логики представления данных
2. **Component Pattern**: Blade компоненты для переиспользуемых элементов
3. **Recursive Patterns**: Для работы с иерархическими категориями

#### ProductController

##### `show(Product $product)`
**Назначение:** Отображение детальной страницы товара

**Параметры:**
- `$product` - Модель товара (Route Model Binding)

**Возвращает:**
```php
[
    'product' => Product,        // Товар с связями
    'breadcrumbs' => Collection  // Хлебные крошки
]
```

### Модели и методы

#### Group Model

##### `getAllCategoryIds()`
**Назначение:** Получение всех ID вложенных категорий

**Возвращает:** `Collection<int>` - коллекция ID категорий

**Пример:**
```php
$category = Group::find(1);
$allIds = $category->getAllCategoryIds();
// Результат: [1, 5, 6, 15] - включая все подкатегории
```

##### `getProductsCount()`
**Назначение:** Подсчет товаров с учетом всех подкатегорий

**Возвращает:** `int` - количество товаров

#### Product Model

##### `sortByName($direction = 'asc')`
**Назначение:** Scope для сортировки по названию

**Параметры:**
- `$direction` - направление (`asc`, `desc`)

##### `sortByPrice($direction = 'asc')`
**Назначение:** Scope для сортировки по цене

**Параметры:**
- `$direction` - направление (`asc`, `desc`)

### Blade компоненты

#### `<x-recursive-menu :categories="$categories" />`
**Назначение:** Рекурсивное меню категорий

**Параметры:**
- `categories` - коллекция категорий верхнего уровня

#### `<x-breadcrumbs :breadcrumbs="$breadcrumbs" />`
**Назначение:** Хлебные крошки навигации

**Параметры:**
- `breadcrumbs` - коллекция элементов навигации

### Маршруты

```php
// Каталог
Route::get('/', [CatalogueController::class, 'index'])
    ->name('catalogue.index');

Route::get('/catalogue/{group}', [CatalogueController::class, 'index'])
    ->name('catalogue.index');

// Товар
Route::get('/product/{product}', [ProductController::class, 'show'])
    ->name('product.show');
```

## Функциональность

### Основные возможности
- ✅ Иерархические категории (неограниченная вложенность)
- ✅ Каталог товаров с пагинацией
- ✅ Сортировка по названию и цене
- ✅ Фильтрация по категориям
- ✅ Хлебные крошки
- ✅ Адаптивный дизайн (Bootstrap 5)
- ✅ Детальные страницы товаров
- ✅ Подсчет товаров в категориях

### Особенности реализации
- **Рекурсивное меню:** Автоматическое построение многоуровневого меню
- **Сохранение состояния:** Фильтры и сортировка сохраняются при навигации
- **SEO-friendly URL:** Человекочитаемые адреса категорий