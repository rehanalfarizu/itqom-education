<template>
  <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen">

    <!-- HERO SECTION -->
    <div class="pt-24 pb-10">
      <div class="container mx-auto px-4 py-16">
        <div class="text-center mb-16">
          <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-6">
            Tentang <span class="gradient-text">Kami</span>
          </h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Platform pembelajaran inovatif yang dirancang khusus untuk generasi muda Indonesia.
            Kami berkomitmen untuk memberikan pengalaman belajar yang menyenangkan dan efektif.
          </p>
        </div>
      </div>
    </div>

    <!-- FEATURES SECTION -->
    <div class="container mx-auto px-4">
      <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div class="space-y-6">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">
              Mengapa Memilih <span class="gradient-text">Platform Kami?</span>
            </h2>
            <div class="space-y-6">
              <div v-for="(feature, index) in features"
                   :key="index"
                   class="flex items-start gap-4 p-4 rounded-xl feature-hover transition-all duration-200">
                <div :class="`w-12 h-12 ${feature.bgColor} rounded-full flex items-center justify-center flex-shrink-0`">
                  <svg class="w-6 h-6" :class="feature.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ feature.title }}</h3>
                  <p class="text-gray-600">{{ feature.description }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 rounded-3xl p-8 shadow-lg">
              <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="text-center">
                  <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                  </div>
                  <h3 class="text-2xl font-bold text-gray-800 mb-2">Platform Pembelajaran</h3>
                  <p class="text-gray-600">Dirancang khusus untuk generasi muda Indonesia</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TEAM SECTION -->
    <div class="container mx-auto px-4 py-16">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
          Tim <span class="gradient-text">Pengembang</span>
        </h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Bertemu dengan tim kreatif yang membuat platform ini menjadi kenyataan
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 max-w-7xl mx-auto">
        <div v-for="member in team"
             :key="member.id"
             class="bg-white rounded-2xl p-6 shadow-lg card-hover cursor-pointer"
             @click="openModal(member)">

          <!-- FOTO TIM -->
          <div class="relative mb-6">
            <div class="w-24 h-24 mx-auto relative">
              <img :src="member.photo"
                   :alt="member.name"
                   class="w-full h-full object-cover rounded-full border-4 border-indigo-200">
              <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
            </div>
          </div>

          <!-- NAMA DAN ROLE -->
          <div class="text-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ member.name }}</h3>
            <p class="text-indigo-600 font-semibold text-sm">{{ member.role }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ member.location }}</p>
          </div>

          <!-- SKILLS -->
          <div class="mb-4">
            <div class="flex flex-wrap gap-1 justify-center">
              <span v-for="skill in member.skills"
                    :key="skill"
                    class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- SOCIAL MEDIA -->
          <div class="flex justify-center gap-3">
            <a v-if="member.github"
               :href="member.github"
               target="_blank"
               class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-800 hover:text-white social-icon"
               @click.stop>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path>
              </svg>
            </a>

            <a v-if="member.instagram"
               :href="member.instagram"
               target="_blank"
               class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 hover:text-white social-icon"
               @click.stop>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
              </svg>
            </a>

            <a v-if="member.linkedin"
               :href="member.linkedin"
               target="_blank"
               class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-blue-600 hover:text-white social-icon"
               @click.stop>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL MEMBER - Perbaikan utama disini -->
    <transition name="modal">
      <div v-if="isModalOpen"
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
           @click="closeModal">
        <transition name="modal-content">
          <div class="bg-white rounded-2xl p-6 max-w-md w-full transform" @click.stop>
            <div class="text-center">
              <img :src="selectedMember.photo"
                   :alt="selectedMember.name"
                   class="w-20 h-20 rounded-full mx-auto mb-4 border-4 border-indigo-200">
              <h3 class="text-xl font-bold text-gray-800 mb-2">{{ selectedMember.name }}</h3>
              <p class="text-indigo-600 font-semibold mb-2">{{ selectedMember.role }}</p>
              <p class="text-gray-500 mb-4">{{ selectedMember.location }}</p>
              <div class="flex flex-wrap gap-2 justify-center mb-6">
                <span v-for="skill in selectedMember.skills"
                      :key="skill"
                      class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">
                  {{ skill }}
                </span>
              </div>

              <!-- Tambahan social media di modal -->
              <div class="flex justify-center gap-3 mb-6">
                <a v-if="selectedMember.github"
                   :href="selectedMember.github"
                   target="_blank"
                   class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-800 hover:text-white social-icon">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path>
                  </svg>
                </a>

                <a v-if="selectedMember.instagram"
                   :href="selectedMember.instagram"
                   target="_blank"
                   class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 hover:text-white social-icon">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                  </svg>
                </a>

              </div>

              <button @click="closeModal"
                      class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tutup
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AboutPage',
  data() {
    return {
      isModalOpen: false, // Ganti nama variable untuk lebih jelas
      selectedMember: {}, // Pastikan object kosong sebagai default
      features: [
        {
          title: "Pembelajaran Interaktif",
          description: "Materi yang disajikan dengan cara yang menarik dan mudah dipahami",
          icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
          bgColor: "bg-indigo-100",
          iconColor: "text-indigo-600"
        },
        {
          title: "Belajar Fleksibel",
          description: "Akses materi kapan saja dan di mana saja sesuai kecepatan Anda",
          icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
          bgColor: "bg-purple-100",
          iconColor: "text-purple-600"
        },
        {
          title: "Sistem Reward",
          description: "Dapatkan poin dan hadiah menarik untuk memotivasi pembelajaran",
          icon: "M13 10V3L4 14h7v7l9-11h-7z",
          bgColor: "bg-pink-100",
          iconColor: "text-pink-600"
        },
        {
          title: "Komunitas Aktif",
          description: "Bergabung dengan ribuan pelajar dari seluruh Indonesia",
          icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
          bgColor: "bg-green-100",
          iconColor: "text-green-600"
        }
      ],
      team: [
        {
          id: 1,
          name: "Dimas Dwi Setyawan",
          role: "Web Developer",
          location: "Bali, Indonesia",
          photo: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face",
          skills: ["Vue.js", "Node.js", "Tailwind"],
          github: "https://github.com/DimsDwi",
          instagram: "/#",
        },
        {
          id: 2,
          name: "Unzurna Zahira Wiyanda",
          role: "Web Developer",
          location: "Magelang, Indonesia",
          photo: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face",
          skills: ["Node.js", "Mysql", "Tailwind"],
          github: "https://github.com/KingEery",
          instagram: "https://instagram.com/unzurna",
        },
        {
          id: 3,
          name: "Muhammad Raihan Alfarizi",
          role: "Web Developer",
          location: "Klaten, Indonesia",
          photo: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face",
          skills: ["Mysql", "Vue,js", "Mysql"],
          github: "https://github.com/rehanalfarizu",
          instagram: "https://www.instagram.com/rehanalfarizi_/",
        },
        {
          id: 4,
          name: "Yafa Nanda Putra",
          role: "Web Developer",
          location: "Yogyakarta, Indonesia",
          photo: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face",
          skills: ["Vue.js", "Laravel", "Tailwind"],
          github: "https://github.com/yafaputra",
          instagram: "https://www.instagram.com/yafa.nanda/",
        },
        {
          id: 5,
          name: "Satrio Raja Nabil Rizky",
          role: "Web Developer",
          location: "Bandung, Indonesia",
          photo: "https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=400&h=400&fit=crop&crop=face",
          skills: ["Vue.js", "Mysql", "Laravel"],
          github: "https://github.com/LazyYoow",
          instagram: "https://www.instagram.com/satrio.rnr/",
        },
        {
          id: 6,
          name: "Rizqy Zulmiata Albar",
          role: "Web Developer",
          location: "Banten, Indonesia",
          photo: "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=400&fit=crop&crop=face",
          skills: ["Mysql", "Vue.js", "Laravel"],
          github: "https://github.com/albarstring",
          instagram: "https://www.instagram.com/rz_albar/",
        },
        {
          id: 7,
          name: "Dimas Bregas Khalis Sumarsana",
          role: "Web Developer",
          location: "Klaten, Indonesia",
          photo: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop&crop=face",
          skills: ["Vue.js", "Mysql", "Laravel"],
          github: "https://github.com/siBregas",
          instagram: "https://www.instagram.com/dmsbregas/?igsh=Z3NrNWE2MnR3bHZ4#",
        }
      ]
    }
  },
  methods: {
    // Ganti nama method dan tambahkan console.log untuk debugging
    openModal(member) {
      console.log('Opening modal for:', member.name); // Debug
      this.selectedMember = { ...member }; // Copy object untuk menghindari reference issues
      this.isModalOpen = true;
      // Prevent body scroll when modal is open
      document.body.style.overflow = 'hidden';
    },
    closeModal() {
      console.log('Closing modal'); // Debug
      this.isModalOpen = false;
      this.selectedMember = {};
      // Restore body scroll
      document.body.style.overflow = 'auto';
    }
  },
  // Cleanup when component is destroyed
  beforeUnmount() {
    document.body.style.overflow = 'auto';
  }
}
</script>

<style scoped>
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.card-hover {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.feature-hover {
  transition: background-color 0.2s ease;
}

.feature-hover:hover {
  background-color: rgba(255, 255, 255, 0.8);
}

.social-icon {
  transition: all 0.2s ease;
}

.social-icon:hover {
  transform: scale(1.1);
}

/* Modal transitions */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-content-enter-active, .modal-content-leave-active {
  transition: all 0.3s ease;
}

.modal-content-enter-from, .modal-content-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(-20px);
}
</style>
