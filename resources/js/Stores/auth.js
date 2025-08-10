import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    permissions: [],
    roles: [],
    isLoading: false,
    isAuthenticated: false
  }),

  getters: {
    hasPermission: (state) => (permission) => {
      return state.permissions.includes(permission)
    },
    
    hasRole: (state) => (role) => {
      return state.roles.includes(role)
    },
    
    isAdmin: (state) => state.roles.includes('admin'),
    isManager: (state) => state.roles.includes('manager'),
    isEmployee: (state) => state.roles.includes('employee'),
    
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || ''
  },

  actions: {
    async login(credentials) {
      this.isLoading = true
      try {
        const { data } = await apiService.post('/auth/login', credentials)
        this.setAuth(data)
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async register(userData) {
      this.isLoading = true
      try {
        const { data } = await apiService.post('/auth/register', userData)
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async logout() {
      try {
        await apiService.post('/auth/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    async fetchUser() {
      if (!this.token) return null
      
      try {
        const { data } = await apiService.get('/auth/user')
        this.user = data.user
        this.permissions = data.permissions || []
        this.roles = data.roles || []
        this.isAuthenticated = true
        return data
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async updateProfile(profileData) {
      try {
        const { data } = await apiService.put('/auth/profile', profileData)
        this.user = data.user
        return data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async changePassword(passwordData) {
      try {
        const { data } = await apiService.post('/auth/change-password', passwordData)
        return data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    setAuth(authData) {
      this.user = authData.user
      this.token = authData.token
      this.permissions = authData.permissions || []
      this.roles = authData.roles || []
      this.isAuthenticated = true
      
      // Store in localStorage (note: in real Claude artifacts, we'd use memory only)
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem('auth-token', authData.token)
        localStorage.setItem('user-data', JSON.stringify(authData.user))
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.permissions = []
      this.roles = []
      this.isAuthenticated = false
      
      if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('auth-token')
        localStorage.removeItem('user-data')
      }
    },

    initializeAuth() {
      if (typeof localStorage !== 'undefined') {
        const token = localStorage.getItem('auth-token')
        const userData = localStorage.getItem('user-data')
        
        if (token && userData) {
          this.token = token
          this.user = JSON.parse(userData)
          this.isAuthenticated = true
          // Fetch fresh user data to get latest permissions
          this.fetchUser().catch(() => this.clearAuth())
        }
      }
    }
  }
})
