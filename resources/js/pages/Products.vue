<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
      <div class="absolute top-20 left-20 w-64 h-64 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="container mx-auto px-4 py-12 relative z-10">
        <!-- Enhanced Page Header -->
        <div class="text-center mb-16 animate-fade-in">
          <h1 class="text-5xl lg:text-6xl font-bold text-white mb-6 holographic">
            🎮 Produk Game 🎮
          </h1>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
            Pilih game favoritmu dan mulai top up sekarang dengan harga terbaik dan proses tercepat
          </p>
          <div class="w-32 h-1 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 mx-auto mt-6 rounded-full"></div>
        </div>

        <!-- Enhanced Loading State -->
        <div v-if="loading" class="flex flex-col justify-center items-center py-20 animate-fade-in">
          <div class="relative">
            <div class="w-16 h-16 border-4 border-green-500/30 rounded-full animate-spin"></div>
            <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-t-green-500 rounded-full animate-spin"></div>
          </div>
          <div class="loading-dots mt-6">
            <div class="loading-dot"></div>
            <div class="loading-dot"></div>
            <div class="loading-dot"></div>
          </div>
          <p class="text-green-400 mt-4 font-semibold animate-pulse">Memuat produk keren...</p>
        </div>

        <!-- Enhanced Filters -->
        <div v-else class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 mb-12 border border-green-500/30 neon-glow animate-slide-in-left">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Enhanced Search -->
            <div class="animate-slide-in-left" style="animation-delay: 0.1s">
              <label class="block text-white font-bold mb-3 flex items-center space-x-2">
                <span class="text-green-400">🔍</span>
                <span>Cari Game</span>
              </label>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Masukkan nama game keren..."
                  class="w-full bg-gradient-to-r from-gray-700 to-gray-800 border-2 border-green-500/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/50 transition-all duration-300 neon-glow"
                  @input="debouncedSearch"
                />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-green-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
            </div>

            <!-- Enhanced Category Filter -->
            <div class="animate-slide-in-left" style="animation-delay: 0.2s">
              <label class="block text-white font-bold mb-3 flex items-center space-x-2">
                <span class="text-blue-400">🎯</span>
                <span>Kategori</span>
              </label>
              <select
                v-model="selectedCategory"
                class="w-full bg-gradient-to-r from-gray-700 to-gray-800 border-2 border-blue-500/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 neon-glow filter-select"
                @change="fetchProducts"
              >
                <option value="" class="option-dark">Semua Kategori</option>
                <option v-for="category in categories" :key="category.id" :value="category.slug" class="option-dark">
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Enhanced Type Filter -->
            <div class="animate-slide-in-left" style="animation-delay: 0.3s">
              <label class="block text-white font-bold mb-3 flex items-center space-x-2">
                <span class="text-purple-400">⚡</span>
                <span>Tipe</span>
              </label>
              <select
                v-model="selectedType"
                class="w-full bg-gradient-to-r from-gray-700 to-gray-800 border-2 border-purple-500/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all duration-300 neon-glow filter-select"
                @change="fetchProducts"
              >
                <option value="" class="option-dark">Semua Tipe</option>
                <option value="diggie" class="option-dark">⚡ Otomatis (Diggie)</option>
                <option value="manual" class="option-dark">🔧 Manual</option>
              </select>
            </div>

            <!-- Enhanced Sort -->
            <div class="animate-slide-in-left" style="animation-delay: 0.4s">
              <label class="block text-white font-bold mb-3 flex items-center space-x-2">
                <span class="text-orange-400">📊</span>
                <span>Urutkan</span>
              </label>
              <select
                v-model="sortBy"
                class="w-full bg-gradient-to-r from-gray-700 to-gray-800 border-2 border-orange-500/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all duration-300 neon-glow filter-select"
                @change="fetchProducts"
              >
                <option value="sort_order" class="option-dark">🎯 Default</option>
                <option value="name" class="option-dark">🔤 Nama A-Z</option>
                <option value="price" class="option-dark">💰 Harga Terendah</option>
                <option value="created_at" class="option-dark">🆕 Terbaru</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Enhanced Products Grid -->
        <div v-if="!loading && products.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
          <div
            v-for="(product, index) in products"
            :key="product.id"
            class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl overflow-hidden hover:from-gray-700 hover:to-gray-800 transition-all duration-500 group cursor-pointer transform hover:scale-110 hover:shadow-2xl border border-gray-700 hover:border-green-500 neon-glow animate-slide-in-left relative"
            :style="`animation-delay: ${index * 0.1}s`"
            @click="goToProduct(product)"
          >
            <!-- Product Image -->
            <div class="aspect-video bg-gradient-to-br from-green-500 to-blue-500 flex items-center justify-center relative overflow-hidden">
              <img 
                v-if="product.image" 
                :src="getImageUrl(product.image)" 
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                @error="handleImageError"
              />
              <div v-else class="text-white font-bold text-4xl group-hover:scale-110 transition-transform duration-300">
                {{ getProductIcon(product.name) }}
              </div>
              
              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              
              <!-- Product Type Badge -->
              <div class="absolute top-3 right-3">
                <span 
                  v-if="product.type === 'diggie'" 
                  class="bg-gradient-to-r from-green-500 to-green-400 text-white text-xs px-3 py-1 rounded-full font-bold animate-pulse pulse-ring"
                >
                  ⚡ AUTO
                </span>
                <span 
                  v-else 
                  class="bg-gradient-to-r from-blue-500 to-blue-400 text-white text-xs px-3 py-1 rounded-full font-bold pulse-ring"
                >
                  🔧 MANUAL
                </span>
              </div>

              <!-- Featured Badge -->
              <div v-if="product.is_featured" class="absolute top-3 left-3">
                <span class="bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs px-3 py-1 rounded-full font-bold animate-pulse pulse-ring">
                  ⭐ FEATURED
                </span>
              </div>

              <!-- Popular Badge -->
              <div v-if="product.price_list_count > 5" class="absolute bottom-3 left-3">
                <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs px-3 py-1 rounded-full font-bold animate-pulse">
                  🔥 POPULER
                </span>
              </div>
            </div>

            <!-- Enhanced Product Info -->
            <div class="p-6 relative">
              <div class="flex items-start justify-between mb-3">
                <h3 class="text-white font-bold text-xl group-hover:text-green-400 transition-colors flex-1 holographic">
                  {{ product.name }}
                </h3>
                <span 
                  v-if="product.category" 
                  class="text-xs bg-gradient-to-r from-gray-700 to-gray-600 text-gray-300 px-3 py-1 rounded-full ml-2 flex-shrink-0 group-hover:from-green-500/20 group-hover:to-blue-500/20 group-hover:text-green-400 transition-all duration-300"
                >
                  {{ product.category.name }}
                </span>
              </div>
              
              <p class="text-gray-400 text-sm mb-4 line-clamp-2 group-hover:text-gray-300 transition-colors">
                {{ product.description || 'Top up game favoritmu dengan mudah dan cepat' }}
              </p>
              
              <!-- Enhanced Price Range -->
              <div class="flex items-center justify-between mb-4">
                <div>
                  <span class="text-gray-400 text-sm">Mulai dari</span>
                  <div class="text-green-400 font-bold text-xl holographic group-hover:scale-105 transition-transform duration-300">
                    {{ formatPrice(product.min_price || product.selling_price) }}
                  </div>
                  <div v-if="product.max_price && product.max_price !== product.min_price" class="text-gray-500 text-xs">
                    s/d {{ formatPrice(product.max_price) }}
                  </div>
                </div>
                <div class="flex flex-col items-end space-y-2">
                  <div v-if="product.stock !== null && product.type === 'manual'" class="text-xs text-gray-400">
                    Stock: {{ product.stock || 'Unlimited' }}
                  </div>
                  <div class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-4 py-2 rounded-full text-sm font-bold group-hover:scale-105 transition-transform duration-300 pulse-ring">
                    {{ product.price_list_count || 0 }} Paket
                  </div>
                </div>
              </div>

              <!-- Enhanced Status -->
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <div 
                    :class="product.is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500'" 
                    class="w-3 h-3 rounded-full pulse-ring"
                  ></div>
                  <span 
                    :class="product.is_active ? 'text-green-400' : 'text-red-400'" 
                    class="text-sm font-bold"
                  >
                    {{ product.is_active ? '✅ Tersedia' : '❌ Tidak Tersedia' }}
                  </span>
                </div>
                <div class="text-sm text-gray-400 group-hover:text-green-400 transition-colors">
                  {{ product.type === 'diggie' ? '⚡ Proses Otomatis' : '🔧 Proses Manual' }}
                </div>
              </div>

              <!-- Hover Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out rounded-2xl"></div>
            </div>
          </div>
        </div>

        <!-- Enhanced Pagination -->
        <div v-if="!loading && pagination.total > pagination.per_page" class="flex justify-center items-center space-x-6 mb-12">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl transition-all duration-300 font-semibold border border-gray-700 hover:border-green-500 neon-glow"
          >
            ← Previous
          </button>
          
          <div class="flex items-center space-x-3">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="changePage(page)"
              :class="page === pagination.current_page ? 'bg-gradient-to-r from-green-500 to-blue-500 text-white shadow-glow-green' : 'bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 text-gray-300 hover:text-white border border-gray-700 hover:border-green-500'"
              class="px-4 py-3 rounded-xl transition-all duration-300 font-semibold neon-glow"
            >
              {{ page }}
            </button>
          </div>
          
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl transition-all duration-300 font-semibold border border-gray-700 hover:border-green-500 neon-glow"
          >
            Next →
          </button>
        </div>

        <!-- Enhanced Empty State -->
        <div v-if="!loading && products.length === 0" class="text-center py-20 animate-fade-in">
          <div class="text-gray-400 mb-6">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center border border-gray-700 neon-glow">
              <span class="text-4xl">🎮</span>
            </div>
          </div>
          <h3 class="text-white font-bold text-2xl mb-4 holographic">Tidak ada produk ditemukan</h3>
          <p class="text-gray-400 text-lg mb-6">Coba ubah filter pencarian Anda atau jelajahi kategori lain</p>
          <button 
            @click="() => { searchQuery = ''; selectedCategory = ''; selectedType = ''; fetchProducts(); }"
            class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-glow-green"
          >
            🔄 Reset Filter
          </button>
        </div>

        <!-- Enhanced Error State -->
        <div v-if="error && !loading && products.length === 0" class="text-center py-20 animate-fade-in">
          <div class="text-red-400 mb-6">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-red-900/50 to-gray-900 rounded-full flex items-center justify-center border border-red-500/50 neon-glow">
              <span class="text-4xl">⚠️</span>
            </div>
          </div>
          <h3 class="text-white font-bold text-2xl mb-4 holographic">Terjadi Kesalahan</h3>
          <p class="text-gray-400 text-lg mb-6">{{ error }}</p>
          <button 
            @click="fetchProducts" 
            class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-bold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-glow-green"
          >
            🔄 Coba Lagi
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import AppLayout from '../components/Layout/AppLayout.vue';
import axios from 'axios';

export default {
  name: 'Products',
  components: {
    AppLayout,
  },
  setup() {
    const router = useRouter();
    const route = useRoute();
    
    const loading = ref(true);
    const error = ref(null);
    const products = ref([]);
    const categories = ref([]);
    const searchQuery = ref('');
    const selectedCategory = ref('');
    const selectedType = ref('');
    const sortBy = ref('sort_order');
    const currentPage = ref(1);
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    });

    let searchTimeout = null;

    // Fetch categories
    const fetchCategories = async () => {
      try {
        const response = await axios.get('/api/v1/categories');
        if (response.data.success) {
          categories.value = response.data.data;
        }
      } catch (err) {
        console.error('Error fetching categories:', err);
      }
    };

    // Fetch products
    const fetchProducts = async (page = 1) => {
      try {
        loading.value = true;
        error.value = null;

        const params = {
          page,
          per_page: 12,
          active: true,
          sort_by: sortBy.value,
          sort_order: sortBy.value === 'price' ? 'asc' : 'asc'
        };

        if (searchQuery.value) {
          params.search = searchQuery.value;
        }

        if (selectedCategory.value) {
          params.category = selectedCategory.value;
        }

        if (selectedType.value) {
          params.type = selectedType.value;
        }

        const response = await axios.get('/api/v1/products', { params });
        
        if (response.data.success) {
          products.value = response.data.data.data;
          pagination.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total
          };
          currentPage.value = page;
          // Clear error when products are successfully loaded
          error.value = null;
        }
      } catch (err) {
        console.error('Error fetching products:', err);
        error.value = err.response?.data?.message || 'Gagal memuat produk. Silakan coba lagi.';
      } finally {
        loading.value = false;
      }
    };

    // Debounced search
    const debouncedSearch = () => {
      if (searchTimeout) {
        clearTimeout(searchTimeout);
      }
      searchTimeout = setTimeout(() => {
        fetchProducts(1);
      }, 500);
    };

    // Change page
    const changePage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page) {
        fetchProducts(page);
      }
    };

    // Visible pages for pagination
    const visiblePages = computed(() => {
      const current = pagination.value.current_page;
      const last = pagination.value.last_page;
      const pages = [];
      
      const start = Math.max(1, current - 2);
      const end = Math.min(last, current + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    });

    // Format price
    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(price);
    };

    // Get product icon
    const getProductIcon = (name) => {
      const icons = {
        'Mobile Legends': '⚔️',
        'Free Fire': '🔥',
        'PUBG': '🎯',
        'Genshin Impact': '⭐',
        'Valorant': '🎮',
        'Steam': '🎮',
        'Youtube': '📺'
      };
      
      for (const [key, icon] of Object.entries(icons)) {
        if (name.toLowerCase().includes(key.toLowerCase())) {
          return icon;
        }
      }
      
      return '🎮';
    };

    // Get image URL
    const getImageUrl = (imagePath) => {
      if (!imagePath) return null;
      if (imagePath.startsWith('http')) return imagePath;
      return `/storage/${imagePath}`;
    };

    // Handle image error
    const handleImageError = (event) => {
      event.target.style.display = 'none';
      event.target.parentElement.innerHTML = `
        <div class="text-white font-bold text-3xl">
          ${getProductIcon(event.target.alt)}
        </div>
      `;
    };


    // Go to product detail using slug for SEO
    const goToProduct = (product) => {
      router.push(`/products/${product.slug}`);
    };

    // Initialize
    onMounted(async () => {
      // Check for category filter from route query
      if (route.query.category) {
        selectedCategory.value = route.query.category;
      }
      
      // Check for game filter from route query (from Home.vue)
      if (route.query.game) {
        // Map game ID to category slug if needed
        const gameMapping = {
          1: 'mobile-legends',
          2: 'free-fire',
          3: 'pubg-mobile',
          4: 'genshin-impact',
          5: 'valorant',
          6: 'youtube-premium'
        };
        selectedCategory.value = gameMapping[route.query.game] || '';
      }

      await fetchCategories();
      await fetchProducts();
    });
    
    return {
      loading,
      error,
      products,
      categories,
      searchQuery,
      selectedCategory,
      selectedType,
      sortBy,
      pagination,
      visiblePages,
      fetchProducts,
      debouncedSearch,
      changePage,
      formatPrice,
      getProductIcon,
      getImageUrl,
      handleImageError,
      goToProduct,
    };
  },
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Filter select styling */
.filter-select {
  color: white !important;
}

.filter-select option {
  color: black !important;
  background-color: white !important;
}

/* Dark theme option styling */
.option-dark {
  color: black !important;
  background-color: white !important;
}
</style>
