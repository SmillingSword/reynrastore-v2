  <template>
  <AppLayout>
    <div class="min-h-screen bg-gray-900 py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
          <p class="text-gray-400">Kelola toko game top-up Anda</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <svg class="animate-spin h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <!-- Stats Cards -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Orders -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-gray-400 text-sm font-medium">Total Pesanan</p>
                <p class="text-3xl font-bold text-white mt-2">{{ stats.totalOrders }}</p>
                <p class="text-green-400 text-sm mt-1 flex items-center" v-if="stats.revenueGrowth >= 0">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                  +{{ stats.revenueGrowth }}% dari bulan lalu
                </p>
                <p class="text-red-400 text-sm mt-1 flex items-center" v-else>
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                  </svg>
                  {{ stats.revenueGrowth }}% dari bulan lalu
                </p>
              </div>
              <div class="bg-blue-500/20 p-4 rounded-xl border border-blue-500/30">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Revenue -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-gray-400 text-sm font-medium">Total Pendapatan</p>
                <p class="text-3xl font-bold text-white mt-2">Rp {{ formatPrice(stats.totalRevenue) }}</p>
                <p class="text-green-400 text-sm mt-1 flex items-center" v-if="stats.revenueGrowth >= 0">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                  +{{ stats.revenueGrowth }}% dari bulan lalu
                </p>
                <p class="text-red-400 text-sm mt-1 flex items-center" v-else>
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                  </svg>
                  {{ stats.revenueGrowth }}% dari bulan lalu
                </p>
              </div>
              <div class="bg-green-500/20 p-4 rounded-xl border border-green-500/30">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Active Products -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-gray-400 text-sm font-medium">Produk Aktif</p>
                <p class="text-3xl font-bold text-white mt-2">{{ stats.activeProducts }}</p>
                <p class="text-purple-400 text-sm mt-1">{{ stats.diggieProducts }} Digiflazz, {{ stats.manualProducts }} Manual</p>
              </div>
              <div class="bg-purple-500/20 p-4 rounded-xl border border-purple-500/30">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Pending Orders -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-gray-400 text-sm font-medium">Pesanan Pending</p>
                <p class="text-3xl font-bold text-white mt-2">{{ stats.pendingOrders }}</p>
                <p class="text-yellow-400 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                  Perlu diproses
                </p>
              </div>
              <div class="bg-yellow-500/20 p-4 rounded-xl border border-yellow-500/30">
                <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
          <!-- Revenue Chart -->
          <div class="lg:col-span-2 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-semibold text-white">Pendapatan 7 Hari Terakhir</h3>
              <div class="flex items-center space-x-2 text-sm text-gray-400">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span>Pendapatan</span>
              </div>
            </div>
            
            <!-- Chart Container with proper overflow handling -->
            <div class="relative bg-gray-900/50 rounded-lg overflow-hidden">
              <div v-if="revenueChart.length === 0" class="flex items-center justify-center h-64">
                <div class="text-center">
                  <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  <p class="text-gray-400 text-sm">Belum ada data pendapatan</p>
                </div>
              </div>
              
              <div v-else class="relative p-4">
                <!-- Chart with proper dimensions -->
                <div class="h-48 relative">
                  <!-- Grid lines -->
                  <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20">
                    <div v-for="i in 5" :key="i" class="border-t border-gray-600"></div>
                  </div>
                  
                  <!-- Y-axis labels - positioned properly -->
                  <div class="absolute -left-2 top-0 h-full flex flex-col justify-between text-xs text-gray-500 pr-2" style="width: 60px;">
                    <span v-if="revenueChart.length > 0" class="text-right">{{ formatPrice(Math.max(...revenueChart.map(d => d.amount))) }}</span>
                    <span class="opacity-60 text-right">{{ formatPrice(Math.max(...revenueChart.map(d => d.amount)) / 2) }}</span>
                    <span class="text-right">0</span>
                  </div>
                  
                  <!-- Chart area with proper margins -->
                  <div class="ml-16 mr-4 h-full flex items-end justify-between space-x-2 pb-6">
                    <!-- Bars -->
                    <div 
                      v-for="(day, index) in revenueChart" 
                      :key="index"
                      class="flex-1 flex flex-col items-center relative group max-w-12"
                    >
                      <!-- Bar -->
                      <div 
                        class="w-full bg-gradient-to-t from-green-600 to-green-400 rounded-t-sm transition-all duration-300 hover:from-green-500 hover:to-green-300 relative shadow-lg"
                        :style="{ 
                          height: revenueChart.some(d => d.amount > 0) 
                            ? `${Math.max(8, (day.amount / Math.max(...revenueChart.map(d => d.amount))) * 85)}%` 
                            : '8px',
                          minHeight: '8px'
                        }"
                      >
                        <!-- Tooltip -->
                        <div class="absolute -top-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap z-30 border border-gray-600">
                          <div class="font-medium text-center">{{ day.day }}</div>
                          <div class="text-green-400 text-center font-semibold">Rp {{ formatPrice(day.amount) }}</div>
                          <!-- Arrow -->
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- X-axis labels -->
                  <div class="absolute bottom-0 left-16 right-4 flex justify-between text-xs text-gray-400">
                    <span v-for="(day, index) in revenueChart" :key="index" class="flex-1 text-center truncate">
                      {{ day.day }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Chart summary -->
            <div class="mt-6 pt-4 border-t border-gray-700">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                  <span class="text-gray-400">Total: Rp {{ formatPrice(revenueChart.reduce((sum, day) => sum + day.amount, 0)) }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                  <span class="text-gray-400">Rata-rata: Rp {{ formatPrice(revenueChart.reduce((sum, day) => sum + day.amount, 0) / Math.max(revenueChart.length, 1)) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-semibold text-white">Pesanan Terbaru</h3>
              <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
            </div>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
              <div v-if="recentOrders.length === 0" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-400 text-sm">Belum ada pesanan terbaru</p>
              </div>
              
              <div 
                v-for="order in recentOrders" 
                :key="order.id"
                class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors border border-gray-600/30"
              >
                <div class="flex-1 min-w-0">
                  <p class="text-white font-medium text-sm truncate">{{ order.product }}</p>
                  <p class="text-gray-400 text-xs truncate">{{ order.customer }}</p>
                  <p class="text-xs text-gray-500">{{ order.time }}</p>
                </div>
                <div class="text-right ml-4">
                  <p class="text-green-400 font-semibold text-sm">Rp {{ formatPrice(order.amount) }}</p>
                  <span 
                    :class="[
                      'text-xs px-2 py-1 rounded-full font-medium',
                      order.status === 'completed' ? 'bg-green-500/20 text-green-400' :
                      order.status === 'processing' ? 'bg-yellow-500/20 text-yellow-400' :
                      order.status === 'pending' ? 'bg-blue-500/20 text-blue-400' :
                      'bg-red-500/20 text-red-400'
                    ]"
                  >
                    {{ getStatusText(order.status) }}
                  </span>
                </div>
              </div>
            </div>
            
            <router-link 
              to="/admin/orders" 
              class="block text-center text-green-400 hover:text-green-300 text-sm mt-6 py-2 px-4 bg-green-500/10 hover:bg-green-500/20 rounded-lg transition-colors border border-green-500/30"
            >
              Lihat Semua Pesanan →
            </router-link>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <router-link 
            to="/admin/products" 
            class="bg-gray-800 hover:bg-gray-700 rounded-lg p-6 transition-colors group"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-blue-500/20 p-3 rounded-lg group-hover:bg-blue-500/30 transition-colors">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div>
                <h4 class="text-white font-semibold">Kelola Produk</h4>
                <p class="text-gray-400 text-sm">Tambah & edit produk</p>
              </div>
            </div>
          </router-link>

          <router-link 
            to="/admin/orders" 
            class="bg-gray-800 hover:bg-gray-700 rounded-lg p-6 transition-colors group"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-green-500/20 p-3 rounded-lg group-hover:bg-green-500/30 transition-colors">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h4 class="text-white font-semibold">Kelola Pesanan</h4>
                <p class="text-gray-400 text-sm">Proses & lacak pesanan</p>
              </div>
            </div>
          </router-link>

          <router-link 
            to="/admin/settings" 
            class="bg-gray-800 hover:bg-gray-700 rounded-lg p-6 transition-colors group"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-purple-500/20 p-3 rounded-lg group-hover:bg-purple-500/30 transition-colors">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h4 class="text-white font-semibold">Pengaturan</h4>
                <p class="text-gray-400 text-sm">Konfigurasi sistem</p>
              </div>
            </div>
          </router-link>

          <button 
            @click="updatePrices"
            :disabled="isUpdatingPrices"
            class="bg-gray-800 hover:bg-gray-700 disabled:opacity-50 rounded-lg p-6 transition-colors group"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-yellow-500/20 p-3 rounded-lg group-hover:bg-yellow-500/30 transition-colors">
                <svg 
                  :class="['w-6 h-6 text-yellow-400', isUpdatingPrices ? 'animate-spin' : '']" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </div>
              <div>
                <h4 class="text-white font-semibold">Update Harga</h4>
                <p class="text-gray-400 text-sm">Sinkronisasi Diggie</p>
              </div>
            </div>
          </button>
        </div>

        <!-- System Status -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700/50 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Status Sistem</h3>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center space-x-4 p-4 bg-gray-700/50 rounded-lg border border-gray-600/30">
              <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
              <div>
                <p class="text-white font-semibold">Diggie API</p>
                <p class="text-green-400 text-sm font-medium">Terhubung</p>
              </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gray-700/50 rounded-lg border border-gray-600/30">
              <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
              <div>
                <p class="text-white font-semibold">Midtrans</p>
                <p class="text-green-400 text-sm font-medium">Aktif</p>
              </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gray-700/50 rounded-lg border border-gray-600/30">
              <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
              <div>
                <p class="text-white font-semibold">Cronjob</p>
                <p class="text-green-400 text-sm font-medium">Berjalan</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import AppLayout from '../../components/Layout/AppLayout.vue'

export default {
  name: 'AdminDashboard',
  components: { AppLayout },
  setup() {
    const isUpdatingPrices = ref(false)
    const loading = ref(true)
    
    const stats = ref({
      totalOrders: 0,
      totalRevenue: 0,
      activeProducts: 0,
      diggieProducts: 0,
      manualProducts: 0,
      pendingOrders: 0,
      revenueGrowth: 0
    })

    const revenueChart = ref([])
    const recentOrders = ref([])

    const fetchDashboardStats = async () => {
      loading.value = true
      try {
        const response = await fetch('/api/v1/admin/dashboard/stats', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        })

        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            stats.value = data.data.stats
            revenueChart.value = data.data.revenueChart
            recentOrders.value = data.data.recentOrders
          }
        } else {
          console.error('Failed to fetch dashboard stats')
        }
      } catch (error) {
        console.error('Error fetching dashboard stats:', error)
      } finally {
        loading.value = false
      }
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID').format(price)
    }

    const getStatusText = (status) => {
      const statusMap = {
        'completed': 'Selesai',
        'processing': 'Proses',
        'failed': 'Gagal',
        'pending': 'Pending',
        'cancelled': 'Dibatalkan'
      }
      return statusMap[status] || status
    }

    const updatePrices = async () => {
      if (isUpdatingPrices.value) return
      
      try {
        isUpdatingPrices.value = true
        
        const response = await fetch('/api/v1/admin/products/sync-digiflazz', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })

        if (response.ok) {
          alert('Sinkronisasi Digiflazz berhasil!')
          // Refresh dashboard stats after sync
          await fetchDashboardStats()
        } else {
          throw new Error('Failed to sync Digiflazz')
        }
      } catch (error) {
        console.error('Sync Digiflazz error:', error)
        alert('Gagal sinkronisasi Digiflazz. Silakan coba lagi.')
      } finally {
        isUpdatingPrices.value = false
      }
    }

    onMounted(() => {
      fetchDashboardStats()
    })

    return {
      stats,
      revenueChart,
      recentOrders,
      isUpdatingPrices,
      loading,
      formatPrice,
      getStatusText,
      updatePrices,
      fetchDashboardStats
    }
  }
}
</script>
