
/ resources/js/app.js
import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { useAuthStore } from './stores/auth'

// Global styles
import '../css/app.css'

// Create Vue app
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize auth state
const authStore = useAuthStore()
authStore.initializeAuth()

app.mount('#app')

// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Import pages
import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Employees from '../pages/Employees.vue'
import Departments from '../pages/Departments.vue'
import Users from '../pages/Users.vue'
import Profile from '../pages/Profile.vue'
import Unauthorized from '../pages/Unauthorized.vue'
import NotFound from '../pages/NotFound.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { 
      requiresAuth: true,
      permission: 'dashboard.view'
    }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: Employees,
    meta: { 
      requiresAuth: true,
      permission: 'employees.view'
    }
  },
  {
    path: '/departments',
    name: 'Departments',
    component: Departments,
    meta: { 
      requiresAuth: true,
      permission: 'departments.view'
    }
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { 
      requiresAuth: true,
      permission: 'users.view'
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/unauthorized',
    name: 'Unauthorized',
    component: Unauthorized
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next('/login')
    }

    // Check permissions
    if (to.meta.permission && !authStore.hasPermission(to.meta.permission)) {
      return next('/unauthorized')
    }

    // Check roles
    if (to.meta.roles && !authStore.hasAnyRole(to.meta.roles)) {
      return next('/unauthorized')
    }
  }

  // Redirect authenticated users away from guest-only pages
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return next('/dashboard')
  }

  next()
})

export default router