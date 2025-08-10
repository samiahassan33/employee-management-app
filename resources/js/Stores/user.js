import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [],
    currentUser: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0
    },
    filters: {
      search: '',
      role: '',
      sort: 'created_at',
      order: 'desc'
    },
    isLoading: false,
    isSubmitting: false
  }),

  getters: {
    roleOptions: () => [
      { value: 'admin', label: 'Administrator' },
      { value: 'manager', label: 'Manager' },
      { value: 'employee', label: 'Employee' }
    ],
    
    getUserById: (state) => (id) => 
      state.users.find(user => user.id === id)
  },

  actions: {
    async fetchUsers(params = {}) {
      this.isLoading = true
      try {
        const queryParams = { ...this.filters, ...params }
        const { data } = await apiService.get('/users', queryParams)
        
        this.users = data.data
        this.pagination = data.meta
        
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async fetchUser(id) {
      this.isLoading = true
      try {
        const { data } = await apiService.get(`/users/${id}`)
        this.currentUser = data.data
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async createUser(userData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.post('/users', userData)
        this.users.unshift(data.data)
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async updateUser(id, userData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.put(`/users/${id}`, userData)
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index] = data.data
        }
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async deleteUser(id) {
      try {
        await apiService.delete(`/users/${id}`)
        this.users = this.users.filter(user => user.id !== id)
        this.pagination.total--
        return true
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async assignRole(userId, role) {
      try {
        const { data } = await apiService.post(`/users/${userId}/assign-role`, { role })
        const index = this.users.findIndex(user => user.id === userId)
        if (index !== -1) {
          this.users[index] = data.data
        }
        return data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    setFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
    },

    clearFilters() {
      this.filters = {
        search: '',
        role: '',
        sort: 'created_at',
        order: 'desc'
      }
    }
  }
})