<template>
  <div class="portal-page">
    <h1>Subcontractor Dashboard</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="profile" class="dashboard">
      <div class="welcome">
        <h2>{{ profile.company_name || profile.full_name }}</h2>
        <span class="status-badge" :class="profile.status">{{ profile.status }}</span>
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-label">Current Assignment</div>
          <div class="stat-value">{{ profile.current_assignment || 'None' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Rating</div>
          <div class="stat-value">{{ profile.rating ?? 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Availability</div>
          <div class="stat-value">{{ profile.availability || 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Assignments</div>
          <div class="stat-value">{{ profile.total_assignments ?? 0 }}</div>
        </div>
      </div>
      <div class="quick-links">
        <NuxtLink to="/subcontractor/assignments" class="quick-link">My Assignments</NuxtLink>
        <NuxtLink to="/subcontractor/rating" class="quick-link">My Rating</NuxtLink>
        <NuxtLink to="/subcontractor/availability" class="quick-link">Update Availability</NuxtLink>
        <NuxtLink to="/subcontractor/documents" class="quick-link">Documents</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'subcontractor', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/subcontractor/profile') as any
    profile.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load dashboard'
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
.welcome { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.welcome h2 { color: white; font-size: 1.25rem; margin: 0; }
.status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 1.25rem; }
.stat-label { color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-bottom: 0.5rem; }
.stat-value { color: white; font-size: 1.5rem; font-weight: 700; }
.quick-links { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.quick-link { padding: 0.6rem 1.2rem; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #93c5fd; border-radius: 8px; text-decoration: none; font-size: 0.85rem; transition: background 0.2s; }
.quick-link:hover { background: rgba(59,130,246,0.25); }
</style>
