<template>
  <div class="portal-page">
    <h1>BOQ Requests</h1>

    <div class="section-header">
      <h2>Submit New Request</h2>
    </div>
    <div class="form-grid">
      <div class="field"><label>Project</label>
        <select v-model="newRequest.project_id">
          <option value="">Select Project...</option>
          <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
      </div>
      <div class="field full"><label>Description</label><textarea v-model="newRequest.description" rows="3" placeholder="Describe the BOQ request..." /></div>
      <div class="field">
        <button class="btn-submit" :disabled="!newRequest.project_id || submitting" @click="submitRequest">
          {{ submitting ? 'Submitting...' : 'Submit Request' }}
        </button>
      </div>
    </div>
    <div v-if="submitError" class="error-message" style="margin-top: 0.5rem;">{{ submitError }}</div>
    <div v-if="submitSuccess" class="success-message" style="margin-top: 0.5rem;">{{ submitSuccess }}</div>

    <hr class="divider" />

    <h2 class="section-title">Previous Requests</h2>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!requests.length" class="empty">No BOQ requests yet.</div>
      <div v-else class="requests-list">
        <div v-for="r in requests" :key="r.id" class="request-card">
          <div class="request-info">
            <div class="request-project">{{ r.project_name || 'Project #' + r.project_id }}</div>
            <div class="request-desc">{{ r.description }}</div>
            <div class="request-date">{{ r.created_at }}</div>
          </div>
          <span class="status-badge" :class="r.status">{{ r.status }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const requests = ref<any[]>([])
const projects = ref<any[]>([])
const submitting = ref(false)
const submitError = ref('')
const submitSuccess = ref('')

const newRequest = reactive({
  project_id: '',
  description: '',
})

onMounted(async () => {
  try {
    const [reqRes, projRes] = await Promise.all([
      apiFetch('/developer/boq-requests') as Promise<any>,
      apiFetch('/developer/projects') as Promise<any>,
    ])
    requests.value = reqRes.data || []
    projects.value = projRes.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load data'
  } finally {
    loading.value = false
  }
})

async function submitRequest() {
  submitting.value = true
  submitError.value = ''
  submitSuccess.value = ''
  try {
    const res = await apiFetch('/developer/boq-requests', { method: 'POST', body: { ...newRequest } }) as any
    requests.value.unshift(res.data)
    newRequest.project_id = ''
    newRequest.description = ''
    submitSuccess.value = 'BOQ request submitted successfully.'
  } catch (e: any) {
    submitError.value = e?.data?.message || 'Failed to submit request'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.success-message { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 0.75rem; border-radius: 10px; }
.section-header h2 { color: white; font-size: 1.1rem; margin-bottom: 0.75rem; }
.section-title { color: white; font-size: 1.1rem; margin-bottom: 0.75rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field.full { grid-column: 1 / -1; }
.field label { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.field select, .field textarea { padding: 0.65rem 0.75rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: white; font-size: 0.9rem; }
.btn-submit { padding: 0.6rem 1.5rem; background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3); color: #93c5fd; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
.divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }
.requests-list { display: flex; flex-direction: column; gap: 0.5rem; }
.request-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.request-project { color: white; font-weight: 500; margin-bottom: 0.2rem; }
.request-desc { color: rgba(255,255,255,0.6); font-size: 0.85rem; }
.request-date { color: rgba(255,255,255,0.3); font-size: 0.75rem; margin-top: 0.2rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.approved { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.rejected { background: rgba(239,68,68,0.2); color: #f87171; }
.status-badge.processing { background: rgba(59,130,246,0.2); color: #93c5fd; }
</style>
