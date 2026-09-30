import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: JSON.parse(localStorage.getItem('yanas_cart') || '[]'),
        isOpen: false,
        coupon: JSON.parse(localStorage.getItem('yanas_coupon') || 'null'),
        deliveryType: 'inside_dhaka', // inside_dhaka, dhaka_suburbs, outside_dhaka
    }),

    getters: {
        totalCount: (state) => {
            return state.items.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
        },

        subtotal: (state) => {
            return state.items.reduce((sum, item) => {
                const price = Number(item.price) || 0;
                return sum + price * (Number(item.quantity) || 1);
            }, 0);
        },

        discountAmount: (state) => {
            if (!state.coupon) return 0;
            const subtotal = state.items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);
            if (state.coupon.type === 'percentage') {
                return Math.round((subtotal * Number(state.coupon.value)) / 100);
            }
            return Math.min(Number(state.coupon.value) || 0, subtotal);
        },

        shippingCharge: (state) => {
            const subtotal = state.items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);
            // Free delivery threshold 2500 BDT
            if (subtotal >= 2500 && subtotal > 0) {
                return 0;
            }
            if (state.deliveryType === 'inside_dhaka') return 70;
            if (state.deliveryType === 'dhaka_suburbs') return 100;
            return 130;
        },

        grandTotal(state) {
            return Math.max(0, this.subtotal - this.discountAmount + this.shippingCharge);
        },
    },

    actions: {
        save() {
            localStorage.setItem('yanas_cart', JSON.stringify(this.items));
            localStorage.setItem('yanas_coupon', JSON.stringify(this.coupon));
            this.syncWithServer();
        },

        async syncWithServer() {
            try {
                await axios.post(route('cart.sync'), {
                    items: this.items
                });
            } catch (error) {
                console.warn('Cart session sync notice:', error);
            }
        },

        async proceedToCheckout() {
            if (this.items.length === 0) {
                this.isOpen = true;
                return;
            }
            await this.syncWithServer();
            this.isOpen = false;
            window.location.href = route('checkout.index');
        },

        addItem(product, quantity = 1, selectedSize = null, selectedColor = null) {
            const key = `${product.id}_${selectedSize || 'default'}_${selectedColor || 'default'}`;
            const existingIndex = this.items.findIndex(item => item.key === key);

            if (existingIndex > -1) {
                this.items[existingIndex].quantity += quantity;
            } else {
                this.items.push({
                    key,
                    id: product.id,
                    product_id: product.id,
                    name: product.name || product.title,
                    title: product.name || product.title,
                    slug: product.slug,
                    image: product.image_url || product.primary_image || (product.images && product.images[0]?.image_url) || product.thumbnail || '/images/placeholder.jpg',
                    thumbnail: product.image_url || product.primary_image || (product.images && product.images[0]?.image_url) || product.thumbnail || '/images/placeholder.jpg',
                    price: Number(product.effective_price || product.selling_price || product.sale_price || product.regular_price || 0),
                    regularPrice: Number(product.regular_price || 0),
                    regular_price: Number(product.regular_price || 0),
                    quantity: quantity,
                    size: selectedSize,
                    color: selectedColor,
                    sku: product.sku || '',
                });
            }

            this.save();
            this.isOpen = true;
        },

        updateQuantity(key, quantity) {
            const item = this.items.find(i => i.key === key);
            if (item) {
                if (quantity <= 0) {
                    this.removeItem(key);
                } else {
                    item.quantity = quantity;
                    this.save();
                }
            }
        },

        removeItem(key) {
            this.items = this.items.filter(i => i.key !== key);
            this.save();
        },

        clearCart() {
            this.items = [];
            this.coupon = null;
            this.save();
        },

        toggleDrawer(open = null) {
            this.isOpen = open !== null ? open : !this.isOpen;
        },

        applyCoupon(couponData) {
            this.coupon = couponData;
            this.save();
        },

        removeCoupon() {
            this.coupon = null;
            this.save();
        },
    },
});
