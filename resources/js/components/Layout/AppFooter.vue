<template>
  <footer class="bg-gradient-to-br from-gray-950 via-black to-gray-950 border-t border-green-500/30 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-t from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-green-500 to-transparent animate-pulse"></div>
    
    <!-- Background Effects -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-24 h-24 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>
    
    <div class="container mx-auto px-4 py-16 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        <!-- Enhanced Brand Section -->
        <div class="space-y-6 animate-slide-in-left">
          <!-- Enhanced Logo with ReynraLogo Component -->
          <router-link to="/" class="flex items-center space-x-3 group">
            <ReynraLogo 
              variant="horizontal" 
              size="lg" 
              :animated="true"
              class="group-hover:scale-105 transition-transform duration-300"
            />
          </router-link>
          <p class="text-gray-300 leading-relaxed">
            Platform top up game terpercaya dengan harga terjangkau dan proses otomatis. 
            Dapatkan diamond, UC, dan item game favoritmu dengan mudah dan aman.
          </p>
          <div class="flex space-x-3">
            <a
              v-for="(social, index) in socialLinks"
              :key="social.name"
              :href="social.url"
              class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-900 hover:from-green-500 hover:to-blue-500 rounded-xl flex items-center justify-center transition-all duration-300 group neon-glow animate-slide-in-left"
              :style="`animation-delay: ${index * 0.1}s`"
              :title="social.name"
            >
              <component :is="social.icon" class="w-6 h-6 text-gray-400 group-hover:text-white group-hover:scale-110 transition-all duration-300" />
              <div class="absolute inset-0 rounded-xl border border-green-500 opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
            </a>
          </div>
        </div>

        <!-- Enhanced Quick Links -->
        <div class="space-y-6 animate-slide-in-left" style="animation-delay: 0.1s">
          <h4 class="text-xl font-bold text-white flex items-center space-x-2">
            <span class="text-green-400">🏠</span>
            <span>Menu Utama</span>
          </h4>
          <ul class="space-y-3">
            <li v-for="(link, index) in quickLinks" :key="link.name">
              <router-link
                :to="link.path"
                class="text-gray-400 hover:text-green-400 transition-all duration-300 text-sm flex items-center space-x-2 group animate-slide-in-left"
                :style="`animation-delay: ${index * 0.05}s`"
              >
                <span class="w-1 h-1 bg-green-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                <span class="group-hover:translate-x-1 transition-transform duration-300">{{ link.name }}</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Enhanced Game Categories -->
        <div class="space-y-6 animate-slide-in-left" style="animation-delay: 0.2s">
          <h4 class="text-xl font-bold text-white flex items-center space-x-2">
            <span class="text-blue-400">🎯</span>
            <span>Kategori Game</span>
          </h4>
          <ul class="space-y-3">
            <li v-for="(category, index) in gameCategories" :key="category">
              <a
                href="#"
                class="text-gray-400 hover:text-blue-400 transition-all duration-300 text-sm flex items-center space-x-2 group animate-slide-in-left"
                :style="`animation-delay: ${index * 0.05}s`"
              >
                <span class="w-1 h-1 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                <span class="group-hover:translate-x-1 transition-transform duration-300">{{ category }}</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Enhanced Contact Info -->
        <div class="space-y-6 animate-slide-in-left" style="animation-delay: 0.3s">
          <h4 class="text-xl font-bold text-white flex items-center space-x-2">
            <span class="text-purple-400">📞</span>
            <span>Hubungi Kami</span>
          </h4>
          <div class="space-y-4">
            <div class="flex items-center space-x-3 group">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <EnvelopeIcon class="w-5 h-5 text-white" />
              </div>
              <span class="text-gray-300 text-sm group-hover:text-green-400 transition-colors duration-300">support@reynrastore.com</span>
            </div>
            <div class="flex items-center space-x-3 group">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <PhoneIcon class="w-5 h-5 text-white" />
              </div>
              <span class="text-gray-300 text-sm group-hover:text-green-400 transition-colors duration-300">+62 838-7323-1154</span>
            </div>
            <div class="flex items-start space-x-3 group">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mt-0.5">
                <MapPinIcon class="w-5 h-5 text-white" />
              </div>
              <span class="text-gray-300 text-sm group-hover:text-green-400 transition-colors duration-300">
                Palmerah, Jakarta Barat, Indonesia<br />
                <span class="text-green-400 font-semibold">🕐 Buka 24/7</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Payment Methods -->
      <div class="mt-16 pt-10 border-t border-green-500/30">
        <div class="text-center space-y-8">
          <h5 class="text-2xl font-bold holographic flex items-center justify-center space-x-2">
            <span>💳</span>
            <span>Metode Pembayaran</span>
          </h5>
          <div class="flex flex-wrap justify-center items-center gap-6">
            <div
              v-for="(payment, index) in paymentMethods"
              :key="payment.name"
              class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-4 hover:from-gray-700 hover:to-gray-800 transition-all duration-300 group neon-glow animate-slide-in-left hover:scale-105 relative"
              :style="`animation-delay: ${index * 0.1}s`"
              :title="payment.name"
            >
              <!-- Special Bank Transfer Design -->
              <div v-if="payment.isBankTransfer" class="w-16 h-10 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 rounded-lg flex items-center justify-center group-hover:shadow-glow-green transition-all duration-300 relative overflow-hidden">
                <!-- Bank Building Icon Background -->
                <div class="absolute inset-0 opacity-20 flex items-center justify-center">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L2 6v2h20V6l-10-5zM4 9v9h2v-9H4zm4 0v9h2v-9H8zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM2 19h20v2H2v-2z"/>
                  </svg>
                </div>
                <!-- Bank Transfer Text -->
                <span class="text-white font-bold text-xs relative z-10 drop-shadow-lg">BANK</span>
                <!-- Money Flow Animation -->
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-green-400 to-transparent animate-pulse"></div>
                <!-- Coin Animation -->
                <div class="absolute top-1 right-1 w-2 h-2 bg-yellow-400 rounded-full animate-bounce opacity-70"></div>
              </div>
              
              <!-- Payment Methods with Images -->
              <div v-else class="w-16 h-10 rounded-lg flex items-center justify-center group-hover:shadow-glow-green transition-all duration-300 overflow-hidden">
                <img 
                  :src="payment.logo" 
                  :alt="payment.name" 
                  class="w-full h-full object-contain"
                  @error="handleImageError"
                />
              </div>
              
              <!-- Hover Border Effect -->
              <div class="absolute inset-0 rounded-2xl border border-green-500 opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
              
              <!-- Special Bank Transfer Glow Effect -->
              <div v-if="payment.isBankTransfer" class="absolute inset-0 rounded-2xl border-2 border-emerald-500 opacity-0 group-hover:opacity-70 transition-opacity duration-300 animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Bottom Bar -->
      <div class="mt-12 pt-10 border-t border-green-500/30">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
          <div class="text-gray-400 text-sm flex items-center space-x-2 animate-slide-in-left">
            <span class="text-green-400">©</span>
            <span>{{ currentYear }} Reynra Store. All rights reserved.</span>
            <span class="text-green-400 animate-pulse">⚡</span>
          </div>
          <div class="flex space-x-8 animate-slide-in-right">
            <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-300 hover:scale-105 relative group">
              Kebijakan Privasi
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-300 hover:scale-105 relative group">
              Syarat & Ketentuan
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-300 hover:scale-105 relative group">
              FAQ
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
            </a>
          </div>
        </div>
        
        <!-- Trust Indicators -->
        <div class="mt-8 pt-6 border-t border-gray-800/50">
          <div class="flex flex-wrap justify-center items-center gap-8 text-center">
            <div class="flex items-center space-x-2 animate-slide-in-left" style="animation-delay: 0.1s">
              <span class="text-2xl">🛡️</span>
              <div>
                <div class="text-green-400 font-semibold text-sm">100% Aman</div>
                <div class="text-gray-500 text-xs">Transaksi Terpercaya</div>
              </div>
            </div>
            <div class="flex items-center space-x-2 animate-slide-in-left" style="animation-delay: 0.2s">
              <span class="text-2xl">⚡</span>
              <div>
                <div class="text-green-400 font-semibold text-sm">Proses Cepat</div>
                <div class="text-gray-500 text-xs">1-3 Menit</div>
              </div>
            </div>
            <div class="flex items-center space-x-2 animate-slide-in-left" style="animation-delay: 0.3s">
              <span class="text-2xl">🎯</span>
              <div>
                <div class="text-green-400 font-semibold text-sm">50K+ Pelanggan</div>
                <div class="text-gray-500 text-xs">Rating 4.9/5</div>
              </div>
            </div>
            <div class="flex items-center space-x-2 animate-slide-in-left" style="animation-delay: 0.4s">
              <span class="text-2xl">🕐</span>
              <div>
                <div class="text-green-400 font-semibold text-sm">24/7 Support</div>
                <div class="text-gray-500 text-xs">Selalu Siap</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { computed } from 'vue';
import ReynraLogo from '../UI/ReynraLogo.vue';
import {
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
} from '@heroicons/vue/24/outline';

// Social media icons
const FacebookIcon = {
  template: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
};

const TwitterIcon = {
  template: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>'
};

const InstagramIcon = {
  template: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>'
};

const TikTokIcon = {
  template: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>'
};

const WhatsAppIcon = {
  template: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/></svg>'
};

export default {
  name: 'AppFooter',
  components: {
    ReynraLogo,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    FacebookIcon,
    TwitterIcon,
    InstagramIcon,
    TikTokIcon,
    WhatsAppIcon,
  },
  setup() {
    const currentYear = computed(() => new Date().getFullYear());
    
    const socialLinks = [
      { name: 'Instagram', url: 'https://www.instagram.com/reynrastore_official/', icon: InstagramIcon },
      { name: 'Facebook', url: 'https://www.facebook.com/profile.php?id=61578912875239', icon: FacebookIcon },
      { name: 'Twitter', url: 'https://x.com/Reynrastore', icon: TwitterIcon },
      { name: 'TikTok', url: 'https://www.tiktok.com/@reynra.store', icon: TikTokIcon },
      { name: 'WhatsApp', url: 'https://api.whatsapp.com/send/?phone=6283873231154&text&type=phone_number&app_absent=0', icon: WhatsAppIcon },
    ];
    
    const quickLinks = [
      { name: 'Beranda', path: '/' },
      { name: 'Produk', path: '/products' },
      { name: 'Tentang Kami', path: '/about' },
      { name: 'Kontak', path: '/contact' },
      { name: 'Lacak Pesanan', path: '/order-tracking' },
    ];
    
    const gameCategories = [
      'Mobile Legends',
      'Free Fire',
      'PUBG Mobile',
      'Genshin Impact',
      'Valorant',
      'Steam Wallet',
    ];
    
    const paymentMethods = [
      { name: 'DANA', logo: '/images/payment/dana.png' },
      { name: 'OVO', logo: '/images/payment/ovo.png' },
      { name: 'GoPay', logo: '/images/payment/gopay.png' },
      { name: 'ShopeePay', logo: '/images/payment/shopeepay.png' },
      { name: 'Bank Transfer', isBankTransfer: true },
      { name: 'QRIS', logo: '/images/payment/qris.png' },
    ];

    // Handle image loading errors
    const handleImageError = (event) => {
      console.warn('Failed to load payment image:', event.target.src);
      // You could set a fallback image here if needed
    };
    
    return {
      currentYear,
      socialLinks,
      quickLinks,
      gameCategories,
      paymentMethods,
      handleImageError,
    };
  },
};
</script>
