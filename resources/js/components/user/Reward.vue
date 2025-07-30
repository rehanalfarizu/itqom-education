<template>
    <section class="bg-gradient-to-br from-yellow-50 to-green-50 min-h-screen py-16">
        <div class="max-w-3xl mx-auto px-6 pt-24">
            <div class="bg-white shadow rounded-xl p-8 mb-12">
                <h2 class="text-3xl font-extrabold text-green-700 mb-4 flex items-center gap-2">
                    🎁 Ambil Reward
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full ml-2">Cek Hadiah &
                        Voucher</span>
                </h2>
                <p class="text-gray-600 mb-6">Kumpulkan XP, selesaikan tantangan, dan tukarkan dengan hadiah seru atau
                    voucher spesial buat Gen Z! Jangan sampai kelewatan, reward terbatas!</p>
                <div>
                    <h3 class="font-bold text-green-700 mb-3">Reward & Voucher Terbaru</h3>
                    <ul class="space-y-4">
                        <li v-for="reward in rewards" :key="reward.id"
                            class="p-4 rounded-lg flex flex-col md:flex-row md:items-center justify-between"
                            :class="{
                                'bg-green-50': reward.status === 'available',
                                'bg-yellow-50': reward.status === 'soon',
                                'bg-purple-50': reward.status === 'claimed'
                            }">
                            <div>
                                <span class="font-semibold text-gray-800">{{ reward.name }}</span>
                                <div class="text-xs text-gray-500 mt-1">Butuh:
                                    <span class="font-bold"
                                        :class="{
                                            'text-green-700': reward.status === 'available',
                                            'text-yellow-700': reward.status === 'soon',
                                            'text-purple-700': reward.status === 'claimed'
                                        }">{{ reward.xpNeeded }}
                                        XP</span>
                                </div>
                            </div>
                            <button v-if="reward.status === 'available'" @click="claimReward(reward.id)"
                                class="bg-green-600 text-white text-xs px-4 py-2 rounded-full font-bold hover:bg-green-700 transition mt-2 md:mt-0">Klaim
                                Sekarang</button>
                            <span v-else-if="reward.status === 'soon'"
                                class="bg-yellow-200 text-yellow-700 text-xs px-3 py-1 rounded-full mt-2 md:mt-0">Segera
                                Hadir</span>
                            <button v-else-if="reward.status === 'claimed'"
                                class="bg-gray-400 text-white text-xs px-4 py-2 rounded-full font-bold cursor-not-allowed mt-2 md:mt-0"
                                disabled>Sudah Diambil</button>
                        </li>
                    </ul>
                </div>
                <div class="text-center mt-8">
                    <p class="text-sm text-gray-600 mb-2">Semakin aktif, semakin banyak reward yang bisa kamu klaim! 🚀</p>
                    <a href="#"
                        class="inline-block bg-green-500 text-white font-bold px-6 py-2 rounded-full shadow hover:scale-105 transition">Lihat
                        Semua Reward</a>
                </div>
            </div>
            <div class="text-center mt-8 animate-bounce">
                <a :href="dashboardRoute"
                    class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-800 text-white font-medium rounded-full hover:from-purple-700 hover:to-purple-900 transition-all shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'RewardPage',
    props: {
        // This prop will be passed from your Laravel Blade view
        // to provide the URL for the dashboard.
        dashboardRoute: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            rewards: [
                {
                    id: 1,
                    name: 'Voucher Belanja Tokopedia 50K',
                    xpNeeded: 1000,
                    status: 'available' // 'available', 'soon', 'claimed'
                },
                {
                    id: 2,
                    name: 'Merchandise Eksklusif ITQOM',
                    xpNeeded: 1500,
                    status: 'soon'
                },
                {
                    id: 3,
                    name: 'Voucher Kelas Premium',
                    xpNeeded: 800,
                    status: 'claimed'
                }
            ]
        };
    },
    methods: {
        claimReward(rewardId) {
            // In a real application, you'd make an API call here
            // to claim the reward and update the backend.
            // Example: axios.post('/api/claim-reward', { reward_id: rewardId })
            //   .then(response => {
            //     // Handle success, maybe update reward status from backend response
            //     alert('Selamat! Voucher berhasil diklaim.');
            //     const reward = this.rewards.find(r => r.id === rewardId);
            //     if (reward) {
            //         reward.status = 'claimed'; // Optimistic update, or based on API response
            //     }
            //   })
            //   .catch(error => {
            //     // Handle error (e.g., not enough XP, already claimed)
            //     alert('Gagal mengklaim reward: ' + (error.response.data.message || 'Terjadi kesalahan.'));
            //   });

            // For now, using a simple alert and local data update
            const reward = this.rewards.find(r => r.id === rewardId);
            if (reward && reward.status === 'available') {
                alert(`Selamat! ${reward.name} berhasil diklaim.`);
                reward.status = 'claimed'; // Update status in local state
            } else if (reward && reward.status === 'claimed') {
                alert('Voucher sudah pernah diambil!');
            } else if (reward && reward.status === 'soon') {
                alert('Reward ini segera hadir!');
            }
        }
    }
};
</script>

<style scoped>
/* Tailwind CSS classes are already included via your main CSS,
   but custom animations like animate-bounce can be defined here
   if they are not part of your Tailwind config. */
.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.transition {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>