export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }

  if (authStore.needsRoleSelection && to.path !== '/select-role') {
    return navigateTo('/select-role')
  }
})
