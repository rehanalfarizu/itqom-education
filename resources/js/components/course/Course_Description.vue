<template>
  <div class="bg-gray-50 min-h-screen pt-24">
    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 py-6 text-center">
      <p>Memuat data kursus...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 py-6 text-center">
      <p class="text-red-600">{{ error }}</p>
      <button @click="fetchCourseData" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
        Coba Lagi
      </button>
    </div>

    <!-- Main Content -->
    <div v-else class="max-w-7xl mx-auto px-4 py-6 flex flex-col lg:flex-row gap-8">
      <!-- Bagian Kiri (Detail Kursus) -->
      <div class="flex-1 space-y-6">
        <!-- Header dengan Border -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow-sm">
          <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 mb-4 leading-tight">
            {{ courseData.title || 'Judul Tidak Tersedia' }}
          </h1>
          <div v-if="courseData.tag" class="mb-2">
            <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-4 py-2 rounded-full border-2 border-blue-300">
              {{ courseData.tag }}
            </span>
          </div>
        </div>

        <!-- Overview Section dengan Border yang Lebih Tegas -->
        <section class="bg-white border-3 border-purple-400 rounded-lg p-8 shadow-md">
          <div class="border-b-2 border-purple-200 pb-3 mb-4">
            <h3 class="text-xl font-bold text-[#5C52A8]">Overview</h3>
          </div>
          <div class="prose prose-lg text-gray-700 leading-relaxed border-l-4 border-purple-300 pl-4"
               v-html="courseData.overview || 'Tidak ada deskripsi'">
          </div>
        </section>
      </div>

      <!-- Bagian Kanan (Sidebar) -->
      <aside class="w-full lg:w-[380px] flex-shrink-0 space-y-6">
        <!-- Card Harga & Beli dengan Border yang Lebih Jelas -->
        <div class="bg-white border-3 border-purple-400 rounded-xl p-6 shadow-md">
          <div class="border-2 border-gray-200 rounded-lg p-2 mb-4">
            <img :src="courseImageUrl"
                 alt="Cover Kursus"
                 class="w-full h-40 object-cover rounded-lg border-2 border-purple-200">
          </div>

          <div class="mb-4 pb-4">
            <div class="text-3xl font-bold text-gray-800 mb-1">
              Rp {{ formatPrice(courseData.price) }}
            </div>
            <div v-if="courseData.price_discount"
                 class="text-lg line-through text-gray-400">
              Rp {{ formatPrice(courseData.price_discount) }}
            </div>
          </div>

          <!-- Status pembayaran jika sudah pernah beli -->
          <div v-if="hasPurchased" class="mb-4 p-3 bg-green-100 border border-green-300 rounded-lg">
            <p class="text-green-800 font-medium">✓ Anda sudah membeli kursus ini</p>
            <p v-if="purchaseDetails" class="text-sm text-green-600 mt-1">
              Dibeli pada: {{ formatDate(purchaseDetails.purchased_at) }}
            </p>
            <a :href="'/course/' + courseData.id"
               class="inline-block mt-2 bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
              Mulai Belajar
            </a>
          </div>

          <!-- Peringatan jika ada pembayaran pending -->
          <div v-if="pendingPaymentWarning" class="mb-4 p-3 bg-yellow-100 border border-yellow-300 rounded-lg">
            <p class="text-yellow-800 font-medium">⚠️ {{ pendingPaymentWarning }}</p>
          </div>

          <button @click="buyCourse"
                  :disabled="loading || paymentLoading || hasPurchased"
                  class="w-full bg-[#5C52A8] hover:bg-[#4a4190] text-white py-3 rounded-lg font-semibold text-base transition-colors duration-200 disabled:opacity-50 shadow-sm border-2 border-[#5C52A8] hover:border-[#4a4190]">
            {{ getButtonText() }}
          </button>
        </div>

        <!-- Card Instruktur dengan Border -->
        <div class="bg-white border-3 border-purple-400 rounded-xl p-6 shadow-md" v-if="courseData.instructor_name">
          <div class="border-b-2 border-purple-200 pb-3 mb-4">
            <h4 class="font-bold text-lg text-[#5C52A8]">Instruktur</h4>
          </div>
          <div class="flex items-center gap-4 border-l-4 border-purple-300 pl-4">
            <img :src="instructorImageUrl"
                 class="w-16 h-16 rounded-full object-cover border-3 border-[#5C52A8]">
            <div>
              <h4 class="font-bold text-lg text-gray-800">{{ courseData.instructor_name }}</h4>
              <p class="text-sm text-gray-600">
                {{ courseData.instructor_position || 'Pengajar' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Card Fitur Kursus dengan Border yang Lebih Jelas -->
        <div class="bg-white border-3 border-purple-400 rounded-xl p-6 shadow-md">
          <div class="border-b-2 border-purple-200 pb-3 mb-4">
            <h4 class="font-bold text-lg text-[#5C52A8]">This Course Include</h4>
          </div>
          <ul class="space-y-4">
            <li class="flex items-center gap-4 border-l-3 border-purple-200 pl-3 py-2" v-if="courseData.video_count">
              <div class="flex items-center justify-center w-8 h-8 bg-[#5C52A8] bg-opacity-10 rounded-full border-2 border-[#5C52A8] border-opacity-30">
                <i class="fas fa-video text-[#5C52A8] text-sm"></i>
              </div>
              <span class="text-gray-700 font-medium">{{ courseData.video_count }} Video</span>
            </li>
            <li class="flex items-center gap-4 border-l-3 border-purple-200 pl-3 py-2" v-if="courseData.duration">
              <div class="flex items-center justify-center w-8 h-8 bg-[#5C52A8] bg-opacity-10 rounded-full border-2 border-[#5C52A8] border-opacity-30">
                <i class="fas fa-clock text-[#5C52A8] text-sm"></i>
              </div>
              <span class="text-gray-700 font-medium">{{ courseData.duration }} Jam</span>
            </li>
            <li class="flex items-center gap-4 border-l-3 border-purple-200 pl-3 py-2">
              <div class="flex items-center justify-center w-8 h-8 bg-[#5C52A8] bg-opacity-10 rounded-full border-2 border-[#5C52A8] border-opacity-30">
                <i class="fas fa-file-alt text-[#5C52A8] text-sm"></i>
              </div>
              <span class="text-gray-700 font-medium">File Projek</span>
            </li>
            <li v-for="(feature, index) in courseData.features"
                :key="index"
                class="flex items-center gap-4 border-l-3 border-purple-200 pl-3 py-2">
              <div class="flex items-center justify-center w-8 h-8 bg-[#5C52A8] bg-opacity-10 rounded-full border-2 border-[#5C52A8] border-opacity-30">
                <i class="fas fa-check text-[#5C52A8] text-sm"></i>
              </div>
              <span class="text-gray-700 font-medium">{{ typeof feature === 'string' ? feature : feature.feature_name || 'Fitur' }}</span>
            </li>
          </ul>
        </div>
      </aside>
    </div>

    <!-- Modal Loading Payment -->
    <div v-if="paymentLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#5C52A8] mx-auto mb-4"></div>
        <p class="text-gray-700">Memproses pembayaran...</p>
      </div>
    </div>

    <!-- Modal Success Payment -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-md mx-4">
        <div class="text-6xl mb-4">🎉</div>
        <h3 class="text-2xl font-bold text-green-600 mb-2">Pembayaran Berhasil!</h3>
        <p class="text-gray-700 mb-4">
          Selamat! Anda berhasil membeli course <strong>"{{ courseData.title }}"</strong>
        </p>
        <p class="text-sm text-gray-600 mb-6">
          Anda akan diarahkan ke dashboard dalam {{ redirectCountdown }} detik...
        </p>
        <div class="flex gap-3 justify-center">
          <button @click="goToDashboard"
                  class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
            Ke Dashboard Sekarang
          </button>
          <button @click="startLearning"
                  class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
            Mulai Belajar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Error - Duplicate Purchase -->
    <div v-if="showErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-md mx-4">
        <div class="text-6xl mb-4">❌</div>
        <h3 class="text-2xl font-bold text-red-600 mb-2">Pembelian Gagal</h3>
        <p class="text-gray-700 mb-4">{{ errorMessage }}</p>
        <div class="flex gap-3 justify-center">
          <button @click="closeErrorModal"
                  class="bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-700 transition">
            Tutup
          </button>
          <button v-if="hasPurchased" @click="startLearning"
                  class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
            Mulai Belajar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CourseDescription',
  data() {
    return {
      courseData: {
        id: null,
        title: null,
        tag: null,
        overview: null,
        image_url: null,
        thumbnail: null,
        price: 0,
        price_discount: null,
        instructor_name: null,
        instructor_position: null,
        instructor_image_url: null,
        video_count: 0,
        duration: 0,
        features: []
      },
      loading: true,
      error: null,
      paymentLoading: false,
      hasPurchased: false,
      purchaseDetails: null,
      userProfile: null,
      showSuccessModal: false,
      showErrorModal: false,
      errorMessage: '',
      pendingPaymentWarning: null,
      redirectCountdown: 5,
      redirectTimer: null
    };
  },
  computed: {
    courseId() {
      return this.$route.params.id;
    },
    courseImageUrl() {
      const path = this.courseData.image_url;
      if (path) {
        if (path.startsWith('/storage/') || path.startsWith('http')) {
          return path;
        }
        return `/storage/${path}`;
      }
      return '/images/default.jpg';
    },
    instructorImageUrl() {
      const path = this.courseData.instructor_image_url;
      if (path) {
        if (path.startsWith('/storage/') || path.startsWith('http')) {
          return path;
        }
        return `/storage/${path}`;
      }
      return '/images/default-instructor.jpg';
    }
  },
  async mounted() {
    console.log('Course ID from route:', this.courseId);
    if (this.courseId) {
      await this.fetchCourseData();
      await this.checkUserProfile();
      await this.loadMidtransScript();
    } else {
      this.error = 'ID kursus tidak ditemukan';
      this.loading = false;
    }
  },
  beforeUnmount() {
    if (this.redirectTimer) {
      clearInterval(this.redirectTimer);
    }
  },
  methods: {
    formatPrice(value) {
      if (value === null || value === undefined) return '0';
      return new Intl.NumberFormat('id-ID').format(Number(value));
    },

    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getButtonText() {
      if (this.paymentLoading) return 'Memproses...';
      if (this.hasPurchased) return 'Sudah Dibeli';
      return 'Beli Course';
    },

    async fetchCourseData() {
      this.loading = true;
      this.error = null;

      try {
        console.log(`Fetching course data for ID: ${this.courseId}`);
        // Use course-description endpoint to get data directly from CourseDescription table
        const response = await axios.get(`https://itqom-platform-aa0ffce6a276.herokuapp.com/api/course-description/${this.courseId}`);
        console.log('Course data response:', response.data);

        if (response.status !== 200) {
          throw new Error(`Failed to fetch course data: ${response.status}`);
        }

        this.courseData = {
          id: response.data.data.id,
          title: response.data.data.title,
          tag: response.data.data.tag,
          overview: response.data.data.overview,
          image_url: response.data.data.image_url,
          thumbnail: response.data.data.thumbnail,
          price: response.data.data.price,
          price_discount: response.data.data.price_discount,
          instructor_name: response.data.data.instructor_name,
          instructor_position: response.data.data.instructor_position,
          instructor_image_url: response.data.data.instructor_image_url,
          video_count: response.data.data.video_count,
          duration: response.data.data.duration,
          features: response.data.data.features || []
        };

        console.log('Course data loaded:', this.courseData);
      } catch (error) {
        console.error('Error fetching course data:', error);
        this.error = error.response?.data?.message || 'Gagal memuat data kursus. Silakan coba lagi.';
      } finally {
        this.loading = false;
      }
    },

async checkUserProfile() {
  try {
    const token = localStorage.getItem('authToken');
    if (!token) {
      console.log('No token found');
      this.userProfile = null;
      return;
    }

    console.log('Token:', token.substring(0, 30) + '...');

    // TRY MULTIPLE ENDPOINTS FOR USER PROFILE
    let response = null;
    
    // Try /api/user first (Sanctum default)
    try {
      response = await axios.get('/api/user', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      console.log('User data from /api/user:', response.data);
    } catch (userError) {
      console.warn('Failed to get user from /api/user:', userError.response?.status);
      
      // Fallback to /api/profile
      try {
        response = await axios.get('/api/profile', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        console.log('User data from /api/profile:', response.data);
      } catch (profileError) {
        console.error('Failed to get user from both endpoints');
        throw profileError;
      }
    }

    if (response && response.data) {
      // Handle different response structures
      const userData = response.data.user || response.data;
      
      this.userProfile = {
        id: userData.id,
        name: userData.fullname || userData.name,
        email: userData.email,
      };

      console.log('User profile set:', this.userProfile);

      // Check if user already purchased this course
      await this.checkCoursePurchase();
    } else {
      console.error('Invalid profile response structure');
      this.userProfile = null;
    }
  } catch (error) {
    console.error('Error fetching profile:', error);

    if (error.response?.status === 401) {
      console.log('Token expired, clearing auth data');
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      this.userProfile = null;
    }
  }
},

    // NEW METHOD: Check course purchase using dedicated endpoint
    async checkCoursePurchase() {
      if (!this.userProfile || !this.userProfile.id || !this.courseData.id) {
        console.log('Missing user profile or course data for purchase check');
        return;
      }

      try {
        const response = await axios.post('https://itqom-platform-aa0ffce6a276.herokuapp.com/api/payment/check-course-purchase', {
          user_profile_id: this.userProfile.id,
          course_id: this.courseData.id
        }, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          }
        });

        console.log('Course purchase check response:', response.data);

        this.hasPurchased = response.data.has_purchased;
        this.purchaseDetails = response.data.payment_details;

        console.log('Has purchased:', this.hasPurchased);
        if (this.purchaseDetails) {
          console.log('Purchase details:', this.purchaseDetails);
        }

      } catch (error) {
        console.error('Error checking course purchase:', error);
        if (error.response?.status === 401) {
          console.log('Token expired, clearing localStorage');
          localStorage.removeItem('authToken');
          localStorage.removeItem('user');
          this.userProfile = null;
        }
      }
    },

    loadMidtransScript() {
      return new Promise((resolve, reject) => {
        if (window.snap) {
          resolve();
          return;
        }

        const script = document.createElement('script');
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', 'SB-Mid-client-YOUR_CLIENT_KEY');

        script.onload = () => {
          console.log('Midtrans script loaded');
          resolve();
        };

        script.onerror = () => {
          console.error('Failed to load Midtrans script');
          reject(new Error('Failed to load Midtrans script'));
        };

        document.head.appendChild(script);
      });
    },

async buyCourse() {
  // Check if user is logged in
  if (!this.userProfile) {
    await this.checkUserProfile();
  }

  if (!this.userProfile) {
    this.showError('Anda harus login terlebih dahulu untuk membeli kursus');
    setTimeout(() => {
      this.$router.push('/login');
    }, 2000);
    return;
  }

  // Double check if user already purchased
  if (this.hasPurchased) {
    this.showError('Anda sudah membeli kursus ini sebelumnya. Silakan langsung mulai belajar.');
    return;
  }

  this.paymentLoading = true;
  this.pendingPaymentWarning = null;

  try {
    const token = localStorage.getItem('authToken');
    
    // Prepare payment data with both user_id and user_profile_id for compatibility
    const paymentData = {
      course_id: this.courseData.id,
      user_profile_id: this.userProfile.id,
      user_id: this.userProfile.id, // Send both for backend compatibility
      amount: this.courseData.price
    };

    console.log('Creating payment with data:', paymentData);

    const response = await axios.post(
      'https://itqom-platform-aa0ffce6a276.herokuapp.com/api/payment/create-snap-token',
      paymentData,
      {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      }
    );

    if (response.data.success && response.data.snap_token) {
      window.snap.pay(response.data.snap_token, {
        onSuccess: (result) => this.handlePaymentSuccess(result),
        onPending: (result) => this.handlePaymentPending(result),
        onError: (error) => this.handlePaymentError(error),
        onClose: () => {
          console.log('Payment dialog closed');
          this.paymentLoading = false;
        }
      });
    } else {
      throw new Error(response.data.error || 'Failed to create payment token');
    }
  } catch (error) {
    console.error('Payment error:', error);
    this.paymentLoading = false;

    // Enhanced error handling
    if (error.response?.status === 401) {
      this.showError('Sesi Anda telah habis. Silakan login kembali.');
      setTimeout(() => {
        localStorage.removeItem('authToken');
        this.$router.push('/login');
      }, 2000);
    } else if (error.response?.status === 404) {
      this.showError('User tidak ditemukan di database. Silakan logout dan login kembali.');
    } else if (error.response?.data?.error_code === 'DUPLICATE_PURCHASE') {
      this.showError('Anda sudah membeli kursus ini sebelumnya. Silakan refresh halaman untuk melihat status terbaru.');
      setTimeout(async () => {
        await this.checkCoursePurchase();
      }, 1000);
    } else if (error.response?.data?.error_code === 'PENDING_PAYMENT_EXISTS') {
      this.pendingPaymentWarning = error.response.data.error;
      this.showError('Anda masih memiliki pembayaran yang sedang diproses untuk kursus ini.');
    } else {
      const errorMsg = error.response?.data?.error || 
                      error.response?.data?.message || 
                      error.message;
      this.showError('Terjadi kesalahan saat memproses pembayaran: ' + errorMsg);
      
      // Log debug info for troubleshooting
      console.error('Payment error details:', {
        status: error.response?.status,
        data: error.response?.data,
        user_profile: this.userProfile
      });
    }
  }
},

    async handlePaymentSuccess(result) {
      console.log('Payment success:', result);
      this.paymentLoading = false;

      try {
        // Manual check payment status dari Midtrans
        const statusResponse = await axios.get(
          `https://itqom-platform-aa0ffce6a276.herokuapp.com/api/payment/status/${result.order_id}`,
          {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            }
          }
        );

        console.log('Payment status check result:', statusResponse.data);

        // Check for duplicate error in response
        if (statusResponse.data.error && statusResponse.data.error.includes('already own this course')) {
          this.showError('Pembayaran berhasil tetapi terdeteksi bahwa Anda sudah memiliki kursus ini sebelumnya.');
          await this.checkCoursePurchase(); // Refresh purchase status
          return;
        }

        // Update status hasPurchased berdasarkan hasil check
        if (statusResponse.data.payment_status === 'success') {
          this.hasPurchased = true;
          this.showSuccessModal = true;
          this.startRedirectCountdown();
        } else {
          // Retry check setelah 2 detik
          setTimeout(async () => {
            await this.checkPaymentStatus(result.order_id);
          }, 2000);
        }

      } catch (error) {
        console.error('Error checking payment status:', error);
        // Tetap tampilkan modal sukses jika ada error
        this.showSuccessModal = true;
        this.startRedirectCountdown();
      }
    },

    async handlePaymentPending(result) {
      console.log('Payment pending:', result);
      this.paymentLoading = false;
      this.pendingPaymentWarning = 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.';
    },

    async checkPaymentStatus(orderId) {
      try {
        const response = await axios.get(
          `https://itqom-platform-aa0ffce6a276.herokuapp.com/api/payment/status/${orderId}`,
          {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            }
          }
        );

        if (response.data.payment_status === 'success') {
          this.hasPurchased = true;
          this.showSuccessModal = true;
          this.startRedirectCountdown();
        } else if (response.data.error && response.data.error.includes('already own this course')) {
          this.showError('Terdeteksi bahwa Anda sudah memiliki kursus ini sebelumnya.');
          await this.checkCoursePurchase(); // Refresh purchase status
        }
      } catch (error) {
        console.error('Status check error:', error);
      }
    },

    handlePaymentError(error) {
      console.error('Payment error:', error);
      this.paymentLoading = false;
      this.showError('Terjadi kesalahan dalam pembayaran. Silakan coba lagi.');
    },

    showError(message) {
      this.errorMessage = message;
      this.showErrorModal = true;
    },

    closeErrorModal() {
      this.showErrorModal = false;
      this.errorMessage = '';
    },

    startRedirectCountdown() {
      this.redirectCountdown = 5;
      this.redirectTimer = setInterval(() => {
        this.redirectCountdown--;
        if (this.redirectCountdown <= 0) {
          this.goToDashboard();
        }
      }, 1000);
    },

    goToDashboard() {
      if (this.redirectTimer) {
        clearInterval(this.redirectTimer);
      }
      this.showSuccessModal = false;

      // Redirect ke dashboard
      this.$router.push({
        path: '/dashboard',
        query: {
          payment_success: 'true',
          course_title: this.courseData.title
        }
      });
    },

    startLearning() {
      if (this.redirectTimer) {
        clearInterval(this.redirectTimer);
      }
      this.showSuccessModal = false;
      this.showErrorModal = false;

      // Redirect langsung ke course content
      this.$router.push(`/course/${this.courseData.id}`);
    }
  }
};
</script>

<style scoped>
.prose {
  max-width: none;
}

.prose h1, .prose h2, .prose h3, .prose h4 {
  color: #1f2937;
}

.prose p {
  margin-bottom: 1rem;
  line-height: 1.75;
}

.prose ul {
  margin: 1rem 0;
}

.prose li {
  margin-bottom: 0.5rem;
}

.border-3 {
  border-width: 3px;
}

.border-l-3 {
  border-left-width: 3px;
}

.border-l-4 {
  border-left-width: 4px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
