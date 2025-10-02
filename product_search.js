// User Product Search and Filtering JS (separate from admin)
let userElements = {
    searchBar: null,
    categoryFilter: null,
    minPriceFilter: null,
    maxPriceFilter: null,
    filterBtn: null,
    clearFiltersBtn: null,
    productTable: null
};

function userInitializeElements() {
    userElements = {
        searchBar: document.querySelector('#product-search .product-search-bar'),
        categoryFilter: document.querySelector('#product-search .category-filter'),
        minPriceFilter: document.querySelector('#product-search .price-filter[placeholder="Min Price"]'),
        maxPriceFilter: document.querySelector('#product-search .price-filter[placeholder="Max Price"]'),
        filterBtn: document.querySelector('#product-search .filter-btn'),
        clearFiltersBtn: document.querySelector('#product-search .clear-filters-btn'),
        productTable: document.querySelector('#product-search .product-table')
    };
    const missing = Object.entries(userElements).filter(([k, v]) => !v).map(([k]) => k);
    if (missing.length > 0) {
        console.error('Missing elements:', missing);
        return false;
    }
    return true;
}

function userLoadFilteredProducts() {
    if (!userInitializeElements()) {
        userShowError('Failed to initialize product search. Please refresh the page.');
        return;
    }
    const searchValue = userElements.searchBar.value;
    const categoryValue = userElements.categoryFilter.value;
    const minPriceValue = userElements.minPriceFilter.value;
    const maxPriceValue = userElements.maxPriceFilter.value;
    const params = new URLSearchParams();
    if (searchValue) params.append('search', searchValue);
    if (categoryValue) params.append('category', categoryValue);
    if (minPriceValue) params.append('minPrice', minPriceValue);
    if (maxPriceValue) params.append('maxPrice', maxPriceValue);
    userShowLoadingState();
    fetch(`get_filtered_products.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userDisplayProducts(data.products);
                userUpdateFilterStats(data);
            } else {
                userShowError(data.message || 'Error loading products');
            }
        })
        .catch(error => {
            userShowError('Failed to load products. Please try again.');
            console.error('Error:', error);
        });
}

function userShowLoadingState() {
    userElements.productTable.innerHTML = `<tr><td colspan="5">Loading products...</td></tr>`;
}

function userShowError(message) {
    userElements.productTable.innerHTML = `<tr><td colspan="5" class="error-message">${message}</td></tr>`;
}

function userUpdateFilterStats(data) {
    const statsDiv = document.createElement('div');
    statsDiv.className = 'filter-stats';
    statsDiv.innerHTML = `<span>Total Products: ${data.totalProducts}</span>`;
    const filterBar = document.querySelector('#product-search .search-filter-bar');
    const existingStats = filterBar.querySelector('.filter-stats');
    if (existingStats) filterBar.removeChild(existingStats);
    filterBar.appendChild(statsDiv);
}

function userDisplayProducts(products) {
    let html = `
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
    `;
    if (!products || products.length === 0) {
        html += `<tr><td colspan="5" class="no-results">No products found matching your criteria</td></tr>`;
    } else {
        products.forEach(product => {
            html += `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.product_name}</td>
                    <td>${product.quantity}</td>
                    <td>Rs. ${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.category}</td>
                </tr>
            `;
        });
    }
    html += '</tbody>';
    userElements.productTable.innerHTML = html;
}

function userClearFilters() {
    if (!userInitializeElements()) return;
    userElements.searchBar.value = '';
    userElements.categoryFilter.value = '';
    userElements.minPriceFilter.value = '';
    userElements.maxPriceFilter.value = '';
    userLoadFilteredProducts();
}

function userInitializeEventListeners() {
    if (!userInitializeElements()) return;
    let searchTimeout;
    userElements.searchBar.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(userLoadFilteredProducts, 300);
    });
    userElements.categoryFilter.addEventListener('change', userLoadFilteredProducts);
    userElements.minPriceFilter.addEventListener('input', userLoadFilteredProducts);
    userElements.maxPriceFilter.addEventListener('input', userLoadFilteredProducts);
    userElements.filterBtn.addEventListener('click', userLoadFilteredProducts);
    userElements.clearFiltersBtn.addEventListener('click', userClearFilters);
}

document.addEventListener('DOMContentLoaded', function() {
    if (userInitializeElements()) {
        userInitializeEventListeners();
        userLoadFilteredProducts();
    }
});
