
<template>
  <AppLayout>
    <!-- Loading State -->
    <div v-if="loading" class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="container mx-auto px-4 py-12">
        <div class="animate-pulse">
          <!-- Hero Skeleton -->
          <div class="loading-skeleton h-96 mb-8"></div>
          
          <!-- Content Skeleton -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
              <div class="loading-skeleton h-32"></div>
              <div class="loading-skeleton h-48"></div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="loading-skeleton h-32"></div>
                <div class="loading-skeleton h-32"></div>
                <div class="loading-skeleton h-32"></div>
                <div class="loading-skeleton h-32"></div>
              </div>
            </div>
            <div class="loading-skeleton h-96"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="container mx-auto px-4 py-12">
        <div class="error-state">
          <div class="error-icon">⚠️</div>
          <h1 class="error-title">Produk Tidak Ditemukan</h1>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchProduct" class="retry-btn">
            🔄 Coba Lagi
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
      <div class="absolute top-20 left-20 w-64 h-64 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>

      <!-- Hero Section -->
      <section class="product-hero">
        <!-- Background Image -->
        <div 
          v-if="product.image" 
          class="product-hero-bg"
          :style="`background-image: url('${getImageUrl(product.image)}')`"
        ></div>
        
        <div class="product-hero-content">
          <div class="container mx-auto px-4 py-16 lg:py-24">
            <div class="max-w-4xl mx-auto text-center space-y-8">
              <!-- Product Title -->
              <h1 class="text-4xl lg:text-6xl font-bold text-white leading-tight holographic animate-fade-in">
                🎮 {{ product.name }} 🎮
              </h1>
              
              <!-- Product Badges -->
              <div class="flex flex-wrap justify-center gap-4 animate-slide-in-left">
                <span 
                  v-if="product.category" 
                  class="product-badge product-badge-manual"
                >
                  📂 {{ product.category.name }}
                </span>
                <span 
                  :class="product.type === 'diggie' ? 'product-badge-auto' : 'product-badge-manual'"
                  class="product-badge"
                >
                  {{ product.type === 'diggie' ? '⚡ OTOMATIS' : '🔧 MANUAL' }}
                </span>
                <span 
                  v-if="product.is_featured" 
                  class="product-badge product-badge-featured"
                >
                  ⭐ FEATURED
                </span>
              </div>

              <!-- Product Rating -->
              <div class="flex justify-center animate-slide-in-right">
                <div class="product-rating">
                  <div class="product-rating-stars">
                    <span class="product-rating-star">★</span>
                    <span class="product-rating-star">★</span>
                    <span class="product-rating-star">★</span>
                    <span class="product-rating-star">★</span>
                    <span class="product-rating-star">★</span>
                  </div>
                  <span class="text-white font-bold text-xl">4.99</span>
                  <span class="text-gray-400">| {{ priceLists.length }} Paket</span>
                </div>
              </div>

              <!-- Product Description -->
              <p v-if="product.description" class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed animate-fade-in">
                {{ product.description }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <div class="container mx-auto px-4 py-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Product Info & Packages -->
          <div class="lg:col-span-2 space-y-8">
            
            <!-- Product Information -->
            <section class="product-info-section p-8 animate-slide-in-left">
              <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <span class="text-green-400">📋</span>
                Informasi Produk
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Product Specs -->
                <div>
                  <h3 class="text-lg font-semibold text-green-400 mb-4">Spesifikasi</h3>
                  <ul class="product-specs-list">
                    <li class="product-specs-item">
                      <span class="product-specs-icon">🎮</span>
                      <span>Game: {{ product.name }}</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">📂</span>
                      <span>Kategori: {{ product.category?.name || 'Umum' }}</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">⚡</span>
                      <span>Tipe: {{ product.type === 'diggie' ? 'Otomatis' : 'Manual' }}</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">📦</span>
                      <span>Paket Tersedia: {{ priceLists.length }}</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">✅</span>
                      <span>Status: {{ product.is_active ? 'Tersedia' : 'Tidak Tersedia' }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Product Features -->
                <div>
                  <h3 class="text-lg font-semibold text-blue-400 mb-4">Keunggulan</h3>
                  <ul class="product-specs-list">
                    <li class="product-specs-item">
                      <span class="product-specs-icon">⚡</span>
                      <span>{{ product.type === 'diggie' ? 'Proses Otomatis 1-3 Menit' : 'Proses Manual 5-30 Menit' }}</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">🛡️</span>
                      <span>100% Aman & Terpercaya</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">💰</span>
                      <span>Harga Terjangkau</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">🎯</span>
                      <span>Support 24/7</span>
                    </li>
                    <li class="product-specs-item">
                      <span class="product-specs-icon">🔄</span>
                      <span>Garansi Uang Kembali</span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Instructions for Manual Products -->
              <div v-if="product.type === 'manual' && product.instructions" class="product-instructions">
                <h3 class="product-instructions-title">
                  <span>📝</span>
                  Petunjuk Pemesanan
                </h3>
                <div class="product-instructions-content" v-html="product.instructions"></div>
              </div>
            </section>

            <!-- Price Packages -->
            <section class="animate-slide-in-left" style="animation-delay: 0.2s">
              <h2 class="text-3xl font-bold text-white mb-8 text-center holographic">
                🎯 Pilih Paket Top Up 🎯
              </h2>
              
              <div v-if="priceLists.length === 0" class="text-center py-12">
                <div class="text-gray-400 mb-4">
                  <span class="text-4xl">📦</span>
                </div>
                <h3 class="text-white font-bold text-xl mb-2">Belum Ada Paket Tersedia</h3>
                <p class="text-gray-400">Paket untuk produk ini sedang dalam persiapan</p>
              </div>

              <!-- Price Lists Grouped by Type -->
              <div v-else class="space-y-8">
                <div 
                  v-for="(type, typeIndex) in priceListTypes" 
                  :key="type"
                  class="price-type-section animate-slide-in-left"
                  :style="`animation-delay: ${typeIndex * 0.1}s`"
                >
                  <!-- Type Header -->
                  <div class="price-type-header mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                      <span class="text-blue-400">📋</span>
                      {{ type }}
                    </h3>
                    <div class="w-full h-px bg-gradient-to-r from-blue-500/50 to-transparent mt-2"></div>
                  </div>

                  <!-- Price Cards Grid -->
                  <div class="price-packages-grid">
                    <div
                      v-for="(priceList, index) in groupedPriceLists[type]"
                      :key="priceList.id"
                      :class="{ 'selected': selectedPackage?.id === priceList.id }"
                      class="price-package-card animate-slide-in-left"
                      :style="`animation-delay: ${(typeIndex * 0.1) + (index * 0.05)}s`"
                      @click="selectPackage(priceList)"
                    >
                      <div class="price-package-header">
                        <h4 class="price-package-name">{{ priceList.name }}</h4>
                        <p v-if="priceList.description" class="price-package-desc">{{ priceList.description }}</p>
                      </div>
                      
                      <div class="price-package-price">
                        <span class="price-package-amount">{{ formatPrice(priceList.selling_price) }}</span>
                        <span v-if="priceList.base_price !== priceList.selling_price" class="price-package-base">
                          {{ formatPrice(priceList.base_price) }}
                        </span>
                      </div>
                      
                      <button 
                        class="price-package-buy-btn"
                        :disabled="!priceList.is_active || !priceList.seller_status"
                        @click.stop="selectAndScrollToForm(priceList)"
                      >
                        <span>
                          {{ !priceList.is_active || !priceList.seller_status ? 'TIDAK TERSEDIA' : 'PILIH PAKET' }}
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- Right Column - Order Form -->
          <div class="lg:col-span-1">
            <div class="order-summary animate-slide-in-right">
              <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <span class="text-orange-400">🛒</span>
                Form Pemesanan
              </h2>

              <!-- Order Form -->
              <form @submit.prevent="submitOrder" class="space-y-6">
                <!-- Selected Package Display -->
                <div v-if="selectedPackage" class="order-form-section p-4 mb-6">
                  <h3 class="text-lg font-semibold text-green-400 mb-3">Paket Dipilih</h3>
                  <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-white font-bold">{{ selectedPackage.name }}</h4>
                    <p class="text-gray-400 text-sm mb-2">{{ selectedPackage.description }}</p>
                    <p class="text-green-400 font-bold text-xl">{{ formatPrice(selectedPackage.selling_price) }}</p>
                  </div>
                </div>

                <!-- Customer Information -->
                <div class="order-form-group">
                  <label class="order-form-label">👤 Nama Lengkap</label>
                  <input
                    v-model="orderForm.customer_name"
                    type="text"
                    class="order-form-input"
                    placeholder="Masukkan nama lengkap"
                    required
                  />
                </div>

                <div class="order-form-group">
                  <label class="order-form-label">📧 Email</label>
                  <input
                    v-model="orderForm.customer_email"
                    type="email"
                    class="order-form-input"
                    placeholder="contoh@email.com"
                    required
                  />
                </div>

                <div class="order-form-group">
                  <label class="order-form-label">📱 No. WhatsApp</label>
                  <input
                    v-model="orderForm.customer_phone"
                    type="tel"
                    class="order-form-input"
                    placeholder="08xxxxxxxxxx"
                    required
                  />
                </div>

                <!-- Game Account Details -->
                <div class="order-form-group">
                  <label class="order-form-label">
                    {{ getAccountFieldLabel() }}
                  </label>
                  <input
                    v-model="orderForm.game_id"
                    type="text"
                    class="order-form-input"
                    :placeholder="getAccountFieldPlaceholder()"
                    required
                  />
                </div>

                <!-- Server field only for Mobile Legends -->
                <div v-if="isGameProduct()" class="order-form-group">
                  <label class="order-form-label">🌐 Server (Opsional)</label>
                  <input
                    v-model="orderForm.server"
                    type="text"
                    class="order-form-input"
                    placeholder="Contoh: Asia, Indonesia, dll"
                  />
                </div>

                <!-- Quantity -->
                <div class="order-form-group">
                  <label class="order-form-label">📦 Jumlah</label>
                  <select v-model="orderForm.quantity" class="order-form-select">
                    <option value="1">1x</option>
                    <option value="2">2x</option>
                    <option value="3">3x</option>
                    <option value="5">5x</option>
                    <option value="10">10x</option>
                  </select>
                </div>

                <!-- Payment Method -->
                <div class="order-form-group">
                  <label class="order-form-label">💳 Metode Pembayaran</label>
                  <div class="space-y-3">
                    <!-- QRIS Option -->
                    <div 
                      class="payment-method-option"
                      :class="{ 'selected': orderForm.payment_method === 'qris' }"
                      @click="orderForm.payment_method = 'qris'"
                    >
                      <div class="flex items-center space-x-3">
                        <div class="payment-method-icon qris">
                          <span class="text-2xl">📱</span>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-white font-bold">QRIS</h4>
                          <p class="text-gray-400 text-sm">Scan QR Code untuk pembayaran instan</p>
                          <p class="text-orange-400 text-xs font-semibold">Fee: {{ paymentFees.qris.label }}</p>
                        </div>
                        <div class="payment-method-check">
                          <div v-if="orderForm.payment_method === 'qris'" class="text-green-400 text-xl">✓</div>
                          <div v-else class="w-5 h-5 border-2 border-gray-500 rounded-full"></div>
                        </div>
                      </div>
                    </div>

                    <!-- Bank Transfer Option -->
                    <div 
                      class="payment-method-option"
                      :class="{ 'selected': orderForm.payment_method === 'bank_transfer' }"
                      @click="orderForm.payment_method = 'bank_transfer'"
                    >
                      <div class="flex items-center space-x-3">
                        <div class="payment-method-icon bank">
                          <span class="text-2xl">🏦</span>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-white font-bold">Transfer Bank</h4>
                          <p class="text-gray-400 text-sm">Transfer manual ke rekening bank</p>
                          <p class="text-orange-400 text-xs font-semibold">Fee: {{ paymentFees.bank_transfer.label }}</p>
                        </div>
                        <div class="payment-method-check">
                          <div v-if="orderForm.payment_method === 'bank_transfer'" class="text-green-400 text-xl">✓</div>
                          <div v-else class="w-5 h-5 border-2 border-gray-500 rounded-full"></div>
                        </div>
                      </div>
                    </div>

                    <!-- E-Wallet Option -->
                    <div 
                      class="payment-method-option"
                      :class="{ 'selected': orderForm.payment_method === 'ewallet' }"
                      @click="orderForm.payment_method = 'ewallet'"
                    >
                      <div class="flex items-center space-x-3">
                        <div class="payment-method-icon ewallet">
                          <span class="text-2xl">💰</span>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-white font-bold">E-Wallet</h4>
                          <p class="text-gray-400 text-sm">GoPay, OVO, DANA, ShopeePay</p>
                          <p class="text-orange-400 text-xs font-semibold">Fee: {{ paymentFees.ewallet.label }}</p>
                        </div>
                        <div class="payment-method-check">
                          <div v-if="orderForm.payment_method === 'ewallet'" class="text-green-400 text-xl">✓</div>
                          <div v-else class="w-5 h-5 border-2 border-gray-500 rounded-full"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-800/50 rounded-lg p-4 space-y-3">
                  <h3 class="text-lg font-semibold text-white">Ringkasan Pesanan</h3>
                  
                  <div class="order-summary-item">
                    <span class="text-gray-400">Produk:</span>
                    <span class="text-white">{{ product.name }}</span>
                  </div>
                  
                  <div v-if="selectedPackage" class="order-summary-item">
                    <span class="text-gray-400">Paket:</span>
                    <span class="text-white">{{ selectedPackage.name }}</span>
                  </div>
                  
                  <div class="order-summary-item">
                    <span class="text-gray-400">Jumlah:</span>
                    <span class="text-white">{{ orderForm.quantity }}x</span>
                  </div>
                  
                  <div v-if="selectedPackage" class="order-summary-item">
                    <span class="text-gray-400">Subtotal:</span>
                    <span class="text-green-400">{{ formatPrice(subtotalAmount) }}</span>
                  </div>
                  
                  <div v-if="selectedPackage && paymentFee > 0" class="order-summary-item">
                    <span class="text-gray-400">Biaya Payment:</span>
                    <span class="text-orange-400">{{ formatPrice(paymentFee) }}</span>
                  </div>
                  
                  <div v-if="selectedPackage" class="order-summary-item border-t border-gray-600 pt-2">
                    <span class="text-white font-bold">Total Bayar:</span>
                    <span class="text-green-400 font-bold text-xl">{{ formatPrice(totalAmount) }}</span>
                  </div>
                </div>

                <!-- Submit Button -->
                <button
                  type="button"
                  :disabled="!selectedPackage || submitting"
                  class="order-submit-btn"
                  @click="showConfirmationModal"
                >
                  <span v-if="submitting">
                    ⏳ Memproses...
                  </span>
                  <span v-else>
                    🚀 Buat Pesanan Sekarang
                  </span>
                </button>
              </form>

              <!-- Trust Indicators -->
              <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-gray-700">
                <div class="text-center">
                  <div class="text-2xl mb-2">⚡</div>
                  <div class="text-green-400 font-semibold text-sm">Proses Cepat</div>
                  <div class="text-gray-400 text-xs">1-30 Menit</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl mb-2">🛡️</div>
                  <div class="text-green-400 font-semibold text-sm">100% Aman</div>
                  <div class="text-gray-400 text-xs">Terpercaya</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl mb-2">💰</div>
                  <div class="text-green-400 font-semibold text-sm">Harga Terbaik</div>
                  <div class="text-gray-400 text-xs">Termurah</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl mb-2">🎯</div>
                  <div class="text-green-400 font-semibold text-sm">24/7 Support</div>
                  <div class="text-gray-400 text-xs">Selalu Siap</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
      <div class="confirmation-modal animate-scale-in">
        <div class="confirmation-modal-header">
          <h3 class="text-2xl font-bold text-white mb-2">🛒 Konfirmasi Pesanan</h3>
          <p class="text-gray-400 text-sm">Pastikan data pesanan Anda sudah benar</p>
        </div>

        <div class="confirmation-modal-content">
          <!-- Order Summary -->
          <div class="confirmation-section">
            <h4 class="confirmation-section-title">📦 Detail Pesanan</h4>
            <div class="confirmation-item">
              <span class="confirmation-label">Produk:</span>
              <span class="confirmation-value">{{ product.name }}</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Paket:</span>
              <span class="confirmation-value">{{ selectedPackage?.name }}</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Jumlah:</span>
              <span class="confirmation-value">{{ orderForm.quantity }}x</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Total:</span>
              <span class="confirmation-value text-green-400 font-bold">{{ formatPrice(totalAmount) }}</span>
            </div>
          </div>

          <!-- Customer Info -->
          <div class="confirmation-section">
            <h4 class="confirmation-section-title">👤 Data Customer</h4>
            <div class="confirmation-item">
              <span class="confirmation-label">Nama:</span>
              <span class="confirmation-value">{{ orderForm.customer_name }}</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Email:</span>
              <span class="confirmation-value">{{ orderForm.customer_email }}</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">WhatsApp:</span>
              <span class="confirmation-value">{{ orderForm.customer_phone }}</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">{{ getAccountFieldLabel().replace('🎮 ', '').replace('📱 ', '').replace('📧 ', '').replace('⚡ ', '').replace('💧 ', '').replace('🏥 ', '') }}:</span>
              <span class="confirmation-value">{{ orderForm.game_id }}</span>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="confirmation-section">
            <h4 class="confirmation-section-title">💳 Metode Pembayaran</h4>
            <div class="confirmation-payment-method">
              <div class="flex items-center gap-3">
                <span class="text-2xl">
                  {{ orderForm.payment_method === 'qris' ? '📱' : orderForm.payment_method === 'bank_transfer' ? '🏦' : '💰' }}
                </span>
                <div>
                  <div class="text-white font-bold">
                    {{ orderForm.payment_method === 'qris' ? 'QRIS' : orderForm.payment_method === 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}
                  </div>
                  <div class="text-gray-400 text-sm">
                    {{ orderForm.payment_method === 'qris' ? 'Scan QR Code untuk pembayaran instan' : 
                       orderForm.payment_method === 'bank_transfer' ? 'Transfer manual ke rekening bank' : 
                       'GoPay, OVO, DANA, ShopeePay' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Important Notice -->
          <div class="confirmation-notice">
            <div class="flex items-start gap-3">
              <span class="text-yellow-400 text-xl">⚠️</span>
              <div>
                <h5 class="text-yellow-400 font-bold mb-1">Penting!</h5>
                <ul class="text-gray-300 text-sm space-y-1">
                  <li>• Pastikan data yang Anda masukkan sudah benar</li>
                  <li>• Setelah klik "Lanjut", Anda akan diarahkan ke halaman pembayaran</li>
                  <li>• Proses pembayaran akan berlangsung otomatis</li>
                  <li v-if="orderForm.payment_method === 'qris'">• QR Code akan expired dalam 10 menit</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="confirmation-modal-actions">
          <button 
            @click="closeModal" 
            class="confirmation-btn-cancel"
            :disabled="submitting"
          >
            ❌ Batal
          </button>
          <button 
            @click="confirmOrder" 
            class="confirmation-btn-confirm"
            :disabled="submitting"
          >
            <span v-if="submitting">⏳ Memproses...</span>
            <span v-else>✅ Lanjut Bayar</span>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppLayout from '../components/Layout/AppLayout.vue';
import axios from 'axios';

export default {
  name: 'ProductDetail',
  components: {
    AppLayout,
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    
    const loading = ref(true);
    const error = ref(null);
    const product = ref({});
    const priceLists = ref([]);
    const selectedPackage = ref(null);
    const submitting = ref(false);
    const showModal = ref(false);
    
    const orderForm = ref({
      customer_name: '',
      customer_email: '',
      customer_phone: '',
      game_id: '',
      server: '',
      quantity: 1,
      payment_method: 'qris'
    });

    // Payment fees configuration
    const paymentFees = {
      qris: { type: 'percentage', value: 0.7, label: '0.7%' },
      bank_transfer: { type: 'fixed', value: 0, label: 'TANPA BIAYA ADMIN!' },
      ewallet: { type: 'fixed', value: 0, label: 'TANPA BIAYA ADMIN!' }
    };

    // Computed
    const subtotalAmount = computed(() => {
      if (!selectedPackage.value) return 0;
      return selectedPackage.value.selling_price * orderForm.value.quantity;
    });

    const paymentFee = computed(() => {
      if (!selectedPackage.value || !orderForm.value.payment_method) return 0;
      
      const fee = paymentFees[orderForm.value.payment_method];
      if (!fee) return 0;
      
      if (fee.type === 'percentage') {
        return Math.round(subtotalAmount.value * (fee.value / 100));
      } else {
        return fee.value;
      }
    });

    const totalAmount = computed(() => {
      return subtotalAmount.value + paymentFee.value;
    });

    // Group price lists by type
    const groupedPriceLists = computed(() => {
      const groups = {};
      
      priceLists.value.forEach(priceList => {
        const type = priceList.type || 'Umum';
        if (!groups[type]) {
          groups[type] = [];
        }
        groups[type].push(priceList);
      });
      
      // Sort each group by selling_price
      Object.keys(groups).forEach(type => {
        groups[type].sort((a, b) => a.selling_price - b.selling_price);
      });
      
      return groups;
    });

    const priceListTypes = computed(() => {
      const types = Object.keys(groupedPriceLists.value);
      
      // Custom sorting: Membership first, then Umum, then alphabetical
      return types.sort((a, b) => {
        // Membership always first
        if (a.toLowerCase() === 'membership') return -1;
        if (b.toLowerCase() === 'membership') return 1;
        
        // Umum always second
        if (a.toLowerCase() === 'umum') return -1;
        if (b.toLowerCase() === 'umum') return 1;
        
        // Rest alphabetically
        return a.localeCompare(b);
      });
    });

    // Methods
    const fetchProduct = async () => {
      try {
        loading.value = true;
        error.value = null;
        
        const slug = route.params.slug;
        const response = await axios.get(`/api/v1/products/slug/${slug}`);
        
        if (response.data.success) {
          product.value = response.data.data;
          await fetchPriceLists();
        } else {
          throw new Error(response.data.message || 'Produk tidak ditemukan');
        }
      } catch (err) {
        console.error('Error fetching product:', err);
        error.value = err.response?.data?.message || err.message || 'Gagal memuat produk';
      } finally {
        loading.value = false;
      }
    };

    const fetchPriceLists = async () => {
      try {
        const response = await axios.get(`/api/v1/products/${product.value.id}/price-lists`);
        
        if (response.data.success) {
          // API returns { product, price_lists } structure
          priceLists.value = response.data.data.price_lists.filter(item => item.is_active);
        }
      } catch (err) {
        console.error('Error fetching price lists:', err);
        priceLists.value = [];
      }
    };

    const selectPackage = (priceList) => {
      if (priceList.is_active && priceList.seller_status) {
        selectedPackage.value = priceList;
      }
    };

    const selectAndScrollToForm = (priceList) => {
      selectPackage(priceList);
      // Scroll to form on mobile
      if (window.innerWidth < 1024) {
        setTimeout(() => {
          const formElement = document.querySelector('.order-summary');
          if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 100);
      }
    };

    const submitOrder = async () => {
      if (!selectedPackage.value) {
        alert('Silakan pilih paket terlebih dahulu');
        return;
      }

      try {
        submitting.value = true;
        
        const orderData = {
          customer_name: orderForm.value.customer_name,
          customer_email: orderForm.value.customer_email,
          customer_phone: orderForm.value.customer_phone,
          items: [
            {
              product_id: product.value.id,
              quantity: parseInt(orderForm.value.quantity),
              form_data: {
                price_list_id: selectedPackage.value.id,
                game_id: orderForm.value.game_id,
                server: orderForm.value.server,
                payment_method: orderForm.value.payment_method,
                total_amount: totalAmount.value,
                subtotal_amount: subtotalAmount.value,
                payment_fee: paymentFee.value
              }
            }
          ]
        };

        const response = await axios.post('/api/v1/orders', orderData);
        
        if (response.data.success) {
          const order = response.data.data;
          // Redirect to payment page
          window.open(`/payment/${order.order_number}`, '_blank');
          // Also redirect current tab to order tracking
          router.push(`/orders/${order.order_number}/track`);
        } else {
          throw new Error(response.data.message || 'Gagal membuat pesanan');
        }
      } catch (err) {
        console.error('Error creating order:', err);
        alert(err.response?.data?.message || err.message || 'Gagal membuat pesanan');
      } finally {
        submitting.value = false;
      }
    };

    const formatPrice = (price) => {
      if (!price || isNaN(price)) return 'Rp 0';
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(price);
    };

    const getImageUrl = (imagePath) => {
      if (!imagePath) return null;
      if (imagePath.startsWith('http')) return imagePath;
      return `/storage/${imagePath}`;
    };

    // Helper methods for dynamic form fields
    const isGameProduct = () => {
      if (!product.value.name) return false;
      const gameName = product.value.name.toLowerCase();
      return gameName.includes('mobile legends') || 
             gameName.includes('ml') || 
             gameName.includes('pubg') || 
             gameName.includes('free fire') ||
             gameName.includes('valorant') ||
             gameName.includes('genshin');
    };

    const getAccountFieldLabel = () => {
      if (!product.value.name) return '🎮 User ID / Game ID';
      
      const productName = product.value.name.toLowerCase();
      
      if (productName.includes('dana')) {
        return '📱 Nomor HP DANA';
      } else if (productName.includes('ovo')) {
        return '📱 Nomor HP OVO';
      } else if (productName.includes('gopay')) {
        return '📱 Nomor HP GoPay';
      } else if (productName.includes('shopeepay')) {
        return '📱 Nomor HP ShopeePay';
      } else if (productName.includes('linkaja')) {
        return '📱 Nomor HP LinkAja';
      } else if (productName.includes('pulsa') || productName.includes('kuota')) {
        return '📱 Nomor HP';
      } else if (productName.includes('pln') || productName.includes('listrik')) {
        return '⚡ Nomor Meter PLN';
      } else if (productName.includes('pdam') || productName.includes('air')) {
        return '💧 Nomor Pelanggan PDAM';
      } else if (productName.includes('bpjs')) {
        return '🏥 Nomor Kartu BPJS';
      } else if (productName.includes('netflix')) {
        return '📧 Email Netflix';
      } else if (productName.includes('spotify')) {
        return '📧 Email Spotify';
      } else if (productName.includes('youtube')) {
        return '📧 Email YouTube Premium';
      } else {
        return '🎮 User ID / Game ID';
      }
    };

    const getAccountFieldPlaceholder = () => {
      if (!product.value.name) return 'Masukkan User ID game';
      
      const productName = product.value.name.toLowerCase();
      
      if (productName.includes('dana')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('ovo')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('gopay')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('shopeepay')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('linkaja')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('pulsa') || productName.includes('kuota')) {
        return 'Contoh: 08123456789';
      } else if (productName.includes('pln') || productName.includes('listrik')) {
        return 'Contoh: 123456789012';
      } else if (productName.includes('pdam') || productName.includes('air')) {
        return 'Contoh: 123456789';
      } else if (productName.includes('bpjs')) {
        return 'Contoh: 0001234567890';
      } else if (productName.includes('netflix')) {
        return 'Contoh: user@email.com';
      } else if (productName.includes('spotify')) {
        return 'Contoh: user@email.com';
      } else if (productName.includes('youtube')) {
        return 'Contoh: user@email.com';
      } else {
        return 'Masukkan User ID game';
      }
    };

    // Modal methods
    const showConfirmationModal = () => {
      if (!selectedPackage.value) {
        alert('Silakan pilih paket terlebih dahulu');
        return;
      }
      
      // Validate form
      if (!orderForm.value.customer_name || !orderForm.value.customer_email || 
          !orderForm.value.customer_phone || !orderForm.value.game_id) {
        alert('Mohon lengkapi semua data yang diperlukan');
        return;
      }
      
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
    };

    const confirmOrder = () => {
      showModal.value = false;
      submitOrder();
    };

    // Lifecycle
    onMounted(() => {
      fetchProduct();
    });

    return {
      loading,
      error,
      product,
      priceLists,
      selectedPackage,
      submitting,
      showModal,
      orderForm,
      paymentFees,
      subtotalAmount,
      paymentFee,
      totalAmount,
      groupedPriceLists,
      priceListTypes,
      fetchProduct,
      selectPackage,
      selectAndScrollToForm,
      submitOrder,
      showConfirmationModal,
      closeModal,
      confirmOrder,
      formatPrice,
      getImageUrl,
      isGameProduct,
      getAccountFieldLabel,
      getAccountFieldPlaceholder,
    };
  },
};
</script>
