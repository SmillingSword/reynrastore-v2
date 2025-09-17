<template>
  <AppLayout>
    <!-- Loading State -->
    <div v-if="loading" class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="container mx-auto px-4 py-8">
        <div class="animate-pulse space-y-4">
          <div class="loading-skeleton h-32"></div>
          <div class="loading-skeleton h-48"></div>
          <div class="loading-skeleton h-32"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="container mx-auto px-4 py-8">
        <div class="error-state">
          <div class="error-icon">⚠️</div>
          <h1 class="error-title">Gagal Memuat Pembayaran</h1>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchPaymentData" class="retry-btn">
            🔄 Coba Lagi
          </button>
        </div>
      </div>
    </div>

    <!-- Payment Content - Mobile First -->
    <div v-else class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="container mx-auto px-4 py-6 max-w-md">
        
        <!-- Header - Compact -->
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-white mb-2 holographic">
            💳 Pembayaran
          </h1>
          <div class="bg-gray-800 bg-opacity-50 rounded-lg p-3 mb-4">
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-400">Order:</span>
              <span class="text-white font-mono">{{ order.order_number }}</span>
            </div>
            <div class="flex justify-between items-center text-lg font-bold mt-2">
              <span class="text-gray-300">Total:</span>
              <span class="text-green-400">{{ formatPrice(order.total_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Method Section -->
        <div class="payment-card mb-6">
          <div class="payment-header">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span class="text-green-400">{{ getPaymentMethodIcon(order.payment_method) }}</span>
              {{ getPaymentMethodName(order.payment_method) }}
            </h2>
          </div>

          <!-- QRIS Payment -->
          <div v-if="order.payment_method === 'qris' && paymentData" class="qris-mobile">
            <!-- QR Code - Large and Prominent -->
            <div class="qr-section">
              <div class="qr-wrapper">
                <div v-if="paymentData && (paymentData.qr_code_url || paymentData.qris_url || paymentData.qris_string)" class="qr-display">
                  <img v-if="paymentData.qr_code_url" 
                       :src="paymentData.qr_code_url" 
                       alt="QRIS QR Code" 
                       class="qr-image"
                       @error="handleQRImageError">
                  <img v-else-if="paymentData.qris_url" 
                       :src="paymentData.qris_url" 
                       alt="QRIS QR Code" 
                       class="qr-image"
                       @error="handleQRImageError">
                  <div v-else-if="paymentData.qris_string" id="qrcode" class="qr-generated"></div>
                </div>
                <div v-else class="qr-placeholder">
                  <div class="placeholder-content">
                    <span class="text-4xl">📱</span>
                    <p class="text-white text-sm mt-2">Memuat QR Code...</p>
                  </div>
                </div>
              </div>
              
            <!-- QR Info - Compact & Reordered -->
            <div class="qr-info">
              <div class="qr-amount">
                <span class="amount-label">💰 Total Bayar</span>
                <span class="amount-value">{{ formatPrice(order.total_amount) }}</span>
              </div>
              
              <!-- Status di atas timer -->
              <div v-if="paymentData?.transaction_id" class="qr-status">
                <span class="status-label">Status</span>
                <span class="status-value" :class="getStatusClass(order.status)">
                  {{ getSimpleStatusText(order.status) }}
                  <span v-if="order.status === 'pending'" class="loading-dots">...</span>
                </span>
              </div>
              
              <!-- Timer dengan auto redirect -->
              <div v-if="paymentData?.expires_at" class="qr-expiry">
                <span class="expiry-label">⏰ Berlaku hingga</span>
                <span class="expiry-value" :class="{ 'text-red-400': isExpiringSoon }">
                  <span v-if="timeRemaining === 'Expired'" class="expired-text">
                    ⚠️ Waktu Habis - Kembali ke halaman sebelumnya...
                  </span>
                  <span v-else>{{ timeRemaining || formatDateTime(paymentData.expires_at) }}</span>
                </span>
              </div>
            </div>
          </div>

            <!-- Payment Instructions -->
            <div class="payment-instructions-card">
              <div class="instructions-header">
                <div class="instructions-icon">📱</div>
                <h3 class="instructions-title">Cara Pembayaran QRIS</h3>
              </div>
              <div class="instructions-steps">
                <div class="instruction-step">
                  <div class="step-number">1</div>
                  <div class="step-text">Buka aplikasi e-wallet atau m-banking Anda</div>
                </div>
                <div class="instruction-step">
                  <div class="step-number">2</div>
                  <div class="step-text">Pilih menu "Scan QR" atau "Bayar"</div>
                </div>
                <div class="instruction-step">
                  <div class="step-number">3</div>
                  <div class="step-text">Arahkan kamera ke QR Code di atas</div>
                </div>
                <div class="instruction-step">
                  <div class="step-number">4</div>
                  <div class="step-text">Konfirmasi pembayaran sebesar {{ formatPrice(order.total_amount) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Bank Transfer Payment -->
          <div v-else-if="paymentData?.payment_type === 'manual' && paymentData?.method === 'bank_transfer'" class="bank-mobile">
            <div class="bank-card">
              <div class="bank-header">
                <span class="bank-icon">🏦</span>
                <div class="bank-info">
                  <h3 class="bank-name">{{ paymentData.bank_name }}</h3>
                  <p class="bank-type">Transfer Bank</p>
                </div>
              </div>
              
              <div class="bank-details">
                <div class="detail-item">
                  <span class="detail-label">Nomor Rekening</span>
                  <div class="detail-value-container">
                    <span class="detail-value">{{ paymentData.account_number }}</span>
                    <button @click="copyToClipboard(paymentData.account_number)" class="copy-btn-mobile">
                      📋
                    </button>
                  </div>
                </div>
                
                <div class="detail-item">
                  <span class="detail-label">Atas Nama</span>
                  <span class="detail-value">{{ paymentData.account_name }}</span>
                </div>
                
                <div class="detail-item amount-detail">
                  <span class="detail-label">Jumlah Transfer</span>
                  <div class="detail-value-container">
                    <span class="detail-value amount">{{ formatPrice(paymentData.amount) }}</span>
                    <button @click="copyToClipboard(paymentData.amount.toString())" class="copy-btn-mobile">
                      📋
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="bank-note">
                <span class="note-icon">💡</span>
                <p class="note-text">{{ paymentData.note }}</p>
              </div>
            </div>
          </div>

          <!-- E-Wallet Payment -->
          <div v-else-if="paymentData?.payment_type === 'manual' && paymentData?.method === 'ewallet'" class="ewallet-mobile">
            <div class="ewallet-card">
              <div class="ewallet-header">
                <span class="ewallet-icon">💰</span>
                <div class="ewallet-info">
                  <h3 class="ewallet-name">{{ paymentData.wallet_name }}</h3>
                  <p class="ewallet-type">E-Wallet Transfer</p>
                </div>
              </div>
              
              <div class="ewallet-details">
                <div class="detail-item">
                  <span class="detail-label">Nomor E-Wallet</span>
                  <div class="detail-value-container">
                    <span class="detail-value">{{ paymentData.phone_number }}</span>
                    <button @click="copyToClipboard(paymentData.phone_number)" class="copy-btn-mobile">
                      📋
                    </button>
                  </div>
                </div>
                
                <div class="detail-item">
                  <span class="detail-label">Atas Nama</span>
                  <span class="detail-value">{{ paymentData.account_name }}</span>
                </div>
                
                <div class="detail-item amount-detail">
                  <span class="detail-label">Jumlah Transfer</span>
                  <div class="detail-value-container">
                    <span class="detail-value amount">{{ formatPrice(paymentData.amount) }}</span>
                    <button @click="copyToClipboard(paymentData.amount.toString())" class="copy-btn-mobile">
                      📋
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="ewallet-note">
                <span class="note-icon">💡</span>
                <p class="note-text">{{ paymentData.note }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Status - Compact -->
        <div class="status-card mb-6">
          <div class="status-info">
            <div class="status-icon">
              <span v-if="order.status === 'pending'" class="text-yellow-400 text-xl">⏳</span>
              <span v-else-if="order.status === 'paid'" class="text-green-400 text-xl">✅</span>
              <span v-else-if="order.status === 'failed'" class="text-red-400 text-xl">❌</span>
              <span v-else class="text-gray-400 text-xl">📋</span>
            </div>
            <div class="status-text">
              <h4 class="status-title">{{ getStatusText(order.status) }}</h4>
              <p class="status-desc">{{ getStatusDescription(order.status) }}</p>
            </div>
          </div>
          
          <button @click="checkPaymentStatus" class="status-check-btn" :disabled="checking">
            <span v-if="checking">⏳ Mengecek...</span>
            <span v-else>🔄 Cek Status</span>
          </button>
        </div>

        <!-- Order Details - Collapsible -->
        <div class="details-card">
          <button @click="showDetails = !showDetails" class="details-toggle">
            <span class="toggle-text">📋 Detail Pesanan</span>
            <span class="toggle-icon" :class="{ 'rotate-180': showDetails }">▼</span>
          </button>
          
          <div v-show="showDetails" class="details-content">
            <div class="detail-section">
              <h4 class="section-title">👤 Customer</h4>
              <div class="section-content">
                <p class="detail-line">{{ order.customer_name }}</p>
                <p class="detail-line">{{ order.customer_email }}</p>
                <p class="detail-line">{{ order.customer_phone }}</p>
              </div>
            </div>
            
            <div class="detail-section">
              <h4 class="section-title">🎮 Produk</h4>
              <div class="section-content">
                <p class="detail-line">{{ order.items?.[0]?.product_name || 'Produk Game' }}</p>
                <p class="detail-line">Qty: {{ order.items?.[0]?.quantity || 1 }}x</p>
                <p class="detail-line">{{ formatPrice(order.items?.[0]?.unit_price || order.subtotal) }}</p>
              </div>
            </div>
            
            <div v-if="order.game_data" class="detail-section">
              <h4 class="section-title">🎯 Game Info</h4>
              <div class="section-content">
                <p class="detail-line">ID: {{ order.game_data.game_id }}</p>
                <p v-if="order.game_data.server" class="detail-line">Server: {{ order.game_data.server }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="goToOrderTracking" class="action-btn secondary">
            📋 Lacak Pesanan
          </button>
          <button @click="goToHome" class="action-btn primary">
            🏠 Kembali ke Home
          </button>
        </div>

        <!-- Footer Info -->
        <div class="footer-info">
          <p class="info-text">💡 Pembayaran otomatis terverifikasi</p>
          <p class="info-subtext">Hubungi CS jika ada kendala</p>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppLayout from '../components/Layout/AppLayout.vue';
import axios from 'axios';
import QRCode from 'qrcode';

export default {
  name: 'Payment',
  components: {
    AppLayout,
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    
    const loading = ref(true);
    const error = ref(null);
    const checking = ref(false);
    const showDetails = ref(false);
    const order = ref({});
    const paymentData = ref(null);
    const statusCheckInterval = ref(null);
    const countdownInterval = ref(null);
    const timeRemaining = ref('');
    const isExpiringSoon = ref(false);

    // Methods
    const fetchPaymentData = async () => {
      try {
        loading.value = true;
        error.value = null;
        
        const orderNumber = route.params.orderNumber;
        
        const orderResponse = await axios.get(`/api/v1/orders/${orderNumber}/payment`);
        
        if (orderResponse.data.success) {
          order.value = orderResponse.data.data.order;
          
          if (order.value.payment_method) {
            await createPayment(orderNumber, order.value.payment_method);
          } else {
            await createPayment(orderNumber, 'qris');
          }
          
          startStatusCheck();
        } else {
          throw new Error(orderResponse.data.message || 'Gagal memuat data pembayaran');
        }
      } catch (err) {
        console.error('Error fetching payment data:', err);
        error.value = err.response?.data?.message || err.message || 'Gagal memuat data pembayaran';
      } finally {
        loading.value = false;
      }
    };

    const createPayment = async (orderNumber, paymentMethod) => {
      try {
        const paymentResponse = await axios.post('/api/v1/payment/create', {
          order_number: orderNumber,
          payment_method: paymentMethod
        });
        
        if (paymentResponse.data.success) {
          paymentData.value = paymentResponse.data.data;
          
          if (paymentMethod === 'qris' && paymentData.value.qris_string && 
              !paymentData.value.qris_url && !paymentData.value.qr_code_url) {
            setTimeout(() => {
              generateQRCode(paymentData.value.qris_string);
            }, 100);
          }
          
          if (paymentMethod === 'qris' && paymentData.value.expires_at) {
            startCountdown();
          }
        } else {
          throw new Error(paymentResponse.data.message || 'Gagal membuat pembayaran');
        }
      } catch (err) {
        console.error('Error creating payment:', err);
        throw err;
      }
    };

    const generateQRCode = async (qrString) => {
      try {
        const qrCodeElement = document.getElementById('qrcode');
        if (qrCodeElement) {
          qrCodeElement.innerHTML = '';
          
          const qrCodeDataURL = await QRCode.toDataURL(qrString, {
            width: 280,
            height: 280,
            color: {
              dark: '#000000',
              light: '#ffffff'
            },
            margin: 2,
            errorCorrectionLevel: 'M'
          });
          
          const img = document.createElement('img');
          img.src = qrCodeDataURL;
          img.alt = 'QRIS QR Code';
          img.className = 'qr-image';
          qrCodeElement.appendChild(img);
        }
      } catch (error) {
        console.error('Error generating QR code:', error);
        
        const qrCodeElement = document.getElementById('qrcode');
        if (qrCodeElement) {
          qrCodeElement.innerHTML = `
            <div class="qr-placeholder">
              <div class="placeholder-content">
                <span class="text-4xl">📱</span>
                <p class="text-white text-sm mt-2">Error generating QR</p>
              </div>
            </div>
          `;
        }
      }
    };

    const checkPaymentStatus = async () => {
      try {
        checking.value = true;
        
        // Check status dari API yang akan sync dengan Midtrans
        const response = await axios.get(`/api/v1/orders/${order.value.order_number}/status`);
        
        if (response.data.success) {
          const newStatus = response.data.data.status;
          const newPaymentStatus = response.data.data.payment_status;
          
          // Update status lokal
          order.value.status = newStatus;
          order.value.payment_status = newPaymentStatus;
          
          if (newPaymentStatus === 'paid' || newStatus === 'paid') {
            alert('🎉 Pembayaran berhasil! Pesanan Anda sedang diproses.');
            stopStatusCheck();
            stopCountdown();
            
            // Redirect ke halaman sukses setelah 2 detik
            setTimeout(() => {
              router.push(`/orders/${order.value.order_number}/track`);
            }, 2000);
          } else if (newPaymentStatus === 'failed' || newStatus === 'failed') {
            alert('❌ Pembayaran gagal. Silakan coba lagi.');
            stopStatusCheck();
          }
        }
      } catch (err) {
        console.error('Error checking payment status:', err);
        alert('❌ Gagal mengecek status pembayaran. Silakan coba lagi.');
      } finally {
        checking.value = false;
      }
    };

    const startStatusCheck = () => {
      statusCheckInterval.value = setInterval(() => {
        if (order.value.status === 'pending') {
          checkPaymentStatus();
        } else {
          stopStatusCheck();
        }
      }, 10000);
    };

    const stopStatusCheck = () => {
      if (statusCheckInterval.value) {
        clearInterval(statusCheckInterval.value);
        statusCheckInterval.value = null;
      }
    };

    const startCountdown = () => {
      if (!paymentData.value?.expires_at) return;
      
      countdownInterval.value = setInterval(() => {
        updateCountdown();
      }, 1000);
    };

    const stopCountdown = () => {
      if (countdownInterval.value) {
        clearInterval(countdownInterval.value);
        countdownInterval.value = null;
      }
    };

    const updateCountdown = () => {
      if (!paymentData.value?.expires_at) return;
      
      // Parse expiry time dari Midtrans dengan timezone yang benar
      const expiryTime = new Date(paymentData.value.expires_at);
      const now = new Date();
      
      // Hitung selisih waktu dalam milidetik
      const distance = expiryTime.getTime() - now.getTime();
      
      if (distance < 0) {
        timeRemaining.value = 'Expired';
        isExpiringSoon.value = true;
        stopCountdown();
        
        // Auto redirect setelah 3 detik
        setTimeout(() => {
          router.go(-1); // Kembali ke halaman sebelumnya
        }, 3000);
        return;
      }
      
      // Hitung jam, menit, dan detik
      const hours = Math.floor(distance / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      
      // Format timer sesuai dengan Midtrans (HH:MM atau MM:SS)
      if (hours > 0) {
        timeRemaining.value = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      } else {
        timeRemaining.value = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      }
      
      // Set warning jika kurang dari 5 menit
      isExpiringSoon.value = distance < (5 * 60 * 1000);
    };

    const handleQRImageError = (event) => {
      console.error('QR Image failed to load:', event);
      if (paymentData.value?.qris_string) {
        setTimeout(() => {
          generateQRCode(paymentData.value.qris_string);
        }, 100);
      }
    };

    const copyToClipboard = async (text) => {
      try {
        await navigator.clipboard.writeText(text);
        alert('📋 Berhasil disalin!');
      } catch (err) {
        console.error('Failed to copy:', err);
        alert('❌ Gagal menyalin');
      }
    };

    const goToOrderTracking = () => {
      router.push(`/orders/${order.value.order_number}/track`);
    };

    const goToHome = () => {
      router.push('/');
    };

    // Utility functions
    const formatPrice = (price) => {
      if (!price || isNaN(price)) return 'Rp 0';
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(price);
    };

    const formatDateTime = (dateString) => {
      return new Date(dateString).toLocaleString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const getStatusText = (status) => {
      const texts = {
        pending: 'Menunggu Pembayaran',
        paid: 'Pembayaran Berhasil',
        failed: 'Pembayaran Gagal',
        processing: 'Sedang Diproses',
        completed: 'Selesai'
      };
      return texts[status] || 'Unknown';
    };

    const getStatusDescription = (status) => {
      const descriptions = {
        pending: 'Silakan lakukan pembayaran',
        paid: 'Pesanan sedang diproses',
        failed: 'Silakan coba lagi',
        processing: 'Pesanan sedang diproses',
        completed: 'Pesanan telah selesai'
      };
      return descriptions[status] || 'Status tidak diketahui';
    };

    const getPaymentMethodIcon = (method) => {
      const icons = {
        qris: '📱',
        bank_transfer: '🏦',
        ewallet: '💰'
      };
      return icons[method] || '💳';
    };

    const getPaymentMethodName = (method) => {
      const names = {
        qris: 'QRIS',
        bank_transfer: 'Transfer Bank',
        ewallet: 'E-Wallet'
      };
      return names[method] || 'Payment';
    };

    const getStatusClass = (status) => {
      const classes = {
        pending: 'text-yellow-400',
        paid: 'text-green-400',
        failed: 'text-red-400',
        processing: 'text-blue-400',
        completed: 'text-purple-400'
      };
      return classes[status] || 'text-gray-400';
    };

    const getSimpleStatusText = (status) => {
      const texts = {
        pending: 'Unpaid',
        paid: 'Paid',
        failed: 'Failed',
        processing: 'Processing',
        completed: 'Completed'
      };
      return texts[status] || 'Unknown';
    };

    // Lifecycle
    onMounted(() => {
      fetchPaymentData();
    });

    onUnmounted(() => {
      stopStatusCheck();
      stopCountdown();
    });

    return {
      loading,
      error,
      checking,
      showDetails,
      order,
      paymentData,
      timeRemaining,
      isExpiringSoon,
      fetchPaymentData,
      checkPaymentStatus,
      copyToClipboard,
      goToOrderTracking,
      goToHome,
      handleQRImageError,
      formatPrice,
      formatDateTime,
      getStatusText,
      getStatusDescription,
      getPaymentMethodIcon,
      getPaymentMethodName,
      getStatusClass,
      getSimpleStatusText,
    };
  },
};
</script>

<style>
/* Mobile-First Responsive Styles */
@media (max-width: 480px) {
  .qr-image {
    width: 16rem !important;
    height: 16rem !important;
  }
  
  .qr-placeholder {
    width: 16rem !important;
    height: 16rem !important;
  }
  
  .apps-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.75rem !important;
  }
  
  .action-buttons {
    flex-direction: column !important;
  }
}

/* Loading dots animation */
.loading-dots {
  animation: loadingDots 1.5s infinite;
}

@keyframes loadingDots {
  0%, 20% { opacity: 0; }
  50% { opacity: 1; }
  80%, 100% { opacity: 0; }
}

/* Additional Mobile Styles */
.qris-mobile {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.payment-apps {
  margin-bottom: 1rem;
}

.apps-title {
  color: white;
  font-size: 0.875rem;
  margin-bottom: 0.75rem;
  text-align: center;
}

.apps-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
}

.instructions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.instruction-step {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
}

.step-number {
  background-color: #3b82f6;
  color: white;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: bold;
  flex-shrink: 0;
}

.step-text {
  color: #d1d5db;
}

.bank-mobile, .ewallet-mobile {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.copy-btn-mobile {
  padding: 0.25rem 0.5rem;
  background-color: #2563eb;
  color: white;
  font-size: 0.75rem;
  border-radius: 0.25rem;
  transition: background-color 0.2s;
}

.copy-btn-mobile:hover {
  background-color: #1d4ed8;
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.action-btn {
  flex: 1;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
  text-align: center;
}

.action-btn.primary {
  background-color: #10b981;
  color: white;
}

.action-btn.primary:hover {
  background-color: #059669;
}

.action-btn.secondary {
  background-color: #4b5563;
  color: white;
}

.action-btn.secondary:hover {
  background-color: #374151;
}

.footer-info {
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-text {
  color: white;
  font-size: 0.875rem;
}

.info-subtext {
  color: #9ca3af;
  font-size: 0.75rem;
}

.details-toggle {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  text-align: left;
  transition: background-color 0.2s;
}

.details-toggle:hover {
  background-color: rgba(55, 65, 81, 0.3);
}

.toggle-text {
  color: white;
  font-weight: 500;
}

.toggle-icon {
  color: #9ca3af;
  transition: transform 0.2s;
}

.rotate-180 {
  transform: rotate(180deg);
}

.details-content {
  padding: 1rem;
  padding-top: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.detail-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.section-title {
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-line {
  color: #d1d5db;
  font-size: 0.875rem;
}

/* Holographic text effect */
.holographic {
  background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7);
  background-size: 400% 400%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: holographic 3s ease-in-out infinite;
}

@keyframes holographic {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}
</style>
