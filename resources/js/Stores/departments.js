import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'

export const useDepartmentStore = defineStore('departments', {
  state: () => ({
    departments: [],
    currentDepartment: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0
    },
    isLoading: false,
    isSubmitting: false
  }),

  getters: {
    departmentOptions: (state) => 
      state.departments.map(dept => ({
        value: dept.id,
        label: dept.name,
        code: dept.code
      })),
      
    getDepartmentById: (state) => (id) => 
      state.departments.find(dept => dept.id === id)
  },

  actions: {
    async fetchDepartments(params = {}) {
      this.isLoading = true
      try {
        const { data } = await apiService.get('/departments', params)
        this.departments = data.data
        this.pagination = data.meta
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async fetchAllDepartments() {
      try {
        const { data } = await apiService.get('/departments', { per_page: 100 })
        this.departments = data.data
        return data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async fetchDepartment(id) {
      this.isLoading = true
      try {
        const { data } = await apiService.get(`/departments/${id}`)
        this.currentDepartment = data.data
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async createDepartment(departmentData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.post('/departments', departmentData)
        this.departments.unshift(data.data)
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async updateDepartment(id, departmentData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.put(`/departments/${id}`, departmentData)
        const index = this.departments.findIndex(dept => dept.id === id)
        if (index !== -1) {
          this.departments[index] = data.data
        }
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async deleteDepartment(id) {
      try {
        await apiService.delete(`/departments/${id}`)
        this.departments = this.departments.filter(dept => dept.id !== id)
        this.pagination.total--
        return true
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async fetchDepartmentsWithEmployees() {
      try {
        const { data } = await apiService.get('/departments/with-employees')
        return data.data
      } catch (error) {
        throw error.response?.data || error
      }

    }
    },
})