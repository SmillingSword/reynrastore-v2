import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import router from './router';
import App from './App.vue';
import { useAuthStore } from './stores/auth';

// Import global styles
import '../css/app.css';

const app = createApp(App);
const pinia = createPinia();

// Add persistence plugin to pinia
pinia.use(piniaPluginPersistedstate);

app.use(pinia);
app.use(router);

// Initialize auth store after pinia is set up
const authStore = useAuthStore();
authStore.initializeAuth();

app.mount('#app');
