<template>
  <div class="portal-page">
    <div class="page-header">
      <h1>My Projects</h1>
      <NuxtLink to="/developer/projects/create" class="btn-create">+ New Project</NuxtLink>
    </div>

    <div class="filters">
      <button
        v-for="s in statusFilters"
        :key="s.value"
        class="filter-btn"
        :class="{ active: currentFilter === s.value }"
        @click="currentFilter = s.value"
      >{{ s.label }}</button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!filteredProjects.length" class="empty">No projects found.</div>
      <div v-else class="projects-list">
        <NuxtLink
          v-for="p in filteredProjects"
          :key="p.id"
          :to="`/developer/projects/${p.id}`"
          class="project-card"
        >
          <div class="project-info">
            <h3>{{ p.name }}</h3>
            <div class="project-meta">
              <span v-if="p.location">{{ p.location }}</span>
              <span v-if="p.created_at">Created: {{ p.created_at }}</span>
            </div>
          </div>
          <span class="status-badge" :class="p.status">{{ p.status }}</span>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const projects = ref<any[]>([])
const currentFilter = ref('all')

const statusFilters = [
  { label: 'All', value: 'all' },
  { label: 'Pending', value: 'pending' },
  { label: 'Active', value: 'active' },
  { label: 'Completed', value: 'completed' },
  { label: 'Rejected', value: 'rejected' },
]

const filteredProjects = computed(() => {
  if (currentFilter.value === 'all') return projects.value
  return projects.value.filter(p => p.status === currentFilter.value)
})

onMounted(async () => {
  try {
    const res = await apiFetch('/developer/projects') as any
    projects.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load projects'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin: 0; }
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.btn-create { padding: 0.5rem 1rem; background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3); color: var(--cx-primary); border-radius: 8px; text-decoration: none; font-size: 0.85rem; }
.filters { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
.filter-btn { padding: 0.35rem 0.8rem; background: var(--cx-bg-card); border: 1px solid var(--cx-border); color: var(--cx-text-muted); border-radius: 20px; cursor: pointer; font-size: 0.8rem; }
.filter-btn.active { background: rgba(59,130,246,0.2); border-color: rgba(59,130,246,0.4); color: var(--cx-primary); }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.empty { color: var(--cx-text-muted); text-align: center; padding: 2rem; }
.projects-list { display: flex; flex-direction: column; gap: 0.75rem; }
.project-card { display: flex; align-items: center; justify-content: space-between; background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 12px; padding: 1rem 1.25rem; text-decoration: none; transition: background 0.2s; }
.project-card:hover { background: var(--cx-bg-muted); }
.project-card h3 { color: var(--cx-text-primary); margin: 0 0 0.25rem; font-size: 1rem; }
.project-meta { display: flex; gap: 1.5rem; color: var(--cx-text-muted); font-size: 0.8rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: var(--cx-led-green); }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.completed { background: rgba(59,130,246,0.2); color: var(--cx-primary); }
.status-badge.rejected { background: rgba(239,68,68,0.2); color: var(--cx-led-red); }
</style>
