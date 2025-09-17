<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-900 py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">Kelola Pesanan</h1>
            <p class="text-gray-400">Kelola dan proses pesanan manual dan otomatis</p>
          </div>
          <div class="flex space-x-4">
            <button 
              @click="refreshOrders"
              :disabled="loading"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>{{ loading ? 'Memuat...' : 'Refresh' }}</span>
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Total Pesanan</p>
                <p class="text-2xl font-bold text-white">{{ stats.total }}</p>
              </div>
              <div class="bg-blue-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-400">{{ stats.pending }}</p>
              </div>
              <div class="bg-yellow-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Proses</p>
                <p class="text-2xl font-bold text-blue-400">{{ stats.processing }}</p>
              </div>
              <div class="bg-blue-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Selesai</p>
                <p class="text-2xl font-bold text-green-400">{{ stats.completed }}</p>
              </div>
              <div class="bg-green-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Gagal</p>
                <p class="text-2xl font-bold text-red-400">{{ stats.failed }}</p>
              </div>
              <div class="bg-red-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Cari Pesanan</label>
              <input 
                v-model="filters.search"
                type="text" 
                placeholder="ID pesanan, email..."
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
              <select 
                v-model="filters.status"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Proses</option>
                <option value="completed">Selesai</option>
                <option value="failed">Gagal</option>
                <option value="cancelled">Dibatalkan</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Tipe</label>
              <select 
                v-model="filters.type"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
                <option value="">Semua Tipe</option>
                <option value="manual">Manual</option>
                <option value="auto">Otomatis</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal</label>
              <input 
                v-model="filters.date"
                type="date" 
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-700">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID Pesanan</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Pelanggan</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Produk</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tipe</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tanggal</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">#{{ order.id }}</div>
                    <div class="text-sm text-gray-400">{{ order.order_number }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ order.customer_name }}</div>
                    <div class="text-sm text-gray-400">{{ order.customer_email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-white">{{ getOrderProducts(order) }}</div>
                    <div class="text-sm text-gray-400">{{ order.orderItems?.length || 0 }} item(s)</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">Rp {{ formatPrice(order.total_amount) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getStatusColor(order.status)"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                    >
                      {{ getStatusText(order.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="order.is_manual ? 'bg-yellow-600' : 'bg-purple-600'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                    >
                      {{ order.is_manual ? 'Manual' : 'Otomatis' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-300">{{ formatDate(order.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        @click="viewOrder(order)"
                        class="text-blue-400 hover:text-blue-300"
                        title="Lihat Detail"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                      </button>
                      <button 
                        v-if="order.status === 'pending' && order.is_manual"
                        @click="processOrder(order)"
                        class="text-green-400 hover:text-green-300"
                        title="Proses Manual"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                      <button 
                        v-if="order.status === 'processing'"
                        @click="completeOrder(order)"
                        class="text-green-400 hover:text-green-300"
                        title="Selesaikan"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </button>
                      <button 
                        v-if="order.status === 'processing' && order.is_manual"
                        @click="cancelOrder(order)"
                        class="text-red-400 hover:text-red-300"
                        title="Batalkan"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-400">
            Menampilkan {{ filteredOrders.length }} dari {{ orders.length }} pesanan
          </div>
        </div>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-white">Detail Pesanan #{{ selectedOrder?.id }}</h3>
          <button @click="closeDetailModal" class="text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="selectedOrder" class="space-y-4">
          <!-- Order Info -->
          <div class="bg-gray-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-white mb-3">Informasi Pesanan</h4>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-400">ID Pesanan:</span>
                <span class="text-white">#{{ selectedOrder.id }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Status:</span>
                <span :class="getStatusColor(selectedOrder.status)" class="px-2 py-1 text-xs font-semibold rounded-full text-white">
                  {{ getStatusText(selectedOrder.status) }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Tipe:</span>
                <span :class="selectedOrder.is_manual ? 'bg-yellow-600' : 'bg-purple-600'" class="px-2 py-1 text-xs font-semibold rounded-full text-white">
                  {{ selectedOrder.is_manual ? 'Manual' : 'Otomatis' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Tanggal:</span>
                <span class="text-white">{{ formatDate(selectedOrder.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Customer Info -->
          <div class="bg-gray-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-white mb-3">Informasi Pelanggan</h4>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-400">Nama:</span>
                <span class="text-white">{{ selectedOrder.customer_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Email:</span>
                <span class="text-white">{{ selectedOrder.customer_email }}</span>
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="bg-gray-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-white mb-3">Informasi Produk</h4>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-400">Produk:</span>
                <span class="text-white">{{ selectedOrder.product_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Data Game:</span>
                <span class="text-white">{{ selectedOrder.game_data }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Total:</span>
                <span class="text-green-400 font-semibold">Rp {{ formatPrice(selectedOrder.total_amount) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-6 pt-4 border-t border-gray-600">
          <button 
            v-if="selectedOrder?.status === 'pending' && selectedOrder?.is_manual"
            @click="processOrder(selectedOrder)"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
          >
            Proses Manual
          </button>
          <button 
            v-if="selectedOrder?.status === 'processing'"
            @click="completeOrder(selectedOrder)"
            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
          >
            Selesaikan
          </button>
          <button 
            @click="closeDetailModal"
            class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../../components/Layout/AppLayout.vue';

export default {
  name: 'AdminOrders',
  components: { AppLayout },
  setup() {
    const orders = ref([]);
    const loading = ref(false);
    const showDetailModal = ref(false);
    const selectedOrder = ref(null);

    const filters = reactive({
      search: '',
      status: '',
      type: '',
      date: ''
    });

    const stats = computed(() => {
      const total = orders.value.length;
      const pending = orders.value.filter(o => o.status === 'pending').length;
      const processing = orders.value.filter(o => o.status === 'processing').length;
      const completed = orders.value.filter(o => o.status === 'completed').length;
      const failed = orders.value.filter(o => o.status === 'failed').length;
      
      return { total, pending, processing, completed, failed };
    });

    const filteredOrders = computed(() => {
      let filtered = orders.value;

      if (filters.search) {
        filtered = filtered.filter(o => 
          o.id.toString().includes(filters.search) ||
          o.order_number?.toLowerCase().includes(filters.search.toLowerCase()) ||
          o.customer_name?.toLowerCase().includes(filters.search.toLowerCase()) ||
          o.customer_email?.toLowerCase().includes(filters.search.toLowerCase())
        );
      }

      if (filters.status) {
        filtered = filtered.filter(o => o.status === filters.status);
      }

      if (filters.type) {
        filtered = filtered.filter(o => 
          filters.type === 'manual' ? o.is_manual : !o.is_manual
        );
      }

      if (filters.date) {
        filtered = filtered.filter(o => 
          new Date(o.created_at).toDateString() === new Date(filters.date).toDateString()
        );
      }

      return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    });

    const fetchOrders = async () => {
      loading.value = true;
      try {
        const response = await fetch('/api/v1/admin/orders', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          orders.value = data.data || [];
        }
      } catch (error) {
        console.error('Error fetching orders:', error);
      } finally {
        loading.value = false;
      }
    };

    const refreshOrders = () => {
      fetchOrders();
    };

    const viewOrder = (order) => {
      selectedOrder.value = order;
      showDetailModal.value = true;
    };

    const closeDetailModal = () => {
      showDetailModal.value = false;
      selectedOrder.value = null;
    };

    const processOrder = async (order) => {
      if (!confirm('Apakah Anda yakin ingin memproses pesanan ini?')) return;

      try {
        const response = await fetch(`/api/v1/admin/orders/${order.id}/process`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Pesanan berhasil diproses');
          await fetchOrders();
          if (selectedOrder.value?.id === order.id) {
            selectedOrder.value.status = 'processing';
          }
        } else {
          alert('Gagal memproses pesanan');
        }
      } catch (error) {
        console.error('Error processing order:', error);
        alert('Gagal memproses pesanan');
      }
    };

    const completeOrder = async (order) => {
      if (!confirm('Apakah Anda yakin pesanan ini sudah selesai?')) return;

      try {
        const response = await fetch(`/api/v1/admin/orders/${order.id}/complete`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Pesanan berhasil diselesaikan');
          await fetchOrders();
          if (selectedOrder.value?.id === order.id) {
            selectedOrder.value.status = 'completed';
          }
        } else {
          alert('Gagal menyelesaikan pesanan');
        }
      } catch (error) {
        console.error('Error completing order:', error);
        alert('Gagal menyelesaikan pesanan');
      }
    };

    const cancelOrder = async (order) => {
      if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.')) return;

      try {
        const response = await fetch(`/api/v1/admin/orders/${order.id}/cancel`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Pesanan berhasil dibatalkan');
          await fetchOrders();
          if (selectedOrder.value?.id === order.id) {
            selectedOrder.value.status = 'cancelled';
          }
        } else {
          alert('Gagal membatalkan pesanan');
        }
      } catch (error) {
        console.error('Error cancelling order:', error);
        alert('Gagal membatalkan pesanan');
      }
    };

    const getStatusColor = (status) => {
      const colors = {
        pending: 'bg-yellow-600',
        processing: 'bg-blue-600',
        completed: 'bg-green-600',
        failed: 'bg-red-600',
        cancelled: 'bg-gray-600'
      };
      return colors[status] || 'bg-gray-600';
    };

    const getStatusText = (status) => {
      const texts = {
        pending: 'Pending',
        processing: 'Proses',
        completed: 'Selesai',
        failed: 'Gagal',
        cancelled: 'Dibatalkan'
      };
      return texts[status] || status;
    };

    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID').format(price);
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleString('id-ID');
    };

    const getOrderProducts = (order) => {
      if (order.orderItems && order.orderItems.length > 0) {
        return order.orderItems.map(item => item.product?.name || 'Unknown Product').join(', ');
      }
      return 'No products';
    };

    onMounted(() => {
      fetchOrders();
    });

    return {
      orders,
      loading,
      showDetailModal,
      selectedOrder,
      filters,
      stats,
      filteredOrders,
      refreshOrders,
      viewOrder,
      closeDetailModal,
      processOrder,
      completeOrder,
      cancelOrder,
      getStatusColor,
      getStatusText,
      formatPrice,
      formatDate,
      getOrderProducts
    };
  }
};
</script>
