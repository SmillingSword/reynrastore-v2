import { defineStore } from 'pinia';

// Main app store
export const useAppStore = defineStore('app', {
  state: () => ({
    loading: false,
    user: null,
    isAuthenticated: false,
    notifications: [],
  }),
  
  getters: {
    isLoading: (state) => state.loading,
    currentUser: (state) => state.user,
    hasNotifications: (state) => state.notifications.length > 0,
  },
  
  actions: {
    setLoading(status) {
      this.loading = status;
    },
    
    setUser(user) {
      this.user = user;
      this.isAuthenticated = !!user;
    },
    
    addNotification(notification) {
      this.notifications.push({
        id: Date.now(),
        type: notification.type || 'info',
        title: notification.title,
        message: notification.message,
        timestamp: new Date(),
      });
    },
    
    removeNotification(id) {
      this.notifications = this.notifications.filter(n => n.id !== id);
    },
    
    clearNotifications() {
      this.notifications = [];
    },
  },
});

// Auth store
export { useAuthStore } from './auth';

// Products store
export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    featuredProducts: [],
    loading: false,
    filters: {
      category: null,
      priceRange: [0, 1000000],
      search: '',
    },
  }),
  
  getters: {
    filteredProducts: (state) => {
      let filtered = state.products;
      
      if (state.filters.category) {
        filtered = filtered.filter(product => product.category_id === state.filters.category);
      }
      
      if (state.filters.search) {
        filtered = filtered.filter(product => 
          product.name.toLowerCase().includes(state.filters.search.toLowerCase())
        );
      }
      
      filtered = filtered.filter(product => 
        product.price >= state.filters.priceRange[0] && 
        product.price <= state.filters.priceRange[1]
      );
      
      return filtered;
    },
    
    getProductById: (state) => (id) => {
      return state.products.find(product => product.id === id);
    },
    
    getProductsByCategory: (state) => (categoryId) => {
      return state.products.filter(product => product.category_id === categoryId);
    },
  },
  
  actions: {
    async fetchProducts() {
      this.loading = true;
      try {
        // TODO: Replace with actual API call
        const response = await fetch('/api/products');
        this.products = await response.json();
      } catch (error) {
        console.error('Error fetching products:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCategories() {
      try {
        // TODO: Replace with actual API call
        const response = await fetch('/api/categories');
        this.categories = await response.json();
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },
    
    async fetchFeaturedProducts() {
      try {
        // TODO: Replace with actual API call
        const response = await fetch('/api/products/featured');
        this.featuredProducts = await response.json();
      } catch (error) {
        console.error('Error fetching featured products:', error);
      }
    },
    
    setFilter(key, value) {
      this.filters[key] = value;
    },
    
    clearFilters() {
      this.filters = {
        category: null,
        priceRange: [0, 1000000],
        search: '',
      };
    },
  },
});
