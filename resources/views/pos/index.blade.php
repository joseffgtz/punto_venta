@extends('layouts.app')

@section('title', 'Punto de venta | UBM')

@section('content')
<header class="topbar">
    <div>
        <span class="eyebrow">Punto de venta</span>
        <h1>Abarrotes UBM</h1>
        <p>
            @if($isAdmin)
                Administra productos, consulta el inventario y confirma ventas sin cambiar de pantalla.
            @else
                Consulta productos disponibles y compra directamente con tu usuario registrado.
            @endif
        </p>
    </div>
    <div class="user-box">
        <span>Sesión activa</span>
        <strong>{{ session('user_name') }}</strong>
        <span class="role-pill {{ $isAdmin ? 'admin' : 'client' }}">{{ $isAdmin ? 'Administrador' : 'Cliente' }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="ghost-btn" type="submit">Cerrar sesión</button>
        </form>
    </div>
</header>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div id="toast" class="toast" hidden></div>

<section class="stats-grid">
    <article class="stat-card">
        <span>Productos registrados</span>
        <strong id="statProducts">{{ $totalProducts }}</strong>
    </article>
    <article class="stat-card">
        <span>Unidades en almacén</span>
        <strong id="statStock">{{ $totalStock }}</strong>
    </article>
    <article class="stat-card">
        <span>Valor del inventario</span>
        <strong>$<span id="statValue">{{ number_format((float) $inventoryValue, 2) }}</span></strong>
    </article>
</section>

<main class="pos-grid {{ $isAdmin ? 'admin-mode' : 'client-mode' }}">
    @if($isAdmin)
        <section class="panel form-panel">
            <div class="panel-title">
                <div>
                    <span class="eyebrow">Datos del producto</span>
                    <h2>Registrar producto</h2>
                </div>
            </div>

            <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label>
                    <span>Imagen</span>
                    <input id="imageInput" type="file" name="image" accept="image/*">
                </label>
                <div class="image-preview" id="imagePreview">
                    <img src="{{ asset('images/no-image.svg') }}" alt="Vista previa">
                </div>

                <label>
                    <span>Nombre</span>
                    <input type="text" name="name" placeholder="Ej. Coca-Cola 600 ml" required>
                </label>

                <label>
                    <span>Descripción</span>
                    <textarea name="description" rows="3" placeholder="Ej. Refresco individual" required></textarea>
                </label>

                <label>
                    <span>Marca</span>
                    <input type="text" name="brand" placeholder="Ej. Coca-Cola" required>
                </label>

                <div class="two-cols">
                    <label>
                        <span>Precio</span>
                        <input type="number" name="price" step="0.01" min="0.01" placeholder="0.00" required>
                    </label>
                    <label>
                        <span>Stock</span>
                        <input type="number" name="stock" min="0" placeholder="0" required>
                    </label>
                </div>

                <button class="primary-btn" type="submit">Registrar producto</button>
            </form>
        </section>
    @endif

    <section class="panel table-panel">
        <div class="panel-title table-heading">
            <div>
                <span class="eyebrow">Consulta de productos</span>
                <h2>{{ $isAdmin ? 'Inventario registrado' : 'Productos disponibles' }}</h2>
            </div>
            <input id="searchInput" class="search-box" type="search" placeholder="Buscar producto, marca o descripción...">
        </div>

        <div class="table-wrap">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>{{ $isAdmin ? 'Vender' : 'Comprar' }}</th>
                        @if($isAdmin)
                            <th>Administrar</th>
                        @endif
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody id="productsBody">
                    @forelse($products as $product)
                        <tr data-search="{{ strtolower($product->name.' '.$product->description.' '.$product->brand) }}" data-id="{{ $product->id }}">
                            <td>
                                <button class="sell-btn" type="button" data-product='@json($product)' {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                    {{ $isAdmin ? 'Vender' : 'Comprar' }}
                                </button>
                            </td>
                            @if($isAdmin)
                                <td>
                                    <div class="row-actions">
                                        <button class="edit-btn" type="button" data-product='@json($product)'>Editar</button>
                                        <button class="delete-btn" type="button" data-id="{{ $product->id }}" data-name="{{ e($product->name) }}">Eliminar</button>
                                    </div>
                                </td>
                            @endif
                            <td><img class="thumb" src="{{ $product->image_url }}" alt="{{ $product->name }}"></td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->description }}</td>
                            <td>{{ $product->brand }}</td>
                            <td>${{ number_format((float) $product->price, 2) }}</td>
                            <td><span class="stock-pill {{ $product->stock <= 0 ? 'empty' : '' }}">{{ $product->stock }}</span></td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="{{ $isAdmin ? 8 : 7 }}">Todavía no hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="saleModal" class="modal-backdrop" hidden>
    <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="saleTitle">
        <button class="modal-close" type="button" id="closeSaleModal">×</button>
        <span class="eyebrow">{{ $isAdmin ? 'Confirmar venta' : 'Confirmar compra' }}</span>
        <h2 id="saleTitle">{{ $isAdmin ? 'Vender producto' : 'Comprar producto' }}</h2>
        <p id="saleDescription">Revisa la información antes de confirmar.</p>

        <form id="saleForm">
            <input type="hidden" id="saleProductId">
            <label>
                <span>Cantidad</span>
                <input id="saleQuantity" type="number" value="1" min="1" required>
            </label>
            <div class="sale-summary">
                <span>Precio unitario</span>
                <strong>$<span id="saleUnitPrice">0.00</span></strong>
                <span>Total</span>
                <strong>$<span id="saleTotal">0.00</span></strong>
            </div>
            <button class="primary-btn" type="submit">{{ $isAdmin ? 'Confirmar venta' : 'Confirmar compra' }}</button>
        </form>
    </section>
</div>

@if($isAdmin)
<div id="editModal" class="modal-backdrop" hidden>
    <section class="modal-card wide-modal" role="dialog" aria-modal="true" aria-labelledby="editTitle">
        <button class="modal-close" type="button" id="closeEditModal">×</button>
        <span class="eyebrow">Administrar producto</span>
        <h2 id="editTitle">Editar producto</h2>
        <p>Modifica los datos del producto. Si no seleccionas imagen, se conservará la actual.</p>

        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editProductId">
            <label>
                <span>Imagen nueva opcional</span>
                <input id="editImageInput" type="file" name="image" accept="image/*">
            </label>
            <div class="image-preview small-preview" id="editImagePreview">
                <img src="{{ asset('images/no-image.svg') }}" alt="Vista previa edición">
            </div>

            <label>
                <span>Nombre</span>
                <input id="editName" type="text" name="name" required>
            </label>

            <label>
                <span>Descripción</span>
                <textarea id="editDescription" name="description" rows="3" required></textarea>
            </label>

            <label>
                <span>Marca</span>
                <input id="editBrand" type="text" name="brand" required>
            </label>

            <div class="two-cols">
                <label>
                    <span>Precio</span>
                    <input id="editPrice" type="number" name="price" step="0.01" min="0.01" required>
                </label>
                <label>
                    <span>Stock</span>
                    <input id="editStock" type="number" name="stock" min="0" required>
                </label>
            </div>

            <button class="primary-btn" type="submit">Guardar cambios</button>
        </form>
    </section>
</div>
@endif
@endsection

@push('scripts')
<script>
    window.routes = {
        productsStore: @json(route('products.store')),
        productBase: @json(url('/productos')),
        salesStore: @json(url('/ventas')),
    };
    window.appConfig = {
        isAdmin: @json($isAdmin),
        actionText: @json($isAdmin ? 'Vender' : 'Comprar'),
        confirmText: @json($isAdmin ? 'Confirmar venta' : 'Confirmar compra'),
        modalActionText: @json($isAdmin ? 'Vender' : 'Comprar'),
    };
</script>
<script src="{{ asset('js/pos.js') }}"></script>
@endpush
