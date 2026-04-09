export default defineNuxtRouteMiddleware((to) => {
  if (import.meta.server)
    return

  const authStore = useAuthStore()

  const requiredRole = to.meta.role as string | string[] | undefined
  if (!requiredRole) return

  const userRole = authStore.userRole
  const allowed = Array.isArray(requiredRole)
    ? requiredRole.includes(userRole!)
    : requiredRole === userRole

  if (!allowed) {
    return navigateTo('/login')
  }
})
