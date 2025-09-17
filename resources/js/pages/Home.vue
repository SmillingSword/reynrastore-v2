<template>
  <AppLayout>
    <!-- Enhanced Hero Section -->
    <section class="relative min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 overflow-hidden">
      <!-- Enhanced Background Effects -->
      <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-blue-500/5 animate-pulse"></div>
      <div class="absolute top-20 left-20 w-64 h-64 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>
      <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-purple-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 2s;"></div>
      
      <div class="relative container mx-auto px-4 py-20 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Enhanced Hero Content -->
          <div class="space-y-8 animate-fade-in">
            <div class="space-y-6">
              <h1 class="text-5xl lg:text-7xl font-bold text-white leading-tight holographic">
                🎮 Top Up Game
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">
                  Terpercaya
                </span> 🎮
              </h1>
              <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed">
                Dapatkan diamond, UC, dan item game favoritmu dengan harga terjangkau dan proses otomatis dalam hitungan detik.
              </p>
              <div class="w-32 h-1 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 rounded-full"></div>
            </div>
            
            <!-- Enhanced Features -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <div class="flex items-center space-x-3 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 backdrop-blur-sm border border-gray-700 hover:border-green-500 transition-all duration-300 neon-glow">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-400 rounded-xl flex items-center justify-center pulse-ring">
                  <BoltIcon class="w-6 h-6 text-white" />
                </div>
                <div>
                  <h3 class="text-white font-bold">⚡ Proses Cepat</h3>
                  <p class="text-green-400 text-sm font-semibold">1-3 menit</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-3 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 backdrop-blur-sm border border-gray-700 hover:border-blue-500 transition-all duration-300 neon-glow">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-400 rounded-xl flex items-center justify-center pulse-ring">
                  <ShieldCheckIcon class="w-6 h-6 text-white" />
                </div>
                <div>
                  <h3 class="text-white font-bold">🛡️ 100% Aman</h3>
                  <p class="text-blue-400 text-sm font-semibold">Terpercaya</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-3 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 backdrop-blur-sm border border-gray-700 hover:border-purple-500 transition-all duration-300 neon-glow">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-400 rounded-xl flex items-center justify-center pulse-ring">
                  <ClockIcon class="w-6 h-6 text-white" />
                </div>
                <div>
                  <h3 class="text-white font-bold">🕒 24/7 Online</h3>
                  <p class="text-purple-400 text-sm font-semibold">Selalu siap</p>
                </div>
              </div>
            </div>
            
            <!-- Enhanced CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-6">
              <router-link
                to="/products"
                class="group bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-glow-green text-center neon-glow relative overflow-hidden"
              >
                <span class="relative z-10 flex items-center justify-center space-x-3">
                  <span class="text-xl">🚀</span>
                  <span>Mulai Top Up Sekarang</span>
                  <span class="group-hover:translate-x-2 transition-transform duration-300">→</span>
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
              </router-link>
              <router-link
                to="/about"
                class="group border-2 border-green-500 text-green-400 hover:bg-green-500 hover:text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 text-center neon-glow relative overflow-hidden"
              >
                <span class="relative z-10 flex items-center justify-center space-x-3">
                  <span class="text-xl">📖</span>
                  <span>Pelajari Lebih Lanjut</span>
                </span>
                <div class="absolute inset-0 bg-green-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
              </router-link>
            </div>
          </div>
          
          <!-- Hero Image/Animation -->
          <div class="relative animate-slide-up">
            <div class="relative z-10">
              <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 shadow-2xl border border-gray-700">
                <div class="space-y-6">
                  <!-- Enhanced Game Icons with Zoom Animation -->
                  <div class="grid grid-cols-3 gap-4">
                    <div 
                      v-for="(game, i) in gameIcons" 
                      :key="i" 
                      class="game-icon-enhanced aspect-square bg-gradient-to-br rounded-xl flex flex-col items-center justify-center text-white font-bold animate-bounce-in relative p-3 neon-glow"
                      :class="game.gradient"
                      :style="`animation-delay: ${i * 0.1}s`"
                      @mouseenter="onGameIconHover(i)"
                      @mouseleave="onGameIconLeave(i)"
                    >
                      <!-- Game Icon -->
                      <div v-if="game.image" class="absolute inset-0 rounded-xl overflow-hidden">
                        <img :src="game.image" :alt="game.name" class="w-full h-full object-cover">
                      </div>
                      <div v-else>
                        <div class="text-3xl mb-2">{{ game.icon }}</div>
                        <div class="text-xs text-white font-semibold tracking-wide">{{ game.name }}</div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Enhanced Real Transaction Slider - Rapi dan Terkandung -->
                  <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl p-6 min-h-[200px] relative border border-gray-600 neon-glow overflow-hidden">
                    <div class="mb-4">
                      <h4 class="text-white font-bold text-lg flex items-center space-x-2">
                        <span class="text-green-400">🎮</span>
                        <span>Transaksi Terbaru</span>
                      </h4>
                    </div>
                    
                    <div v-if="loading" class="flex items-center justify-center py-8">
                      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                      <span class="ml-3 text-gray-400">Memuat transaksi...</span>
                    </div>
                    
                    <div v-else-if="recentOrders.length === 0" class="text-center py-8">
                      <p class="text-gray-400">Belum ada transaksi terbaru</p>
                    </div>
                    
                    <div v-else class="relative">
                      <div 
                        v-for="(order, index) in recentOrders" 
                        :key="order.id"
                        v-show="currentTransactionIndex === index"
                        class="transaction-slide animate-slide-in-from-right"
                      >
                        <!-- Rapi Transaction Card -->
                        <div class="bg-gray-800/60 rounded-lg p-4 space-y-3 border border-gray-600/40 w-full">
                          <!-- Header dengan Customer Info -->
                          <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                {{ getGameIcon(order.orderItems[0]?.product?.name) }}
                              </div>
                              <div class="min-w-0 flex-1">
                                <p class="text-white font-bold text-base">{{ order.customer_name || 'Customer' }}</p>
                                <p class="text-gray-400 text-sm">{{ order.order_number }}</p>
                              </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                              <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Total</p>
                              <p class="text-green-400 font-bold text-lg">{{ formatPrice(order.total_amount) }}</p>
                            </div>
                          </div>
                          
                          <!-- Product Info -->
                          <div class="flex items-center justify-between py-1">
                            <div class="min-w-0 flex-1">
                              <p class="text-white font-semibold text-sm mb-1">{{ order.orderItems[0]?.product?.name || 'Unknown Product' }}</p>
                              <p class="text-gray-400 text-xs">{{ getProductType(order.orderItems[0]?.product?.type) }}</p>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4">
                              <p class="text-gray-400 text-xs mb-1">Qty</p>
                              <p class="text-white font-semibold text-base">{{ order.orderItems[0]?.quantity || 1 }}x</p>
                            </div>
                          </div>
                          
                          <!-- Progress Bar -->
                          <div class="space-y-2">
                            <div class="flex items-center justify-between">
                              <span class="text-gray-300 text-sm font-medium">{{ getStatusText(order.status) }}</span>
                              <span class="text-green-400 font-bold text-sm">{{ getProgressPercentage(order.status) }}%</span>
                            </div>
                            <div class="bg-gray-700 rounded-full h-2 overflow-hidden">
                              <div 
                                class="bg-gradient-to-r from-green-500 to-green-400 h-2 rounded-full transition-all duration-1000 ease-out"
                                :style="`width: ${getProgressPercentage(order.status)}%`"
                              ></div>
                            </div>
                          </div>
                          
                          <!-- Status Footer -->
                          <div class="flex items-center justify-between pt-2 border-t border-gray-600/50">
                            <div class="flex items-center space-x-2 min-w-0 flex-1">
                              <div :class="getStatusColor(order.status)" class="w-2 h-2 rounded-full animate-pulse flex-shrink-0"></div>
                              <span :class="getStatusTextColor(order.status)" class="text-xs font-semibold">
                                {{ getStatusLabel(order.status) }}
                              </span>
                              <span class="text-gray-500 text-xs">• {{ order.time_ago || 'Baru saja' }}</span>
                            </div>
                            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse flex-shrink-0">
                              🔥 LIVE
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Enhanced Floating Elements -->
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-primary-500 rounded-full opacity-20 animate-bounce animate-pulse-glow"></div>
            <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-accent-green rounded-full opacity-20 animate-bounce animate-pulse-glow" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 -left-8 w-12 h-12 bg-orange-500 rounded-full opacity-15 animate-float" style="animation-delay: 2s;"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Enhanced Popular Games Section -->
    <section class="py-20 bg-gradient-to-br from-gray-800 via-gray-900 to-gray-800 relative overflow-hidden">
      <!-- Background Effects -->
      <div class="absolute inset-0 cyber-grid opacity-20"></div>
      <div class="absolute top-10 right-10 w-40 h-40 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
      <div class="absolute bottom-10 left-10 w-32 h-32 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 animate-fade-in">
          <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 holographic">
            🎮 Game Populer 🎮
          </h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
            Top up game favoritmu dengan harga terbaik dan proses tercepat di Indonesia
          </p>
          <div class="w-24 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mt-6 rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
          <div
            v-for="(game, index) in popularGames"
            :key="game.id"
            class="group cursor-pointer animate-slide-in-left"
            :style="`animation-delay: ${index * 0.1}s`"
            @click="goToGame(game.id)"
          >
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-6 text-center transition-all duration-500 group-hover:from-gray-700 group-hover:to-gray-600 group-hover:scale-110 group-hover:shadow-glow-green border border-gray-700 group-hover:border-green-500 neon-glow relative">
              <div class="w-20 h-20 rounded-2xl mx-auto mb-4 group-hover:rotate-12 transition-all duration-300 pulse-ring relative overflow-hidden">
                <!-- Game Image -->
                <div v-if="getGameImage(game.icon)" class="absolute inset-0 rounded-2xl overflow-hidden">
                  <img :src="getGameImage(game.icon)" :alt="game.name" class="w-full h-full object-cover">
                </div>
                <!-- Fallback Icon -->
                <div v-else class="w-full h-full bg-gradient-to-br from-green-500 to-blue-500 rounded-2xl flex items-center justify-center text-white font-bold text-2xl">
                  {{ game.icon }}
                </div>
              </div>
              <h3 class="text-white font-bold mb-2 text-lg group-hover:text-green-400 transition-colors">{{ game.name }}</h3>
              <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">{{ game.description }}</p>
              
              <!-- Hover Effect Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-green-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Enhanced Features Section -->
    <section class="py-24 bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden">
      <!-- Background Effects -->
      <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-64 h-64 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500 rounded-full opacity-3 animate-pulse" style="animation-delay: 1s;"></div>
      </div>
      
      <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 animate-fade-in">
          <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
            ⚡ Mengapa Pilih 
            <span class="holographic">Reynra Store</span>? ⚡
          </h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
            Kami berkomitmen memberikan layanan terbaik untuk para gamers Indonesia dengan teknologi terdepan
          </p>
          <div class="w-32 h-1 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 mx-auto mt-6 rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <div
            v-for="(feature, index) in features"
            :key="feature.title"
            class="group cursor-pointer animate-slide-in-left"
            :style="`animation-delay: ${index * 0.15}s`"
          >
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 text-center transition-all duration-500 group-hover:from-gray-700 group-hover:to-gray-800 group-hover:scale-105 group-hover:shadow-2xl border border-gray-700 group-hover:border-green-500 neon-glow relative overflow-hidden">
              <!-- Animated Background -->
              <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Icon Container -->
              <div class="relative z-10 w-20 h-20 bg-gradient-to-br from-green-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 pulse-ring">
                <component :is="feature.icon" class="w-10 h-10 text-white group-hover:animate-pulse" />
              </div>
              
              <!-- Content -->
              <div class="relative z-10">
                <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-green-400 transition-colors duration-300">
                  {{ feature.title }}
                </h3>
                <p class="text-gray-400 leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
                  {{ feature.description }}
                </p>
              </div>
              
              <!-- Hover Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
              
              <!-- Corner Accent -->
              <div class="absolute top-4 right-4 w-3 h-3 bg-green-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Enhanced Stats Section -->
    <section class="py-24 bg-gradient-to-br from-gray-800 via-gray-900 to-black relative overflow-hidden">
      <!-- Background Effects -->
      <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-72 h-72 bg-green-500 rounded-full opacity-5 animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-56 h-56 bg-blue-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="cyber-grid absolute inset-0 opacity-10"></div>
      </div>
      
      <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 animate-fade-in">
          <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
            📊 <span class="holographic">Statistik Keren</span> 📊
          </h2>
          <p class="text-xl text-gray-300 max-w-2xl mx-auto">
            Angka-angka yang membuktikan kepercayaan gamers Indonesia
          </p>
          <div class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mt-6 rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="(stat, index) in stats"
            :key="stat.label"
            class="text-center group animate-slide-in-left"
            :style="`animation-delay: ${index * 0.2}s`"
          >
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 group-hover:border-green-500 transition-all duration-500 group-hover:scale-105 neon-glow relative overflow-hidden">
              <!-- Background Glow -->
              <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Content -->
              <div class="relative z-10">
                <div class="text-5xl lg:text-6xl font-bold holographic mb-4 group-hover:scale-110 transition-transform duration-300">
                  {{ stat.value }}
                </div>
                <div class="text-gray-400 font-semibold text-lg group-hover:text-green-400 transition-colors duration-300">
                  {{ stat.label }}
                </div>
              </div>
              
              <!-- Pulse Ring Effect -->
              <div class="absolute inset-0 rounded-2xl border-2 border-green-500 opacity-0 group-hover:opacity-30 animate-pulse"></div>
              
              <!-- Corner Accent -->
              <div class="absolute top-4 right-4 w-2 h-2 bg-green-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Enhanced CTA Section -->
    <section class="py-24 bg-gradient-to-br from-green-900 via-gray-900 to-black relative overflow-hidden">
      <!-- Background Effects -->
      <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-80 h-80 bg-green-500 rounded-full opacity-10 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-64 h-64 bg-blue-500 rounded-full opacity-10 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500 rounded-full opacity-5 animate-pulse" style="animation-delay: 2s;"></div>
      </div>
      
      <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-4xl mx-auto space-y-10 animate-fade-in">
          <!-- Main Heading -->
          <div class="space-y-6">
            <h2 class="text-5xl lg:text-6xl font-bold text-white leading-tight">
              🚀 Siap untuk 
              <span class="holographic">Top Up Game</span> 
              Favoritmu? 🚀
            </h2>
            <p class="text-2xl text-gray-300 leading-relaxed">
              Bergabung dengan <span class="text-green-400 font-bold">50,000+</span> gamers yang sudah mempercayai Reynra Store
            </p>
            <div class="w-40 h-1 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 mx-auto rounded-full"></div>
          </div>
          
          <!-- Enhanced CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <router-link
              to="/products"
              class="group bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600 hover:to-green-500 text-white font-bold py-6 px-12 rounded-2xl transition-all duration-500 transform hover:scale-110 shadow-glow-green relative overflow-hidden btn-morph neon-glow"
            >
              <span class="relative z-10 flex items-center space-x-3">
                <span class="text-xl">🎮</span>
                <span class="text-lg">Mulai Top Up Sekarang</span>
                <span class="text-xl group-hover:translate-x-2 transition-transform duration-300">→</span>
              </span>
            </router-link>
            
            <router-link
              to="/order-tracking"
              class="group border-3 border-green-500 text-green-400 hover:bg-green-500 hover:text-white font-bold py-6 px-12 rounded-2xl transition-all duration-500 transform hover:scale-105 relative overflow-hidden neon-glow"
            >
              <span class="relative z-10 flex items-center space-x-3">
                <span class="text-xl">📱</span>
                <span class="text-lg">Lacak Pesanan</span>
              </span>
              <!-- Hover Background -->
              <div class="absolute inset-0 bg-green-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            </router-link>
          </div>
          
          <!-- Trust Indicators -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 pt-12 border-t border-gray-700">
            <div class="flex flex-col items-center space-y-2 animate-slide-in-left" style="animation-delay: 0.1s">
              <div class="text-3xl">⚡</div>
              <div class="text-green-400 font-semibold">Proses Instan</div>
              <div class="text-gray-400 text-sm">1-3 Menit</div>
            </div>
            <div class="flex flex-col items-center space-y-2 animate-slide-in-left" style="animation-delay: 0.2s">
              <div class="text-3xl">🛡️</div>
              <div class="text-green-400 font-semibold">100% Aman</div>
              <div class="text-gray-400 text-sm">Terpercaya</div>
            </div>
            <div class="flex flex-col items-center space-y-2 animate-slide-in-left" style="animation-delay: 0.3s">
              <div class="text-3xl">💰</div>
              <div class="text-green-400 font-semibold">Harga Terbaik</div>
              <div class="text-gray-400 text-sm">Termurah</div>
            </div>
            <div class="flex flex-col items-center space-y-2 animate-slide-in-left" style="animation-delay: 0.4s">
              <div class="text-3xl">🎯</div>
              <div class="text-green-400 font-semibold">24/7 Support</div>
              <div class="text-gray-400 text-sm">Selalu Siap</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '../components/Layout/AppLayout.vue';
import axios from 'axios';
import {
  BoltIcon,
  ShieldCheckIcon,
  ClockIcon,
  CurrencyDollarIcon,
  UserGroupIcon,
  ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/outline';

export default {
  name: 'Home',
  components: {
    AppLayout,
    BoltIcon,
    ShieldCheckIcon,
    ClockIcon,
    CurrencyDollarIcon,
    UserGroupIcon,
    ChatBubbleLeftRightIcon,
  },
  setup() {
    const router = useRouter();
    const currentTransactionIndex = ref(0);
    const loading = ref(true);
    const recentOrders = ref([]);
    let transactionInterval = null;
    
    // Enhanced game icons with better styling
    const gameIcons = [
      { 
        name: 'ML', 
        icon: '⚔️', 
        gradient: 'from-blue-500 to-purple-600',
        popular: true,
        image: '/images/logo-game/ml.png'
      },
      { 
        name: 'FF', 
        icon: '🔥', 
        gradient: 'from-orange-500 to-red-600',
        popular: true,
        image: '/images/logo-game/freefire.png'
      },
      { 
        name: 'PUBG', 
        icon: '🎯', 
        gradient: 'from-yellow-500 to-orange-600',
        popular: false,
        image: '/images/logo-game/pubg.png'
      },
      { 
        name: 'GI', 
        icon: '⭐', 
        gradient: 'from-purple-500 to-pink-600',
        popular: true,
        image: '/images/logo-game/genshin.png'
      },
      { 
        name: 'VL', 
        icon: '🎮', 
        gradient: 'from-red-500 to-pink-600',
        popular: false,
        image: '/images/logo-game/valorant.png'
      },
      { 
        name: 'YT', 
        icon: '📺', 
        gradient: 'from-red-600 to-red-700',
        popular: false,
        image: '/images/logo-game/netflix.png'
      },
    ];
    
    // Fetch recent orders from database
    const fetchRecentOrders = async () => {
      try {
        loading.value = true;
        const response = await axios.get('/api/v1/orders/recent', {
          params: {
            limit: 6
          }
        });
        
        if (response.data.success) {
          recentOrders.value = response.data.data;
          console.log('Recent orders loaded:', recentOrders.value);
        }
      } catch (error) {
        console.error('Error fetching recent orders:', error);
        recentOrders.value = [];
      } finally {
        loading.value = false;
      }
    };
    
    const popularGames = [
      { id: 1, name: 'Mobile Legends', icon: 'ML', description: 'Diamond & Starlight' },
      { id: 2, name: 'Free Fire', icon: 'FF', description: 'Diamond & Elite Pass' },
      { id: 3, name: 'PUBG Mobile', icon: 'PB', description: 'UC & Royale Pass' },
      { id: 4, name: 'Genshin Impact', icon: 'GI', description: 'Genesis Crystal' },
      { id: 5, name: 'Valorant', icon: 'VL', description: 'VP & Battle Pass' },
      { id: 6, name: 'Netflix', icon: 'NF', description: 'Premium Subscription' },
    ];
    
    const features = [
      {
        title: 'Proses Otomatis',
        description: 'Sistem otomatis yang memproses pesanan dalam hitungan detik tanpa perlu menunggu lama.',
        icon: BoltIcon,
      },
      {
        title: 'Harga Terjangkau',
        description: 'Dapatkan harga terbaik dengan berbagai promo menarik setiap harinya.',
        icon: CurrencyDollarIcon,
      },
      {
        title: 'Customer Support 24/7',
        description: 'Tim support yang siap membantu kapan saja untuk mengatasi masalah Anda.',
        icon: ChatBubbleLeftRightIcon,
      },
      {
        title: '100% Aman & Terpercaya',
        description: 'Transaksi aman dengan sistem keamanan berlapis dan garansi uang kembali.',
        icon: ShieldCheckIcon,
      },
      {
        title: 'Ribuan Pelanggan Puas',
        description: 'Dipercaya oleh ribuan gamers di seluruh Indonesia dengan rating 4.9/5.',
        icon: UserGroupIcon,
      },
      {
        title: 'Open 24 Jam',
        description: 'Layanan tersedia 24 jam sehari, 7 hari seminggu tanpa libur.',
        icon: ClockIcon,
      },
    ];
    
    const stats = [
      { value: '50K+', label: 'Pelanggan Puas' },
      { value: '100K+', label: 'Transaksi Sukses' },
      { value: '99.9%', label: 'Uptime Server' },
      { value: '4.9/5', label: 'Rating Pelanggan' },
    ];
    
    // Helper functions
    const formatPrice = (price) => {
      if (!price || isNaN(price)) return 'Rp 0';
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(price);
    };

    const getGameIcon = (gameName) => {
      if (!gameName) return 'FF';
      const name = gameName.toLowerCase();
      if (name.includes('mobile legends')) return 'ML';
      if (name.includes('free fire')) return 'FF';
      if (name.includes('pubg')) return 'PB';
      if (name.includes('genshin')) return 'GI';
      if (name.includes('valorant')) return 'VL';
      if (name.includes('youtube')) return 'YT';
      return 'GM';
    };

    const getStatusText = (status) => {
      const statusMap = {
        'pending': 'Menunggu pembayaran...',
        'paid': 'Memproses pesanan...',
        'processing': 'Sedang diproses...',
        'completed': 'Berhasil! ✅',
        'failed': 'Gagal',
        'cancelled': 'Dibatalkan'
      };
      return statusMap[status] || 'Memproses...';
    };

    const getProgressPercentage = (status) => {
      const progressMap = {
        'pending': 20,
        'paid': 40,
        'processing': 70,
        'completed': 100,
        'failed': 0,
        'cancelled': 0
      };
      return progressMap[status] || 50;
    };

    // Additional helper functions for enhanced display
    const getProductType = (type) => {
      const typeMap = {
        'manual': '🔧 Manual',
        'diggie': '⚡ Otomatis',
        'auto': '⚡ Otomatis'
      };
      return typeMap[type] || '🎮 Game';
    };

    const getStatusColor = (status) => {
      const colorMap = {
        'pending': 'bg-yellow-500',
        'processing': 'bg-blue-500',
        'completed': 'bg-green-500',
        'failed': 'bg-red-500',
        'cancelled': 'bg-gray-500'
      };
      return colorMap[status] || 'bg-yellow-500';
    };

    const getStatusTextColor = (status) => {
      const colorMap = {
        'pending': 'text-yellow-400',
        'processing': 'text-blue-400',
        'completed': 'text-green-400',
        'failed': 'text-red-400',
        'cancelled': 'text-gray-400'
      };
      return colorMap[status] || 'text-yellow-400';
    };

    const getStatusLabel = (status) => {
      const labelMap = {
        'pending': 'Menunggu',
        'processing': 'Diproses',
        'completed': 'Selesai',
        'failed': 'Gagal',
        'cancelled': 'Dibatalkan'
      };
      return labelMap[status] || 'Pending';
    };

    // Auto-slide transactions
    const startTransactionSlider = () => {
      if (recentOrders.value.length > 0) {
        transactionInterval = setInterval(() => {
          currentTransactionIndex.value = (currentTransactionIndex.value + 1) % recentOrders.value.length;
        }, 4000); // Change every 4 seconds
      }
    };
    
    const stopTransactionSlider = () => {
      if (transactionInterval) {
        clearInterval(transactionInterval);
        transactionInterval = null;
      }
    };
    
    // Game icon hover effects
    const onGameIconHover = (index) => {
      // Add additional hover effects if needed
      console.log(`Hovering game icon ${index}`);
    };
    
    const onGameIconLeave = (index) => {
      // Remove hover effects if needed
      console.log(`Left game icon ${index}`);
    };
    
    const goToGame = (gameId) => {
      router.push(`/products?game=${gameId}`);
    };

    // Get game image based on icon
    const getGameImage = (icon) => {
      const imageMap = {
        'ML': '/images/logo-game/ml.png',
        'FF': '/images/logo-game/freefire.png',
        'PB': '/images/logo-game/pubg.png',
        'GI': '/images/logo-game/genshin.png',
        'VL': '/images/logo-game/valorant.png',
        'NF': '/images/logo-game/netflix.png',
        'YT': '/images/logo-game/netflix.png'
      };
      return imageMap[icon] || null;
    };
    
    // Lifecycle hooks
    onMounted(async () => {
      await fetchRecentOrders();
      startTransactionSlider();
    });
    
    onUnmounted(() => {
      stopTransactionSlider();
    });
    
    return {
      loading,
      gameIcons,
      recentOrders,
      currentTransactionIndex,
      popularGames,
      features,
      stats,
      formatPrice,
      getGameIcon,
      getGameImage,
      getStatusText,
      getProgressPercentage,
      getProductType,
      getStatusColor,
      getStatusTextColor,
      getStatusLabel,
      goToGame,
      onGameIconHover,
      onGameIconLeave,
    };
  },
};
</script>
