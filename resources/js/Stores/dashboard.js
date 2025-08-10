import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    permissions: [],
    roles: [],
    isAuthenticated: false,
    isLoading: false
  }),

  actions: {
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
          this.fetchUser().catch(() => this.clearAuth())
        }
      }
    }
  }
})
