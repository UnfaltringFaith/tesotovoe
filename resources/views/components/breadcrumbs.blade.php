{{-- Компонент хлебных крошек --}}
@props(['breadcrumbs'])

@if($breadcrumbs && $breadcrumbs->count() > 0)
<nav aria-label="Навигация" class="mb-3">
    <ol class="breadcrumb">
        {{-- Главная страница --}}
        <li class="breadcrumb-item">
            <a href="{{ route('catalogue.index') }}">Главная</a>
        </li>
        
        {{-- Категории и товары --}}
        @foreach($breadcrumbs as $index => $crumb)
            @if($loop->last)
                {{-- Последний элемент не кликабельный --}}
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb->name }}</li>
            @else
                {{-- Промежуточные категории кликабельные --}}
                <li class="breadcrumb-item">
                    @if(isset($crumb->id_parent) || method_exists($crumb, 'children'))
                        {{-- Это категория --}}
                        <a href="{{ route('catalogue.index', $crumb) }}">{{ $crumb->name }}</a>
                    @else
                        {{-- Это товар или другой объект --}}
                        <span class="text-muted">{{ $crumb->name }}</span>
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif