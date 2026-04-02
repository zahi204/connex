<template>
  <div />
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

const role = authStore.userRole
if (role && roleRouteMap[role]) {
  navigateTo(roleRouteMap[role])
} else if (!authStore.isAuthenticated) {
  navigateTo('/login')
} else {
  navigateTo('/select-role')
}
</script>
