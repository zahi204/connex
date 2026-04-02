<template>
  <div class="directory-search">
    <h2>Worker Directory</h2>

    <div class="filters-bar">
      <div class="filter-field">
        <label>Skill</label>
        <select v-model="filters.skill" @change="search">
          <option value="">All Skills</option>
          <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>
      <div class="filter-field">
        <label>Status</label>
        <select v-model="filters.status" @change="search">
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="available">Available</option>
          <option value="assigned">Assigned</option>
        </select>
      </div>
      <div class="filter-field">
        <label>Area</label>
        <select v-model="filters.area" @change="search">
          <option value="">All Areas</option>
          <option value="north">North</option>
          <option value="center">Center</option>
          <option value="south">South</option>
        </select>
      </div>
      <div class="filter-field">
        <label>Min Rating</label>
        <input v-model.number="filters.min_rating" type="number" min="0" max="5" step="0.5" placeholder="0" @change="search" />
      </div>
      <div class="filter-field">
        <button class="btn-search" @click="search">Search</button>
      </div>
    </div>

    <div class="view-toggle">
      <button :class="{ active: viewMode === 'card' }" @click="viewMode = 'card'">Cards</button>
      <button :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">List</button>
    </div>

    <div v-if="loading" class="loading">Searching...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!workers.length" class="empty">No workers found matching your criteria.</div>

      <!-- Card View -->
      <div v-else-if="viewMode === 'card'" class="workers-grid">
        <div v-for="w in workers" :key="w.id" class="worker-card">
          <div class="card-header">
            <div class="card-name">{{ w.full_name }}</div>
            <span class="status-badge" :class="w.status">{{ w.status }}</span>
          </div>
          <div class="card-details">
            <div v-if="w.primary_skill" class="card-detail"><span class="detail-label">Skill:</span> {{ w.primary_skill }}</div>
            <div v-if="w.preferred_work_area" class="card-detail"><span class="detail-label">Area:</span> {{ w.preferred_work_area }}</div>
            <div v-if="w.professional_rating != null" class="card-detail"><span class="detail-label">Rating:</span> {{ w.professional_rating }}</div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <table v-else class="workers-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Skill</th>
            <th>Area</th>
            <th>Rating</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="w in workers" :key="w.id">
            <td>{{ w.full_name }}</td>
            <td>{{ w.primary_skill || '-' }}</td>
            <td>{{ w.preferred_work_area || '-' }}</td>
            <td>{{ w.professional_rating ?? '-' }}</td>
            <td><span class="status-badge" :class="w.status">{{ w.status }}</span></td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button :disabled="page <= 1" @click="goToPage(page - 1)">Prev</button>
        <span class="page-info">Page {{ page }} of {{ totalPages }}</span>
        <button :disabled="page >= totalPages" @click="goToPage(page + 1)">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { apiFetch } = useApi()

const loading = ref(false)
const error = ref('')
const workers = ref<any[]>([])
const viewMode = ref<'card' | 'list'>('card')
const page = ref(1)
const totalPages = ref(1)
const perPage = 20

const skills = ['formwork', 'rebar', 'general', 'finishing', 'skeleton', 'electrical', 'plumbing', 'painting', 'waterproofing']

const filters = reactive({
  skill: '',
  status: '',
  area: '',
  min_rating: null as number | null,
})

async function search() {
  page.value = 1
  await fetchWorkers()
}

async function goToPage(p: number) {
  page.value = p
  await fetchWorkers()
}

async function fetchWorkers() {
  loading.value = true
  error.value = ''
  try {
    const params = new URLSearchParams()
    params.set('page', String(page.value))
    params.set('per_page', String(perPage))
    if (filters.skill) params.set('skill', filters.skill)
    if (filters.status) params.set('status', filters.status)
    if (filters.area) params.set('area', filters.area)
    if (filters.min_rating != null && filters.min_rating > 0) params.set('min_rating', String(filters.min_rating))

    const res = await apiFetch(`/workers/directory?${params.toString()}`) as any
    workers.value = res.data?.data || res.data || []
    totalPages.value = res.data?.last_page || res.meta?.last_page || 1
  } catch (e: any) {
    error.value = e?.data?.message || 'Search failed'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchWorkers()
})
</script>

<style scoped>
.directory-search h2 { color: white; font-size: 1.3rem; margin-bottom: 1rem; }
.filters-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: flex-end; }
.filter-field { display: flex; flex-direction: column; gap: 0.2rem; }
.filter-field label { color: rgba(255,255,255,0.5); font-size: 0.75rem; }
.filter-field select, .filter-field input {
  padding: 0.5rem 0.6rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
  border-radius: 8px; color: white; font-size: 0.85rem; min-width: 120px;
}
.btn-search { padding: 0.5rem 1.2rem; background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3); color: #93c5fd; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }

.view-toggle { display: flex; gap: 0.25rem; margin-bottom: 1rem; }
.view-toggle button { padding: 0.35rem 0.8rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border-radius: 6px; cursor: pointer; font-size: 0.8rem; }
.view-toggle button.active { background: rgba(59,130,246,0.2); border-color: rgba(59,130,246,0.4); color: #93c5fd; }

.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }

.workers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 0.75rem; }
.worker-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 1rem; }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.card-name { color: white; font-weight: 500; }
.card-details { display: flex; flex-direction: column; gap: 0.25rem; }
.card-detail { color: rgba(255,255,255,0.6); font-size: 0.8rem; }
.detail-label { color: rgba(255,255,255,0.4); }

.workers-table { width: 100%; border-collapse: collapse; }
.workers-table th { text-align: left; color: rgba(255,255,255,0.5); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
.workers-table td { color: rgba(255,255,255,0.8); padding: 0.6rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem; }

.status-badge { padding: 0.15rem 0.5rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.active { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.available { background: rgba(59,130,246,0.2); color: #93c5fd; }
.status-badge.assigned { background: rgba(139,92,246,0.2); color: #c4b5fd; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }

.pagination { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 1.5rem; }
.pagination button { padding: 0.4rem 1rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.7); border-radius: 6px; cursor: pointer; font-size: 0.85rem; }
.pagination button:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { color: rgba(255,255,255,0.5); font-size: 0.85rem; }
</style>
