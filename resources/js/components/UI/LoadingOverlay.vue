<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-gray-800 rounded-lg p-8 flex flex-col items-center space-y-4">
      <!-- Animated Logo -->
      <div class="relative">
        <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-green rounded-lg flex items-center justify-center animate-pulse-green">
          <span class="text-black font-bold text-2xl">R</span>
        </div>
        <div class="absolute -inset-2 bg-gradient-to-br from-primary-500 to-accent-green rounded-lg blur opacity-25 animate-glow"></div>
      </div>
      
      <!-- Loading Text -->
      <div class="text-center">
        <h3 class="text-white font-semibold text-lg">{{ title }}</h3>
        <p class="text-gray-400 text-sm mt-1">{{ message }}</p>
      </div>
      
      <!-- Progress Bar -->
      <div class="w-48 bg-gray-700 rounded-full h-2 overflow-hidden">
        <div class="h-full bg-gradient-to-r from-primary-500 to-accent-green rounded-full animate-pulse"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingOverlay',
  props: {
    title: {
      type: String,
      default: 'Memuat...',
    },
    message: {
      type: String,
      default: 'Mohon tunggu sebentar',
    },
  },
};
</script>
