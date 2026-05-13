const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const appConfig = window.appConfig || {};
const isAdmin = Boolean(appConfig.isAdmin);
const actionText = appConfig.actionText || (isAdmin ? 'Vender' : 'Comprar');
const confirmText = appConfig.confirmText || (isAdmin ? 'Confirmar venta' : 'Confirmar compra');

const productForm = document.getElementById('productForm');
const productsBody = document.getElementById('productsBody');
const imageInput = document.getElementById('imageInput');
const imagePreview = document.querySelector('#imagePreview img');
const searchInput = document.getElementById('searchInput');

const saleModal = document.getElementById('saleModal');
const closeSaleModal = document.getElementById('closeSaleModal');
const saleForm = document.getElementById('saleForm');
const saleProductId = document.getElementById('saleProductId');
const saleTitle = document.getElementById('saleTitle');
const saleDescription = document.getElementById('saleDescription');
const saleQuantity = document.getElementById('saleQuantity');
const saleUnitPrice = document.getElementById('saleUnitPrice');
const saleTotal = document.getElementById('saleTotal');

const editModal = document.getElementById('editModal');
const closeEditModal = document.getElementById('closeEditModal');
const editProductForm = document.getElementById('editProductForm');
const editProductId = document.getElementById('editProductId');
const editName = document.getElementById('editName');
const editDescription = document.getElementById('editDescription');
const editBrand = document.getElementById('editBrand');
const editPrice = document.getElementById('editPrice');
const editStock = document.getElementById('editStock');
const editImageInput = document.getElementById('editImageInput');
const editImagePreview = document.querySelector('#editImagePreview img');

let selectedProduct = null;
let editingProduct = null;

const money = (value) => Number(value || 0).toLocaleString('es-MX', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

function showToast(message, type = 'ok') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.toggle('error', type === 'error');
    toast.hidden = false;
    clearTimeout(showToast.timer);
    showToast.timer = setTimeout(() => { toast.hidden = true; }, 3600);
}

function updateStats(stats) {
    if (!stats) return;
    const products = document.getElementById('statProducts');
    const stock = document.getElementById('statStock');
    const value = document.getElementById('statValue');
    if (products) products.textContent = stats.totalProducts;
    if (stock) stock.textContent = stats.totalStock;
    if (value) value.textContent = money(stats.inventoryValue);
}


function decodeEntities(value) {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = value || '';
    return textarea.value;
}

function readProductFromButton(button) {
    const raw = button?.dataset?.product || '';

    try {
        return JSON.parse(raw);
    } catch (firstError) {
        try {
            return JSON.parse(decodeEntities(raw));
        } catch (secondError) {
            console.error('Producto inválido en data-product:', raw, secondError);
            throw secondError;
        }
    }
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function safeProductJson(product) {
    return escapeHtml(JSON.stringify(product));
}

function productRow(product) {
    const searchText = `${product.name} ${product.description} ${product.brand}`.toLowerCase();
    const disabled = Number(product.stock) <= 0 ? 'disabled' : '';
    const stockClass = Number(product.stock) <= 0 ? 'stock-pill empty' : 'stock-pill';
    const imageUrl = product.image_url || '/images/no-image.svg';
    const adminControls = isAdmin ? `
        <td>
            <div class="row-actions">
                <button class="edit-btn" data-product="${safeProductJson(product)}">Editar</button>
                <button class="delete-btn" data-id="${product.id}" data-name="${escapeHtml(product.name)}">Eliminar</button>
            </div>
        </td>
    ` : '';

    return `
        <tr data-search="${escapeHtml(searchText)}" data-id="${product.id}">
            <td><button class="sell-btn" data-product="${safeProductJson(product)}" ${disabled}>${actionText}</button></td>
            ${adminControls}
            <td><img class="thumb" src="${imageUrl}" alt="${escapeHtml(product.name)}"></td>
            <td><strong>${escapeHtml(product.name)}</strong></td>
            <td>${escapeHtml(product.description)}</td>
            <td>${escapeHtml(product.brand)}</td>
            <td>$${money(product.price)}</td>
            <td><span class="${stockClass}">${product.stock}</span></td>
        </tr>
    `;
}

function refreshRow(product) {
    const oldRow = productsBody?.querySelector(`tr[data-id="${product.id}"]`);
    const emptyRow = productsBody?.querySelector('.empty-row');
    if (emptyRow) emptyRow.remove();

    if (oldRow) {
        oldRow.outerHTML = productRow(product);
    } else {
        productsBody?.insertAdjacentHTML('afterbegin', productRow(product));
    }
}

function removeRow(productId) {
    const oldRow = productsBody?.querySelector(`tr[data-id="${productId}"]`);
    if (oldRow) oldRow.remove();
}

function openSaleModal(product) {
    selectedProduct = product;
    saleProductId.value = product.id;
    saleTitle.textContent = `${actionText}: ${product.name}`;
    saleDescription.textContent = `${product.description} · Stock disponible: ${product.stock}`;
    saleQuantity.value = 1;
    saleQuantity.max = product.stock;
    saleUnitPrice.textContent = money(product.price);
    saleTotal.textContent = money(product.price);

    saleModal.hidden = false;
    saleModal.removeAttribute('hidden');
    document.body.classList.add('modal-open');
    setTimeout(() => saleQuantity.focus(), 60);
}

function closeSale() {
    selectedProduct = null;
    if (saleForm) saleForm.reset();
    if (saleModal) {
        saleModal.hidden = true;
        saleModal.setAttribute('hidden', 'hidden');
    }
    document.body.classList.remove('modal-open');
}

function openEditModal(product) {
    if (!isAdmin || !editModal) return;
    editingProduct = product;
    editProductId.value = product.id;
    editName.value = product.name || '';
    editDescription.value = product.description || '';
    editBrand.value = product.brand || '';
    editPrice.value = product.price || '';
    editStock.value = product.stock ?? 0;
    if (editImageInput) editImageInput.value = '';
    if (editImagePreview) editImagePreview.src = product.image_url || '/images/no-image.svg';

    editModal.hidden = false;
    editModal.removeAttribute('hidden');
    document.body.classList.add('modal-open');
    setTimeout(() => editName.focus(), 60);
}

function closeEdit() {
    editingProduct = null;
    if (editProductForm) editProductForm.reset();
    if (editModal) {
        editModal.hidden = true;
        editModal.setAttribute('hidden', 'hidden');
    }
    document.body.classList.remove('modal-open');
}

imageInput?.addEventListener('change', () => {
    const file = imageInput.files?.[0];
    if (!file || !imagePreview) return;
    const reader = new FileReader();
    reader.onload = (event) => { imagePreview.src = event.target.result; };
    reader.readAsDataURL(file);
});

editImageInput?.addEventListener('change', () => {
    const file = editImageInput.files?.[0];
    if (!file || !editImagePreview) return;
    const reader = new FileReader();
    reader.onload = (event) => { editImagePreview.src = event.target.result; };
    reader.readAsDataURL(file);
});

productForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const submit = productForm.querySelector('button[type="submit"]');
    submit.disabled = true;
    submit.textContent = 'Guardando...';

    try {
        const response = await fetch(productForm.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: new FormData(productForm),
        });

        const data = await response.json();
        if (!response.ok) {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'No se pudo registrar el producto.');
            throw new Error(errors);
        }

        refreshRow(data.product);
        updateStats(data.stats);
        productForm.reset();
        if (imagePreview) imagePreview.src = '/images/no-image.svg';
        showToast(data.message);
    } catch (error) {
        showToast(error.message, 'error');
    } finally {
        submit.disabled = false;
        submit.textContent = 'Registrar producto';
    }
});

searchInput?.addEventListener('input', () => {
    const term = searchInput.value.trim().toLowerCase();
    productsBody?.querySelectorAll('tr').forEach(row => {
        if (!row.dataset.search) return;
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});

productsBody?.addEventListener('click', async (event) => {
    const sellButton = event.target.closest('.sell-btn');
    const editButton = event.target.closest('.edit-btn');
    const deleteButton = event.target.closest('.delete-btn');

    if (sellButton && !sellButton.disabled) {
        try {
            openSaleModal(readProductFromButton(sellButton));
        } catch (error) {
            showToast('No se pudo abrir la ventana de confirmación.', 'error');
        }
        return;
    }

    if (editButton) {
        try {
            openEditModal(readProductFromButton(editButton));
        } catch (error) {
            showToast('No se pudo abrir la ventana de edición.', 'error');
        }
        return;
    }

    if (deleteButton) {
        const productId = deleteButton.dataset.id;
        const productName = deleteButton.dataset.name || 'este producto';
        const ok = confirm(`¿Seguro que deseas eliminar ${productName}? Esta acción no se puede deshacer.`);
        if (!ok) return;

        deleteButton.disabled = true;
        deleteButton.textContent = 'Eliminando...';

        try {
            const response = await fetch(`${window.routes.productBase}/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'No se pudo eliminar el producto.');
            }
            removeRow(productId);
            updateStats(data.stats);
            showToast(data.message);
        } catch (error) {
            showToast(error.message, 'error');
            deleteButton.disabled = false;
            deleteButton.textContent = 'Eliminar';
        }
    }
});

saleQuantity?.addEventListener('input', () => {
    if (!selectedProduct) return;
    const max = Number(selectedProduct.stock || 1);
    let qty = Number(saleQuantity.value || 1);
    if (qty < 1) qty = 1;
    if (qty > max) qty = max;
    saleQuantity.value = qty;
    saleTotal.textContent = money(qty * Number(selectedProduct.price));
});

closeSaleModal?.addEventListener('click', closeSale);
closeEditModal?.addEventListener('click', closeEdit);

saleModal?.addEventListener('click', (event) => {
    if (event.target === saleModal) closeSale();
});

editModal?.addEventListener('click', (event) => {
    if (event.target === editModal) closeEdit();
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (saleModal && !saleModal.hidden) closeSale();
    if (editModal && !editModal.hidden) closeEdit();
});

saleForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!selectedProduct) return;

    const submit = saleForm.querySelector('button[type="submit"]');
    submit.disabled = true;
    submit.textContent = 'Confirmando...';

    try {
        const response = await fetch(`${window.routes.salesStore}/${selectedProduct.id}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ quantity: saleQuantity.value }),
        });

        const data = await response.json();
        if (!response.ok) {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'No se pudo confirmar la operación.');
            throw new Error(errors);
        }

        refreshRow(data.product);
        updateStats(data.stats);
        closeSale();
        showToast(data.message);
    } catch (error) {
        showToast(error.message, 'error');
    } finally {
        submit.disabled = false;
        submit.textContent = confirmText;
    }
});

editProductForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!editingProduct) return;

    const submit = editProductForm.querySelector('button[type="submit"]');
    submit.disabled = true;
    submit.textContent = 'Guardando...';

    try {
        const formData = new FormData(editProductForm);
        formData.append('_method', 'PUT');

        const response = await fetch(`${window.routes.productBase}/${editingProduct.id}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        });

        const data = await response.json();
        if (!response.ok) {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'No se pudo actualizar el producto.');
            throw new Error(errors);
        }

        refreshRow(data.product);
        updateStats(data.stats);
        closeEdit();
        showToast(data.message);
    } catch (error) {
        showToast(error.message, 'error');
    } finally {
        submit.disabled = false;
        submit.textContent = 'Guardar cambios';
    }
});
