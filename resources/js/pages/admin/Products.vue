<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-900 py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">Kelola Produk</h1>
            <p class="text-gray-400">Kelola produk manual dan sinkronisasi dengan Digiflazz</p>
          </div>
          <div class="flex space-x-4">
            <button 
              @click="syncDigiflazz"
              :disabled="syncing"
              class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors"
            >
              <svg v-if="syncing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>{{ syncing ? 'Syncing...' : 'Sync Digiflazz' }}</span>
            </button>
            <button 
              @click="showAddModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span>Tambah Produk Manual</span>
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Total Produk</p>
                <p class="text-2xl font-bold text-white">{{ stats.total }}</p>
              </div>
              <div class="bg-blue-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Produk Aktif</p>
                <p class="text-2xl font-bold text-green-400">{{ stats.active }}</p>
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
                <p class="text-gray-400 text-sm">Digiflazz</p>
                <p class="text-2xl font-bold text-purple-400">{{ stats.digiflazz }}</p>
              </div>
              <div class="bg-purple-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Manual</p>
                <p class="text-2xl font-bold text-yellow-400">{{ stats.manual }}</p>
              </div>
              <div class="bg-yellow-600 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Cari Produk</label>
              <input 
                v-model="filters.search"
                type="text" 
                placeholder="Nama produk..."
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
              <select 
                v-model="filters.category"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
                <option value="">Semua Kategori</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
              <select 
                v-model="filters.status"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Tipe</label>
              <select 
                v-model="filters.type"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
                <option value="">Semua Tipe</option>
                <option value="diggie">Digiflazz</option>
                <option value="manual">Manual</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Products Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-700">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Produk</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kategori</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Harga</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tipe</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="relative group">
                        <img :src="getProductImage(product)" :alt="product.name" class="h-12 w-12 rounded-lg object-cover mr-4">
                        <!-- Upload overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center mr-4">
                          <label :for="`upload-${product.id}`" class="cursor-pointer">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                          </label>
                          <input 
                            :id="`upload-${product.id}`"
                            type="file" 
                            class="hidden" 
                            accept="image/*"
                            @change="uploadProductImage(product, $event)"
                          />
                        </div>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-white">{{ product.name }}</div>
                        <div class="text-sm text-gray-400">{{ product.sku }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ product.category?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">Rp {{ formatPrice(product.selling_price) }}</div>
                    <div class="text-sm text-gray-400">Base: Rp {{ formatPrice(product.base_price) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="product.type === 'diggie' ? 'bg-purple-600' : 'bg-yellow-600'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                    >
                      {{ product.type === 'diggie' ? 'Digiflazz' : 'Manual' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="product.is_active ? 'bg-green-600' : 'bg-red-600'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                    >
                      {{ product.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        @click="viewPriceLists(product)"
                        class="text-green-400 hover:text-green-300"
                        title="Lihat Price List"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                      </button>
                      <button 
                        v-if="product.type === 'manual'"
                        @click="openAddPriceListModal(product)"
                        class="text-purple-400 hover:text-purple-300"
                        title="Tambah Price List"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                      </button>
                      <button 
                        @click="editProduct(product)"
                        class="text-blue-400 hover:text-blue-300"
                        title="Edit"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button 
                        @click="toggleStatus(product)"
                        :class="product.is_active ? 'text-red-400 hover:text-red-300' : 'text-green-400 hover:text-green-300'"
                        :title="product.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                      >
                        <svg v-if="product.is_active" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                      <button 
                        @click="deleteProduct(product)"
                        class="text-red-400 hover:text-red-300"
                        title="Hapus"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
            Menampilkan {{ filteredProducts.length }} dari {{ products.length }} produk
          </div>
        </div>
      </div>
    </div>

    <!-- Add Product Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-white">Tambah Produk Manual</h3>
          <button @click="closeAddModal" class="text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Nama Produk</label>
              <input 
                v-model="productForm.name"
                type="text" 
                required
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
              <div class="space-y-2">
                <select 
                  v-model="productForm.category_id"
                  v-show="!showNewCategoryInput"
                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Pilih Kategori</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                
                <input 
                  v-show="showNewCategoryInput"
                  v-model="newCategoryName"
                  type="text" 
                  placeholder="Nama kategori baru..."
                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                
                <div class="flex space-x-2">
                  <button 
                    type="button"
                    @click="toggleNewCategoryInput"
                    class="text-sm text-blue-400 hover:text-blue-300"
                  >
                    {{ showNewCategoryInput ? 'Pilih dari kategori yang ada' : 'Buat kategori baru' }}
                  </button>
                  <button 
                    v-if="showNewCategoryInput && newCategoryName"
                    type="button"
                    @click="createNewCategory"
                    :disabled="creatingCategory"
                    class="text-sm text-green-400 hover:text-green-300 disabled:text-gray-500"
                  >
                    {{ creatingCategory ? 'Membuat...' : 'Simpan Kategori' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
            <textarea 
              v-model="productForm.description"
              rows="3"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Gambar Produk</label>
            <input 
              v-model="productForm.image"
              type="url" 
              placeholder="https://example.com/image.jpg"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
            <p class="text-sm text-gray-400 mt-1">Masukkan URL gambar produk</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Harga Dasar</label>
              <input 
                v-model="productForm.base_price"
                type="number" 
                required
                min="0"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Profit (%)</label>
              <input 
                v-model="productForm.profit_percentage"
                type="number" 
                min="0"
                max="100"
                step="0.1"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Stock</label>
              <input 
                v-model="productForm.stock"
                type="number" 
                min="0"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button 
              type="button"
              @click="closeAddModal"
              class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white rounded-lg transition-colors"
            >
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Price Lists Modal -->
    <div v-if="showPriceListModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg p-6 w-full max-w-4xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-xl font-bold text-white">Price List - {{ selectedProduct?.name }}</h3>
            <p class="text-gray-400 text-sm">{{ selectedProduct?.type === 'diggie' ? 'Digiflazz' : 'Manual' }} Product</p>
          </div>
          <button @click="closePriceListModal" class="text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="loadingPriceLists" class="flex justify-center py-8">
          <svg class="animate-spin h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <div v-else>
          <div v-if="priceLists.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-300">Tidak ada price list</h3>
            <p class="mt-1 text-sm text-gray-400">
              {{ selectedProduct?.type === 'manual' ? 'Mulai dengan menambahkan price list pertama.' : 'Price list dikelola otomatis oleh Digiflazz.' }}
            </p>
            <div v-if="selectedProduct?.type === 'manual'" class="mt-6">
              <button 
                @click="openAddPriceListModal(selectedProduct); closePriceListModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 mx-auto transition-colors"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Price List</span>
              </button>
            </div>
          </div>

          <div v-else class="space-y-4">
            <div class="flex justify-between items-center">
              <h4 class="text-lg font-medium text-white">Daftar Price List ({{ priceLists.length }})</h4>
              <button 
                v-if="selectedProduct?.type === 'manual'"
                @click="openAddPriceListModal(selectedProduct); closePriceListModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Price List</span>
              </button>
            </div>

            <div class="bg-gray-700 rounded-lg overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gray-600">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nama</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Harga Dasar</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Profit</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Harga Jual</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stock</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                      <th v-if="selectedProduct?.type === 'manual'" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-600">
                    <tr v-for="priceList in priceLists" :key="priceList.id" class="hover:bg-gray-600">
                      <td class="px-4 py-3">
                        <div class="text-sm font-medium text-white">{{ priceList.name }}</div>
                        <div v-if="priceList.description" class="text-sm text-gray-400">{{ priceList.description }}</div>
                      </td>
                      <td class="px-4 py-3 text-sm text-white">
                        Rp {{ formatPrice(priceList.base_price) }}
                      </td>
                      <td class="px-4 py-3 text-sm text-white">
                        {{ priceList.profit_percentage }}%
                      </td>
                      <td class="px-4 py-3 text-sm text-white">
                        Rp {{ formatPrice(priceList.selling_price) }}
                      </td>
                      <td class="px-4 py-3 text-sm text-white">
                        {{ priceList.unlimited_stock ? 'Unlimited' : priceList.stock }}
                      </td>
                      <td class="px-4 py-3">
                        <span 
                          :class="priceList.is_active ? 'bg-green-600' : 'bg-red-600'"
                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                        >
                          {{ priceList.is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                      </td>
                      <td v-if="selectedProduct?.type === 'manual'" class="px-4 py-3 text-sm font-medium">
                        <div class="flex space-x-2">
                          <button 
                            @click="editPriceList(priceList)"
                            class="text-blue-400 hover:text-blue-300"
                            title="Edit"
                          >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                          <button 
                            @click="deletePriceList(priceList)"
                            class="text-red-400 hover:text-red-300"
                            title="Hapus"
                          >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Price List Modal -->
    <div v-if="showAddPriceListModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-xl font-bold text-white">Tambah Price List</h3>
            <p class="text-gray-400 text-sm">{{ selectedProduct?.name }}</p>
          </div>
          <button @click="closeAddPriceListModal" class="text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="savePriceList" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Price List</label>
            <input 
              v-model="priceListForm.name"
              type="text" 
              required
              placeholder="Contoh: Paket 1GB, Pulsa 10K, dll"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi (Opsional)</label>
            <textarea 
              v-model="priceListForm.description"
              rows="2"
              placeholder="Deskripsi tambahan untuk price list ini"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Harga Dasar</label>
              <input 
                v-model="priceListForm.base_price"
                type="number" 
                required
                min="0"
                step="0.01"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Profit (%)</label>
              <input 
                v-model="priceListForm.profit_percentage"
                type="number" 
                min="0"
                max="100"
                step="0.1"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Stock</label>
              <input 
                v-model="priceListForm.stock"
                type="number" 
                min="0"
                :disabled="priceListForm.unlimited_stock"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent disabled:opacity-50"
              >
            </div>
            <div class="flex items-center pt-6">
              <input 
                v-model="priceListForm.unlimited_stock"
                type="checkbox"
                id="unlimited_stock"
                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700"
              >
              <label for="unlimited_stock" class="ml-2 text-sm text-gray-300">
                Stock Unlimited
              </label>
            </div>
          </div>

          <div class="flex items-center">
            <input 
              v-model="priceListForm.is_active"
              type="checkbox"
              id="is_active"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700"
            >
            <label for="is_active" class="ml-2 text-sm text-gray-300">
              Aktifkan price list ini
            </label>
          </div>

          <div v-if="priceListForm.base_price && priceListForm.profit_percentage" class="bg-gray-700 rounded-lg p-4">
            <div class="text-sm text-gray-300 mb-2">Preview Harga:</div>
            <div class="text-lg font-bold text-green-400">
              Rp {{ formatPrice(priceListForm.base_price * (1 + (priceListForm.profit_percentage / 100))) }}
            </div>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button 
              type="button"
              @click="closeAddPriceListModal"
              class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="savingPriceList"
              class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white rounded-lg transition-colors"
            >
              {{ savingPriceList ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../../components/Layout/AppLayout.vue';

export default {
  name: 'AdminProducts',
  components: { AppLayout },
  setup() {
    const products = ref([]);
    const categories = ref([]);
    const loading = ref(false);
    const syncing = ref(false);
    const saving = ref(false);
    const showAddModal = ref(false);
    const showNewCategoryInput = ref(false);
    const newCategoryName = ref('');
    const creatingCategory = ref(false);
    const showPriceListModal = ref(false);
    const showAddPriceListModal = ref(false);
    const selectedProduct = ref(null);
    const priceLists = ref([]);
    const loadingPriceLists = ref(false);
    const savingPriceList = ref(false);

    const filters = reactive({
      search: '',
      category: '',
      status: '',
      type: ''
    });

    const productForm = reactive({
      name: '',
      category_id: '',
      description: '',
      image: '',
      base_price: '',
      profit_percentage: 15,
      stock: 0,
      type: 'manual'
    });

    const priceListForm = reactive({
      name: '',
      description: '',
      base_price: '',
      profit_percentage: 15,
      selling_price: '',
      stock: 0,
      unlimited_stock: false,
      is_active: true
    });

    const stats = computed(() => {
      const total = products.value.length;
      const active = products.value.filter(p => p.is_active).length;
      const digiflazz = products.value.filter(p => p.type === 'diggie').length;
      const manual = products.value.filter(p => p.type === 'manual').length;
      
      return { total, active, digiflazz, manual };
    });

    const filteredProducts = computed(() => {
      let filtered = products.value;

      if (filters.search) {
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(filters.search.toLowerCase()) ||
          p.sku?.toLowerCase().includes(filters.search.toLowerCase())
        );
      }

      if (filters.category) {
        filtered = filtered.filter(p => p.category_id == filters.category);
      }

      if (filters.status) {
        filtered = filtered.filter(p => 
          filters.status === 'active' ? p.is_active : !p.is_active
        );
      }

      if (filters.type) {
        filtered = filtered.filter(p => p.type === filters.type);
      }

      return filtered;
    });

    const fetchProducts = async () => {
      loading.value = true;
      try {
        const response = await fetch('/api/v1/admin/products?per_page=1000', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          // Handle both paginated and non-paginated responses
          if (data.success) {
            products.value = Array.isArray(data.data) ? data.data : (data.data?.data || []);
          } else {
            products.value = [];
          }
        } else {
          products.value = [];
        }
      } catch (error) {
        console.error('Error fetching products:', error);
        products.value = [];
      } finally {
        loading.value = false;
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

    const syncDigiflazz = async () => {
      syncing.value = true;
      try {
        const response = await fetch('/api/v1/admin/products/sync-digiflazz', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Sinkronisasi Digiflazz berhasil');
          await fetchProducts();
        } else {
          alert('Gagal sinkronisasi Digiflazz');
        }
      } catch (error) {
        console.error('Error syncing Digiflazz:', error);
        alert('Gagal sinkronisasi Digiflazz');
      } finally {
        syncing.value = false;
      }
    };

    const saveProduct = async () => {
      saving.value = true;
      try {
        const response = await fetch('/api/v1/admin/products', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(productForm)
        });

        if (response.ok) {
          alert('Produk berhasil ditambahkan');
          closeAddModal();
          await fetchProducts();
        } else {
          alert('Gagal menambahkan produk');
        }
      } catch (error) {
        console.error('Error saving product:', error);
        alert('Gagal menambahkan produk');
      } finally {
        saving.value = false;
      }
    };

    const toggleStatus = async (product) => {
      try {
        const response = await fetch(`/api/v1/admin/products/${product.id}/toggle-status`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          product.is_active = !product.is_active;
          alert('Status produk berhasil diubah');
        } else {
          alert('Gagal mengubah status produk');
        }
      } catch (error) {
        console.error('Error toggling status:', error);
        alert('Gagal mengubah status produk');
      }
    };

    const deleteProduct = async (product) => {
      if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) return;

      try {
        const response = await fetch(`/api/v1/admin/products/${product.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Produk berhasil dihapus');
          await fetchProducts();
        } else {
          alert('Gagal menghapus produk');
        }
      } catch (error) {
        console.error('Error deleting product:', error);
        alert('Gagal menghapus produk');
      }
    };

    const editProduct = (product) => {
      selectedProduct.value = product;
      showAddModal.value = true;
      // Fill form with selected product data
      productForm.name = product.name || '';
      productForm.category_id = product.category_id || '';
      productForm.description = product.description || '';
      productForm.image = product.image || '';
      productForm.base_price = product.base_price || '';
      productForm.profit_percentage = product.profit_percentage || 15;
      productForm.stock = product.stock || 0;
      productForm.type = product.type || 'manual';
    };

    const viewPriceLists = async (product) => {
      selectedProduct.value = product;
      showPriceListModal.value = true;
      loadingPriceLists.value = true;
      
      try {
        const response = await fetch(`/api/v1/admin/products/${product.id}/price-lists`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          console.log('Price lists response:', data); // Debug log
          
          // Handle different response structures
          if (data.success && data.data && data.data.price_lists) {
            priceLists.value = data.data.price_lists;
          } else if (data.success && Array.isArray(data.data)) {
            priceLists.value = data.data;
          } else if (Array.isArray(data)) {
            priceLists.value = data;
          } else {
            priceLists.value = [];
          }
        } else {
          alert('Gagal memuat price list');
        }
      } catch (error) {
        console.error('Error fetching price lists:', error);
        alert('Gagal memuat price list');
      } finally {
        loadingPriceLists.value = false;
      }
    };

    const openAddPriceListModal = (product) => {
      selectedProduct.value = product;
      showAddPriceListModal.value = true;
      // Reset form
      Object.keys(priceListForm).forEach(key => {
        if (key === 'profit_percentage') {
          priceListForm[key] = 15;
        } else if (key === 'stock') {
          priceListForm[key] = 0;
        } else if (key === 'is_active') {
          priceListForm[key] = true;
        } else if (key === 'unlimited_stock') {
          priceListForm[key] = false;
        } else {
          priceListForm[key] = '';
        }
      });
    };

    const savePriceList = async () => {
      savingPriceList.value = true;
      try {
        const response = await fetch(`/api/v1/admin/products/${selectedProduct.value.id}/price-lists`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(priceListForm)
        });

        if (response.ok) {
          alert('Price list berhasil ditambahkan');
          closeAddPriceListModal();
          // Refresh price lists if modal is open
          if (showPriceListModal.value) {
            await viewPriceLists(selectedProduct.value);
          }
        } else {
          alert('Gagal menambahkan price list');
        }
      } catch (error) {
        console.error('Error saving price list:', error);
        alert('Gagal menambahkan price list');
      } finally {
        savingPriceList.value = false;
      }
    };

    const closePriceListModal = () => {
      showPriceListModal.value = false;
      selectedProduct.value = null;
      priceLists.value = [];
    };

    const closeAddPriceListModal = () => {
      showAddPriceListModal.value = false;
      selectedProduct.value = null;
      // Reset form
      Object.keys(priceListForm).forEach(key => {
        if (key === 'profit_percentage') {
          priceListForm[key] = 15;
        } else if (key === 'stock') {
          priceListForm[key] = 0;
        } else if (key === 'is_active') {
          priceListForm[key] = true;
        } else if (key === 'unlimited_stock') {
          priceListForm[key] = false;
        } else {
          priceListForm[key] = '';
        }
      });
    };

    const toggleNewCategoryInput = () => {
      showNewCategoryInput.value = !showNewCategoryInput.value;
      if (!showNewCategoryInput.value) {
        newCategoryName.value = '';
        productForm.category_id = '';
      }
    };

    const createNewCategory = async () => {
      if (!newCategoryName.value.trim()) return;
      
      creatingCategory.value = true;
      try {
        const response = await fetch('/api/v1/admin/categories', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name: newCategoryName.value.trim(),
            slug: newCategoryName.value.trim().toLowerCase().replace(/\s+/g, '-'),
            is_active: true
          })
        });

        if (response.ok) {
          const data = await response.json();
          if (data.success) {
            // Add new category to list
            categories.value.push(data.data);
            // Select the new category
            productForm.category_id = data.data.id;
            // Reset form
            showNewCategoryInput.value = false;
            newCategoryName.value = '';
            alert('Kategori baru berhasil dibuat');
          } else {
            alert('Gagal membuat kategori baru');
          }
        } else {
          alert('Gagal membuat kategori baru');
        }
      } catch (error) {
        console.error('Error creating category:', error);
        alert('Gagal membuat kategori baru');
      } finally {
        creatingCategory.value = false;
      }
    };

    const closeAddModal = () => {
      showAddModal.value = false;
      showNewCategoryInput.value = false;
      newCategoryName.value = '';
      Object.keys(productForm).forEach(key => {
        if (key === 'profit_percentage') {
          productForm[key] = 15;
        } else if (key === 'stock') {
          productForm[key] = 0;
        } else if (key === 'type') {
          productForm[key] = 'manual';
        } else {
          productForm[key] = '';
        }
      });
    };

    const editPriceList = (priceList) => {
      // TODO: Implement edit price list functionality
      alert('Fitur edit price list akan segera tersedia');
    };

    const deletePriceList = async (priceList) => {
      if (!confirm('Apakah Anda yakin ingin menghapus price list ini?')) return;

      try {
        const response = await fetch(`/api/v1/admin/products/${selectedProduct.value.id}/price-lists/${priceList.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          alert('Price list berhasil dihapus');
          // Refresh price lists
          await viewPriceLists(selectedProduct.value);
        } else {
          alert('Gagal menghapus price list');
        }
      } catch (error) {
        console.error('Error deleting price list:', error);
        alert('Gagal menghapus price list');
      }
    };

    const getProductImage = (product) => {
      if (product.image) {
        // If image starts with data:, it's base64
        if (product.image.startsWith('data:')) {
          return product.image;
        }
        // If it's a URL, return as is
        if (product.image.startsWith('http')) {
          return product.image;
        }
        // If it's a relative path, make it absolute
        return `/storage/${product.image}`;
      }
      // Default placeholder image
      return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNiAxNkMyMC40MTgzIDE2IDI0IDE5LjU4MTcgMjQgMjRDMjQgMjguNDE4MyAyMC40MTgzIDMyIDE2IDMyQzExLjU4MTcgMzIgOCAyOC40MTgzIDggMjRDOCAxOS41ODE3IDExLjU4MTcgMTYgMTYgMTZaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0yNiAxNkMzMC40MTgzIDE2IDM0IDE5LjU4MTcgMzQgMjRDMzQgMjguNDE4MyAzMC40MTgzIDMyIDI2IDMyQzIxLjU4MTcgMzIgMTggMjguNDE4MyAxOCAyNEMxOCAxOS41ODE3IDIxLjU4MTcgMTYgMjYgMTZaIiBmaWxsPSIjOUNBM0FGIi8+Cjwvc3ZnPgo=';
    };

    const uploadProductImage = async (product, event) => {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type
      if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar (PNG, JPG, JPEG, GIF)');
        return;
      }

      // Validate file size (2MB max)
      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB');
        return;
      }

      try {
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('image', file);
        
        // Update product image via dedicated API endpoint
        const response = await fetch(`/api/v1/admin/products/${product.id}/image`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
            // Don't set Content-Type, let browser set it with boundary for FormData
          },
          body: formData
        });

        if (response.ok) {
          const data = await response.json();
          // Update local product data with new image path
          const productIndex = products.value.findIndex(p => p.id === product.id);
          if (productIndex !== -1) {
            products.value[productIndex].image = data.data.image;
          }
          alert('Gambar produk berhasil diupdate!');
        } else {
          const errorData = await response.json();
          alert('Gagal mengupdate gambar produk: ' + (errorData.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error uploading image:', error);
        alert('Gagal mengupload gambar');
      }
    };

    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID').format(price);
    };

    onMounted(() => {
      fetchProducts();
      fetchCategories();
    });

    return {
      products,
      categories,
      loading,
      syncing,
      saving,
      showAddModal,
      showNewCategoryInput,
      newCategoryName,
      creatingCategory,
      showPriceListModal,
      showAddPriceListModal,
      selectedProduct,
      priceLists,
      loadingPriceLists,
      savingPriceList,
      filters,
      productForm,
      priceListForm,
      stats,
      filteredProducts,
      syncDigiflazz,
      saveProduct,
      toggleStatus,
      deleteProduct,
      editProduct,
      viewPriceLists,
      openAddPriceListModal,
      savePriceList,
      closePriceListModal,
      closeAddPriceListModal,
      toggleNewCategoryInput,
      createNewCategory,
      closeAddModal,
      editPriceList,
      deletePriceList,
      getProductImage,
      uploadProductImage,
      formatPrice
    };
  }
};
</script>
