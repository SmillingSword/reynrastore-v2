<template>
  <header class="sticky top-0 z-50 bg-gradient-to-r from-gray-900/95 via-black/95 to-gray-900/95 backdrop-blur-md border-b border-green-500/30 shadow-glow-green relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
    <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-green-500 to-transparent animate-pulse"></div>
    
    <div class="container mx-auto px-4 relative z-10">
      <div class="flex items-center justify-between h-16">
        <!-- Enhanced Logo with New ReynraLogo Component -->
        <router-link to="/" class="flex items-center space-x-3 group animate-slide-in-left">
          <ReynraLogo 
            variant="horizontal" 
            size="lg" 
            :animated="true"
            class="group-hover:scale-105 transition-transform duration-300"
          />
        </router-link>

        <!-- Enhanced Navigation Menu -->
        <nav class="hidden md:flex items-center space-x-8">
          <router-link
            v-for="(item, index) in navigationItems"
            :key="item.name"
            :to="item.path"
            class="text-gray-300 hover:text-green-400 transition-all duration-300 font-semibold relative group px-3 py-2 rounded-lg hover:bg-green-500/10 animate-slide-in-left"
            :class="{ 'text-green-400 bg-green-500/20': $route.path === item.path }"
            :style="`animation-delay: ${index * 0.1}s`"
          >
            <span class="relative z-10">{{ item.name }}</span>
            <!-- Hover Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-blue-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <!-- Bottom Border -->
            <span class="absolute -bottom-1 left-0 w-0 h-1 bg-gradient-to-r from-green-500 to-blue-500 transition-all duration-300 group-hover:w-full rounded-full"></span>
            <!-- Shine Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out rounded-lg"></div>
          </router-link>
        </nav>

        <!-- Enhanced Right Side Actions -->
        <div class="flex items-center space-x-4 animate-slide-in-right">
          <!-- Search Button -->
          <button
            @click="toggleSearch"
            class="p-3 text-gray-400 hover:text-green-400 transition-all duration-300 rounded-xl hover:bg-green-500/20 group neon-glow"
            title="Search"
          >
            <MagnifyingGlassIcon class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" />
            <div class="absolute inset-0 rounded-xl border border-green-500 opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
          </button>


          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden p-3 text-gray-400 hover:text-green-400 transition-all duration-300 rounded-xl hover:bg-green-500/20 group neon-glow"
            title="Menu"
          >
            <Bars3Icon v-if="!isMobileMenuOpen" class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" />
            <XMarkIcon v-else class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" />
            <div class="absolute inset-0 rounded-xl border border-green-500 opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
          </button>
        </div>
      </div>

      <!-- Enhanced Search Bar -->
      <transition
        enter-active-class="transition duration-500 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0 scale-95"
        enter-to-class="transform translate-y-0 opacity-100 scale-100"
        leave-active-class="transition duration-300 ease-in"
        leave-from-class="transform translate-y-0 opacity-100 scale-100"
        leave-to-class="transform -translate-y-4 opacity-0 scale-95"
      >
        <div v-if="isSearchOpen" class="pb-6">
          <div class="relative max-w-lg mx-auto">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="🔍 Cari game atau produk keren..."
                class="w-full bg-gradient-to-r from-gray-800 to-gray-900 border-2 border-green-500/50 rounded-2xl px-6 py-4 pl-14 text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/50 transition-all duration-300 neon-glow"
                @keyup.enter="performSearch"
              />
              <MagnifyingGlassIcon class="absolute left-4 top-4 w-6 h-6 text-green-400 animate-pulse" />
              <!-- Search Button -->
              <button
                @click="performSearch"
                class="absolute right-2 top-2 bg-gradient-to-r from-green-500 to-blue-500 text-white px-4 py-2 rounded-xl font-semibold hover:scale-105 transition-transform duration-300 shadow-glow-green"
              >
                Cari
              </button>
            </div>
            <!-- Search Suggestions (Mock) -->
            <div class="absolute top-full left-0 right-0 mt-2 bg-gray-800 border border-green-500/30 rounded-xl shadow-2xl overflow-hidden opacity-0 hover:opacity-100 transition-opacity duration-300">
              <div class="p-2">
                <div class="text-green-400 text-sm font-semibold px-3 py-2">🔥 Trending:</div>
                <div class="space-y-1">
                  <div class="px-3 py-2 hover:bg-green-500/20 rounded-lg cursor-pointer text-gray-300 hover:text-green-400 transition-colors">Mobile Legends Diamond</div>
                  <div class="px-3 py-2 hover:bg-green-500/20 rounded-lg cursor-pointer text-gray-300 hover:text-green-400 transition-colors">Free Fire Diamond</div>
                  <div class="px-3 py-2 hover:bg-green-500/20 rounded-lg cursor-pointer text-gray-300 hover:text-green-400 transition-colors">PUBG UC</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Enhanced Mobile Menu -->
      <transition
        enter-active-class="transition duration-500 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-300 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-4 opacity-0"
      >
        <div v-if="isMobileMenuOpen" class="md:hidden border-t border-green-500/30 py-6 bg-gradient-to-r from-gray-900/50 to-black/50 backdrop-blur-sm">
          <nav class="flex flex-col space-y-2">
            <router-link
              v-for="(item, index) in navigationItems"
              :key="item.name"
              :to="item.path"
              class="text-gray-300 hover:text-green-400 transition-all duration-300 font-semibold px-4 py-3 rounded-xl hover:bg-green-500/20 mx-2 group animate-slide-in-left"
              :class="{ 'text-green-400 bg-green-500/20': $route.path === item.path }"
              :style="`animation-delay: ${index * 0.1}s`"
              @click="closeMobileMenu"
            >
              <span class="flex items-center space-x-3">
                <span class="text-xl">{{ item.icon }}</span>
                <span>{{ item.name }}</span>
              </span>
              <!-- Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out rounded-xl"></div>
            </router-link>
          </nav>
        </div>
      </transition>
    </div>
  </header>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useProductsStore } from '../../stores';
import ReynraLogo from '../UI/ReynraLogo.vue';
import {
  MagnifyingGlassIcon,
  Bars3Icon,
  XMarkIcon,
} from '@heroicons/vue/24/outline';

export default {
  name: 'AppHeader',
  components: {
    ReynraLogo,
    MagnifyingGlassIcon,
    Bars3Icon,
    XMarkIcon,
  },
  setup() {
    const router = useRouter();
    const productsStore = useProductsStore();
    
    const isSearchOpen = ref(false);
    const isMobileMenuOpen = ref(false);
    const searchQuery = ref('');
    
    const navigationItems = [
      { name: 'Beranda', path: '/' },
      { name: 'Produk', path: '/products' },
      { name: 'Tentang', path: '/about' },
      { name: 'Kontak', path: '/contact' },
    ];
    
    const toggleSearch = () => {
      isSearchOpen.value = !isSearchOpen.value;
      if (isSearchOpen.value) {
        isMobileMenuOpen.value = false;
      }
    };
    
    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value;
      if (isMobileMenuOpen.value) {
        isSearchOpen.value = false;
      }
    };
    
    const closeMobileMenu = () => {
      isMobileMenuOpen.value = false;
    };
    
    const performSearch = () => {
      if (searchQuery.value.trim()) {
        productsStore.setFilter('search', searchQuery.value.trim());
        router.push('/products');
        isSearchOpen.value = false;
      }
    };
    
    return {
      navigationItems,
      isSearchOpen,
      isMobileMenuOpen,
      searchQuery,
      toggleSearch,
      toggleMobileMenu,
      closeMobileMenu,
      performSearch,
    };
  },
};
</script>
