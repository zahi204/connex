<template>
  <div class="directory-search">
    <h2 class="cx-page-title">Worker Directory</h2>

    <div class="filters-bar">
      <div class="filter-field">
        <label class="cx-label">Skill</label>
        <select v-model="filters.skill" class="cx-select" @change="search">
          <option value="">All Skills</option>
          <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>
      <div class="filter-field">
        <label class="cx-label">Status</label>
        <select v-model="filters.status" class="cx-select" @change="search">
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="available">Available</option>
          <option value="assigned">Assigned</option>
        </select>
      </div>
      <div class="filter-field">
        <label class="cx-label">Area</label>
        <select v-model="filters.area" class="cx-select" @change="search">
          <option value="">All Areas</option>
          <option value="north">North</option>
          <option value="center">Center</option>
          <option value="south">South</option>
        </select>
      </div>
      <div class="filter-field">
        <label class="cx-label">Min Rating</label>
        <input v-model.number="filters.min_rating" class="cx-input" type="number" min="0" max="5" step="0.5" placeholder="0" @change="search" />
      </div>
      <div class="filter-field">
        <button class="cx-btn cx-btn-primary" @click="search">Search</button>
      </div>
    </div>

    <div class="view-toggle">
      <button :class="['cx-btn cx-btn-secondary', { active: viewMode === 'card' }]" @click="viewMode = 'card'">Cards</button>
      <button :class="['cx-btn cx-btn-secondary', { active: viewMode === 'list' }]" @click="viewMode = 'list'">List</button>
    </div>

    <div v-if="loading" class="cx-loading">Searching...</div>
    <div v-else-if="error" class="cx-error">{{ error }}</div>
    <div v-else>
      <div v-if="!workers.length" class="cx-empty">No workers found matching your criteria.</div>

      <!-- Card View -->
      <div v-else-if="viewMode === 'card'" class="cx-bento">
        <div v-for="w in workers" :key="w.id" class="cx-card">
          <div class="card-header">
            <div class="cx-font-bold">{{ w.full_name }}</div>
            <span class="cx-badge" :class="statusBadge(w.status)">{{ w.status }}</span>
          </div>
          <div class="card-details">
            <div v-if="w.primary_skill" class="cx-text-sm cx-text-secondary"><span class="cx-text-muted">Skill:</span> {{ w.primary_skill }}</div>
            <div v-if="w.preferred_work_area" class="cx-text-sm cx-text-secondary"><span class="cx-text-muted">Area:</span> {{ w.preferred_work_area }}</div>
            <div v-if="w.professional_rating != null" class="cx-text-sm cx-text-secondary"><span class="cx-text-muted">Rating:</span> <span class="cx-mono">{{ w.professional_rating }}</span></div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <table v-else class="cx-table">
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
            <td><span class="cx-mono">{{ w.professional_rating ?? '-' }}</span></td>
            <td><span class="cx-badge" :class="statusBadge(w.status)">{{ w.status }}</span></td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button class="cx-btn cx-btn-secondary" :disabled="page <= 1" @click="goToPage(page - 1)">Prev</button>
        <span class="cx-text-muted cx-text-sm">Page {{ page }} of {{ totalPages }}</span>
        <button class="cx-btn cx-btn-secondary" :disabled="page >= totalPages" @click="goToPage(page + 1)">Next</button>
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

function statusBadge(status: string) {
  switch (status) {
    case 'active': return 'cx-badge-green'
    case 'available': return 'cx-badge-blue'
    case 'assigned': return 'cx-badge-orange'
    case 'pending': return 'cx-badge-yellow'
    default: return 'cx-badge-gray'
  }
}

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
.filters-bar {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
  align-items: flex-end;
}

.filter-field {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.filter-field .cx-select,
.filter-field .cx-input {
  min-width: 120px;
  width: auto;
}

.view-toggle {
  display: flex;
  gap: 0.25rem;
  margin-bottom: 1rem;
}

.view-toggle .cx-btn {
  min-height: auto;
  padding: 0.4rem 0.85rem;
  font-size: var(--cx-font-xs);
}

.view-toggle .cx-btn.active {
  background: var(--cx-accent-soft);
  border-color: var(--cx-border-accent);
  color: var(--cx-accent);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.card-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination .cx-btn {
  min-height: auto;
  padding: 0.4rem 1rem;
  font-size: var(--cx-font-sm);
}
</style>
