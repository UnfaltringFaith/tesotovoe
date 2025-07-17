{{-- Рекурсивное многоуровневое меню --}}
@props(['categories', 'level' => 0])

<ul class="menu-level-{{ $level }}">
    @foreach ($categories as $category)
        <li class="menu-item">
            <div class="menu-header">
                <a href="{{ route('catalogue.index', $category) }}" class="menu-link">
                    {{ $category->name }}
                    @php
                        $totalProducts = $category->getTotalProductsCount();
                    @endphp
                    @if ($totalProducts > 0)
                        <span class="products-count">({{ $totalProducts }})</span>
                    @endif
                </a>
                
                @if ($category->children->count() > 0)
                    <button class="expand-btn" onclick="toggleMenu({{ $category->id }}, {{ $level }})" type="button">
                        <span class="arrow" id="arrow-{{ $category->id }}">▶</span>
                    </button>
                @endif
            </div>
            
            {{-- Рекурсивно показываем подменю --}}
            @if ($category->children->count() > 0)
                <div class="submenu" id="submenu-{{ $category->id }}" style="display: none;">
                    <x-recursive-menu :categories="$category->children" :level="$level + 1" />
                </div>
            @endif
        </li>
    @endforeach
</ul>

@if ($level === 0)
<script>
function toggleMenu(categoryId, level) {
    const submenu = document.getElementById('submenu-' + categoryId);
    const arrow = document.getElementById('arrow-' + categoryId);
    
    if (submenu.style.display === 'none') {
        submenu.style.display = 'block';
        arrow.textContent = '▼';
    } else {
        submenu.style.display = 'none';
        arrow.textContent = '▶';
    }
}
</script>

<style>
.menu-level-0 {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-level-1, .menu-level-2, .menu-level-3 {
    list-style: none;
    padding: 0;
    margin: 0;
    padding-left: 20px;
}

.menu-item {
    border-bottom: 1px solid #eee;
}

.menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: white;
}

.menu-level-1 .menu-header {
    background: #f8f9fa;
    padding-left: 20px;
}

.menu-level-2 .menu-header {
    background: #e9ecef;
    padding-left: 40px;
}

.menu-level-3 .menu-header {
    background: #dee2e6;
    padding-left: 60px;
}

.menu-link {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    display: flex;
    align-items: center;
    flex: 1;
    font-size: 14px;
}

.menu-level-1 .menu-link {
    font-size: 13px;
    font-weight: normal;
}

.menu-level-2 .menu-link {
    font-size: 12px;
    font-weight: normal;
    color: #555;
}

.menu-level-3 .menu-link {
    font-size: 11px;
    font-weight: normal;
    color: #666;
}

.products-count {
    color: #6c757d;
    font-size: 11px;
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
    font-weight: normal;
}

.menu-level-1 .products-count {
    background: #d4edda;
    color: #155724;
}

.menu-level-2 .products-count {
    background: #cce5ff;
    color: #004085;
}

.expand-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    font-size: 12px;
    color: #6c757d;
}

.expand-btn:hover {
    color: #333;
}

.submenu {
    border-left: 2px solid #dee2e6;
    margin-left: 10px;
}

.menu-item:hover > .menu-header {
    background-color: #f1f3f4;
}

.menu-level-1 .menu-item:hover > .menu-header {
    background-color: #e2e6ea;
}

.menu-level-2 .menu-item:hover > .menu-header {
    background-color: #d1ecf1;
}
</style>
@endif
