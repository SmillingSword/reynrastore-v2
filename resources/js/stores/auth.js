import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('admin_token') || null,
    isAuthenticated: false,
    loading: false
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || ''
  },

  actions: {
    async login(credentials) {
      this.loading = true
      
      try {
        const response = await axios.post('/api/v1/auth/login', credentials)
        
        if (response.data.success) {
          this.token = response.data.data.token
          this.user = response.data.data.user
          this.isAuthenticated = true
          
          // Store token in localStorage
          localStorage.setItem('admin_token', this.token)
          
          // Set default authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          
          return response.data
        }
      } catch (error) {
        this.clearAuth()
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      
      try {
        if (this.token) {
          await axios.post('/api/v1/auth/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      this.loading = true
      
      try {
        const response = await axios.get('/api/v1/auth/me')
        
        if (response.data.success) {
          this.user = response.data.data
          this.isAuthenticated = true
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        this.clearAuth()
      } finally {
        this.loading = false
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      
      // Remove token from localStorage
      localStorage.removeItem('admin_token')
      
      // Remove authorization header
      delete axios.defaults.headers.common['Authorization']
    },

    initializeAuth() {
      if (this.token) {
        // Set authorization header
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Fetch user data
        this.fetchUser()
      }
    }
  }
})
