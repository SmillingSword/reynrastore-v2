<template>
  <AppLayout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-gaming-pattern opacity-5"></div>
      
      <!-- Animated Background Elements -->
      <div class="absolute inset-0">
        <div class="absolute top-20 left-10 w-32 h-32 bg-green-500 rounded-full opacity-10 animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-24 h-24 bg-green-400 rounded-full opacity-10 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/3 w-16 h-16 bg-green-600 rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
      </div>
      
      <div class="relative container mx-auto px-4 py-20 lg:py-32">
        <div class="text-center max-w-4xl mx-auto">
          <h1 class="text-4xl lg:text-6xl font-bold text-white leading-tight mb-6">
            Hubungi
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-green-600">
              Kami
            </span>
          </h1>
          <p class="text-xl text-gray-300 leading-relaxed mb-8">
            Kami siap membantu Anda 24/7. Jangan ragu untuk menghubungi tim support Reynra Store
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Information Section -->
    <section class="py-20 bg-gray-800">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Contact Info -->
          <div class="space-y-8">
            <div>
              <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                Hubungi Kami
              </h2>
              <p class="text-gray-300 leading-relaxed mb-8">
                Tim customer service kami siap membantu Anda dengan segala pertanyaan seputar top up game, pembayaran, atau masalah teknis lainnya.
              </p>
            </div>

            <!-- Contact Cards -->
            <div class="space-y-6">
              <!-- Email -->
              <div class="bg-gray-900 rounded-xl p-6 border-l-4 border-green-500 hover:bg-gray-700 transition-all duration-300 group">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all duration-300">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-white font-semibold mb-1">Email Support</h3>
                    <p class="text-green-400 font-medium">support@reynrastore.com</p>
                    <p class="text-gray-400 text-sm">Respon dalam 1-2 jam</p>
                  </div>
                </div>
              </div>

              <!-- Phone -->
              <div class="bg-gray-900 rounded-xl p-6 border-l-4 border-blue-500 hover:bg-gray-700 transition-all duration-300 group">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-all duration-300">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-white font-semibold mb-1">Telepon / WhatsApp</h3>
                    <p class="text-blue-400 font-medium">+62 838-7323-1154</p>
                    <p class="text-gray-400 text-sm">Buka 24/7</p>
                  </div>
                </div>
              </div>

              <!-- Address -->
              <div class="bg-gray-900 rounded-xl p-6 border-l-4 border-purple-500 hover:bg-gray-700 transition-all duration-300 group">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-all duration-300">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-white font-semibold mb-1">Alamat Kantor</h3>
                    <p class="text-purple-400 font-medium">Palmerah, Jakarta Barat, Indonesia</p>
                    <p class="text-gray-400 text-sm">Buka 24/7</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Social Media -->
            <div class="bg-gray-900 rounded-xl p-6">
              <h3 class="text-white font-semibold mb-4">Ikuti Kami</h3>
              <div class="flex space-x-4">
                <!-- Instagram -->
                <a href="https://www.instagram.com/reynrastore_official/" target="_blank" class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-110">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
                
                <!-- Facebook -->
                <a href="https://www.facebook.com/profile.php?id=61578912875239" target="_blank" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-all duration-300 transform hover:scale-110">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </a>
                
                <!-- Twitter/X -->
                <a href="https://x.com/Reynrastore" target="_blank" class="w-10 h-10 bg-black rounded-lg flex items-center justify-center hover:bg-gray-800 transition-all duration-300 transform hover:scale-110">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                </a>
                
                <!-- TikTok -->
                <a href="https://www.tiktok.com/@reynra.store" target="_blank" class="w-10 h-10 bg-black rounded-lg flex items-center justify-center hover:bg-gray-800 transition-all duration-300 transform hover:scale-110">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                  </svg>
                </a>
                
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send/?phone=6283873231154&text&type=phone_number&app_absent=0" target="_blank" class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center hover:bg-green-700 transition-all duration-300 transform hover:scale-110">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="bg-gray-900 rounded-2xl p-8 border border-gray-700">
            <h3 class="text-2xl font-bold text-white mb-6">Kirim Pesan</h3>
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                  placeholder="Masukkan nama lengkap Anda"
                  required
                >
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                  placeholder="nama@email.com"
                  required
                >
              </div>

              <!-- Subject -->
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">Subjek</label>
                <select
                  id="subject"
                  v-model="form.subject"
                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                  required
                >
                  <option value="">Pilih subjek</option>
                  <option value="top-up">Masalah Top Up</option>
                  <option value="payment">Masalah Pembayaran</option>
                  <option value="account">Masalah Akun</option>
                  <option value="technical">Masalah Teknis</option>
                  <option value="suggestion">Saran & Kritik</option>
                  <option value="other">Lainnya</option>
                </select>
              </div>

              <!-- Message -->
              <div>
                <label for="message" class="block text-sm font-medium text-gray-300 mb-2">Pesan</label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="5"
                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 resize-none"
                  placeholder="Jelaskan masalah atau pertanyaan Anda dengan detail..."
                  required
                ></textarea>
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed"
              >
                <span v-if="!isSubmitting">Kirim Pesan</span>
                <span v-else class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Mengirim...
                </span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-900">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
            Pertanyaan yang Sering Diajukan
          </h2>
          <p class="text-gray-400 text-lg max-w-2xl mx-auto">
            Temukan jawaban untuk pertanyaan yang paling sering ditanyakan
          </p>
        </div>

        <div class="max-w-4xl mx-auto space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="bg-gray-800 rounded-lg border border-gray-700"
          >
            <button
              @click="toggleFaq(index)"
              class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-700 transition-colors rounded-lg"
            >
              <span class="text-white font-semibold">{{ faq.question }}</span>
              <svg
                :class="['w-5 h-5 text-green-400 transition-transform', openFaq === index ? 'rotate-180' : '']"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div
              v-show="openFaq === index"
              class="px-6 pb-4"
            >
              <p class="text-gray-300 leading-relaxed">{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-green-900 to-gray-900">
      <div class="container mx-auto px-4 text-center">
        <div class="max-w-3xl mx-auto space-y-8">
          <h2 class="text-3xl lg:text-4xl font-bold text-white">
            Butuh Bantuan Segera?
          </h2>
          <p class="text-xl text-gray-300">
            Tim customer service kami siap membantu Anda 24/7 melalui WhatsApp
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="https://wa.me/6283873231154"
              target="_blank"
              class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
              </svg>
              <span>Chat WhatsApp</span>
            </a>
            <a
              href="mailto:support@reynrastore.com"
              class="border-2 border-green-500 text-green-400 hover:bg-green-500 hover:text-white font-semibold py-4 px-8 rounded-lg transition-all duration-300 flex items-center justify-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span>Kirim Email</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script>
import { ref } from 'vue';
import AppLayout from '../components/Layout/AppLayout.vue';

export default {
  name: 'Contact',
  components: { AppLayout },
  setup() {
    const form = ref({
      name: '',
      email: '',
      subject: '',
      message: ''
    });

    const isSubmitting = ref(false);
    const openFaq = ref(null);

    const faqs = ref([
      {
        question: 'Berapa lama proses top up?',
        answer: 'Proses top up biasanya memakan waktu 1-5 menit setelah pembayaran berhasil. Untuk produk otomatis, proses akan lebih cepat (1-2 menit), sedangkan untuk produk manual bisa memakan waktu hingga 5 menit.'
      },
      {
        question: 'Metode pembayaran apa saja yang tersedia?',
        answer: 'Kami menerima berbagai metode pembayaran termasuk QRIS, Transfer Bank (BCA, BRI, BNI, Mandiri), E-Wallet (OVO, DANA, GoPay, LinkAja), dan Kartu Kredit/Debit.'
      },
      {
        question: 'Bagaimana jika top up gagal?',
        answer: 'Jika top up gagal, dana akan dikembalikan secara otomatis dalam 1x24 jam. Anda juga bisa menghubungi customer service kami untuk penanganan lebih cepat.'
      },
      {
        question: 'Apakah ada garansi untuk setiap transaksi?',
        answer: 'Ya, kami memberikan garansi 100% untuk setiap transaksi. Jika ada masalah dengan pesanan Anda, kami akan mengganti atau mengembalikan dana sepenuhnya.'
      },
      {
        question: 'Bagaimana cara melacak status pesanan?',
        answer: 'Anda bisa melacak status pesanan melalui halaman Order Tracking dengan memasukkan nomor pesanan yang dikirim via email setelah pembayaran berhasil.'
      },
      {
        question: 'Apakah data pribadi saya aman?',
        answer: 'Keamanan data pribadi Anda adalah prioritas utama kami. Kami menggunakan enkripsi SSL dan tidak menyimpan informasi pembayaran sensitif di server kami.'
      }
    ]);

    const submitForm = async () => {
      isSubmitting.value = true;
      
      try {
        // Simulate form submission
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Reset form
        form.value = {
          name: '',
          email: '',
          subject: '',
          message: ''
        };
        
        alert('Pesan berhasil dikirim! Tim kami akan segera menghubungi Anda.');
      } catch (error) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
      } finally {
        isSubmitting.value = false;
      }
    };

    const toggleFaq = (index) => {
      openFaq.value = openFaq.value === index ? null : index;
    };

    return {
      form,
      isSubmitting,
      openFaq,
      faqs,
      submitForm,
      toggleFaq
    };
  }
};
</script>
