<template>
    <div class="pt-16">
        <header class="bg-[#564AB1] shadow-md fixed top-0 left-0 w-full z-50">
            <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <!-- Logo -->
            <router-link to="/" class="logo flex items-center">
                <img src="/image/logo.png" alt="Logo" class="h-8 w-auto" />
            </router-link>

            <!-- Mobile Menu Button -->
            <button
                @click="toggleMenu"
                class="md:hidden flex flex-col justify-center items-center w-10 h-10 focus:outline-none relative z-60"
                :class="{ 'active': isMenuOpen }"
            >
                <span
                class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out"
                :class="{ 'rotate-45 translate-y-2': isMenuOpen, 'mb-1': !isMenuOpen }"
                ></span>
                <span
                class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out"
                :class="{ 'opacity-0': isMenuOpen, 'mb-1': !isMenuOpen }"
                ></span>
                <span
                class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out"
                :class="{ '-rotate-45 -translate-y-2': isMenuOpen }"
                ></span>
            </button>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <ul class="flex items-center space-x-6">
                <li v-for="link in navLinks" :key="link.href">
                    <router-link
                    :to="link.href"
                    class="text-white hover:text-yellow-300 transition-colors duration-200 font-medium"
                    :class="[$route.path === link.href ? 'text-yellow-300 font-semibold' : '']"
                    >
                    {{ link.text }}
                    </router-link>
                </li>
                </ul>
            </nav>

            <!-- Desktop Auth Buttons -->
            <div class="auth-buttons hidden md:flex items-center space-x-3">
                <template v-if="isLoggedIn">
                <!-- Desktop User Dropdown -->
                <div class="relative">
                    <button
                    @click.stop="toggleDropdown"
                    class="flex items-center gap-2 text-white hover:text-yellow-300 focus:outline-none transition-colors duration-200"
                    >
                    <img
                        :src="getAvatarUrl()"
                        alt="Avatar"
                        class="w-8 h-8 rounded-full border-2 border-yellow-300 object-cover"
                    >
                    <span class="hidden lg:block">{{ displayName }}</span>
                    <svg
                        class="w-4 h-4 text-white transition-transform duration-200"
                        :class="{ 'rotate-180': openDropdown }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    </button>

                    <!-- Desktop Dropdown Menu -->
                    <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95 translate-y-1"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-1"
                    >
                    <div
                        v-show="openDropdown"
                        v-click-outside="closeDropdown"
                        class="origin-top-right absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200"
                    >
                        <div class="flex flex-col items-center mb-2 px-4 pt-2">
                        <img :src="getAvatarUrl()" alt="Avatar" class="w-12 h-12 rounded-full border-2 border-purple-700 mb-2 object-cover">
                        <span class="font-bold text-gray-800 text-sm">{{ displayName }}</span>
                        <span class="text-xs text-gray-500">{{ userEmail }}</span>
                        </div>
                        <hr class="my-1 border-gray-200">
                        <ul class="space-y-1 px-2 text-gray-700 text-sm">
                        <li v-for="item in dropdownItems" :key="item.href">
                            <router-link
                            v-if="item.href.startsWith('/')"
                            :to="item.href"
                            @click.native="closeDropdown"
                            class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200"
                            >
                            <span>{{ item.icon }}</span>
                            {{ item.text }}
                            </router-link>
                            <a
                            v-else
                            :href="item.href"
                            target="_blank"
                            class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200"
                            >
                            <span>{{ item.icon }}</span>
                            {{ item.text }}
                            </a>
                        </li>
                        </ul>
                        <hr class="my-1 border-gray-200">
                        <button
                        @click="logout"
                        class="block w-full text-center text-red-600 font-medium py-2 px-3 hover:bg-red-50 rounded-lg transition-colors duration-200 text-sm"
                        >
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

            <!-- Mobile Menu Overlay -->
            <transition
            enter-active-class="transition-opacity ease-linear duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
            >
            <div
                v-if="isMenuOpen"
                @click="closeMenu"
                class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
            ></div>
            </transition>

            <!-- Mobile Menu -->
            <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform translate-x-full"
            enter-to-class="transform translate-x-0"
            leave-active-class="transition ease-in duration-300"
            leave-from-class="transform translate-x-0"
            leave-to-class="transform translate-x-full"
            >
            <div
                v-if="isMenuOpen"
                class="fixed top-0 right-0 h-full w-80 max-w-[90vw] bg-white shadow-xl z-50 md:hidden overflow-y-auto"
            >
                <!-- Mobile Menu Header -->
                <div class="bg-[#564AB1] p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img
                    :src="getAvatarUrl()"
                    alt="Avatar"
                    class="w-10 h-10 rounded-full border-2 border-yellow-300 object-cover"
                    v-if="isLoggedIn"
                    >
                    <div v-if="isLoggedIn">
                    <div class="text-white font-semibold text-sm">{{ displayName }}</div>
                    <div class="text-yellow-200 text-xs">{{ userEmail }}</div>
                    </div>
                    <div v-else class="text-white font-semibold">Menu</div>
                </div>
                <button
                    @click="closeMenu"
                    class="text-white hover:text-yellow-300 transition-colors duration-200"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                </div>

                <!-- Mobile Menu Content -->
                <div class="p-4">
                <!-- Navigation Links -->
                <nav class="mb-6">
                    <ul class="space-y-2">
                    <li v-for="link in navLinks" :key="link.href">
                        <router-link
                        :to="link.href"
                        @click.native="closeMenu"
                        class="flex items-center gap-3 p-3 rounded-lg transition-colors duration-200"
                        :class="[
                            $route.path === link.href
                            ? 'bg-purple-100 text-purple-700 font-semibold'
                            : 'text-gray-700 hover:bg-gray-100'
                        ]"
                        >
                        <span v-html="link.icon"></span>
                        {{ link.text }}
                        </router-link>
                    </li>
                    </ul>
                </nav>

                <!-- User Menu (if logged in) -->
                <div v-if="isLoggedIn" class="mb-6">
                    <h3 class="text-gray-500 text-xs uppercase tracking-wide font-semibold mb-3">Akun Saya</h3>
                    <ul class="space-y-2">
                    <li v-for="item in dropdownItems" :key="item.href">
                        <router-link
                        v-if="item.href.startsWith('/')"
                        :to="item.href"
                        @click.native="closeMenu"
                        class="flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200"
                        >
                        <span>{{ item.icon }}</span>
                        {{ item.text }}
                        </router-link>
                        <a
                        v-else
                        :href="item.href"
                        target="_blank"
                        class="flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200"
                        >
                        <span>{{ item.icon }}</span>
                        {{ item.text }}
                        </a>
                    </li>
                    </ul>
                </div>

                <!-- Auth Buttons -->
                <div class="border-t pt-4">
                    <template v-if="isLoggedIn">
                    <button
                        @click="logout"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
                    >
                        Logout
                    </button>
                    </template>
                    <template v-else>
                    <div class="space-y-3">
                        <router-link to="/login" @click.native="closeMenu">
                        <button class="login w-full py-3">Login</button>
                        </router-link>
                        <router-link to="/register" @click.native="closeMenu">
                        <button class="signup w-full py-3">Sign Up</button>
                        </router-link>
                    </div>
                    </template>
                </div>
                </div>
            </div>
            </transition>
        </header>
    </div>
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
      userName: '',
      userEmail: '',
      displayName: '',
      avatarUrl: '/image/hajisodikin.jpg',
      userProfile: null,
      navLinks: [
        {
          text: 'Home',
          href: '/',
          icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m-4 0h4"></path></svg>`
        },
        {
          text: 'Course',
          href: '/course',
          icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>`
        },
        {
          text: 'About Us',
          href: '/about',
          icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
        }
      ],
      dropdownItems: [
        { text: 'My Dashboard', href: '/dashboard', icon: '🏠' },
        { text: 'Profile Saya', href: '/profil_pengguna', icon: '🧑‍💼' },
        { text: 'Ganti Password', href: '/change-password', icon: '🔑' },
        { text: 'Reward Saya', href: '/reward', icon: '🎁' },
        { text: 'Hubungi Kami', href: 'https://wa.me/6281227026268', icon: '📞' }
      ]
    };
  },
  watch: {
    '$route.path': 'checkLoginStatus',
    isMenuOpen(newVal) {
      // Prevent body scroll when mobile menu is open
      if (newVal) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  },
  created() {
    this.checkLoginStatus();
    window.addEventListener('localStorageUpdated', this.checkLoginStatus);
    window.addEventListener('profileUpdated', this.loadUserProfile);
  },
  beforeUnmount() {
    window.removeEventListener('localStorageUpdated', this.checkLoginStatus);
    window.removeEventListener('profileUpdated', this.loadUserProfile);
    // Ensure body scroll is restored
    document.body.style.overflow = '';
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
      // Close dropdown when opening mobile menu
      if (this.isMenuOpen) {
        this.openDropdown = false;
      }
    },
    closeMenu() {
      this.isMenuOpen = false;
    },
    toggleDropdown() {
      this.openDropdown = !this.openDropdown;
      // Close mobile menu when opening dropdown
      if (this.openDropdown) {
        this.isMenuOpen = false;
      }
    },
    closeDropdown() {
      this.openDropdown = false;
    },

    getAvatarUrl() {
      if (this.userProfile && this.userProfile.avatar) {
        if (this.userProfile.avatar.startsWith('http')) {
          return this.userProfile.avatar;
        }
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

      sessionStorage.removeItem('userProfile');

      try {
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
          sessionStorage.setItem('userProfile', JSON.stringify(data.profile));
          this.updateDisplayName();
          this.avatarUrl = this.getAvatarUrl();
        } else if (response.status === 404) {
          this.userProfile = null;
          this.displayName = this.userName;
          this.avatarUrl = '/image/hajisodikin.jpg';
        } else {
          throw new Error('Failed to fetch profile');
        }
      } catch (error) {
        console.error('Error loading user profile:', error);
        this.userProfile = null;
        this.displayName = this.userName;
        this.avatarUrl = '/image/hajisodikin.jpg';
      }
    },

    updateDisplayName() {
      if (this.userProfile && this.userProfile.fullname) {
        this.displayName = this.userProfile.fullname;
        this.updateLocalStorageUser();
      } else if (this.userProfile && this.userProfile.username) {
        this.displayName = this.userProfile.username;
      } else {
        this.displayName = this.userName;
      }
    },

    updateLocalStorageUser() {
      try {
        const userData = localStorage.getItem('user');
        if (userData && this.userProfile) {
          const user = JSON.parse(userData);
          user.name = this.userProfile.fullname || user.name;
          user.email = this.userProfile.email || user.email;
          localStorage.setItem('user', JSON.stringify(user));
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
      this.isMenuOpen = false;
      this.$router.push('/');
      window.dispatchEvent(new Event('localStorageUpdated'));
    }
  }
};
</script>

<style scoped>
.login {
  background-color: #2563eb;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.login:hover {
  background-color: #1d4ed8;
}

.signup {
  background-color: #16a34a;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.signup:hover {
  background-color: #15803d;
}

.router-link-active {
  color: #fde047;
  font-weight: 600;
}

/* Mobile menu animations */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: transform 0.3s ease-in-out;
}

.mobile-menu-enter-from {
  transform: translateX(100%);
}

.mobile-menu-leave-to {
  transform: translateX(100%);
}

/* Hamburger animation */
.hamburger-line {
  transition: all 0.3s ease-in-out;
}

/* Ensure mobile menu is above everything */
.mobile-menu {
  z-index: 9999;
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, transform, opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Touch-friendly sizing for mobile */
@media (max-width: 768px) {
  .touch-target {
    min-height: 44px;
    min-width: 44px;
  }
}
</style>
