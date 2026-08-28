/**
 * Yana's Fashion - E-Commerce Client Engine
 * High-Conversion Interactive Functions
 */

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch();
    initCartDrawer();
    initCheckoutCalculator();
    initProductGallery();
    initSizeChartModal();
});

// =========================================================================
// 1. Live Autocomplete Header Search
// =========================================================================
function initLiveSearch() {
    const searchInput = document.getElementById('global-search-input');
    const dropdown = document.getElementById('search-results-dropdown');
    let debounceTimer;

    if (!searchInput || !dropdown) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/search-products?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(products => {
                    if (products.length === 0) {
                        dropdown.innerHTML = '<div style="padding:14px; text-align:center; color:#888; font-size:0.88rem;">কোন পণ্য পাওয়া যায়নি</div>';
                        dropdown.style.display = 'block';
                        return;
                    }

                    let html = '';
                    products.forEach(p => {
                        const price = p.sale_price ? `৳${Number(p.sale_price).toLocaleString()}` : `৳${Number(p.regular_price).toLocaleString()}`;
                        html += `
                            <a href="/product/${p.slug}" class="search-result-item">
                                <img src="${p.thumbnail}" alt="${p.title}">
                                <div>
                                    <div class="item-title">${p.title}</div>
                                    <div class="item-price">${price}</div>
                                </div>
                            </a>
                        `;
                    });
                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';
                })
                .catch(err => console.error(err));
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

// =========================================================================
// 2. Slide-out Cart Drawer & AJAX Operations
// =========================================================================
function initCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-drawer-overlay');
    const openBtns = document.querySelectorAll('.trigger-cart-drawer');
    const closeBtn = document.getElementById('close-cart-drawer');

    if (!drawer) return;

    const openDrawer = () => {
        refreshCartDrawer();
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    const closeDrawer = () => {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    openBtns.forEach(btn => btn.addEventListener('click', openDrawer));
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);
}

// Refresh Cart Drawer Content via AJAX
function refreshCartDrawer() {
    const drawerBody = document.getElementById('drawer-cart-items');
    const drawerSubtotal = document.getElementById('drawer-subtotal-amount');
    const cartCounters = document.querySelectorAll('.cart-counter-badge');
    const floatingCartTotal = document.getElementById('floating-cart-total');

    fetch('/cart/data')
        .then(res => res.json())
        .then(data => {
            // Update badges
            cartCounters.forEach(b => b.innerText = data.count);
            if (floatingCartTotal) floatingCartTotal.innerText = `৳${Number(data.total).toLocaleString()}`;
            if (drawerSubtotal) drawerSubtotal.innerText = `৳${Number(data.total).toLocaleString()}`;

            if (!drawerBody) return;

            if (data.items.length === 0) {
                drawerBody.innerHTML = `
                    <div style="text-align:center; padding:48px 16px;">
                        <i class="fa-solid fa-bag-shopping" style="font-size:3rem; color:#ccc; margin-bottom:16px;"></i>
                        <p style="color:#666; font-size:1rem; margin-bottom:16px;">আপনার শপিং ব্যাগ বর্তমানে খালি</p>
                        <a href="/shop" class="btn btn-primary btn-sm">কেনাকাটা শুরু করুন</a>
                    </div>
                `;
                return;
            }

            let html = '';
            data.items.forEach(item => {
                html += `
                    <div class="drawer-item" data-key="${item.key}">
                        <img src="${item.thumbnail}" alt="${item.title}" class="drawer-item-img">
                        <div class="drawer-item-details">
                            <a href="/product/${item.slug}" class="drawer-item-title">${item.title}</a>
                            <div class="drawer-item-size">${item.size ? 'সাইজ: ' + item.size : ''}</div>
                            <div class="drawer-item-bottom">
                                <div class="qty-stepper">
                                    <button class="qty-btn" onclick="updateCartQty('${item.key}', ${item.quantity - 1})">-</button>
                                    <span class="qty-count">${item.quantity}</span>
                                    <button class="qty-btn" onclick="updateCartQty('${item.key}', ${item.quantity + 1})">+</button>
                                </div>
                                <div style="font-weight:700; color:var(--primary);">৳${Number(item.price * item.quantity).toLocaleString()}</div>
                                <button class="drawer-item-delete" onclick="removeCartItem('${item.key}')" title="Remove">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            drawerBody.innerHTML = html;
        })
        .catch(err => console.error(err));
}

// Add to Cart via AJAX
function addToCartAjax(productId, quantity = 1, size = null) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity,
            size: size
        })
    })
    .then(res => res.json())
    .then(data => {
        // Open drawer smoothly
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        if (drawer && overlay) {
            refreshCartDrawer();
            drawer.classList.add('open');
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    })
    .catch(err => console.error(err));
}

// Update Qty in Cart
function updateCartQty(key, newQty) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (newQty <= 0) {
        removeCartItem(key);
        return;
    }

    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ key: key, quantity: newQty })
    })
    .then(res => res.json())
    .then(() => refreshCartDrawer())
    .catch(err => console.error(err));
}

// Remove Item from Cart
function removeCartItem(key) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ key: key })
    })
    .then(res => res.json())
    .then(() => refreshCartDrawer())
    .catch(err => console.error(err));
}

// =========================================================================
// 3. Fast Checkout Live Delivery & Total Calculator
// =========================================================================
function initCheckoutCalculator() {
    const zoneRadios = document.querySelectorAll('input[name="delivery_zone"]');
    const deliveryFeeEl = document.getElementById('checkout-delivery-fee');
    const grandTotalEl = document.getElementById('checkout-grand-total');
    const subtotalEl = document.getElementById('checkout-subtotal');
    const discountEl = document.getElementById('checkout-discount-amount');

    if (!zoneRadios.length || !grandTotalEl || !subtotalEl) return;

    const subtotal = parseFloat(subtotalEl.dataset.amount || 0);

    const updateTotals = () => {
        let deliveryCharge = 70;
        let selectedZone = 'inside_dhaka';

        zoneRadios.forEach(radio => {
            if (radio.checked) {
                selectedZone = radio.value;
                if (selectedZone === 'inside_dhaka') deliveryCharge = 70;
                else if (selectedZone === 'dhaka_suburbs') deliveryCharge = 100;
                else if (selectedZone === 'outside_dhaka') deliveryCharge = 130;
            }
        });

        // Free shipping check over 3000 in Dhaka
        if (subtotal >= 3000 && selectedZone === 'inside_dhaka') {
            deliveryCharge = 0;
            if (deliveryFeeEl) deliveryFeeEl.innerHTML = '<span style="color:var(--success); font-weight:700;">ফ্রি (Free Delivery)</span>';
        } else {
            if (deliveryFeeEl) deliveryFeeEl.innerText = `৳${deliveryCharge}`;
        }

        const discount = discountEl ? parseFloat(discountEl.dataset.amount || 0) : 0;
        const grandTotal = Math.max(0, subtotal + deliveryCharge - discount);

        grandTotalEl.innerText = `৳${grandTotal.toLocaleString()}`;
    };

    zoneRadios.forEach(radio => radio.addEventListener('change', updateTotals));
    updateTotals();
}

// Apply Coupon Code via AJAX
function applyCouponCode() {
    const couponInput = document.getElementById('coupon-code-input');
    const couponMsg = document.getElementById('coupon-status-msg');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!couponInput || !couponInput.value.trim()) return;

    fetch('/checkout/coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ code: couponInput.value.trim() })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (couponMsg) {
                couponMsg.innerHTML = `<span style="color:var(--success); font-weight:600;"><i class="fa-solid fa-circle-check"></i> ${data.message}</span>`;
            }
            setTimeout(() => window.location.reload(), 800);
        } else {
            if (couponMsg) {
                couponMsg.innerHTML = `<span style="color:var(--danger); font-weight:600;"><i class="fa-solid fa-circle-xmark"></i> ${data.message}</span>`;
            }
        }
    })
    .catch(err => console.error(err));
}

// =========================================================================
// 4. Single Product Gallery & Zoom
// =========================================================================
function initProductGallery() {
    const mainImg = document.getElementById('product-main-image');
    const thumbBtns = document.querySelectorAll('.thumb-btn');

    if (!mainImg || !thumbBtns.length) return;

    thumbBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            thumbBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const targetSrc = this.dataset.src;
            if (targetSrc) mainImg.src = targetSrc;
        });
    });
}

// =========================================================================
// 5. Size Chart Modal
// =========================================================================
function initSizeChartModal() {
    const modal = document.getElementById('size-chart-modal');
    const openBtn = document.getElementById('open-size-chart');
    const closeBtn = document.getElementById('close-size-chart');

    if (!modal) return;

    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}
