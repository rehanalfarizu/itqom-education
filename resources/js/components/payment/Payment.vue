// File: PaymentResult.vue
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
      <!-- Loading State -->
      <div v-if="loading" class="space-y-4">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="text-gray-600">Memverifikasi pembayaran...</p>
      </div>

      <!-- Success State -->
      <div v-else-if="paymentStatus === 'success'" class="space-y-4">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Pembayaran Berhasil!</h2>
        <p class="text-gray-600">Terima kasih! Pembayaran Anda telah berhasil diproses.</p>
        <p class="text-sm text-gray-500">Anda sekarang memiliki akses ke kursus yang dibeli.</p>

        <div class="space-y-3 pt-4">
          <button @click="goToDashboard"
                  class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Ke Dashboard
          </button>
          <button @click="goToCourse"
                  v-if="courseId"
                  class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Mulai Belajar
          </button>
        </div>
      </div>

      <!-- Failure State -->
      <div v-else-if="paymentStatus === 'failed' || paymentStatus === 'expired'" class="space-y-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Pembayaran Gagal</h2>
        <p class="text-gray-600">Maaf, pembayaran Anda tidak dapat diproses.</p>
        <p class="text-sm text-gray-500 mb-2">{{ errorMessage || 'Silakan coba lagi atau hubungi customer service.' }}</p>

        <div class="space-y-3 pt-4">
          <button @click="retryPayment"
                  class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Coba Lagi
          </button>
          <button @click="goToDashboard"
                  class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Ke Dashboard
          </button>
        </div>
      </div>

      <!-- Pending State -->
      <div v-else-if="paymentStatus === 'pending'" class="space-y-4">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Pembayaran Pending</h2>
        <p class="text-gray-600">Pembayaran Anda sedang diproses.</p>
        <p class="text-sm text-gray-500">Kami akan memberitahu Anda ketika pembayaran dikonfirmasi.</p>

        <div class="space-y-3 pt-4">
          <button @click="checkStatus"
                  :disabled="checkingStatus"
                  class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition-colors disabled:opacity-50">
            {{ checkingStatus ? 'Mengecek...' : 'Cek Status' }}
          </button>
          <button @click="goToDashboard"
                  class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Ke Dashboard
          </button>
        </div>
      </div>

      <!-- Unknown/Error State -->
      <div v-else class="space-y-4">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Status Tidak Diketahui</h2>
        <p class="text-gray-600">Tidak dapat menentukan status pembayaran.</p>
        <p class="text-sm text-red-500" v-if="errorMessage">{{ errorMessage }}</p>

        <div class="space-y-3 pt-4">
          <button @click="checkStatus"
                  :disabled="checkingStatus"
                  class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition-colors disabled:opacity-50">
            {{ checkingStatus ? 'Mengecek...' : 'Cek Status' }}
          </button>
          <button @click="goToDashboard"
                  class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
            Ke Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PaymentResult',
  data() {
    return {
      loading: true,
      paymentStatus: null,
      orderId: null,
      courseId: null,
      checkingStatus: false,
      errorMessage: null
    };
  },
  async mounted() {
    // Get parameters from URL
    this.orderId = this.$route.query.order_id;
    this.courseId = this.$route.query.course_id;
    const resultType = this.$route.query.result;

    console.log('Payment result page loaded', {
      orderId: this.orderId,
      courseId: this.courseId,
      resultType: resultType,
      query: this.$route.query
    });

    if (this.orderId) {
      await this.checkPaymentStatus();
    } else {
      this.loading = false;
      this.paymentStatus = 'unknown';
      this.errorMessage = 'Order ID tidak ditemukan';
    }
  },
  methods: {
    async checkPaymentStatus() {
      try {
        this.loading = true;
        this.errorMessage = null;

        // Get auth token
        const token = localStorage.getItem('auth_token');
        if (!token) {
          throw new Error('Token autentikasi tidak ditemukan');
        }

        console.log('Checking payment status for order:', this.orderId);

        // Call the correct API endpoint with proper error handling
        const response = await axios.get(`/api/payment/status/${this.orderId}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          timeout: 30000 // 30 second timeout
        });

        console.log('Payment status response:', response.data);

        // Parse response - handle different response structures
        if (response.data) {
          // Extract payment status from different possible response formats
          this.paymentStatus = response.data.payment_status || 
                              response.data.status || 
                              (response.data.success ? 'success' : 'failed');
          
          // Extract course ID if available
          this.courseId = this.courseId || response.data.course_id;
          
          // Check for error messages
          if (response.data.error) {
            this.errorMessage = response.data.error;
          }
        } else {
          this.paymentStatus = 'unknown';
          this.errorMessage = 'Response data tidak valid';
        }

      } catch (error) {
        console.error('Error checking payment status:', error);

        // Handle different error types with better error messages
        if (error.response) {
          console.error('Response error:', error.response.data);
          
          const status = error.response.status;
          const data = error.response.data;
          
          if (status === 404) {
            this.paymentStatus = 'not_found';
            this.errorMessage = 'Pembayaran tidak ditemukan';
          } else if (status === 401) {
            this.paymentStatus = 'unauthorized';
            this.errorMessage = 'Sesi telah berakhir, silakan login kembali';
          } else if (status === 500) {
            this.paymentStatus = 'server_error';
            this.errorMessage = data.message || data.error || 'Terjadi kesalahan server';
          } else {
            this.paymentStatus = 'error';
            this.errorMessage = data.message || data.error || `Error ${status}`;
          }
        } else if (error.request) {
          console.error('Network error:', error.request);
          this.paymentStatus = 'network_error';
          this.errorMessage = 'Tidak dapat terhubung ke server';
        } else {
          console.error('Unknown error:', error.message);
          this.paymentStatus = 'unknown_error';
          this.errorMessage = error.message || 'Terjadi kesalahan tidak diketahui';
        }
      } finally {
        this.loading = false;
      }
    },

    async checkStatus() {
      this.checkingStatus = true;
      await this.checkPaymentStatus();
      this.checkingStatus = false;
    },

    goToDashboard() {
      this.$router.push('/dashboard');
    },

    goToCourse() {
      if (this.courseId) {
        this.$router.push(`/course/${this.courseId}/learn`);
      } else {
        this.$router.push('/my-courses');
      }
    },

    retryPayment() {
      if (this.courseId) {
        this.$router.push(`/course/${this.courseId}`);
      } else {
        this.$router.push('/courses');
      }
    }
  }
};
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>