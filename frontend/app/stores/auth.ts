import { defineStore } from 'pinia'
import { unwrapJsonResource } from '~/composables/useApi'

interface User {
  id: number
  phone: string
  role: string | null
  role_locked?: boolean
  needs_onboarding?: boolean
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

    /** Laravel UserResource may nest attributes under `.data`. */
    setUserFromApi(raw: unknown): boolean {
      const user = (unwrapJsonResource<User>(raw) ?? raw) as User | null
      if (user && typeof user === 'object' && 'id' in user) {
        this.setUser(user)
        return true
      }
      return false
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
    needsOnboarding: (state): boolean => {
      const u = state.user
      if (!u?.role) return false
      if (typeof u.needs_onboarding === 'boolean') return u.needs_onboarding
      if (u.role_locked) return false
      return ['worker', 'developer', 'subcontractor', 'agency'].includes(u.role)
    },
  },
})
