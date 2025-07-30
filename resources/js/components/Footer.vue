<template>
  <footer class="bg-gray-800 text-white px-6 py-8 mt-12">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center">
      <div class="mb-4 md:mb-0">
        <h2 class="text-lg font-bold">ITQOM</h2>
        <p class="text-sm opacity-80">Jl. Menteng Raya No. 21, Jakarta Pusat, Indonesia 10340</p>
        <p class="text-sm opacity-80">info@itqom.co.id</p>
      </div>
      <div class="flex space-x-4">
        <a href="#" class="hover:underline">Privacy Policy</a>
        <a href="#" class="hover:underline">Terms of Service</a>
        <a href="#" class="hover:underline">Contact Us</a>
      </div>
    </div>

    <!-- Garis horizontal -->
    <hr class="border-t border-gray-600 my-4">

    <div class="max-w-7xl mx-auto flex flex-col md:flex-row text-left text-sm opacity-70 mt-4">
      &copy; {{ new Date().getFullYear() }} ITQOM. All rights reserved.
    </div>
  </footer>
</template>

<script>
export default {
  name: "FooterBar",
};
</script>

<style scoped>
footer {
  font-family: 'Inter', sans-serif;
  margin-top: 0 !important;
}
</style>
