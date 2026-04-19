const CART_STORAGE_KEY = 'coffeeHavenCart';

const CoffeeCart = {
    // Retrieves the current cart array from localStorage
    getCart: function() {
        try {
            const cartString = localStorage.getItem(CART_STORAGE_KEY);
            return cartString ? JSON.parse(cartString) : [];
        } catch (e) {
            console.error("Error reading cart from localStorage:", e);
            return [];
        }
    },

    // Saves the current cart array to localStorage
    saveCart: function(cart) {
        try {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
            // Dispatch a custom event so UI can react when the cart changes (same-tab listeners)
            try {
                const totalQuantity = (cart || []).reduce((sum, it) => sum + (parseInt(it.quantity) || 0), 0);
                const evt = new CustomEvent('cartUpdated', { detail: { totalQuantity, items: cart } });
                window.dispatchEvent(evt);
            } catch (evErr) {
                console.error('Error dispatching cartUpdated event:', evErr);
            }
        } catch (e) {
            console.error("Error saving cart to localStorage:", e);
        }
    },

    // Adds a new item to the cart or increments quantity if it exists
    addItem: function(item) {
        const cart = this.getCart();
        const existingItem = cart.find(i => i.id === item.id && i.variant === item.variant);
        
        if (existingItem) {
            existingItem.quantity = (parseInt(existingItem.quantity) || 0) + (parseInt(item.quantity) || 1);
        } else {
            // Ensure quantity is a number
            item.quantity = parseInt(item.quantity) || 1; 
            cart.push(item);
        }
        
        this.saveCart(cart);
    },
    
    // Removes an item at a specific index
    removeItem: function(index) {
        const cart = this.getCart();
        if (index >= 0 && index < cart.length) {
            cart.splice(index, 1);
            this.saveCart(cart);
        }
    },

    // Updates the quantity of an item at a specific index
    updateQuantity: function(index, newQuantity) {
        const cart = this.getCart();
        const qty = parseInt(newQuantity);
        if (index >= 0 && index < cart.length && qty > 0) {
            cart[index].quantity = qty;
            this.saveCart(cart);
        } else if (index >= 0 && index < cart.length && qty <= 0) {
             // Optional: remove item if quantity drops to 0 or less
             this.removeItem(index);
        }
    },

    // Clears the entire cart
    clearCart: function() {
        this.saveCart([]);
    },
    
    // Gets the total number of unique items in the cart
    getCartItemCount: function() {
        const cart = this.getCart();
        return cart.length;
    }
};

// Expose CoffeeCart on the window object for other scripts that check window.CoffeeCart
try{
    if (typeof window !== 'undefined') window.CoffeeCart = CoffeeCart;
}catch(e){ console.warn('Could not attach CoffeeCart to window', e); }