<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-900 py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">Pengaturan</h1>
            <p class="text-gray-400">Kelola pengaturan sistem dan profit margin</p>
          </div>
          <div class="flex space-x-4">
            <button 
              @click="updatePrices"
              :disabled="updatingPrices"
              class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors"
            >
              <svg v-if="updatingPrices" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>{{ updatingPrices ? 'Update Harga...' : 'Update Harga' }}</span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Profit Margin Settings -->
          <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold text-white mb-6">Pengaturan Profit Margin</h2>
            
            <form @submit.prevent="saveProfitSettings" class="space-y-6">
              <!-- Default Profit Margin -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Default Profit Margin (%)
                </label>
                <div class="relative">
                  <input 
                    v-model="profitSettings.default_profit_margin"
                    type="number" 
                    step="0.1"
                    min="0"
                    max="100"
                    required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 pr-8 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  >
                  <span class="absolute right-3 top-2 text-gray-400">%</span>
                </div>
                <p class="text-sm text-gray-400 mt-1">
                  Margin keuntungan default untuk semua produk Digiflazz
                </p>
              </div>

              <!-- Category Specific Margins -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-4">
                  Margin Per Kategori
                </label>
                <div class="space-y-4">
                  <div v-for="category in categories" :key="category.id" class="flex items-center justify-between bg-gray-700 rounded-lg p-4">
                    <div>
                      <div class="text-white font-medium">{{ category.name }}</div>
                      <div class="text-sm text-gray-400">{{ category.description }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <input 
                        v-model="categoryMargins[category.id]"
                        type="number" 
                        step="0.1"
                        min="0"
                        max="100"
                        class="w-20 bg-gray-600 border border-gray-500 rounded px-2 py-1 text-white text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                      <span class="text-gray-400 text-sm">%</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Auto Update Settings -->
              <div class="border-t border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-white mb-4">Pengaturan Auto Update</h3>
                
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-white font-medium">Auto Update Harga</div>
                      <div class="text-sm text-gray-400">Update harga otomatis dari Digiflazz</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input 
                        v-model="profitSettings.auto_update_enabled"
                        type="checkbox" 
                        class="sr-only peer"
                      >
                      <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Interval Update (menit)
                    </label>
                    <select 
                      v-model="profitSettings.update_interval"
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                      <option value="15">15 menit</option>
                      <option value="30">30 menit</option>
                      <option value="60">1 jam</option>
                      <option value="180">3 jam</option>
                      <option value="360">6 jam</option>
                      <option value="720">12 jam</option>
                      <option value="1440">24 jam</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit"
                  :disabled="savingProfit"
                  class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white rounded-lg transition-colors"
                >
                  {{ savingProfit ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </button>
              </div>
            </form>
          </div>

          <!-- System Settings -->
          <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold text-white mb-6">Pengaturan Sistem</h2>
            
            <form @submit.prevent="saveSystemSettings" class="space-y-6">
              <!-- Website Settings -->
              <div>
                <h3 class="text-lg font-semibold text-white mb-4">Pengaturan Website</h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Nama Website
                    </label>
                    <input 
                      v-model="systemSettings.site_name"
                      type="text" 
                      required
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Deskripsi Website
                    </label>
                    <textarea 
                      v-model="systemSettings.site_description"
                      rows="3"
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Email Kontak
                    </label>
                    <input 
                      v-model="systemSettings.contact_email"
                      type="email" 
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      WhatsApp
                    </label>
                    <input 
                      v-model="systemSettings.whatsapp_number"
                      type="text" 
                      placeholder="628123456789"
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                  </div>
                </div>
              </div>

              <!-- Payment Settings -->
              <div class="border-t border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-white mb-4">Pengaturan Pembayaran</h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Biaya Admin (Rp)
                    </label>
                    <input 
                      v-model="systemSettings.admin_fee"
                      type="number" 
                      min="0"
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Minimum Order (Rp)
                    </label>
                    <input 
                      v-model="systemSettings.minimum_order"
                      type="number" 
                      min="0"
                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                  </div>

                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-white font-medium">Maintenance Mode</div>
                      <div class="text-sm text-gray-400">Aktifkan mode maintenance</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input 
                        v-model="systemSettings.maintenance_mode"
                        type="checkbox" 
                        class="sr-only peer"
                      >
                      <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit"
                  :disabled="savingSystem"
                  class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white rounded-lg transition-colors"
                >
                  {{ savingSystem ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- API Status -->
        <div class="mt-8 bg-gray-800 rounded-lg p-6">
          <h2 class="text-xl font-bold text-white mb-6">Status API</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Digiflazz Status -->
            <div class="bg-gray-700 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Digiflazz API</h3>
                <span 
                  :class="apiStatus.digiflazz ? 'bg-green-600' : 'bg-red-600'"
                  class="px-2 py-1 text-xs font-semibold rounded-full text-white"
                >
                  {{ apiStatus.digiflazz ? 'Connected' : 'Disconnected' }}
                </span>
              </div>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-400">Last Check:</span>
                  <span class="text-white">{{ formatDate(apiStatus.digiflazz_last_check) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-400">Response Time:</span>
                  <span class="text-white">{{ apiStatus.digiflazz_response_time }}ms</span>
                </div>
              </div>
              <button 
                @click="testDigiflazzConnection"
                :disabled="testingDigiflazz"
                class="mt-4 w-full bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 text-white py-2 rounded-lg transition-colors"
              >
                {{ testingDigiflazz ? 'Testing...' : 'Test Connection' }}
              </button>
            </div>

            <!-- Midtrans Status -->
            <div class="bg-gray-700 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Midtrans API</h3>
                <span 
                  :class="apiStatus.midtrans ? 'bg-green-600' : 'bg-red-600'"
                  class="px-2 py-1 text-xs font-semibold rounded-full text-white"
                >
                  {{ apiStatus.midtrans ? 'Connected' : 'Disconnected' }}
                </span>
              </div>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-400">Last Check:</span>
                  <span class="text-white">{{ formatDate(apiStatus.midtrans_last_check) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-400">Response Time:</span>
                  <span class="text-white">{{ apiStatus.midtrans_response_time }}ms</span>
                </div>
              </div>
              <button 
                @click="testMidtransConnection"
                :disabled="testingMidtrans"
                class="mt-4 w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white py-2 rounded-lg transition-colors"
              >
                {{ testingMidtrans ? 'Testing...' : 'Test Connection' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-8 bg-gray-800 rounded-lg p-6">
          <h2 class="text-xl font-bold text-white mb-6">Aktivitas Terbaru</h2>
          
          <div class="space-y-4">
            <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between bg-gray-700 rounded-lg p-4">
              <div class="flex items-center space-x-4">
                <div 
                  :class="getActivityColor(activity.type)"
                  class="w-3 h-3 rounded-full"
                ></div>
                <div>
                  <div class="text-white font-medium">{{ activity.description }}</div>
                  <div class="text-sm text-gray-400">{{ formatDate(activity.created_at) }}</div>
                </div>
              </div>
              <span 
                :class="getActivityBadgeColor(activity.type)"
                class="px-2 py-1 text-xs font-semibold rounded-full text-white"
              >
                {{ activity.type }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import AppLayout from '../../components/Layout/AppLayout.vue';

export default {
  name: 'AdminSettings',
  components: { AppLayout },
  setup() {
    const categories = ref([]);
    const categoryMargins = ref({});
    const savingProfit = ref(false);
    const savingSystem = ref(false);
    const updatingPrices = ref(false);
    const testingDigiflazz = ref(false);
    const testingMidtrans = ref(false);
    const recentActivities = ref([]);

    const profitSettings = reactive({
      default_profit_margin: 10,
      auto_update_enabled: true,
      update_interval: 60
    });

    const systemSettings = reactive({
      site_name: 'Reynra Store',
      site_description: 'Game Top Up Terpercaya',
      contact_email: 'admin@reynrastore.com',
      whatsapp_number: '628123456789',
      admin_fee: 1000,
      minimum_order: 5000,
      maintenance_mode: false
    });

    const apiStatus = reactive({
      digiflazz: true,
      digiflazz_last_check: new Date(),
      digiflazz_response_time: 150,
      midtrans: true,
      midtrans_last_check: new Date(),
      midtrans_response_time: 200
    });

    const fetchSettings = async () => {
      try {
        const response = await fetch('/api/v1/admin/settings', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          Object.assign(profitSettings, data.profit_settings || {});
          Object.assign(systemSettings, data.system_settings || {});
          categoryMargins.value = data.category_margins || {};
        }
      } catch (error) {
        console.error('Error fetching settings:', error);
      }
    };

    const fetchCategories = async () => {
      try {
        const response = await fetch('/api/v1/categories');
        if (response.ok) {
          const data = await response.json();
          categories.value = data.data || [];
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    const fetchRecentActivities = async () => {
      try {
        const response = await fetch('/api/v1/admin/activities', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          recentActivities.value = data.data || [];
        }
      } catch (error) {
        console.error('Error fetching activities:', error);
      }
    };

    const saveProfitSettings = async () => {
      savingProfit.value = true;
      try {
        const response = await fetch('/api/v1/admin/settings/profit', {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            ...profitSettings,
            category_margins: categoryMargins.value
          })
        });

        if (response.ok) {
          alert('Pengaturan profit berhasil disimpan');
        } else {
          alert('Gagal menyimpan pengaturan profit');
        }
      } catch (error) {
        console.error('Error saving profit settings:', error);
        alert('Gagal menyimpan pengaturan profit');
      } finally {
        savingProfit.value = false;
      }
    };

    const saveSystemSettings = async () => {
      savingSystem.value = true;
      try {
        const response = await fetch('/api/v1/admin/settings/system', {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(systemSettings)
        });

        if (response.ok) {
          alert('Pengaturan sistem berhasil disimpan');
        } else {
          alert('Gagal menyimpan pengaturan sistem');
        }
      } catch (error) {
        console.error('Error saving system settings:', error);
        alert('Gagal menyimpan pengaturan sistem');
      } finally {
        savingSystem.value = false;
      }
    };

    const updatePrices = async () => {
      updatingPrices.value = true;
      try {
        const response = await fetch('/api/v1/admin/products/update-prices', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Harga berhasil diupdate');
        } else {
          alert('Gagal update harga');
        }
      } catch (error) {
        console.error('Error updating prices:', error);
        alert('Gagal update harga');
      } finally {
        updatingPrices.value = false;
      }
    };

    const testDigiflazzConnection = async () => {
      testingDigiflazz.value = true;
      try {
        const response = await fetch('/api/v1/admin/test/digiflazz', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          apiStatus.digiflazz = data.success;
          apiStatus.digiflazz_last_check = new Date();
          apiStatus.digiflazz_response_time = data.response_time;
          alert(data.success ? 'Koneksi Digiflazz berhasil' : 'Koneksi Digiflazz gagal');
        }
      } catch (error) {
        console.error('Error testing Digiflazz:', error);
        apiStatus.digiflazz = false;
        alert('Gagal test koneksi Digiflazz');
      } finally {
        testingDigiflazz.value = false;
      }
    };

    const testMidtransConnection = async () => {
      testingMidtrans.value = true;
      try {
        const response = await fetch('/api/v1/admin/test/midtrans', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          apiStatus.midtrans = data.success;
          apiStatus.midtrans_last_check = new Date();
          apiStatus.midtrans_response_time = data.response_time;
          alert(data.success ? 'Koneksi Midtrans berhasil' : 'Koneksi Midtrans gagal');
        }
      } catch (error) {
        console.error('Error testing Midtrans:', error);
        apiStatus.midtrans = false;
        alert('Gagal test koneksi Midtrans');
      } finally {
        testingMidtrans.value = false;
      }
    };

    const getActivityColor = (type) => {
      const colors = {
        'price_update': 'bg-blue-500',
        'order_created': 'bg-green-500',
        'order_completed': 'bg-green-600',
        'order_failed': 'bg-red-500',
        'system_update': 'bg-purple-500'
      };
      return colors[type] || 'bg-gray-500';
    };

    const getActivityBadgeColor = (type) => {
      const colors = {
        'price_update': 'bg-blue-600',
        'order_created': 'bg-green-600',
        'order_completed': 'bg-green-700',
        'order_failed': 'bg-red-600',
        'system_update': 'bg-purple-600'
      };
      return colors[type] || 'bg-gray-600';
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleString('id-ID');
    };

    onMounted(() => {
      fetchSettings();
      fetchCategories();
      fetchRecentActivities();
    });

    return {
      categories,
      categoryMargins,
      profitSettings,
      systemSettings,
      apiStatus,
      recentActivities,
      savingProfit,
      savingSystem,
      updatingPrices,
      testingDigiflazz,
      testingMidtrans,
      saveProfitSettings,
      saveSystemSettings,
      updatePrices,
      testDigiflazzConnection,
      testMidtransConnection,
      getActivityColor,
      getActivityBadgeColor,
      formatDate
    };
  }
};
</script>
