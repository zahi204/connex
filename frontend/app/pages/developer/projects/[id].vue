<template>
  <div class="portal-page">
    <NuxtLink to="/developer/projects" class="back-link">&larr; Back to Projects</NuxtLink>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="project">
      <div class="project-header">
        <h1>{{ project.name }}</h1>
        <span class="status-badge" :class="project.status">{{ project.status }}</span>
      </div>

      <div class="info-grid">
        <div class="info-item"><span class="label">Location</span><span class="value">{{ project.location || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Type</span><span class="value">{{ project.project_type || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Total Area</span><span class="value">{{ project.total_area ? project.total_area + ' sqm' : 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Units</span><span class="value">{{ project.number_of_units ?? 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Floors</span><span class="value">{{ project.number_of_floors ?? 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Workers Needed</span><span class="value">{{ project.workers_needed ?? 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Start Date</span><span class="value">{{ project.estimated_start_date || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">End Date</span><span class="value">{{ project.estimated_end_date || 'N/A' }}</span></div>
      </div>

      <div v-if="project.description" class="description-section">
        <h2>Description</h2>
        <p>{{ project.description }}</p>
      </div>

      <div v-if="project.assigned_resources && project.assigned_resources.length" class="resources-section">
        <h2>Assigned Resources</h2>
        <div class="resources-list">
          <div v-for="r in project.assigned_resources" :key="r.id" class="resource-card">
            <span class="resource-name">{{ r.name || r.full_name }}</span>
            <span v-if="r.role" class="resource-role">{{ r.role }}</span>
          </div>
        </div>
        <p class="hint">Only names are shown. Financial details are managed by Connex administration.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const route = useRoute()
const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const project = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch(`/developer/projects/${route.params.id}`) as any
    project.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load project'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin: 0; }
.back-link { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.85rem; display: inline-block; margin-bottom: 1rem; }
.back-link:hover { color: rgba(255,255,255,0.8); }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.project-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.completed { background: rgba(59,130,246,0.2); color: #93c5fd; }
.status-badge.rejected { background: rgba(239,68,68,0.2); color: #f87171; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem; }
.info-item { background: rgba(255,255,255,0.05); border-radius: 8px; padding: 0.75rem; }
.info-item .label { display: block; color: rgba(255,255,255,0.5); font-size: 0.7rem; margin-bottom: 0.2rem; }
.info-item .value { color: white; font-size: 0.9rem; }
.description-section, .resources-section { margin-bottom: 1.5rem; }
.description-section h2, .resources-section h2 { color: white; font-size: 1.1rem; margin-bottom: 0.75rem; }
.description-section p { color: rgba(255,255,255,0.7); line-height: 1.5; }
.resources-list { display: flex; flex-direction: column; gap: 0.5rem; }
.resource-card { background: rgba(255,255,255,0.06); border-radius: 8px; padding: 0.6rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.resource-name { color: white; }
.resource-role { color: rgba(255,255,255,0.5); font-size: 0.8rem; }
.hint { color: rgba(255,255,255,0.35); font-size: 0.8rem; font-style: italic; margin-top: 0.75rem; }
</style>
