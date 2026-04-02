<template>
  <div class="portal-page">
    <h1>Developer Dashboard</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="profile" class="dashboard">
      <div class="welcome">
        <h2>{{ profile.company_name || profile.full_name }}</h2>
        <span class="status-badge" :class="profile.status">{{ profile.status }}</span>
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-label">Active Projects</div>
          <div class="stat-value">{{ profile.active_projects_count ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Items</div>
          <div class="stat-value">{{ profile.pending_items_count ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Projects</div>
          <div class="stat-value">{{ profile.total_projects_count ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Account Status</div>
          <div class="stat-value">{{ profile.status || 'N/A' }}</div>
        </div>
      </div>
      <div class="quick-links">
        <NuxtLink to="/developer/projects" class="quick-link">My Projects</NuxtLink>
        <NuxtLink to="/developer/projects/create" class="quick-link">Submit Project</NuxtLink>
        <NuxtLink to="/developer/boq" class="quick-link">BOQ Requests</NuxtLink>
        <NuxtLink to="/developer/profile" class="quick-link">Company Profile</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/developer/profile') as any
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
