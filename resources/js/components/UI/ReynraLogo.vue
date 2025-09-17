<template>
  <div class="reynra-logo" :class="sizeClass">
    <!-- Use uploaded logo image -->
    <img 
      :src="logoSrc"
      :alt="brandName"
      :width="width"
      :height="height"
      class="logo-img"
      :class="{ 'logo-animated': animated }"
    />
    
    <!-- Text Logo (if horizontal and showText is true) -->
    <div v-if="showText && variant === 'horizontal'" class="logo-text" :class="textClass">
      <span class="brand-name">{{ brandName }}</span>
      <span v-if="tagline" class="tagline">{{ tagline }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ReynraLogo',
  props: {
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
    },
    width: {
      type: [String, Number],
      default: null
    },
    height: {
      type: [String, Number],
      default: null
    },
    variant: {
      type: String,
      default: 'icon', // 'icon', 'horizontal', 'vertical'
      validator: (value) => ['icon', 'horizontal', 'vertical'].includes(value)
    },
    primaryColor: {
      type: String,
      default: '#22c55e' // green-500
    },
    accentColor: {
      type: String,
      default: '#10b981' // green-600
    },
    backgroundColor: {
      type: String,
      default: 'transparent'
    },
    borderColor: {
      type: String,
      default: '#22c55e'
    },
    strokeWidth: {
      type: Number,
      default: 2
    },
    showBackground: {
      type: Boolean,
      default: false
    },
    brandName: {
      type: String,
      default: 'REYNRA STORE'
    },
    tagline: {
      type: String,
      default: 'Game Top Up Terpercaya'
    },
    animated: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    logoSrc() {
      return '/images/logo/logo-1024.png';
    },
    showText() {
      return this.variant === 'horizontal' || this.variant === 'vertical';
    },
    sizeClass() {
      const sizes = {
        xs: 'logo-xs',
        sm: 'logo-sm', 
        md: 'logo-md',
        lg: 'logo-lg',
        xl: 'logo-xl',
        '2xl': 'logo-2xl'
      };
      return `${sizes[this.size]} logo-${this.variant} ${this.animated ? 'logo-animated' : ''}`;
    },
    textClass() {
      return {
        'text-horizontal': this.variant === 'horizontal',
        'text-vertical': this.variant === 'vertical'
      };
    }
  }
};
</script>

<style scoped>
.reynra-logo {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-img {
  transition: all 0.3s ease;
  object-fit: contain;
  max-width: 100%;
  height: auto;
}

.logo-animated .logo-img {
  animation: logoGlow 3s ease-in-out infinite alternate;
}

/* Size variants for image */
.logo-xs .logo-img { width: 24px; height: 24px; }
.logo-sm .logo-img { width: 32px; height: 32px; }
.logo-md .logo-img { width: 40px; height: 40px; }
.logo-lg .logo-img { width: 48px; height: 48px; }
.logo-xl .logo-img { width: 64px; height: 64px; }
.logo-2xl .logo-img { width: 80px; height: 80px; }

/* Layout variants */
.logo-horizontal {
  flex-direction: row;
}

.logo-vertical {
  flex-direction: column;
  text-align: center;
}

/* Text styling */
.logo-text {
  display: flex;
  flex-direction: column;
}

.text-horizontal .logo-text {
  align-items: flex-start;
}

.text-vertical .logo-text {
  align-items: center;
}

.brand-name {
  font-weight: 800;
  font-size: 1.25rem;
  color: #ffffff;
  letter-spacing: 0.05em;
  line-height: 1.2;
}

.tagline {
  font-size: 0.75rem;
  color: #22c55e;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-top: 0.125rem;
}

/* Hover effects */
.reynra-logo:hover .logo-img {
  transform: scale(1.05);
  filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.5));
}

/* Animations */
@keyframes logoGlow {
  0% {
    filter: drop-shadow(0 0 5px rgba(34, 197, 94, 0.3));
  }
  100% {
    filter: drop-shadow(0 0 15px rgba(34, 197, 94, 0.8));
  }
}

@keyframes letterPulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

@keyframes snakeMove1 {
  0%, 100% {
    transform: translateX(0) translateY(0);
  }
  25% {
    transform: translateX(2px) translateY(-1px);
  }
  50% {
    transform: translateX(0) translateY(-2px);
  }
  75% {
    transform: translateX(-2px) translateY(-1px);
  }
}

@keyframes snakeMove2 {
  0%, 100% {
    transform: translateX(0) translateY(0);
  }
  25% {
    transform: translateX(-2px) translateY(1px);
  }
  50% {
    transform: translateX(0) translateY(2px);
  }
  75% {
    transform: translateX(2px) translateY(1px);
  }
}

@keyframes snakeHeadMove {
  0%, 100% {
    transform: translateX(0) translateY(0) rotate(0deg);
  }
  25% {
    transform: translateX(1px) translateY(-1px) rotate(2deg);
  }
  50% {
    transform: translateX(0) translateY(-1px) rotate(0deg);
  }
  75% {
    transform: translateX(-1px) translateY(0) rotate(-2deg);
  }
}

@keyframes eyeBlink {
  0%, 90%, 100% {
    opacity: 1;
  }
  95% {
    opacity: 0;
  }
}

@keyframes dragonMove {
  0%, 100% {
    transform: translateX(0) translateY(0);
  }
  25% {
    transform: translateX(1px) translateY(-1px);
  }
  50% {
    transform: translateX(0) translateY(-1px);
  }
  75% {
    transform: translateX(-1px) translateY(0);
  }
}

@keyframes dragonHeadMove {
  0%, 100% {
    transform: translateX(0) translateY(0) rotate(0deg);
  }
  25% {
    transform: translateX(1px) translateY(-1px) rotate(3deg);
  }
  50% {
    transform: translateX(0) translateY(-1px) rotate(0deg);
  }
  75% {
    transform: translateX(-1px) translateY(0) rotate(-3deg);
  }
}

@keyframes dragonTailMove {
  0%, 100% {
    transform: translateX(0) translateY(0) rotate(0deg);
  }
  25% {
    transform: translateX(-1px) translateY(1px) rotate(-2deg);
  }
  50% {
    transform: translateX(0) translateY(1px) rotate(0deg);
  }
  75% {
    transform: translateX(1px) translateY(0) rotate(2deg);
  }
}

@keyframes dragonEyeGlow {
  0%, 100% {
    opacity: 0.8;
    filter: drop-shadow(0 0 2px #ff0000);
  }
  50% {
    opacity: 1;
    filter: drop-shadow(0 0 4px #ff0000);
  }
}

@keyframes scalesShimmer {
  0% {
    opacity: 0.7;
    stroke-width: 1.2;
  }
  100% {
    opacity: 1;
    stroke-width: 1.5;
  }
}

@keyframes spikesGlow {
  0% {
    opacity: 0.6;
    stroke-width: 1;
  }
  100% {
    opacity: 1;
    stroke-width: 1.3;
  }
}

@keyframes hornsMove {
  0%, 100% {
    transform: rotate(0deg);
  }
  50% {
    transform: rotate(2deg);
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .brand-name {
    font-size: 1rem;
  }
  
  .tagline {
    font-size: 0.625rem;
  }
}
</style>
