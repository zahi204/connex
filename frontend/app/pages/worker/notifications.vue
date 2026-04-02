<template>
  <div class="portal-page">
    <h1>Notifications</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!notifications.length" class="empty">No notifications.</div>
      <div v-else class="notifications-list">
        <div v-for="n in notifications" :key="n.id" class="notification-card" :class="{ unread: !n.read_at }">
          <div class="notification-content">
            <div class="notification-title">{{ n.title || n.type }}</div>
            <div class="notification-body">{{ n.message || n.body }}</div>
            <div class="notification-time">{{ n.created_at }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const notifications = ref<any[]>([])

onMounted(async () => {
  try {
    const res = await apiFetch('/notifications') as any
    notifications.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load notifications'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }
.notifications-list { display: flex; flex-direction: column; gap: 0.5rem; }
.notification-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 0.75rem 1rem; }
.notification-card.unread { border-left: 3px solid #3b82f6; background: rgba(59,130,246,0.08); }
.notification-title { color: white; font-weight: 500; margin-bottom: 0.25rem; }
.notification-body { color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-bottom: 0.25rem; }
.notification-time { color: rgba(255,255,255,0.3); font-size: 0.75rem; }
</style>
