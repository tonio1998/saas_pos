const productInput = document.getElementById('productName');
const suggestionBox = document.getElementById('productSuggestions');
const barcodeInput = document.getElementById('barcode');
const categorySelect = document.getElementById('category_id');
const unitSelect = document.getElementById('unit_id');
const descriptionInput = document.getElementById('description');
const costPriceInput = document.getElementById('cost_price');
const sellingPriceInput = document.getElementById('selling_price');
const wholesalePriceInput = document.querySelector('input[name="wholesale_price"]');
const skuInput = document.getElementById('sku');


let debounceTimer = null;
let controller = null;
let selectedIndex = -1;
let products = [];
let previousKeyword = '';

const CACHE_PREFIX = 'product_search_';
const CACHE_TIME = 1000 * 60 * 30;

let currentKeyword = '';

if (!productInput) {
    console.error('Product input not found.');
}

function fillProduct(product) {

    console.table(product);

    barcodeInput.value = product.barcode ?? '';
    productInput.value = product.name ?? '';
    descriptionInput.value = product.description ?? '';
    costPriceInput.value = product.cost_price ?? '';

    if (sellingPriceInput) {
        sellingPriceInput.value = product.selling_price ?? '';
    }

    if (wholesalePriceInput && product.wholesale_price) {
        wholesalePriceInput.value = product.wholesale_price ?? '';
    }

    skuInput.value = product.sku ?? '';

    const categoryOption = categorySelect.querySelector(
        `option[value="${product.category_id}"]`
    );

    const unitOption = unitSelect.querySelector(
        `option[value="${product.unit_id}"]`
    );

    console.log('Category ID:', product.category_id);
    console.log('Unit ID:', product.unit_id);

    console.log('Category Option:', categoryOption);
    console.log('Unit Option:', unitOption);

    if (categoryOption) {
        categorySelect.value = String(product.category_id);
    } else {
        console.warn(
            `Category ID ${product.category_id} does not exist in the dropdown.`
        );
    }

    if (unitOption) {
        unitSelect.value = String(product.unit_id);
    } else {
        console.warn(
            `Unit ID ${product.unit_id} does not exist in the dropdown.`
        );
    }

    // Trigger profit calculations if present
    if (costPriceInput) costPriceInput.dispatchEvent(new Event('input'));
    if (sellingPriceInput) sellingPriceInput.dispatchEvent(new Event('input'));
    if (wholesalePriceInput) wholesalePriceInput.dispatchEvent(new Event('input'));

    suggestionBox.classList.add('d-none');

}

async function searchProducts(keyword) {

    currentKeyword = keyword;

    const cacheKey = CACHE_PREFIX + keyword.toLowerCase();

    let hasCache = false;

    const cache = localStorage.getItem(cacheKey);

    if (cache) {

        try {

            const data = JSON.parse(cache);

            if (Date.now() - data.time < CACHE_TIME) {

                hasCache = true;

                products = rankProducts(keyword, data.items);

                renderSuggestions(products);

            }

        } catch {

            localStorage.removeItem(cacheKey);

        }

    }

    if (controller) {
        controller.abort();
    }

    controller = new AbortController();

    if (!hasCache) {

        suggestionBox.innerHTML = `
            <div class="p-3 text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Searching products...
            </div>
        `;

        suggestionBox.classList.remove('d-none');

    }

    try {

        const response = await fetch(
            `/products/suggestions?keyword=${encodeURIComponent(keyword)}`,
            {
                signal: controller.signal,
                headers: {
                    Accept: 'application/json'
                }
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (currentKeyword !== keyword) {
            return;
        }

        products = rankProducts(keyword, result);

        localStorage.setItem(
            cacheKey,
            JSON.stringify({
                time: Date.now(),
                items: products
            })
        );

        renderSuggestions(products);

    } catch (e) {

        if (e.name !== 'AbortError') {

            console.error(e);

            if (!hasCache) {

                suggestionBox.innerHTML = `
                    <div class="empty-suggestion">
                        Unable to load products.
                    </div>
                `;

            }

        }

    }

}

function highlight(text, keyword){

    if(!keyword)
        return text;

    const regex = new RegExp(
        '('+keyword.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')',
        'ig'
    );

    return text.replace(
        regex,
        '<mark>$1</mark>'
    );

}

(function(){

    const now = Date.now();

    Object.keys(localStorage).forEach(key=>{

        if(!key.startsWith(CACHE_PREFIX))
            return;

        try{

            const item = JSON.parse(localStorage.getItem(key));

            if(now-item.time>CACHE_TIME){

                localStorage.removeItem(key);

            }

        }catch{

            localStorage.removeItem(key);

        }

    });

})();

function rankProducts(keyword, items){

    keyword = keyword.toLowerCase();

    return items.sort((a,b)=>{

        const aStarts = a.name.toLowerCase().startsWith(keyword);
        const bStarts = b.name.toLowerCase().startsWith(keyword);

        if(aStarts && !bStarts) return -1;
        if(!aStarts && bStarts) return 1;

        return b.usage_count-a.usage_count;

    });

}

productInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const keyword = this.value.trim();
    if (keyword === previousKeyword) {
        return;
    }

    previousKeyword = keyword;
    if (keyword.length < 2) {
        suggestionBox.classList.add('d-none');
        suggestionBox.innerHTML = '';
        return;
    }

    debounceTimer = setTimeout(() => {
        searchProducts(keyword);
    }, 300);
});


function renderSuggestions(items) {

    selectedIndex = -1;

    if (!items.length) {

        suggestionBox.innerHTML = `
            <div class="empty-suggestion">
                No products found.
            </div>
        `;

        suggestionBox.classList.remove('d-none');

        return;

    }

    let html = '';

    items.forEach((product, index) => {

        html += `
        <div
            class="product-suggestion"
            data-index="${index}"
            data-category="${product.category ?? ''}"
            data-unit="${product.unit ?? ''}"
        >

            <img
                src="${product.image || '/images/no_image.jpg'}"
                class="product-image"
                loading="lazy"
            >

            <div class="flex-grow-1">

                <div class="product-title">
                    ${highlight(product.name, productInput.value)}
                </div>

                <div class="product-meta">

                    <div>
                        <strong>Barcode:</strong>
                        ${product.barcode || '-'}
                    </div>

                    <div>
                        <strong>${product.category ?? '-'}</strong>
                        •
                        <strong>${product.unit ?? '-'}</strong>
                    </div>

                    <div>
                        Retail: <strong class="text-success">₱${Number(product.selling_price || 0).toFixed(2)}</strong>
                        ${product.wholesale_price ? ` • Wholesale: <strong class="text-primary">₱${Number(product.wholesale_price).toFixed(2)}</strong>` : ''}
                    </div>

                    <div class="text-muted extra-small">
                        Used by <strong>${product.usage_count ?? 0}</strong> other store(s)
                    </div>

                </div>

            </div>

            <span class="product-badge">
                Auto-Fill
            </span>

        </div>
    `;

    });

    suggestionBox.innerHTML = html;

    suggestionBox.classList.remove('d-none');

}


suggestionBox.addEventListener('click', function (e) {

    const item = e.target.closest('.product-suggestion');

    if (!item) return;

    fillProduct(products[item.dataset.index]);

});

productInput.addEventListener('keydown', function (e) {

    if (!products.length) return;

    const items = suggestionBox.querySelectorAll('.product-suggestion');

    if (e.key === 'ArrowDown') {

        e.preventDefault();

        selectedIndex++;

        if (selectedIndex >= items.length) {

            selectedIndex = 0;

        }

    }

    else if (e.key === 'ArrowUp') {

        e.preventDefault();

        selectedIndex--;

        if (selectedIndex < 0) {

            selectedIndex = items.length - 1;

        }

    }

    else if (e.key === 'Enter') {

        if (selectedIndex >= 0) {

            e.preventDefault();

            fillProduct(products[selectedIndex]);

        }

        return;

    }

    else if (e.key === 'Escape') {

        suggestionBox.classList.add('d-none');

        return;

    }

    items.forEach(x => x.classList.remove('active'));

    if (selectedIndex >= 0) {

        items[selectedIndex].classList.add('active');

        items[selectedIndex].scrollIntoView({
            block: 'nearest'
        });

    }

});

document.addEventListener('click', function (e) {

    if (
        !suggestionBox.contains(e.target) &&
        e.target !== productInput
    ) {

        suggestionBox.classList.add('d-none');

    }

});

productInput.addEventListener('focus', function () {

    if (products.length) {

        suggestionBox.classList.remove('d-none');

    }

});

