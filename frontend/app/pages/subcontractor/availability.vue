<template>
  <div class="portal-page">
    <h1>Update Availability</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="successMsg" class="success-message">{{ successMsg }}</div>

      <div class="current-status">
        <h2>Current Availability</h2>
        <div class="info-grid">
          <div class="info-item"><span class="label">Status</span><span class="value">{{ availability.status || 'N/A' }}</span></div>
          <div class="info-item"><span class="label">Available From</span><span class="value">{{ availability.available_from || 'N/A' }}</span></div>
          <div class="info-item"><span class="label">Available Until</span><span class="value">{{ availability.available_until || 'N/A' }}</span></div>
          <div class="info-item"><span class="label">Notes</span><span class="value">{{ availability.notes || 'None' }}</span></div>
        </div>
      </div>

      <hr class="divider" />

      <h2 class="section-title">Update Availability</h2>
      <div class="form-grid">
        <div class="field">
          <label>Availability Status</label>
          <select v-model="form.status">
            <option value="available">Available</option>
            <option value="busy">Busy</option>
            <option value="unavailable">Unavailable</option>
            <option value="limited">Limited Availability</option>
          </select>
        </div>
        <div class="field"><label>Available From</label><input v-model="form.available_from" type="date" /></div>
        <div class="field"><label>Available Until</label><input v-model="form.available_until" type="date" /></div>
        <div class="field full"><label>Notes</label><textarea v-model="form.notes" rows="2" placeholder="Any additional availability notes..." /></div>
        <div class="field">
          <button class="btn-submit" :disabled="saving" @click="updateAvailability">
            {{ saving ? 'Submitting...' : 'Submit for Approval' }}
          </button>
        </div>
      </div>
      <p class="hint">Availability changes are submitted for coordinator approval.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'subcontractor', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const successMsg = ref('')
const availability = ref<any>({})

const form = reactive({
  status: 'available',
  available_from: '',
  available_until: '',
  notes: '',
})

onMounted(async () => {
  try {
    const res = await apiFetch('/subcontractor/availability') as any
    availability.value = res.data || {}
    if (res.data) {
      form.status = res.data.status || 'available'
      form.available_from = res.data.available_from || ''
      form.available_until = res.data.available_until || ''
      form.notes = res.data.notes || ''
    }
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load availability'
  } finally {
    loading.value = false
  }
})

async function updateAvailability() {
  saving.value = true
  error.value = ''
  successMsg.value = ''
  try {
    const res = await apiFetch('/subcontractor/availability', { method: 'PUT', body: { ...form } }) as any
    availability.value = res.data || { ...form }
    successMsg.value = 'Availability update submitted for approval.'
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to update availability'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.success-message { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: var(--cx-led-green); padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; }
.current-status h2, .section-title { color: var(--cx-text-primary); font-size: 1.1rem; margin-bottom: 0.75rem; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.info-item { background: var(--cx-bg-card); border-radius: 8px; padding: 0.75rem; }
.info-item .label { display: block; color: var(--cx-text-muted); font-size: 0.75rem; margin-bottom: 0.2rem; }
.info-item .value { color: var(--cx-text-primary); font-size: 0.9rem; }
.divider { border: none; border-top: 1px solid var(--cx-border); margin: 1.5rem 0; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field.full { grid-column: 1 / -1; }
.field label { color: var(--cx-text-secondary); font-size: 0.85rem; }
.field input, .field select, .field textarea { padding: 0.65rem 0.75rem; background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 8px; color: var(--cx-text-primary); font-size: 0.9rem; }
.btn-submit { padding: 0.6rem 1.5rem; background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3); color: var(--cx-primary); border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
.hint { color: var(--cx-text-muted); font-size: 0.8rem; font-style: italic; margin-top: 0.75rem; }
</style>
