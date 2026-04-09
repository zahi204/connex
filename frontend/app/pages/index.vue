<template>
  <div class="home-redirect cx-loading" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    {{ $t('common.loading') }}
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const authStore = useAuthStore()

const roleRouteMap: Record<string, string> = {
  worker: '/worker',
  developer: '/developer',
  subcontractor: '/subcontractor',
  agency: '/agency',
  admin: '/admin',
  coordinator: '/admin',
}

onMounted(async () => {
  const role = authStore.userRole
  if (role && roleRouteMap[role])
    await navigateTo(roleRouteMap[role], { replace: true })
  else if (!authStore.isAuthenticated)
    await navigateTo('/login', { replace: true })
  else
    await navigateTo('/select-role', { replace: true })
})
</script>
