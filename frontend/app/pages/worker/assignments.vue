<template>
  <div class="portal-page">
    <h1>My Assignments</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!assignments.length" class="empty">No assignments found.</div>
      <div v-else class="assignments-list">
        <div v-for="a in assignments" :key="a.id" class="assignment-card">
          <div class="assignment-header">
            <h3>{{ a.project_name || 'Project #' + a.project_id }}</h3>
            <span class="status-badge" :class="a.status">{{ a.status }}</span>
          </div>
          <div class="assignment-details">
            <span v-if="a.start_date">Start: {{ a.start_date }}</span>
            <span v-if="a.end_date">End: {{ a.end_date }}</span>
            <span v-if="a.role">Role: {{ a.role }}</span>
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
const assignments = ref<any[]>([])

onMounted(async () => {
  try {
    const res = await apiFetch('/worker/assignments') as any
    assignments.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load assignments'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.empty { color: var(--cx-text-muted); text-align: center; padding: 2rem; }
.assignments-list { display: flex; flex-direction: column; gap: 0.75rem; }
.assignment-card { background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 12px; padding: 1rem 1.25rem; }
.assignment-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.assignment-header h3 { color: var(--cx-text-primary); margin: 0; font-size: 1rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: var(--cx-led-green); }
.status-badge.completed { background: rgba(59,130,246,0.2); color: var(--cx-primary); }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.assignment-details { display: flex; gap: 1.5rem; color: var(--cx-text-muted); font-size: 0.8rem; }
</style>
