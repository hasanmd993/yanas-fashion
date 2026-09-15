/**
 * Yanas Fashion - E-Commerce Client Engine
 * High-Conversion Interactive Functions
 */

document.addEventListener('DOMContentLoaded', function () {
    initHeroSlider();
    initTypewriterSearch();
    initLiveSearch();
    initCartDrawer();
    initCheckoutCalculator();
    initProductGallery();
    initSizeChartModal();
    initPdpTabs();
    initPdpStickyBar();
    initFloatingCommunicationWidget();
    initMobileNavDrawer();
});

// =========================================================================
// 0. Luxury Hero Slider Engine (Auto-Play, Swipe & Indicators)
// =========================================================================
function initHeroSlider() {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot, .hp-dot');
    const prevBtn = document.getElementById('heroPrevBtn');
    const nextBtn = document.getElementById('heroNextBtn');
    const totalSlides = slides.length;

    if (totalSlides <= 1) return;

    let currentSlide = 0;
    let autoPlayInterval = 3000;
    let autoPlayTimer = null;

    function updateCounter(idx) {
        // Update dash indicators inside the active slide
        const activeSlide = slides[idx];
        const allDashes = slider.querySelectorAll('.hp-slide-dash');
        allDashes.forEach((d, i) => {
            d.classList.toggle('active', i === idx);
        });
    }

    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

        currentSlide = (index + totalSlides) % totalSlides;

        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
        updateCounter(currentSlide);
    }


    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    function prevSlide() {
        goToSlide(currentSlide - 1);
    }

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(nextSlide, autoPlayInterval);
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    // Button Click Listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoPlay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAutoPlay();
        });
    }

    // Dot Click Listeners
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            goToSlide(idx);
            startAutoPlay();
        });
    });

    // Pause on Hover
    slider.addEventListener('mouseenter', stopAutoPlay);
    slider.addEventListener('mouseleave', startAutoPlay);

    // Touch / Swipe Gestures for Mobile
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoPlay();
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startAutoPlay();
    }, { passive: true });

    function handleSwipe() {
        const threshold = 40; // minimum distance to trigger swipe
        if (touchStartX - touchEndX > threshold) {
            nextSlide(); // Swipe Left
        } else if (touchEndX - touchStartX > threshold) {
            prevSlide(); // Swipe Right
        }
    }

    // Start auto-play
    startAutoPlay();
}

// =========================================================================
// 1. Live Autocomplete Header & Mobile Search
// =========================================================================
function initLiveSearch() {
    const searchPairs = [
        { input: document.getElementById('global-search-input'), dropdown: document.getElementById('search-results-dropdown') },
        { input: document.getElementById('mobile-search-input'), dropdown: document.getElementById('mobile-search-results-dropdown') }
    ];

    searchPairs.forEach(({ input, dropdown }) => {
        if (!input || !dropdown) return;
        let debounceTimer;

        input.addEventListener('input', function () {
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
                            dropdown.innerHTML = '<div style="padding:14px; text-align:center; color:#888; font-size:0.88rem;">No products found</div>';
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
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
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
                        <p style="color:#666; font-size:1rem; margin-bottom:16px;">Your shopping bag is currently empty</p>
                        <a href="/shop" class="btn btn-primary btn-sm">Start Shopping</a>
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
                            <div class="drawer-item-size">${item.size ? 'Size: ' + item.size : ''}</div>
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
            if (deliveryFeeEl) deliveryFeeEl.innerHTML = '<span style="color:var(--success); font-weight:700;">FREE (Free Delivery)</span>';
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
// 4. Single Product Gallery & Fullscreen Lightbox
// =========================================================================
function initProductGallery() {
    const mainImg = document.getElementById('pdp-main-img') || document.getElementById('product-main-image');
    const thumbBtns = document.querySelectorAll('.pdp-thumb-btn, .thumb-btn');
    const fullscreenBtn = document.getElementById('pdpFullscreenBtn');
    const lightboxModal = document.getElementById('pdp-lightbox-modal');
    const lightboxImg = document.getElementById('pdp-lightbox-img');
    const lightboxClose = document.getElementById('pdp-lightbox-close');

    if (thumbBtns.length && mainImg) {
        thumbBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                thumbBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const targetSrc = this.dataset.src;
                if (targetSrc) {
                    mainImg.src = targetSrc;
                    if (mainImg.dataset) mainImg.dataset.zoom = targetSrc;
                }
            });
        });
    }

    if (fullscreenBtn && lightboxModal && lightboxImg && mainImg) {
        fullscreenBtn.addEventListener('click', () => {
            lightboxImg.src = mainImg.src;
            lightboxModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        const closeLightbox = () => {
            lightboxModal.classList.remove('open');
            document.body.style.overflow = '';
        };

        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        lightboxModal.addEventListener('click', (e) => {
            if (e.target === lightboxModal) closeLightbox();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightboxModal.classList.contains('open')) {
                closeLightbox();
            }
        });
    }
}

// =========================================================================
// 5. Size Chart Modal
// =========================================================================
function initSizeChartModal() {
    const modal = document.getElementById('size-chart-modal');
    const openBtn = document.getElementById('open-size-chart');
    const closeBtn = document.getElementById('close-size-chart');

    if (!modal) return;

    const openModal = () => {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });
}

// =========================================================================
// 5b. PDP Tab & Mobile Luxury Accordion System
// =========================================================================
function initPdpTabs() {
    const tabBtns = document.querySelectorAll('.pdp-tab-btn');
    const tabPanels = document.querySelectorAll('.pdp-tab-panel');
    const mobileToggles = document.querySelectorAll('.pdp-accordion-toggle');

    // Desktop Tab Switching
    if (tabBtns.length) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.dataset.tab;
                if (!targetId) return;

                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanels.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                const targetPanel = document.getElementById(targetId);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    const toggle = targetPanel.querySelector('.pdp-accordion-toggle');
                    if (toggle) toggle.classList.add('active');
                }
            });
        });
    }

    // Mobile Accordion Toggle
    if (mobileToggles.length) {
        mobileToggles.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const panel = this.closest('.pdp-tab-panel');
                if (!panel) return;

                const isOpen = panel.classList.contains('active');

                // Toggle this panel
                if (isOpen) {
                    panel.classList.remove('active');
                    this.classList.remove('active');
                } else {
                    panel.classList.add('active');
                    this.classList.add('active');
                }
            });
        });
    }
}

// =========================================================================
// 5c. PDP Mobile Floating Sticky CTA Bar (Scroll-Triggered)
// =========================================================================
function initPdpStickyBar() {
    const stickyBar = document.getElementById('pdpMobileStickyBar');
    const triggerArea = document.getElementById('pdpBuyTriggerArea');

    if (!stickyBar || !triggerArea) return;

    let ticking = false;

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const rect = triggerArea.getBoundingClientRect();
                // When buy trigger area is scrolled past the top of the viewport
                if (rect.bottom < 0) {
                    stickyBar.classList.add('visible');
                } else {
                    stickyBar.classList.remove('visible');
                }
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
}

// =========================================================================
// 6. Typewriter Animated Search Placeholder (Wasitex BD Style)
// =========================================================================
function initTypewriterSearch() {
    const inputs = document.querySelectorAll('.live-search-input, #global-search-input, #mobile-search-input');
    if (!inputs.length) return;

    const phrases = [
        'Search shirts, t-shirts...',
        'Search cargo pants & trousers...',
        'Search festive panjabi...',
        'Search premium polo shirts...',
        'Search new luxury arrivals...'
    ];

    let phraseIdx = 0;
    let charIdx = 0;
    let isDeleting = false;
    let typingTimer = null;
    let isPaused = false;

    function typeLoop() {
        if (isPaused) return;

        const currentPhrase = phrases[phraseIdx];
        const currentText = isDeleting 
            ? currentPhrase.substring(0, charIdx - 1) 
            : currentPhrase.substring(0, charIdx + 1);

        inputs.forEach(input => {
            input.setAttribute('placeholder', currentText);
        });

        if (isDeleting) {
            charIdx--;
        } else {
            charIdx++;
        }

        let typeSpeed = isDeleting ? 45 : 85;

        if (!isDeleting && charIdx === currentPhrase.length) {
            typeSpeed = 1800; // Pause after typing full phrase
            isDeleting = true;
        } else if (isDeleting && charIdx === 0) {
            isDeleting = false;
            phraseIdx = (phraseIdx + 1) % phrases.length;
            typeSpeed = 400; // Pause before typing next phrase
        }

        typingTimer = setTimeout(typeLoop, typeSpeed);
    }

    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            isPaused = true;
            clearTimeout(typingTimer);
        });

        input.addEventListener('blur', function () {
            if (!this.value.trim()) {
                isPaused = false;
                typeLoop();
            }
        });
    });

    // Start typewriter loop
    typeLoop();
}

// =========================================================================
// 7. Expandable 3-in-1 Floating Communication Widget
// =========================================================================
function initFloatingCommunicationWidget() {
    const widget = document.getElementById('floatingCommunicationWidget');
    const toggleBtn = document.getElementById('widgetToggleBtn');

    if (!widget || !toggleBtn) return;

    toggleBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        widget.classList.toggle('active');
    });

    document.addEventListener('click', function (e) {
        if (!widget.contains(e.target)) {
            widget.classList.remove('active');
        }
    });
}

// =========================================================================
// 8. Mobile Slide-out Navigation Drawer & Accordion Engine
// =========================================================================
function initMobileNavDrawer() {
    const drawer = document.getElementById('mobileNavDrawer');
    const overlay = document.getElementById('mobileNavOverlay');
    const openBtns = document.querySelectorAll('#mobileMenuToggle, #mobileBottomMenuToggle, .trigger-mobile-menu');
    const closeBtn = document.getElementById('closeMobileNav');

    if (!drawer || !overlay) return;

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    openBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openDrawer();
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }

    overlay.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer.classList.contains('open')) {
            closeDrawer();
        }
    });

    // Close drawer when clicking a child category link (smooth navigation)
    const navLinks = drawer.querySelectorAll('.mobile-nav-link:not(.has-accordion), .mobile-sub-link:not(.has-child-accordion), .mobile-child-link, .mobile-sub-explore');
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            closeDrawer();
        });
    });

    // Tier 1 Accordions: Parent -> Subcategories
    const parentAccordions = drawer.querySelectorAll('.mobile-nav-li.has-accordion');
    parentAccordions.forEach(li => {
        const btn = li.querySelector('.mobile-accordion-btn');
        const content = li.querySelector('.mobile-accordion-content');
        if (!btn || !content) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = li.classList.contains('open');
            if (isOpen) {
                li.classList.remove('open');
                content.style.display = 'none';
            } else {
                li.classList.add('open');
                content.style.display = 'block';
            }
        });
    });

    // Tier 2 Accordions: Subcategory -> Child categories
    const childAccordions = drawer.querySelectorAll('.mobile-sub-li.has-child-accordion');
    childAccordions.forEach(li => {
        const btn = li.querySelector('.mobile-child-accordion-btn');
        const list = li.querySelector('.mobile-child-list');
        if (!btn || !list) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = li.classList.contains('open');
            if (isOpen) {
                li.classList.remove('open');
                list.style.display = 'none';
                btn.innerHTML = '<i class="fa-solid fa-plus"></i>';
            } else {
                li.classList.add('open');
                list.style.display = 'block';
                btn.innerHTML = '<i class="fa-solid fa-minus"></i>';
            }
        });
    });
}



