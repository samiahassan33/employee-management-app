import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'

export const useEmployeeStore = defineStore('employees', {
  state: () => ({
    employees: [],
    currentEmployee: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0
    },
    filters: {
      search: '',
      department_id: null,
      sort: 'created_at',
      order: 'desc'
    },
    isLoading: false,
    isSubmitting: false
  }),

  getters: {
    paginatedEmployees: (state) => ({
      data: state.employees,
      meta: state.pagination
    })
  },

  actions: {
    async fetchEmployees(params = {}) {
      this.isLoading = true
      try {
        const queryParams = { ...this.filters, ...params }
        const { data } = await apiService.get('/employees', queryParams)
        
        this.employees = data.data
        this.pagination = data.meta
        
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async fetchEmployee(id) {
      this.isLoading = true
      try {
        const { data } = await apiService.get(`/employees/${id}`)
        this.currentEmployee = data.data
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isLoading = false
      }
    },

    async createEmployee(employeeData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.post('/employees', employeeData)
        this.employees.unshift(data.data)
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async updateEmployee(id, employeeData) {
      this.isSubmitting = true
      try {
        const { data } = await apiService.put(`/employees/${id}`, employeeData)
        const index = this.employees.findIndex(emp => emp.id === id)
        if (index !== -1) {
          this.employees[index] = data.data
        }
        return data
      } catch (error) {
        throw error.response?.data || error
      } finally {
        this.isSubmitting = false
      }
    },

    async deleteEmployee(id) {
      try {
        await apiService.delete(`/employees/${id}`)
        this.employees = this.employees.filter(emp => emp.id !== id)
        this.pagination.total--
        return true
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async searchEmployees(term) {
      try {
        const { data } = await apiService.get(`/employees/search/${term}`)
        return data.data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async exportEmployees(format = 'csv') {
      try {
        const response = await apiService.get(`/employees/export/${format}`, {
          ...this.filters
        }, {
          responseType: 'blob'
        })
        
        // Create download link
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `employees_${new Date().toISOString().split('T')[0]}.${format}`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        
        return true
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
        department_id: null,
        sort: 'created_at',
        order: 'desc'
      }
    }
  }
})