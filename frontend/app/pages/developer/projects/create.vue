<template>
  <div class="portal-page">
    <h1>Submit New Project</h1>
    <div v-if="error" class="error-message">{{ error }}</div>

    <div class="wizard">
      <div class="steps-indicator">
        <div
          v-for="(s, i) in steps"
          :key="i"
          class="step-dot"
          :class="{ active: step === i, done: step > i }"
        >
          <span class="step-num">{{ i + 1 }}</span>
          <span class="step-title">{{ s }}</span>
        </div>
      </div>

      <!-- Step 1: Basic Info -->
      <div v-if="step === 0" class="form-grid">
        <div class="field"><label>Project Name *</label><input v-model="form.name" type="text" /></div>
        <div class="field"><label>Location</label><input v-model="form.location" type="text" /></div>
        <div class="field full"><label>Description</label><textarea v-model="form.description" rows="3" /></div>
      </div>

      <!-- Step 2: Project Details -->
      <div v-if="step === 1" class="form-grid">
        <div class="field"><label>Project Type</label><input v-model="form.project_type" type="text" /></div>
        <div class="field"><label>Total Area (sqm)</label><input v-model.number="form.total_area" type="number" /></div>
        <div class="field"><label>Number of Units</label><input v-model.number="form.number_of_units" type="number" /></div>
        <div class="field"><label>Number of Floors</label><input v-model.number="form.number_of_floors" type="number" /></div>
      </div>

      <!-- Step 3: Timeline -->
      <div v-if="step === 2" class="form-grid">
        <div class="field"><label>Estimated Start Date</label><input v-model="form.estimated_start_date" type="date" /></div>
        <div class="field"><label>Estimated End Date</label><input v-model="form.estimated_end_date" type="date" /></div>
        <div class="field"><label>Duration (months)</label><input v-model.number="form.duration_months" type="number" /></div>
      </div>

      <!-- Step 4: Requirements -->
      <div v-if="step === 3" class="form-grid">
        <div class="field"><label>Workers Needed</label><input v-model.number="form.workers_needed" type="number" /></div>
        <div class="field full"><label>Required Skills (comma separated)</label><input v-model="skillsStr" type="text" /></div>
        <div class="field full"><label>Special Requirements</label><textarea v-model="form.special_requirements" rows="3" /></div>
      </div>

      <!-- Step 5: Documents -->
      <div v-if="step === 4" class="form-grid">
        <p class="hint">Document uploads (permits, plans, etc.) will be available after project submission is approved.</p>
      </div>

      <!-- Step 6: Review -->
      <div v-if="step === 5" class="review-section">
        <h3>Review Project Details</h3>
        <div class="review-grid">
          <div class="review-item"><strong>Name:</strong> {{ form.name }}</div>
          <div class="review-item"><strong>Location:</strong> {{ form.location || 'N/A' }}</div>
          <div class="review-item"><strong>Type:</strong> {{ form.project_type || 'N/A' }}</div>
          <div class="review-item"><strong>Area:</strong> {{ form.total_area ? form.total_area + ' sqm' : 'N/A' }}</div>
          <div class="review-item"><strong>Start:</strong> {{ form.estimated_start_date || 'N/A' }}</div>
          <div class="review-item"><strong>Workers:</strong> {{ form.workers_needed || 'N/A' }}</div>
          <div class="review-item"><strong>Skills:</strong> {{ (form.required_skills || []).join(', ') || 'N/A' }}</div>
        </div>
      </div>

      <div class="wizard-actions">
        <button v-if="step > 0" class="btn-prev" @click="step--">Previous</button>
        <div class="spacer" />
        <button v-if="step < steps.length - 1" class="btn-next" :disabled="!canProceed" @click="step++">Next</button>
        <button v-else class="btn-submit" :disabled="submitting" @click="handleSubmit">
          {{ submitting ? 'Submitting...' : 'Submit Project' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const { apiFetch } = useApi()
const step = ref(0)
const error = ref('')
const submitting = ref(false)

const steps = ['Basic Info', 'Details', 'Timeline', 'Requirements', 'Documents', 'Review']

const form = reactive({
  name: '',
  location: '',
  description: '',
  project_type: '',
  total_area: null as number | null,
  number_of_units: null as number | null,
  number_of_floors: null as number | null,
  estimated_start_date: '',
  estimated_end_date: '',
  duration_months: null as number | null,
  workers_needed: null as number | null,
  required_skills: [] as string[],
  special_requirements: '',
})

const skillsStr = computed({
  get: () => form.required_skills.join(', '),
  set: (val: string) => { form.required_skills = val.split(',').map(s => s.trim()).filter(Boolean) },
})

const canProceed = computed(() => {
  if (step.value === 0) return !!form.name
  return true
})

async function handleSubmit() {
  submitting.value = true
  error.value = ''
  try {
    await apiFetch('/developer/projects', { method: 'POST', body: { ...form } })
    await navigateTo('/developer/projects')
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to submit project'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; }
.steps-indicator { display: flex; gap: 0.5rem; margin-bottom: 2rem; overflow-x: auto; }
.step-dot { display: flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; background: rgba(255,255,255,0.06); border-radius: 20px; white-space: nowrap; }
.step-dot.active { background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.4); }
.step-dot.done { background: rgba(34,197,94,0.15); }
.step-num { color: rgba(255,255,255,0.5); font-size: 0.75rem; font-weight: 700; }
.step-dot.active .step-num { color: #93c5fd; }
.step-dot.done .step-num { color: #4ade80; }
.step-title { color: rgba(255,255,255,0.5); font-size: 0.75rem; }
.step-dot.active .step-title { color: #93c5fd; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field.full { grid-column: 1 / -1; }
.field label { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.field input, .field textarea { padding: 0.65rem 0.75rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: white; font-size: 0.9rem; }
.hint { color: rgba(255,255,255,0.4); font-style: italic; grid-column: 1 / -1; }
.review-section { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 1.5rem; }
.review-section h3 { color: white; margin: 0 0 1rem; }
.review-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
.review-item { color: rgba(255,255,255,0.7); font-size: 0.9rem; }
.review-item strong { color: rgba(255,255,255,0.9); }
.wizard-actions { display: flex; align-items: center; margin-top: 2rem; gap: 0.75rem; }
.spacer { flex: 1; }
.btn-prev, .btn-next, .btn-submit { padding: 0.6rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; }
.btn-prev { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.15); }
.btn-next { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
.btn-submit { background: rgba(34,197,94,0.2); color: #86efac; border: 1px solid rgba(34,197,94,0.3); }
.btn-next:disabled, .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
