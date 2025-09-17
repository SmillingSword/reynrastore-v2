<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <transition-group
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform translate-x-full opacity-0"
      enter-to-class="transform translate-x-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform translate-x-0 opacity-100"
      leave-to-class="transform translate-x-full opacity-0"
    >
      <div
        v-for="notification in appStore.notifications"
        :key="notification.id"
        class="max-w-sm w-full bg-gray-800 border border-gray-700 rounded-lg shadow-lg overflow-hidden"
        :class="getNotificationClass(notification.type)"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <component
                :is="getNotificationIcon(notification.type)"
                class="w-6 h-6"
                :class="getIconClass(notification.type)"
              />
            </div>
            <div class="ml-3 w-0 flex-1">
              <p class="text-sm font-medium text-white">
                {{ notification.title }}
              </p>
              <p class="mt-1 text-sm text-gray-300">
                {{ notification.message }}
              </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="appStore.removeNotification(notification.id)"
                class="bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-200 focus:outline-none"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
        <div
          class="h-1 bg-gradient-to-r"
          :class="getProgressBarClass(notification.type)"
        ></div>
      </div>
    </transition-group>
  </div>
</template>

<script>
import { useAppStore } from '../../stores';
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline';

export default {
  name: 'NotificationContainer',
  components: {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    InformationCircleIcon,
    XMarkIcon,
  },
  setup() {
    const appStore = useAppStore();
    
    const getNotificationIcon = (type) => {
      const icons = {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        error: XCircleIcon,
        info: InformationCircleIcon,
      };
      return icons[type] || InformationCircleIcon;
    };
    
    const getNotificationClass = (type) => {
      const classes = {
        success: 'border-l-4 border-l-green-500',
        warning: 'border-l-4 border-l-yellow-500',
        error: 'border-l-4 border-l-red-500',
        info: 'border-l-4 border-l-blue-500',
      };
      return classes[type] || classes.info;
    };
    
    const getIconClass = (type) => {
      const classes = {
        success: 'text-green-400',
        warning: 'text-yellow-400',
        error: 'text-red-400',
        info: 'text-blue-400',
      };
      return classes[type] || classes.info;
    };
    
    const getProgressBarClass = (type) => {
      const classes = {
        success: 'from-green-500 to-green-600',
        warning: 'from-yellow-500 to-yellow-600',
        error: 'from-red-500 to-red-600',
        info: 'from-blue-500 to-blue-600',
      };
      return classes[type] || classes.info;
    };
    
    return {
      appStore,
      getNotificationIcon,
      getNotificationClass,
      getIconClass,
      getProgressBarClass,
    };
  },
};
</script>
