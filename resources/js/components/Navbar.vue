<template>
  <header class="bg-[#564AB1] shadow-md fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto flex items-center justify-between px-4 py-3">
      <router-link to="/" class="logo flex items-center">
        <img src="/image/logo.png" alt="Logo" class="h-6" />
      </router-link>

      <button @click="toggleMenu"
        class="md:hidden flex flex-col justify-center items-center w-10 h-10 focus:outline-none">
        <span class="block w-6 h-0.5 bg-white mb-1"></span>
        <span class="block w-6 h-0.5 bg-white mb-1"></span>
        <span class="block w-6 h-0.5 bg-white"></span>
      </button>

      <ul
        :class="['nav-links', isMenuOpen ? 'flex' : 'hidden', 'md:flex', 'flex-col', 'md:flex-row', 'md:items-center', 'md:space-x-6', 'absolute', 'md:static', 'top-16', 'left-0', 'w-full', 'md:w-auto', 'bg-[#564AB1]', 'md:bg-transparent', 'shadow', 'md:shadow-none', 'px-6', 'md:px-0', 'py-4', 'md:py-0', 'transition-all', 'duration-300']">
        <li v-for="link in navLinks" :key="link.href">
          <router-link :to="link.href" class="flex items-center gap-2 hover:text-yellow-300"
            :class="[$route.path === link.href ? 'text-yellow-300 font-semibold active' : 'text-white']">
            <span v-html="link.icon"></span>
            {{ link.text }}
          </router-link>
        </li>

        <li class="md:hidden flex flex-col space-y-2 mt-2">
          <template v-if="isLoggedIn">
            <router-link to="/profil_pengguna" class="text-white hover:text-yellow-300 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              {{ displayName }}
            </router-link>
            <button @click="logout"
              class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
              Logout
            </button>
          </template>
          <template v-else>
            <router-link to="/login">
              <button class="login w-full">Login</button>
            </router-link>
            <router-link to="/register">
              <button class="signup w-full">Sign Up</button>
            </router-link>
          </template>
        </li>
      </ul>

      <div class="auth-buttons hidden md:flex space-x-3">
        <template v-if="isLoggedIn">
          <!-- Dropdown Menu untuk Desktop -->
          <div class="relative">
            <button @click.stop="toggleDropdown"
              class="flex items-center gap-2 text-white hover:text-yellow-300 focus:outline-none">
              <img :src="getAvatarUrl()" alt="Avatar" class="w-8 h-8 rounded-full border-2 border-yellow-300 object-cover">
              <span>{{ displayName }}</span>
              <svg class="w-4 h-4 text-white transition-transform duration-200" :class="{ 'rotate-180': openDropdown }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <transition enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95">
              <div v-show="openDropdown" v-click-outside="closeDropdown"
                class="origin-top-right absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200">
                <div class="flex flex-col items-center mb-2 px-4 pt-2">
                  <img :src="getAvatarUrl()" alt="Avatar" class="w-12 h-12 rounded-full border-2 border-purple-700 mb-2 object-cover">
                  <span class="font-bold text-gray-800 text-sm">{{ displayName }}</span>
                  <span class="text-xs text-gray-500">{{ userEmail }}</span>
                </div>
                <hr class="my-1 border-gray-200">
                <ul class="space-y-1 px-2 text-gray-700 text-sm">
                  <li>
                    <router-link to="/dashboard" @click.native="closeDropdown"
                      class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                      <span>🏠</span> My Dashboard
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/profil_pengguna" @click.native="closeDropdown"
                      class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                      <span>🧑‍💼</span> Profile Saya
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/change-password" @click.native="closeDropdown"
                      class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                      <span>🔑</span> Ganti Password
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/reward" @click.native="closeDropdown"
                      class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                      <span>🎁</span> Reward Saya
                    </router-link>
                  </li>
                  <li>
                    <a href="https://wa.me/6281227026268" target="_blank"
                      class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                      <span>📞</span> Hubungi Kami
                    </a>
                  </li>
                </ul>
                <hr class="my-1 border-gray-200">
                <button @click="logout"
                  class="block w-full text-center text-red-600 font-medium py-2 px-3 hover:bg-red-50 rounded-lg transition text-sm">
                  Logout
                </button>
              </div>
            </transition>
          </div>
        </template>
        <template v-else>
          <router-link to="/login">
            <button class="login">Login</button>
          </router-link>
          <router-link to="/register">
            <button class="signup">Sign Up</button>
          </router-link>
        </template>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Navbar',
  directives: {
    'click-outside': {
      bind(el, binding, vnode) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            vnode.context[binding.expression](event);
          }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
      },
      unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  },
  data() {
    return {
      isMenuOpen: false,
      openDropdown: false,
      isLoggedIn: false,
      userName: '', // dari tabel users
      userEmail: '', // dari tabel users
      displayName: '', // akan menggunakan fullname dari profile atau name dari users
      avatarUrl: '/image/hajisodikin.jpg', // default avatar
      userProfile: null, // data dari users_profile
      navLinks: [
        {
          text: 'Home',
          href: '/',
          icon: `<svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m-4 0h4"></path></svg>`
        },
        {
          text: 'Course',
          href: '/course',
          icon: `<svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M12 4v16m0 0H3"></path></svg>`
        },
        {
          text: 'About Us',
          href: '/about',
          icon: `<svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
        }
      ]
    };
  },
  watch: {
    '$route.path': 'checkLoginStatus'
  },
  created() {
    this.checkLoginStatus();
    window.addEventListener('localStorageUpdated', this.checkLoginStatus);
    // Listen untuk update profile
    window.addEventListener('profileUpdated', this.loadUserProfile);
  },
  unmounted() {
    window.removeEventListener('localStorageUpdated', this.checkLoginStatus);
    window.removeEventListener('profileUpdated', this.loadUserProfile);
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleDropdown() {
      this.openDropdown = !this.openDropdown;
    },
    closeDropdown() {
      this.openDropdown = false;
    },

    // Method untuk mengatur URL avatar
    getAvatarUrl() {
      if (this.userProfile && this.userProfile.avatar) {
        // Jika avatar sudah full URL
        if (this.userProfile.avatar.startsWith('http')) {
          return this.userProfile.avatar;
        }
        // Jika avatar adalah path relatif
        return `/storage/${this.userProfile.avatar}`;
      }
      return '/image/hajisodikin.jpg';
    },

    async checkLoginStatus() {
      const authToken = localStorage.getItem('authToken');
      const userData = localStorage.getItem('user');

      if (authToken && userData) {
        try {
          const user = JSON.parse(userData);
          this.isLoggedIn = true;
          this.userName = user.name;
          this.userEmail = user.email;

          // Load profile data
          await this.loadUserProfile();

        } catch (e) {
          console.error("Gagal mengurai data pengguna:", e);
          this.logout();
        }
      } else {
        this.resetUserData();
      }
    },

    async loadUserProfile() {
      const authToken = localStorage.getItem('authToken');
      if (!authToken) return;

      // Clear cache untuk memaksa reload data terbaru
      sessionStorage.removeItem('userProfile');

      try {
        // Fetch user profile from API
        const response = await fetch('/api/profile', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          this.userProfile = data.profile;

          // Cache the profile data
          sessionStorage.setItem('userProfile', JSON.stringify(data.profile));

          this.updateDisplayName();
          this.avatarUrl = this.getAvatarUrl();

        } else if (response.status === 404) {
          // Profile tidak ditemukan, gunakan data dari users table
          this.userProfile = null;
          this.displayName = this.userName;
          this.avatarUrl = '/image/hajisodikin.jpg';
        } else {
          throw new Error('Failed to fetch profile');
        }
      } catch (error) {
        console.error('Error loading user profile:', error);
        // Fallback ke data user basic
        this.userProfile = null;
        this.displayName = this.userName;
        this.avatarUrl = '/image/hajisodikin.jpg';
      }
    },

    updateDisplayName() {
      console.log('🔄 Updating display name...');
      console.log('📋 userProfile:', this.userProfile);
      console.log('📋 userName:', this.userName);

      // Set display name: prioritas fullname dari profile, fallback ke name dari users
      if (this.userProfile && this.userProfile.fullname) {
        this.displayName = this.userProfile.fullname;
        console.log('✅ Using fullname from profile:', this.userProfile.fullname);

        // Update localStorage user data juga
        this.updateLocalStorageUser();
      } else if (this.userProfile && this.userProfile.username) {
        this.displayName = this.userProfile.username;
        console.log('✅ Using username from profile:', this.userProfile.username);
      } else {
        this.displayName = this.userName;
        console.log('✅ Using name from users table:', this.userName);
      }

      console.log('🎯 Final displayName:', this.displayName);
    },

    updateLocalStorageUser() {
      try {
        const userData = localStorage.getItem('user');
        if (userData && this.userProfile) {
          const user = JSON.parse(userData);
          user.name = this.userProfile.fullname || user.name;
          user.email = this.userProfile.email || user.email;
          localStorage.setItem('user', JSON.stringify(user));
          console.log('💾 Updated localStorage user data:', user);
        }
      } catch (e) {
        console.error('Error updating localStorage user data:', e);
      }
    },

    resetUserData() {
      this.isLoggedIn = false;
      this.userName = '';
      this.userEmail = '';
      this.displayName = '';
      this.avatarUrl = '/image/hajisodikin.jpg';
      this.userProfile = null;
    },

    logout() {
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      this.resetUserData();
      this.openDropdown = false;
      this.$router.push('/');
      window.dispatchEvent(new Event('localStorageUpdated'));
    }
  }
};
</script>

<style scoped>
.login {
  @apply bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300;
}

.signup {
  @apply bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300;
}

.router-link-active {
  @apply text-yellow-300 font-semibold;
}

/* Animasi untuk avatar */
.avatar-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-5px);
  }
}

/* Animasi untuk dropdown */
.scale-enter-active {
  transition: all 0.2s ease-out;
}

.scale-leave-active {
  transition: all 0.1s ease-in;
}

.scale-enter-from,
.scale-leave-to {
  transform: scale(0.95);
  opacity: 0;
}
</style>
