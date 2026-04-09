export default defineNuxtRouteMiddleware(async (to) => {
  if (import.meta.server)
    return

  const authStore = useAuthStore()

  if (!authStore.isAuthenticated)
    await useAuth().fetchUser()

  if (!authStore.isAuthenticated)
    return navigateTo('/login')

  if (authStore.needsRoleSelection && to.path !== '/select-role')
    return navigateTo('/select-role')

  if (authStore.needsOnboarding && !to.path.startsWith('/wizard/')) {
    const role = authStore.userRole
    if (role)
      return navigateTo(`/wizard/${role}`)
  }
})
