<template>
  <div class="home-redirect cx-loading" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    {{ $t('common.loading') }}
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const authStore = useAuthStore()
const config = useRuntimeConfig()

const roleRouteMap: Record<string, string> = {
  worker: '/worker',
  developer: '/developer',
  subcontractor: '/subcontractor',
  agency: '/agency',
}

onMounted(async () => {
  const role = authStore.userRole

  // Admin & coordinator use the Filament panel on the backend, not the Nuxt app.
  if (role === 'admin' || role === 'coordinator') {
    const backend = config.public.apiBaseUrl as string
    await navigateTo(`${backend}/admin`, { external: true, replace: true })
    return
  }

  if (role && roleRouteMap[role])
    await navigateTo(roleRouteMap[role], { replace: true })
  else if (!authStore.isAuthenticated)
    await navigateTo('/login', { replace: true })
  else
    await navigateTo('/select-role', { replace: true })
})
</script>
