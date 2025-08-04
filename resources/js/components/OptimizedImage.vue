<template>
  <img
    :src="imageSrc"
    :alt="alt"
    :class="computedClass"
    @error="handleError"
    @load="handleLoad"
    :loading="lazy ? 'lazy' : 'eager'"
  />
</template>

<script>
export default {
  name: 'OptimizedImage',
  props: {
    src: {
      type: String,
      default: ''
    },
    alt: {
      type: String,
      default: ''
    },
    fallback: {
      type: String,
      default: '/images/default-course.jpg'
    },
    lazy: {
      type: Boolean,
      default: true
    },
    class: {
      type: [String, Array, Object],
      default: ''
    }
  },
  data() {
    return {
      hasError: false,
      isLoaded: false
    }
  },
  computed: {
    imageSrc() {
      if (this.hasError || !this.src) {
        return this.fallback;
      }

      // Jika sudah berupa URL lengkap (http/https), gunakan langsung
      if (this.src.startsWith('http')) {
        return this.src;
      }

      // Jika dimulai dengan storage/, gunakan sebagai path storage Laravel
      if (this.src.startsWith('storage/')) {
        return `/${this.src}`;
      }

      // Jika dimulai dengan courses/, tambahkan prefix storage
      if (this.src.startsWith('courses/')) {
        return `/storage/${this.src}`;
      }

      // Default: gunakan sebagai path relatif
      return this.src.startsWith('/') ? this.src : `/${this.src}`;
    },
    computedClass() {
      let classes = this.class;

      if (!this.isLoaded) {
        classes += ' opacity-0 bg-gray-200 animate-pulse';
      } else {
        classes += ' opacity-100 transition-opacity duration-300';
      }

      return classes;
    }
  },
  methods: {
    handleError() {
      this.hasError = true;
      this.$emit('error');
    },
    handleLoad() {
      this.isLoaded = true;
      this.$emit('load');
    }
  },
  watch: {
    src() {
      this.hasError = false;
      this.isLoaded = false;
    }
  }
}
</script>

<style scoped>
img {
  transition: opacity 0.3s ease-in-out;
}
</style>
