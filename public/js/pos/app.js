// All JavaScript for POS functionality will go here
const posApp = {
    apiToken: null,
    products: [],
    cart: [],
    productSearchInput: null, // Declare productSearchInput

    init() {
        this.apiToken = this.getApiToken(); // Function to get API token
        this.productSearchInput = document.getElementById('product-search-input'); // Initialize it here
        this.fetchProducts();
        this.setupEventListeners();
        this.renderCart(); // Render cart on init to display "Cart is empty."
    },

    getApiToken() {
        // This will be dynamically provided by Laravel Blade
        // It's a placeholder here, the actual value will be injected.
        // During testing, ensure your Blade template passes the token.
        return '';
    },

    fetchProducts() {
        fetch('/api/pos/products?query=' + this.productSearchInput.value, { // Use relative path
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + this.apiToken
            }
        })
        .then(response => response.json())
        .then(data => {
            this.products = data;
            this.renderProducts();
        })
        .catch(error => console.error('Error fetching products:', error));
    },

    renderProducts() {
        const productList = document.getElementById('product-list');
        productList.innerHTML = ''; // Clear previous products

        if (this.products.length === 0) {
            productList.innerHTML = '<p class="col-span-full text-center text-gray-500">No products found.</p>';
            return;
        }

        this.products.forEach(product => {
            const productCard = `
                <div class="bg-gray-100 p-4 rounded-lg shadow cursor-pointer"
                     data-product-id="${product.id}"
                     data-product-name="${product.name}"
                     data-product-price="${product.price}">
                    <h3 class="font-semibold text-lg">${product.name}</h3>
                    <p class="text-gray-600">$${product.price.toFixed(2)}</p>
                </div>
            `;
            productList.innerHTML += productCard;
        });
        this.setupProductCardClickListeners();
    },

    setupEventListeners() {
        this.productSearchInput.addEventListener('input', () => this.fetchProducts());
        document.getElementById('process-sale-btn').addEventListener('click', () => this.processSale());
    },

    setupProductCardClickListeners() {
        document.querySelectorAll('#product-list > div').forEach(card => {
            card.addEventListener('click', (event) => {
                const productId = event.currentTarget.dataset.productId;
                const productName = event.currentTarget.dataset.productName;
                const productPrice = parseFloat(event.currentTarget.dataset.productPrice);
                this.showAddToCartPopup(productId, productName, productPrice);
            });
        });
    },

    showAddToCartPopup(productId, productName, productPrice) {
        Swal.fire({
            title: `Add ${productName} to Cart`,
            html: `
                <label for="swal-quantity" class="text-left block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                <input id="swal-quantity" type="number" value="1" min="1" class="swal2-input w-full px-3 py-2 border rounded shadow appearance-none leading-tight focus:outline-none focus:shadow-outline mb-4">
                <label for="swal-remarks" class="text-left block text-gray-700 text-sm font-bold mb-2">Remarks (Optional):</label>
                <input id="swal-remarks" type="text" placeholder="Add remarks here" class="swal2-input w-full px-3 py-2 border rounded shadow appearance-none leading-tight focus:outline-none focus:shadow-outline">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Add to Cart',
            preConfirm: () => {
                const quantity = parseInt(document.getElementById('swal-quantity').value);
                const remarks = document.getElementById('swal-remarks').value;

                if (isNaN(quantity) || quantity <= 0) {
                    Swal.showValidationMessage('Please enter a valid quantity.');
                    return false;
                }
                return { quantity: quantity, remarks: remarks };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.addToCart(productId, productName, productPrice, result.value.quantity, result.value.remarks);
            }
        });
    },

    addToCart(productId, productName, productPrice, quantity = 1, remarks = '') {
        const existingItemIndex = this.cart.findIndex(item => item.id == productId && item.remarks == remarks);

        if (existingItemIndex > -1) {
            this.cart[existingItemIndex].quantity += quantity;
        } else {
            this.cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: quantity,
                remarks: remarks,
            });
        }
        this.renderCart();
    },

    removeItemFromCart(productId) {
        const existingItemIndex = this.cart.findIndex(item => item.id == productId);

        if (existingItemIndex > -1) {
            if (this.cart[existingItemIndex].quantity > 1) {
                this.cart[existingItemIndex].quantity--;
            } else {
                this.cart.splice(existingItemIndex, 1);
            }
        }
        this.renderCart();
    },

    renderCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        cartItemsContainer.innerHTML = ''; // Clear previous cart items

        if (this.cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-gray-500">Cart is empty.</p>';
        }

        let subtotal = 0;
        this.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            const cartItem = `
                <div class="flex justify-between items-center py-2 border-b">
                    <span>${item.name} x ${item.quantity}</span>
                    <span>$${itemTotal.toFixed(2)}</span>
                    <button data-product-id="${item.id}" class="text-red-500 hover:text-red-700 text-sm ml-2 remove-from-cart-btn">Remove</button>
                </div>
            `;
            cartItemsContainer.innerHTML += cartItem;
        });

        document.getElementById('cart-subtotal').innerText = `$${subtotal.toFixed(2)}`;
        document.getElementById('cart-total').innerText = `$${subtotal.toFixed(2)}`; // For now, total is subtotal
        this.setupRemoveFromCartButtons();
    },

    setupRemoveFromCartButtons() {
        document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const productId = event.target.dataset.productId;
                this.removeItemFromCart(productId);
            });
        });
    },

    processSale() {
        if (this.cart.length === 0) {
            Swal.fire('Error', 'Cart is empty. Cannot process sale.', 'error');
            return;
        }

        // Prepare items for API
        const items = this.cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price,
        }));

        // For simplicity, payment method is hardcoded to Cash
        const payments = [{
            amount: parseFloat(document.getElementById('cart-total').innerText.replace('$', '')),
            payment_method: 'Cash',
        }];

        fetch('/api/pos/sales', { // Use relative path
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + this.apiToken,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                outlet_id: {{ Auth::user()->outlet_id ?? 'null' }}, // This will be replaced by Blade
                user_id: {{ Auth::id() ?? 'null' }}, // This will be replaced by Blade
                customer_id: null, // Placeholder for customer selection
                total_amount: parseFloat(document.getElementById('cart-total').innerText.replace('$', '')),
                status: 'paid', // Assuming immediate payment
                items: items,
                payments: payments,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Sale processed successfully') {
                Swal.fire('Success', 'Sale processed successfully!', 'success');
                this.cart = [];
                this.renderCart(); // Clear cart display
            } else {
                Swal.fire('Error', data.message || 'Error processing sale.', 'error');
            }
        })
        .catch(error => {
            console.error('Error processing sale:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }
};

// Initialize the POS application
document.addEventListener('DOMContentLoaded', () => {
    posApp.init();
});
