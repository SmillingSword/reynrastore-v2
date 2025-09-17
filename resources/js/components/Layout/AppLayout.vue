<template>
  <div class="min-h-screen bg-gray-900">
    <!-- Navigation Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <main class="relative">
      <slot />
    </main>
    
    <!-- Footer -->
    <AppFooter />
    
    <!-- Notifications -->
    <NotificationContainer />
    
    <!-- Loading Overlay -->
    <LoadingOverlay v-if="appStore.isLoading" />
  </div>
</template>

<script>
import { useAppStore } from '../../stores';
import AppHeader from './AppHeader.vue';
import AppFooter from './AppFooter.vue';
import NotificationContainer from '../UI/NotificationContainer.vue';
import LoadingOverlay from '../UI/LoadingOverlay.vue';

export default {
  name: 'AppLayout',
  components: {
    AppHeader,
    AppFooter,
    NotificationContainer,
    LoadingOverlay,
  },
  setup() {
    const appStore = useAppStore();
    
    return {
      appStore,
    };
  },
};
</script>
