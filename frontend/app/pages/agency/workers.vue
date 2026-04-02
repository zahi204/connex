<template>
  <div class="portal-page">
    <h1>My Workers</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!workers.length" class="empty">No workers registered under this agency.</div>
      <div v-else>
        <div class="workers-summary">
          <span>Total: {{ workers.length }}</span>
        </div>
        <div class="workers-list">
          <div v-for="w in workers" :key="w.id" class="worker-card">
            <div class="worker-info">
              <div class="worker-name">{{ w.full_name }}</div>
              <div class="worker-meta">
                <span v-if="w.primary_skill">{{ w.primary_skill }}</span>
                <span v-if="w.training_status" class="training-badge" :class="w.training_status">Training: {{ w.training_status }}</span>
              </div>
            </div>
            <div class="worker-right">
              <div v-if="w.professional_rating != null" class="worker-rating">{{ w.professional_rating }}</div>
              <span class="status-badge" :class="w.status">{{ w.status }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'agency', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const workers = ref<any[]>([])

onMounted(async () => {
  try {
    const res = await apiFetch('/agency/workers') as any
    workers.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load workers'
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
.workers-summary { color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 1rem; }
.workers-list { display: flex; flex-direction: column; gap: 0.5rem; }
.worker-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.worker-name { color: white; font-weight: 500; margin-bottom: 0.2rem; }
.worker-meta { display: flex; gap: 0.75rem; color: rgba(255,255,255,0.5); font-size: 0.8rem; }
.training-badge { padding: 0.1rem 0.5rem; border-radius: 12px; font-size: 0.7rem; }
.training-badge.passed { background: rgba(34,197,94,0.2); color: #4ade80; }
.training-badge.failed { background: rgba(239,68,68,0.2); color: #f87171; }
.training-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.worker-right { text-align: right; display: flex; align-items: center; gap: 0.75rem; }
.worker-rating { color: white; font-weight: 700; font-size: 1.1rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.suspended { background: rgba(239,68,68,0.2); color: #f87171; }
</style>
