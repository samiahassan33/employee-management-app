// services/apiService.js
class ApiService {
  constructor() {
    this.client = axios.create({
      baseURL: '/api',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    this.setupInterceptors()
  }

  setupInterceptors() {
    this.client.interceptors.request.use(config => {
      const token = localStorage.getItem('auth-token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })
  }

  get(url, params = {}) {
    return this.client.get(url, { params })
  }

  post(url, data) {
    return this.client.post(url, data)
  }
}

export const apiService = new ApiService()