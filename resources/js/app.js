import { createApp } from 'vue';
import App from './app.vue';
import 'swiper/css/bundle';
import Swiper from 'swiper/bundle';
import router from './router';

const app = createApp(App);
app.use(router);
app.mount('#app');
