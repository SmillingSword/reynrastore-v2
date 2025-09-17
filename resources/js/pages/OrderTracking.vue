<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden">
      <!-- Enhanced Background Effects -->
      <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
      <div class="absolute top-20 left-20 w-64 h-64 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="container mx-auto px-4 py-12 relative z-10">
        <!-- Enhanced Page Header -->
        <div class="text-center mb-16 animate-fade-in">
          <h1 class="text-5xl lg:text-6xl font-bold text-white mb-6 holographic">
            📱 Lacak Pesanan 📱
          </h1>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
            Masukkan nomor pesanan Anda untuk melacak status dan progress pesanan secara real-time
          </p>
          <div class="w-32 h-1 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 mx-auto mt-6 rounded-full"></div>
        </div>

        <!-- Enhanced Search Form -->
        <div class="max-w-2xl mx-auto mb-12 animate-slide-in-left">
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 neon-glow">
            <form @submit.prevent="trackOrder" class="space-y-6">
              <div>
                <label class="block text-white font-bold mb-3 flex items-center space-x-2">
                  <span class="text-green-400">🔍</span>
                  <span>Nomor Pesanan</span>
                </label>
                <div class="relative">
                  <input
                    v-model="orderNumber"
                    type="text"
                    placeholder="Contoh: RS20240115ABC123"
                    class="w-full bg-gradient-to-r from-gray-700 to-gray-800 border-2 border-green-500/50 rounded-xl px-4 py-4 text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/50 transition-all duration-300 neon-glow text-lg"
                    :disabled="loading"
                  />
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-green-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>
              
              <button
                type="submit"
                :disabled="loading || !orderNumber.trim()"
                class="w-full group bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 disabled:from-gray-600 disabled:to-gray-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:scale-100 shadow-glow-green neon-glow relative overflow-hidden"
              >
                <span v-if="loading" class="relative z-10 flex items-center justify-center space-x-3">
                  <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Mencari Pesanan...</span>
                </span>
                <span v-else class="relative z-10 flex items-center justify-center space-x-3">
                  <span class="text-xl">🚀</span>
                  <span>Lacak Pesanan</span>
                  <span class="group-hover:translate-x-2 transition-transform duration-300">→</span>
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
              </button>
            </form>
          </div>
        </div>

        <!-- Enhanced Error State -->
        <div v-if="error && !loading" class="max-w-2xl mx-auto mb-12 animate-fade-in">
          <div class="bg-gradient-to-br from-red-900/50 to-gray-900 rounded-2xl p-8 border border-red-500/50 neon-glow">
            <div class="text-center">
              <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">❌</span>
              </div>
              <h3 class="text-white font-bold text-xl mb-2">Pesanan Tidak Ditemukan</h3>
              <p class="text-red-400 mb-4">{{ error }}</p>
              <button 
                @click="clearError"
                class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-bold px-6 py-2 rounded-lg transition-all duration-300 transform hover:scale-105"
              >
                Coba Lagi
              </button>
            </div>
          </div>
        </div>

        <!-- Enhanced Order Details -->
        <div v-if="order && !loading" class="max-w-4xl mx-auto animate-fade-in">
          <!-- Order Header -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 mb-8 border border-gray-700 neon-glow">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
              <div>
                <h2 class="text-3xl font-bold text-white mb-2 holographic">
                  Pesanan #{{ order.order_number }}
                </h2>
                <p class="text-gray-400">
                  Dibuat pada {{ formatDate(order.created_at) }}
                </p>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-right">
                  <div class="text-sm text-gray-400">Total Pembayaran</div>
                  <div class="text-2xl font-bold text-green-400 holographic">
                    {{ formatPrice(order.total_amount) }}
                  </div>
                </div>
                <div :class="getStatusBadgeClass(order.status)" class="px-4 py-2 rounded-full font-bold text-sm pulse-ring">
                  {{ getStatusText(order.status) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Status Timeline -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 mb-8 border border-gray-700 neon-glow">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center space-x-2">
              <span class="text-blue-400">📊</span>
              <span>Status Pesanan</span>
            </h3>
            
            <div class="relative">
              <!-- Timeline Line -->
              <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-green-500 to-blue-500"></div>
              
              <div class="space-y-8">
                <!-- Pending -->
                <div class="flex items-center space-x-4">
                  <div :class="getTimelineIconClass('pending', order.status)" class="w-12 h-12 rounded-full flex items-center justify-center relative z-10 pulse-ring">
                    <span class="text-xl">📝</span>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-white font-bold">Pesanan Diterima</h4>
                    <p class="text-gray-400 text-sm">Pesanan Anda telah diterima dan sedang diproses</p>
                    <p class="text-green-400 text-xs">{{ formatDate(order.created_at) }}</p>
                  </div>
                </div>

                <!-- Processing -->
                <div class="flex items-center space-x-4">
                  <div :class="getTimelineIconClass('processing', order.status)" class="w-12 h-12 rounded-full flex items-center justify-center relative z-10 pulse-ring">
                    <span class="text-xl">⚙️</span>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-white font-bold">Sedang Diproses</h4>
                    <p class="text-gray-400 text-sm">Pesanan sedang diproses oleh sistem</p>
                    <p v-if="isStatusReached('processing', order.status)" class="text-green-400 text-xs">
                      {{ order.updated_at ? formatDate(order.updated_at) : 'Dalam proses...' }}
                    </p>
                  </div>
                </div>

                <!-- Completed -->
                <div class="flex items-center space-x-4">
                  <div :class="getTimelineIconClass('completed', order.status)" class="w-12 h-12 rounded-full flex items-center justify-center relative z-10 pulse-ring">
                    <span class="text-xl">✅</span>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-white font-bold">Selesai</h4>
                    <p class="text-gray-400 text-sm">Pesanan telah selesai diproses</p>
                    <p v-if="order.status === 'completed'" class="text-green-400 text-xs">
                      {{ order.completed_at ? formatDate(order.completed_at) : formatDate(order.updated_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Customer Info -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 neon-glow">
              <h3 class="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                <span class="text-green-400">👤</span>
                <span>Informasi Pelanggan</span>
              </h3>
              <div class="space-y-3">
                <div>
                  <span class="text-gray-400 text-sm">Nama:</span>
                  <p class="text-white font-semibold">{{ order.customer_name }}</p>
                </div>
                <div>
                  <span class="text-gray-400 text-sm">Email:</span>
                  <p class="text-white font-semibold">{{ order.customer_email }}</p>
                </div>
                <div>
                  <span class="text-gray-400 text-sm">Telepon:</span>
                  <p class="text-white font-semibold">{{ order.customer_phone }}</p>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 neon-glow">
              <h3 class="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                <span class="text-blue-400">💳</span>
                <span>Status Pembayaran</span>
              </h3>
              <div class="space-y-3">
                <div>
                  <span class="text-gray-400 text-sm">Status:</span>
                  <div :class="getPaymentStatusBadgeClass(order.payment_status)" class="inline-block px-3 py-1 rounded-full font-bold text-sm mt-1">
                    {{ getPaymentStatusText(order.payment_status) }}
                  </div>
                </div>
                <div>
                  <span class="text-gray-400 text-sm">Total:</span>
                  <p class="text-2xl font-bold text-green-400 holographic">{{ formatPrice(order.total_amount) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Order Items -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 neon-glow">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center space-x-2">
              <span class="text-purple-400">🛍️</span>
              <span>Detail Pesanan</span>
            </h3>
            
            <div class="space-y-6">
              <div 
                v-for="(item, index) in order.order_items" 
                :key="item.id"
                class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl p-6 border border-gray-600 hover:border-green-500 transition-all duration-300 animate-slide-in-left"
                :style="`animation-delay: ${index * 0.1}s`"
              >
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                  <div class="flex-1">
                    <h4 class="text-xl font-bold text-white mb-2">{{ item.product.name }}</h4>
                    <p class="text-gray-400 mb-3">{{ item.product.description || 'Top up game favoritmu' }}</p>
                    
                    <!-- Form Data Display -->
                    <div v-if="item.form_data" class="bg-gray-800/50 rounded-lg p-4 mb-3">
                      <h5 class="text-sm font-bold text-green-400 mb-2">Data Game:</h5>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        <div v-for="(value, key) in item.form_data" :key="key">
                          <span class="text-gray-400">{{ formatFieldName(key) }}:</span>
                          <span class="text-white font-semibold ml-2">{{ value }}</span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 text-sm">
                      <span class="text-gray-400">Jumlah: <span class="text-white font-semibold">{{ item.quantity }}</span></span>
                      <span class="text-gray-400">Harga: <span class="text-green-400 font-semibold">{{ formatPrice(item.unit_price) }}</span></span>
                    </div>
                  </div>
                  
                  <div class="text-right">
                    <div class="text-2xl font-bold text-green-400 holographic mb-2">
                      {{ formatPrice(item.total_price) }}
                    </div>
                    <div :class="getStatusBadgeClass(item.status || order.status)" class="px-3 py-1 rounded-full font-bold text-xs">
                      {{ getStatusText(item.status || order.status) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Action Buttons -->
          <div class="text-center mt-8">
            <button
              @click="trackOrder"
              class="group bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-glow-green neon-glow relative overflow-hidden mr-4"
            >
              <span class="relative z-10 flex items-center space-x-2">
                <span class="text-lg">🔄</span>
                <span>Refresh Status</span>
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
            </button>
            
            <button
              @click="clearOrder"
              class="group border-2 border-green-500 text-green-400 hover:bg-green-500 hover:text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 neon-glow relative overflow-hidden"
            >
              <span class="relative z-10 flex items-center space-x-2">
                <span class="text-lg">🔍</span>
                <span>Lacak Pesanan Lain</span>
              </span>
              <div class="absolute inset-0 bg-green-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            </button>
          </div>
        </div>

        <!-- Enhanced Help Section -->
        <div v-if="!order && !loading && !error" class="max-w-4xl mx-auto mt-16 animate-slide-in-left">
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 neon-glow">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">
              ❓ Butuh Bantuan?
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="text-center p-6 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300">
                <div class="text-4xl mb-4">📞</div>
                <h4 class="text-white font-bold mb-2">Customer Service</h4>
                <p class="text-gray-400 text-sm">Hubungi CS kami untuk bantuan lebih lanjut</p>
              </div>
              
              <div class="text-center p-6 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300">
                <div class="text-4xl mb-4">💬</div>
                <h4 class="text-white font-bold mb-2">Live Chat</h4>
                <p class="text-gray-400 text-sm">Chat langsung dengan tim support</p>
              </div>
              
              <div class="text-center p-6 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300">
                <div class="text-4xl mb-4">📧</div>
                <h4 class="text-white font-bold mb-2">Email Support</h4>
                <p class="text-gray-400 text-sm">Kirim email untuk pertanyaan detail</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, computed } from 'vue';
import AppLayout from '../components/Layout/AppLayout.vue';
import axios from 'axios';

export default {
  name: 'OrderTracking',
  components: {
    AppLayout,
  },
  setup() {
    const orderNumber = ref('');
    const order = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const trackOrder = async () => {
      if (!orderNumber.value.trim()) {
        error.value = 'Silakan masukkan nomor pesanan';
        return;
      }

      loading.value = true;
      error.value = null;

      try {
        const response = await axios.get(`/api/v1/orders/${orderNumber.value.trim()}/track`);
        
        if (response.data.success) {
          order.value = response.data.data;
        } else {
          error.value = response.data.message || 'Pesanan tidak ditemukan';
        }
      } catch (err) {
        console.error('Error tracking order:', err);
        if (err.response?.status === 404) {
          error.value = 'Pesanan dengan nomor tersebut tidak ditemukan. Pastikan nomor pesanan sudah benar.';
        } else {
          error.value = err.response?.data?.message || 'Terjadi kesalahan saat melacak pesanan. Silakan coba lagi.';
        }
      } finally {
        loading.value = false;
      }
    };

    const clearOrder = () => {
      order.value = null;
      orderNumber.value = '';
      error.value = null;
    };

    const clearError = () => {
      error.value = null;
    };

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const formatPrice = (price) => {
      // Handle null, undefined, or non-numeric values
      const numericPrice = parseFloat(price);
      if (isNaN(numericPrice) || numericPrice === null || numericPrice === undefined) {
        return 'Rp 0';
      }
      
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(numericPrice);
    };

    const formatFieldName = (fieldName) => {
      const fieldNames = {
        'user_id': 'User ID',
        'zone_id': 'Zone ID',
        'server_id': 'Server ID',
        'player_id': 'Player ID',
        'email': 'Email',
        'phone': 'No. Telepon'
      };
      return fieldNames[fieldName] || fieldName.replace('_', ' ').toUpperCase();
    };

    const getStatusText = (status) => {
      const statusTexts = {
        'pending': 'Menunggu',
        'processing': 'Diproses',
        'completed': 'Selesai',
        'failed': 'Gagal',
        'cancelled': 'Dibatalkan'
      };
      return statusTexts[status] || status;
    };

    const getStatusBadgeClass = (status) => {
      const classes = {
        'pending': 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white',
        'processing': 'bg-gradient-to-r from-blue-500 to-purple-500 text-white',
        'completed': 'bg-gradient-to-r from-green-500 to-green-400 text-white',
        'failed': 'bg-gradient-to-r from-red-500 to-red-400 text-white',
        'cancelled': 'bg-gradient-to-r from-gray-500 to-gray-400 text-white'
      };
      return classes[status] || 'bg-gray-500 text-white';
    };

    const getPaymentStatusText = (status) => {
      const statusTexts = {
        'pending': 'Menunggu Pembayaran',
        'paid': 'Sudah Dibayar',
        'failed': 'Pembayaran Gagal',
        'expired': 'Pembayaran Kedaluwarsa',
        'cancelled': 'Pembayaran Dibatalkan'
      };
      return statusTexts[status] || status;
    };

    const getPaymentStatusBadgeClass = (status) => {
      const classes = {
        'pending': 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white',
        'paid': 'bg-gradient-to-r from-green-500 to-green-400 text-white',
        'failed': 'bg-gradient-to-r from-red-500 to-red-400 text-white',
        'expired': 'bg-gradient-to-r from-gray-500 to-gray-400 text-white',
        'cancelled': 'bg-gradient-to-r from-gray-500 to-gray-400 text-white'
      };
      return classes[status] || 'bg-gray-500 text-white';
    };

    const getTimelineIconClass = (stepStatus, currentStatus) => {
      const statusOrder = ['pending', 'processing', 'completed'];
      const stepIndex = statusOrder.indexOf(stepStatus);
      const currentIndex = statusOrder.indexOf(currentStatus);
      
      if (currentStatus === 'failed' || currentStatus === 'cancelled') {
        return stepIndex === 0 ? 'bg-gradient-to-r from-green-500 to-green-400 text-white' : 'bg-gray-600 text-gray-400';
      }
      
      if (stepIndex <= currentIndex) {
        return 'bg-gradient-to-r from-green-500 to-green-400 text-white';
      } else {
        return 'bg-gray-600 text-gray-400';
      }
    };

    const isStatusReached = (stepStatus, currentStatus) => {
      const statusOrder = ['pending', 'processing', 'completed'];
      const stepIndex = statusOrder.indexOf(stepStatus);
      const currentIndex = statusOrder.indexOf(currentStatus);
      
      return stepIndex <= currentIndex;
    };

    return {
      orderNumber,
      order,
      loading,
      error,
      trackOrder,
      clearOrder,
      clearError,
      formatDate,
      formatPrice,
      formatFieldName,
      getStatusText,
      getStatusBadgeClass,
      getPaymentStatusText,
      getPaymentStatusBadgeClass,
      getTimelineIconClass,
      isStatusReached,
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
</style>
