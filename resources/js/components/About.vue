<template>
  <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen pt-20">
    <!-- HERO SECTION -->
    <div class="container mx-auto px-4 py-16">
      <div class="text-center mb-16">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-6">
          Tentang <span class="text-indigo-600">Kami</span>
        </h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
          Platform pembelajaran inovatif yang dirancang khusus untuk generasi muda Indonesia. 
          Kami berkomitmen untuk memberikan pengalaman belajar yang menyenangkan dan efektif.
        </p>
      </div>
    </div>

    <!-- SECTION: ABOUT -->
    <div class="container mx-auto px-4 py-16">
      <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div class="space-y-6">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">
              Mengapa Memilih <span class="text-indigo-600">Platform Kami?</span>
            </h2>
            <div class="space-y-4">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-800 mb-2">Pembelajaran Interaktif</h3>
                  <p class="text-gray-600">Materi yang disajikan dengan cara yang menarik dan mudah dipahami</p>
                </div>
              </div>
              
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-800 mb-2">Belajar Fleksibel</h3>
                  <p class="text-gray-600">Akses materi kapan saja dan di mana saja sesuai kecepatan Anda</p>
                </div>
              </div>
              
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-800 mb-2">Sistem Reward</h3>
                  <p class="text-gray-600">Dapatkan poin dan hadiah menarik untuk memotivasi pembelajaran</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="relative">
            <div class="bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 rounded-3xl p-8 shadow-2xl">
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

    <!-- SECTION: TEAM -->
    <div class="container mx-auto px-4 py-16">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
          Tim <span class="text-indigo-600">Pengembang</span>
        </h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Bertemu dengan tim kreatif yang membuat platform ini menjadi kenyataan
        </p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div v-for="member in team" :key="member.id" 
             class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
          
          <!-- FOTO TIM -->
          <div class="relative mb-6">
            <div class="w-48 h-48 mx-auto relative">
              <div class="absolute inset-0 bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 rounded-full transform rotate-6 group-hover:rotate-12 transition-transform duration-300"></div>
              <img :src="member.photo" 
                   :alt="member.name"
                   class="relative w-full h-full object-cover rounded-full border-4 border-white shadow-lg">
            </div>
          </div>
          
          <!-- NAMA DAN ROLE -->
          <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ member.name }}</h3>
            <p class="text-indigo-600 font-semibold">{{ member.role || 'Developer' }}</p>
          </div>
          
          <!-- SOCIAL MEDIA -->
          <div class="flex justify-center gap-4">
            <a v-if="member.github" 
               :href="member.github" 
               target="_blank" 
               title="GitHub"
               class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center hover:bg-indigo-100 transition-colors duration-300 group">
              <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png"
                   class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" alt="GitHub">
            </a>
            
            <a v-if="member.instagram" 
               :href="member.instagram" 
               target="_blank" 
               title="Instagram"
               class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center hover:bg-indigo-100 transition-colors duration-300 group">
              <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png"
                   class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" alt="Instagram">
                   class="w-7 h-7" alt="Instagram">
            </a>
          </div>
        </div>
        
        <div v-if="team.length === 0" class="col-span-full text-center text-gray-500">
          Belum ada data tim.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'About',
  data() {
    return {
      about: null,
      team: []
    }
  },
  async mounted() {
    try {
      // Fetch about data
      const aboutResponse = await fetch('/api/about');
      if (aboutResponse.ok) {
        this.about = await aboutResponse.json();
      }
      
      // Fetch team data
      const teamResponse = await fetch('/api/team');
      if (teamResponse.ok) {
        this.team = await teamResponse.json();
      }
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  }
}
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
