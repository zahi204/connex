export const useNotifications = () => {
  const { apiFetch } = useApi()

  const unreadCount = ref(0)
  const notifications = ref<any[]>([])
  let pollTimer: ReturnType<typeof setInterval> | null = null

  async function fetchUnreadCount() {
    try {
      const res = await apiFetch('/notifications/unread-count') as any
      unreadCount.value = res?.data?.count ?? res?.count ?? 0
    } catch {
      // silently ignore
    }
  }

  async function list() {
    try {
      const res = await apiFetch('/notifications') as any
      notifications.value = res?.data ?? []
    } catch {
      notifications.value = []
    }
    return notifications.value
  }

  async function markAsRead(id: string | number) {
    try {
      await apiFetch(`/notifications/${id}/read`, { method: 'POST' })
      const n = notifications.value.find(x => x.id === id)
      if (n) n.read_at = new Date().toISOString()
      await fetchUnreadCount()
    } catch { /* ignore */ }
  }

  async function markAllRead() {
    try {
      await apiFetch('/notifications/mark-all-read', { method: 'POST' })
      notifications.value.forEach(n => { n.read_at = new Date().toISOString() })
      unreadCount.value = 0
    } catch { /* ignore */ }
  }

  function startPolling(intervalMs = 30_000) {
    fetchUnreadCount()
    pollTimer = setInterval(fetchUnreadCount, intervalMs)
  }

  function stopPolling() {
    if (pollTimer !== null) {
      clearInterval(pollTimer)
      pollTimer = null
    }
  }

  onUnmounted(() => stopPolling())

  return {
    unreadCount: readonly(unreadCount),
    notifications,
    fetchUnreadCount,
    list,
    markAsRead,
    markAllRead,
    startPolling,
    stopPolling,
  }
}
