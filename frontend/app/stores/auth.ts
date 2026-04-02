import { defineStore } from 'pinia'

interface User {
  id: number
  phone: string
  role: string | null
  preferred_language: string
  is_active: boolean
  profile?: Record<string, any>
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    isAuthenticated: false,
    needsRoleSelection: false,
  }),

  actions: {
    setUser(user: User) {
      this.user = user
      this.isAuthenticated = true
      this.needsRoleSelection = !user.role
    },

    logout() {
      this.user = null
      this.isAuthenticated = false
      this.needsRoleSelection = false
    },
  },

  getters: {
    userRole: (state) => state.user?.role,
    isAdmin: (state) => state.user?.role === 'admin' || state.user?.role === 'coordinator',
  },
})
