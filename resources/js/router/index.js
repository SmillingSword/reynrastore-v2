import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Import page components
import Home from '../pages/Home.vue';
import Products from '../pages/Products.vue';
import ProductDetail from '../pages/ProductDetail.vue';
import OrderTracking from '../pages/OrderTracking.vue';
import Payment from '../pages/Payment.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';

// Admin pages
import AdminLogin from '../pages/admin/Login.vue';
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminProducts from '../pages/admin/Products.vue';
import AdminOrders from '../pages/admin/Orders.vue';
import AdminSettings from '../pages/admin/Settings.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { title: 'Reynra Store - Game Top Up Terpercaya' }
  },
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: { title: 'Produk - Reynra Store' }
  },
  {
    path: '/products/:slug',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: { title: 'Detail Produk - Reynra Store' }
  },
  {
    path: '/order-tracking',
    name: 'OrderTracking',
    component: OrderTracking,
    meta: { title: 'Lacak Pesanan - Reynra Store' }
  },
  {
    path: '/orders/:orderNumber/track',
    name: 'OrderTrackingDetail',
    component: OrderTracking,
    meta: { title: 'Lacak Pesanan - Reynra Store' }
  },
  {
    path: '/payment/:orderNumber',
    name: 'Payment',
    component: Payment,
    meta: { title: 'Pembayaran - Reynra Store' }
  },
  {
    path: '/about',
    name: 'About',
    component: About,
    meta: { title: 'Tentang Kami - Reynra Store' }
  },
  {
    path: '/contact',
    name: 'Contact',
    component: Contact,
    meta: { title: 'Kontak - Reynra Store' }
  },
  
  // Admin login
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { 
      title: 'Admin Login - Reynra Store',
      requiresGuest: true 
    }
  },
  
  // Admin routes
  {
    path: '/admin',
    redirect: '/admin/dashboard'
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { 
      title: 'Dashboard Admin - Reynra Store', 
      requiresAuth: true 
    }
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: AdminProducts,
    meta: { 
      title: 'Kelola Produk - Reynra Store', 
      requiresAuth: true 
    }
  },
  {
    path: '/admin/orders',
    name: 'AdminOrders',
    component: AdminOrders,
    meta: { 
      title: 'Kelola Pesanan - Reynra Store', 
      requiresAuth: true 
    }
  },
  {
    path: '/admin/settings',
    name: 'AdminSettings',
    component: AdminSettings,
    meta: { 
      title: 'Pengaturan - Reynra Store', 
      requiresAuth: true 
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  // Update document title
  if (to.meta.title) {
    document.title = to.meta.title;
  }

  const authStore = useAuthStore();
  
  // Initialize auth if token exists
  if (authStore.token && !authStore.isAuthenticated) {
    await authStore.fetchUser();
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      next({ name: 'AdminLogin', query: { redirect: to.fullPath } });
      return;
    }
    
    // Check if user is admin
    if (!authStore.isAdmin) {
      next({ name: 'Home' });
      return;
    }
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'AdminDashboard' });
    return;
  }

  next();
});

export default router;
