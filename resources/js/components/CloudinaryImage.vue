<template>
  <img
    :src="optimizedSrc"
    :alt="alt"
    :class="computedClass"
    @error="handleError"
    @load="handleLoad"
    :loading="lazy ? 'lazy' : 'eager'"
  />
</template>

<script>
export default {
  name: 'CloudinaryImage',
  props: {
    src: {
      type: String,
      default: ''
    },
    alt: {
      type: String,
      default: ''
    },
    width: {
      type: [Number, String],
      default: 400
    },
    height: {
      type: [Number, String],
      default: 300
    },
    quality: {
      type: String,
      default: 'auto'
    },
    format: {
      type: String,
      default: 'auto'
    },
    crop: {
      type: String,
      default: 'fill'
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
    optimizedSrc() {
      if (this.hasError) {
        return this.fallback;
      }

      if (!this.src) {
        return this.fallback;
      }

      // If it's a local storage path (starts with 'courses/' or 'storage/')
      if (this.src.startsWith('courses/') || this.src.startsWith('storage/')) {
        // Convert to full storage URL
        if (this.src.startsWith('courses/')) {
          return `/storage/${this.src}`;
        }
        return `/${this.src}`;
      }

      // If it's already a full URL (http/https), return as is
      if (this.src.startsWith('http')) {
        return this.src;
      }

      // If it's a Cloudinary public_id (no extension, no slashes at start)
      if (!this.src.includes('.') && !this.src.startsWith('/')) {
        // This would be a Cloudinary public_id, but since we're using local storage,
        // treat it as a local path
        return `/storage/courses/${this.src}`;
      }

      // Default: treat as local path
      return this.src.startsWith('/') ? this.src : `/${this.src}`;
    },
    computedClass() {
      let classes = this.class;

      if (!this.isLoaded) {
        classes += ' opacity-0';
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
