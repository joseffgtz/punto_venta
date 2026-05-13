<?php $__env->startSection('title', 'Punto de venta | UBM'); ?>

<?php $__env->startSection('content'); ?>
<header class="topbar">
    <div>
        <span class="eyebrow">Punto de venta</span>
        <h1>Abarrotes UBM</h1>
        <p>
            <?php if($isAdmin): ?>
                Administra productos, consulta el inventario y confirma ventas sin cambiar de pantalla.
            <?php else: ?>
                Consulta productos disponibles y compra directamente con tu usuario registrado.
            <?php endif; ?>
        </p>
    </div>
    <div class="user-box">
        <span>Sesión activa</span>
        <strong><?php echo e(session('user_name')); ?></strong>
        <span class="role-pill <?php echo e($isAdmin ? 'admin' : 'client'); ?>"><?php echo e($isAdmin ? 'Administrador' : 'Cliente'); ?></span>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="ghost-btn" type="submit">Cerrar sesión</button>
        </form>
    </div>
</header>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div id="toast" class="toast" hidden></div>

<section class="stats-grid">
    <article class="stat-card">
        <span>Productos registrados</span>
        <strong id="statProducts"><?php echo e($totalProducts); ?></strong>
    </article>
    <article class="stat-card">
        <span>Unidades en almacén</span>
        <strong id="statStock"><?php echo e($totalStock); ?></strong>
    </article>
    <article class="stat-card">
        <span>Valor del inventario</span>
        <strong>$<span id="statValue"><?php echo e(number_format((float) $inventoryValue, 2)); ?></span></strong>
    </article>
</section>

<main class="pos-grid <?php echo e($isAdmin ? 'admin-mode' : 'client-mode'); ?>">
    <?php if($isAdmin): ?>
        <section class="panel form-panel">
            <div class="panel-title">
                <div>
                    <span class="eyebrow">Datos del producto</span>
                    <h2>Registrar producto</h2>
                </div>
            </div>

            <form id="productForm" action="<?php echo e(route('products.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <label>
                    <span>Imagen</span>
                    <input id="imageInput" type="file" name="image" accept="image/*">
                </label>
                <div class="image-preview" id="imagePreview">
                    <img src="<?php echo e(asset('images/no-image.svg')); ?>" alt="Vista previa">
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
    <?php endif; ?>

    <section class="panel table-panel">
        <div class="panel-title table-heading">
            <div>
                <span class="eyebrow">Consulta de productos</span>
                <h2><?php echo e($isAdmin ? 'Inventario registrado' : 'Productos disponibles'); ?></h2>
            </div>
            <input id="searchInput" class="search-box" type="search" placeholder="Buscar producto, marca o descripción...">
        </div>

        <div class="table-wrap">
            <table class="products-table">
                <thead>
                    <tr>
                        <th><?php echo e($isAdmin ? 'Vender' : 'Comprar'); ?></th>
                        <?php if($isAdmin): ?>
                            <th>Administrar</th>
                        <?php endif; ?>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody id="productsBody">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-search="<?php echo e(strtolower($product->name.' '.$product->description.' '.$product->brand)); ?>" data-id="<?php echo e($product->id); ?>">
                            <td>
                                <button class="sell-btn" type="button" data-product='<?php echo json_encode($product, 15, 512) ?>' <?php echo e($product->stock <= 0 ? 'disabled' : ''); ?>>
                                    <?php echo e($isAdmin ? 'Vender' : 'Comprar'); ?>

                                </button>
                            </td>
                            <?php if($isAdmin): ?>
                                <td>
                                    <div class="row-actions">
                                        <button class="edit-btn" type="button" data-product='<?php echo json_encode($product, 15, 512) ?>'>Editar</button>
                                        <button class="delete-btn" type="button" data-id="<?php echo e($product->id); ?>" data-name="<?php echo e(e($product->name)); ?>">Eliminar</button>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td><img class="thumb" src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>"></td>
                            <td><strong><?php echo e($product->name); ?></strong></td>
                            <td><?php echo e($product->description); ?></td>
                            <td><?php echo e($product->brand); ?></td>
                            <td>$<?php echo e(number_format((float) $product->price, 2)); ?></td>
                            <td><span class="stock-pill <?php echo e($product->stock <= 0 ? 'empty' : ''); ?>"><?php echo e($product->stock); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="empty-row">
                            <td colspan="<?php echo e($isAdmin ? 8 : 7); ?>">Todavía no hay productos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="saleModal" class="modal-backdrop" hidden>
    <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="saleTitle">
        <button class="modal-close" type="button" id="closeSaleModal">×</button>
        <span class="eyebrow"><?php echo e($isAdmin ? 'Confirmar venta' : 'Confirmar compra'); ?></span>
        <h2 id="saleTitle"><?php echo e($isAdmin ? 'Vender producto' : 'Comprar producto'); ?></h2>
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
            <button class="primary-btn" type="submit"><?php echo e($isAdmin ? 'Confirmar venta' : 'Confirmar compra'); ?></button>
        </form>
    </section>
</div>

<?php if($isAdmin): ?>
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
                <img src="<?php echo e(asset('images/no-image.svg')); ?>" alt="Vista previa edición">
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
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    window.routes = {
        productsStore: <?php echo json_encode(route('products.store'), 15, 512) ?>,
        productBase: <?php echo json_encode(url('/productos'), 15, 512) ?>,
        salesStore: <?php echo json_encode(url('/ventas'), 15, 512) ?>,
    };
    window.appConfig = {
        isAdmin: <?php echo json_encode($isAdmin, 15, 512) ?>,
        actionText: <?php echo json_encode($isAdmin ? 'Vender' : 'Comprar', 15, 512) ?>,
        confirmText: <?php echo json_encode($isAdmin ? 'Confirmar venta' : 'Confirmar compra', 15, 512) ?>,
        modalActionText: <?php echo json_encode($isAdmin ? 'Vender' : 'Comprar', 15, 512) ?>,
    };
</script>
<script src="<?php echo e(asset('js/pos.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\adria\Desktop\xampp\htdocs\Proyecto\resources\views/pos/index.blade.php ENDPATH**/ ?>