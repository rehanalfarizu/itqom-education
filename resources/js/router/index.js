import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../components/homepage/HomePage.vue';
import Course from '../components/course/Course.vue';
import Course_Description from '../components/course/Course_Description.vue';
import LearnMore from '../components/learnmore/learnmore.vue';
import Profil_Pengguna from '../components/user/Profil_Pengguna.vue';
import Dashboard from '../components/user/Dashboard.vue';
import Reward from '../components/user/Reward.vue';
import TanyaMentor from '../components/user/Tanyamentor.vue';
import About from '../components/About.vue';
import AdminLogin from '../components/admin/Login.vue';
import UserLogin from '../components/auth/UserLogin.vue';
import Register from '../components/auth/Register.vue';
import UserTable from '../components/UserTable.vue';
import course_content from '../components/course_content/course_content.vue';
import ChangePassword from '../components/auth/ChangePassword.vue';

const routes = [
  { path: '/', name: 'HomePage', component: HomePage },
  { path: '/Course', name: 'Course', component: Course },
  { path: '/Course_Description/:id', name: 'CourseDescription', component: Course_Description },
  { path: '/learnmore', name: 'LearnMore', component: LearnMore },
  { path: '/About', name: 'About', component: About },
  { path: '/UserTable', name: 'UserTable', component: UserTable },

  // Rute User Login dan Register
  { path: '/login', name: 'UserLogin', component: UserLogin, meta: { hideLayout: true } },
  { path: '/register', name: 'Register', component: Register, meta: { hideLayout: true } },

  // Rute Admin Login
  { path: '/admin/login', name: 'AdminLogin', component: AdminLogin },

  // --- RUTE YANG MEMBUTUHKAN LOGIN ---
  { path: '/Profil_Pengguna', name: 'ProfilPengguna', component: Profil_Pengguna, meta: { requiresAuth: true } },
  { path: '/Dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/Reward', name: 'Reward', component: Reward, meta: { requiresAuth: true } },
  { path: '/Tanya_Mentor', name: 'Tanya_Mentor', component: TanyaMentor, meta: { requiresAuth: true } },
  { path: '/course/:courseDescriptionId', name: 'CourseContent', component: course_content, meta: { hideLayout: true, requiresAuth: true } },
  { path: '/change-password', name: 'ChangePassword', component: ChangePassword, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Fungsi untuk memeriksa apakah token otentikasi ada di localStorage
function isAuthenticated() {
  // Cek token di localStorage, bisa sesuaikan nama token jika perlu
  return !!localStorage.getItem('authToken');
}

// Navigation Guard Global
router.beforeEach((to, from, next) => {
  console.log('🛣️ Route change:', { from: from.path, to: to.path });

  // Jika rute butuh login dan user belum login, redirect ke login
  if (to.meta.requiresAuth && !isAuthenticated()) {
    console.log('🔒 Auth required, redirecting to login');
    next({ name: 'UserLogin' });
  } else {
    console.log('✅ Route access granted');
    next();
  }
});

export default router;

