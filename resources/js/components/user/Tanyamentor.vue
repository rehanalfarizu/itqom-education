<template>
    <section class="bg-gradient-to-br from-purple-50 to-indigo-50 min-h-screen py-16">
        <div class="max-w-4xl mx-auto px-6 pt-24">
            <!-- Header -->
            <div class="bg-white shadow rounded-xl p-8 mb-6">
                <h2 class="text-3xl font-extrabold text-purple-800 mb-4 flex items-center gap-2">
                    💬 Tanya Mentor
                    <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full ml-2">Live Chat</span>
                </h2>
                <p class="text-gray-600 mb-4">Punya pertanyaan soal materi, tugas, atau karir IT? Yuk, chat langsung dengan mentor kami!</p>
                
                <!-- Mentor Status -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-green-600 font-medium">Mentor Online</span>
                    </div>
                    <span class="text-sm text-gray-500">• Response time: 2-5 menit</span>
                </div>
            </div>

            <!-- Chat Interface -->
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <!-- Chat Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-purple-600 flex items-center justify-center text-white font-bold">M</div>
                        <div>
                            <h3 class="font-bold">Mentor Tim</h3>
                            <p class="text-sm opacity-90">Full Stack Developer & Coding Instructor</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-xs bg-green-500 px-2 py-1 rounded-full">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="h-96 overflow-y-auto p-4 space-y-4" ref="chatContainer">
                    <!-- Welcome Message (jika tidak ada pesan) -->
                    <div v-if="!messages || messages.length === 0" class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full mt-1 bg-purple-600 flex items-center justify-center text-white text-xs font-bold">M</div>
                        <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                            <p class="text-sm text-gray-800">Halo! 👋 Saya Mentor Tim, siap membantu kamu dengan pertanyaan seputar coding, karir IT, atau materi pembelajaran. Apa yang bisa saya bantu?</p>
                            <span class="text-xs text-gray-500 mt-1 block">{{ formatTime(new Date()) }}</span>
                        </div>
                    </div>

                    <!-- User Messages -->
                    <div v-for="message in messages" :key="message.id" 
                         :class="message.sender === 'user' ? 'flex justify-end' : 'flex items-start gap-3'">
                        <div v-if="message.sender === 'mentor'" class="w-8 h-8 rounded-full mt-1 bg-purple-600 flex items-center justify-center text-white text-xs font-bold">M</div>
                        <div :class="message.sender === 'user' 
                                   ? 'bg-purple-600 text-white rounded-lg p-3 max-w-xs' 
                                   : 'bg-gray-100 rounded-lg p-3 max-w-xs'">
                            <p class="text-sm">{{ message.text }}</p>
                            <span :class="message.sender === 'user' ? 'text-xs opacity-75 mt-1 block' : 'text-xs text-gray-500 mt-1 block'">
                                {{ formatTime(message.timestamp) }}
                            </span>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div v-if="isTyping" class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full mt-1 bg-purple-600 flex items-center justify-center text-white text-xs font-bold">M</div>
                        <div class="bg-gray-100 rounded-lg p-3">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="border-t p-4">
                    <form @submit.prevent="sendMessage" class="flex gap-3">
                        <input 
                            v-model="newMessage" 
                            type="text" 
                            placeholder="Ketik pertanyaan kamu di sini..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :disabled="isTyping"
                        >
                        <button 
                            type="submit" 
                            :disabled="!newMessage.trim() || isTyping"
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-xl p-6 mt-6">
                <h3 class="font-bold text-purple-700 mb-4">Pertanyaan Cepat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button 
                        v-for="quickQuestion in quickQuestions" 
                        :key="quickQuestion.id"
                        @click="sendQuickQuestion(quickQuestion.text)"
                        class="text-left p-3 bg-gray-50 rounded-lg hover:bg-purple-50 transition text-sm"
                    >
                        {{ quickQuestion.text }}
                    </button>
                </div>
            </div>

            <!-- Back to Dashboard -->
            <div class="text-center mt-8 animate-bounce">
                <a :href="dashboardRoute"
                    class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-800 text-white font-medium rounded-full hover:from-purple-700 hover:to-purple-900 transition-all shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform"
                        viewBox="0 0 20 20" fill="currentColor">
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
import Dashboard from './Dashboard.vue';
import axios from 'axios';

export default {
    name: 'TanyaMentorPage',
    props: {
        // This prop will be passed from your Laravel Blade view
        // to provide the URL for the dashboard.
        dashboardRoute: {
            type: String,
            default: '/Dashboard'
        }
    },
    data() {
        console.log('📋 Initializing TanyaMentor data...');
        return {
            messages: [{
                id: 1,
                text: 'Halo! 👋 Saya Mentor Tim, siap membantu kamu dengan pertanyaan seputar coding, karir IT, atau materi pembelajaran. Apa yang bisa saya bantu?',
                sender: 'mentor',
                timestamp: new Date().toISOString()
            }],
            newMessage: '',
            isTyping: false,
            quickQuestions: [
                { id: 1, text: 'Bagaimana cara deploy website ke Vercel?' },
                { id: 2, text: 'Tips biar nggak bosen belajar coding?' },
                { id: 3, text: 'Apa bedanya UI & UX?' },
                { id: 4, text: 'Cara belajar bahasa pemrograman yang efektif' },
                { id: 5, text: 'Apa itu Git dan fungsinya' },
                { id: 6, text: 'Bagaimana cara mengatasi error coding?' }
            ],
            mentorResponses: {
                'deploy': 'Untuk deploy ke Vercel, kamu bisa: 1) Push code ke GitHub, 2) Connect repository di Vercel, 3) Vercel akan otomatis build dan deploy. Sangat mudah! 🚀',
                'tips': 'Tips belajar coding: 1) Mulai dari dasar, 2) Praktek setiap hari, 3) Buat project kecil, 4) Bergabung komunitas, 5) Jangan takut error! 💪',
                'ui ux': 'UI (User Interface) = tampilan visual yang user lihat. UX (User Experience) = pengalaman user saat menggunakan aplikasi. UI fokus ke estetika, UX fokus ke kemudahan penggunaan! 🎨',
                'belajar': 'Cara efektif belajar programming: 1) Pahami konsep dasar, 2) Praktek coding setiap hari, 3) Buat project real, 4) Debug dan solve problems, 5) Terus belajar teknologi baru! 📚',
                'git': 'Git adalah sistem version control untuk tracking perubahan code. Fungsinya: 1) Menyimpan history perubahan, 2) Kolaborasi tim, 3) Backup code, 4) Branch management. Essential banget untuk developer! 🔄',
                'error': 'Cara mengatasi error: 1) Baca error message dengan teliti, 2) Cek dokumentasi, 3) Search di Google/Stack Overflow, 4) Debug step by step, 5) Jangan panik, error adalah bagian dari belajar! 🐛'
            }
        };
    },
    mounted() {
        console.log('🚀 TanyaMentor component mounted');
        try {
            this.loadMessages();
            this.scrollToBottom();
            console.log('✅ Component initialized successfully');
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
    },
    updated() {
        try {
            this.scrollToBottom();
        } catch (error) {
            console.error('❌ Error in updated:', error);
        }
    },
    methods: {
        formatTime(timestamp) {
            try {
                const date = new Date(timestamp);
                const now = new Date();
                const diffMinutes = (now - date) / (1000 * 60);

                if (diffMinutes < 1) {
                    return 'Baru saja';
                } else if (diffMinutes < 60) {
                    return `${Math.floor(diffMinutes)} menit lalu`;
                } else if (diffMinutes < 1440) { // less than 24 hours
                    return `${Math.floor(diffMinutes / 60)} jam lalu`;
                } else {
                    return `${Math.floor(diffMinutes / 1440)} hari lalu`;
                }
            } catch (error) {
                console.error('❌ Error formatting time:', error);
                return 'Baru saja';
            }
        },
        
        async loadMessages() {
            console.log('📥 Loading messages...');
            try {
                const authToken = localStorage.getItem('authToken');
                if (!authToken) {
                    console.log('❌ No auth token found');
                    // Add welcome message if no auth
                    this.messages = [{
                        id: 1,
                        text: 'Halo! 👋 Saya Mentor Tim, siap membantu kamu dengan pertanyaan seputar coding, karir IT, atau materi pembelajaran. Silakan login untuk mulai chat!',
                        sender: 'mentor',
                        timestamp: new Date().toISOString()
                    }];
                    return;
                }

                console.log('🔑 Auth token found, fetching messages...');
                const response = await axios.get('/api/chat/messages', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });

                console.log('📨 API Response:', response.data);

                if (response.data.success) {
                    this.messages = response.data.messages.map(msg => ({
                        id: msg.id,
                        text: msg.message,
                        sender: msg.sender,
                        timestamp: msg.created_at
                    }));
                    console.log('✅ Messages loaded:', this.messages);
                } else {
                    // Add welcome message if no messages
                    this.messages = [{
                        id: 1,
                        text: 'Halo! 👋 Saya Mentor Tim, siap membantu kamu dengan pertanyaan seputar coding, karir IT, atau materi pembelajaran. Apa yang bisa saya bantu?',
                        sender: 'mentor',
                        timestamp: new Date().toISOString()
                    }];
                }
            } catch (error) {
                console.error('❌ Error loading messages:', error);
                // Add welcome message on error
                this.messages = [{
                    id: 1,
                    text: 'Halo! 👋 Saya Mentor Tim, siap membantu kamu dengan pertanyaan seputar coding, karir IT, atau materi pembelajaran. Apa yang bisa saya bantu?',
                    sender: 'mentor',
                    timestamp: new Date().toISOString()
                }];
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            const messageText = this.newMessage;
            this.newMessage = '';
            this.isTyping = true;

            try {
                const authToken = localStorage.getItem('authToken');
                if (!authToken) {
                    console.log('No auth token found');
                    return;
                }

                const response = await axios.post('/api/chat/send', {
                    message: messageText
                }, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data.success) {
                    // Add user message
                    this.messages.push({
                        id: response.data.user_message.id,
                        text: response.data.user_message.message,
                        sender: 'user',
                        timestamp: response.data.user_message.created_at
                    });

                    // Add mentor response
                    this.messages.push({
                        id: response.data.mentor_message.id,
                        text: response.data.mentor_message.message,
                        sender: 'mentor',
                        timestamp: response.data.mentor_message.created_at
                    });
                }
            } catch (error) {
                console.error('Error sending message:', error);
                // Fallback to local response
                this.messages.push({
                    id: this.messages.length + 1,
                    text: messageText,
                    sender: 'user',
                    timestamp: new Date().toISOString()
                });

                setTimeout(() => {
                    this.isTyping = false;
                    const response = this.generateMentorResponse(messageText);
                    this.messages.push({
                        id: this.messages.length + 1,
                        text: response,
                        sender: 'mentor',
                        timestamp: new Date().toISOString()
                    });
                }, 2000 + Math.random() * 3000);
            } finally {
                this.isTyping = false;
            }
        },

        generateMentorResponse(userMessage) {
            const message = userMessage.toLowerCase();
            
            // Check for specific keywords and return appropriate responses
            if (message.includes('deploy') || message.includes('vercel')) {
                return this.mentorResponses.deploy;
            } else if (message.includes('tips') || message.includes('bosen') || message.includes('motivasi')) {
                return this.mentorResponses.tips;
            } else if (message.includes('ui') && message.includes('ux')) {
                return this.mentorResponses['ui ux'];
            } else if (message.includes('belajar') || message.includes('efektif')) {
                return this.mentorResponses.belajar;
            } else if (message.includes('git')) {
                return this.mentorResponses.git;
            } else if (message.includes('error') || message.includes('bug')) {
                return this.mentorResponses.error;
            } else {
                // Default responses for general questions
                const defaultResponses = [
                    'Pertanyaan yang bagus! 🤔 Bisa kamu jelaskan lebih detail tentang apa yang ingin kamu tanyakan?',
                    'Menarik sekali pertanyaannya! 💡 Saya akan coba bantu. Bisa kamu berikan contoh atau konteks lebih spesifik?',
                    'Wah, pertanyaan yang challenging! 🚀 Saya siap membantu kamu. Apa yang sudah kamu coba sejauh ini?',
                    'Pertanyaan yang sangat relevan untuk developer! 👨‍💻 Mari kita bahas bersama. Apa yang membuat kamu tertarik dengan topik ini?'
                ];
                return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
            }
        },

        sendQuickQuestion(text) {
            this.newMessage = text;
            this.sendMessage();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatContainer) {
                    this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                }
            });
        }
    }
};
</script>

<style scoped>
/* Tailwind CSS classes should already be globally available from your main CSS.
   Any custom animations or specific styles not covered by Tailwind can go here. */
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