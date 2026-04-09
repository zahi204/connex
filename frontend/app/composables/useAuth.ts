export const useAuth = () => {
  const authStore = useAuthStore()
  const { apiFetch, baseURL } = useApi()

  const fetchUser = async () => {
    try {
      const response = await apiFetch('/me') as any
      const rawUser = response?.data?.user
      if (!rawUser || !authStore.setUserFromApi(rawUser))
        authStore.logout()
    }
    catch {
      authStore.logout()
    }
  }

  const initCsrf = async () => {
    await $fetch(`${baseURL}/sanctum/csrf-cookie`, { credentials: 'include' })
  }

  const sendOtp = async (phone: string) => {
    await initCsrf()
    await apiFetch('/auth/send-otp', {
      method: 'POST',
      body: { phone },
    })
  }

  const verifyOtp = async (phone: string, code: string) => {
    await initCsrf()
    const response = await apiFetch('/auth/verify-otp', {
      method: 'POST',
      body: { phone, code },
    }) as any

    const rawUser = response?.data?.user
    if (rawUser)
      authStore.setUserFromApi(rawUser)
    return response.data
  }

  const selectRole = async (role: string) => {
    const response = await apiFetch('/auth/select-role', {
      method: 'POST',
      body: { role },
    }) as any

    const rawUser = response?.data?.user
    if (rawUser)
      authStore.setUserFromApi(rawUser)
    return response.data
  }

  const logout = async () => {
    try {
      await apiFetch('/auth/logout', { method: 'POST' })
    }
    finally {
      authStore.logout()
      await navigateTo('/login')
    }
  }

  return {
    fetchUser,
    initCsrf,
    sendOtp,
    verifyOtp,
    selectRole,
    logout,
    user: computed(() => authStore.user),
    isAuthenticated: computed(() => authStore.isAuthenticated),
  }
}
