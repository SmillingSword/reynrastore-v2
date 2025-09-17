<template>
  <div id="app" class="min-h-screen bg-gray-900">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
}
</script>

<style>
/* Global styles */
body {
  font-family: 'Inter', sans-serif;
  background-color: #111827;
  color: #f9fafb;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #1f2937;
}

::-webkit-scrollbar-thumb {
  background: #22c55e;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #16a34a;
}

/* Gaming-style selection */
::selection {
  background-color: #22c55e;
  color: #000;
}

/* Smooth transitions */
* {
  transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}
</style>
