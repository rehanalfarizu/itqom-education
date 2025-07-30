<template>
  <div>
    <Navbar v-if="!$route.meta.hideLayout" />

    <router-view />

    <FooterBar v-if="!$route.meta.hideLayout" />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import FooterBar from './components/Footer.vue';

export default {
  components: {
    Navbar,
    FooterBar,
  }
};
</script>

<style>
/* Gaya global atau lainnya */
</style>
