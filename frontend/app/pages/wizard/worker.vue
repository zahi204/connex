<template>
  <div class="wizard-page">
    <h1>Worker Registration</h1>
    <WizardStepper
      :steps="steps"
      :current-step="step"
      :can-proceed="canProceed"
      :loading="loading"
      @next="step++"
      @prev="step--"
      @submit="handleSubmit"
    >
      <template #step-0>
        <div class="form-grid">
          <div class="field"><label>Full Name *</label><input v-model="form.full_name" type="text" /></div>
          <div class="field"><label>ID Number</label><input v-model="form.id_number" type="text" /></div>
        </div>
      </template>
      <template #step-1>
        <div class="form-grid">
          <div class="field"><label>Country of Origin</label><input v-model="form.country_of_origin" type="text" /></div>
          <div class="field"><label>Languages (comma separated)</label><input v-model="languagesStr" type="text" /></div>
          <div class="field"><label>Date of Arrival</label><input v-model="form.date_of_arrival" type="date" /></div>
        </div>
      </template>
      <template #step-2>
        <div class="form-grid">
          <div class="field">
            <label>Primary Skill</label>
            <select v-model="form.primary_skill">
              <option value="">Select...</option>
              <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div class="field">
            <label>Secondary Skill</label>
            <select v-model="form.secondary_skill">
              <option value="">Select...</option>
              <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div class="field full"><label>Previous Experience</label><textarea v-model="form.previous_experience" rows="3" /></div>
        </div>
      </template>
      <template #step-3>
        <div class="form-grid">
          <div class="field">
            <label>Preferred Work Area</label>
            <div class="radio-group">
              <label v-for="a in areas" :key="a"><input type="radio" v-model="form.preferred_work_area" :value="a" /> {{ a }}</label>
            </div>
          </div>
          <div class="field"><label><input type="checkbox" v-model="form.available_daily" /> Available for Daily Work</label></div>
          <div class="field"><label><input type="checkbox" v-model="form.available_contract" /> Available for Contract Work</label></div>
        </div>
      </template>
      <template #step-4>
        <div class="form-grid">
          <p class="hint">Document uploads will be available after registration.</p>
        </div>
      </template>
      <template #step-5>
        <div class="form-grid">
          <p class="hint">If you were referred by a staffing agency, select it here. Otherwise, skip this step.</p>
        </div>
      </template>
      <template #step-6>
        <div class="review-section">
          <h3>Review Your Information</h3>
          <div class="review-item"><strong>Name:</strong> {{ form.full_name }}</div>
          <div class="review-item"><strong>Skill:</strong> {{ form.primary_skill || 'Not set' }}</div>
          <div class="review-item"><strong>Area:</strong> {{ form.preferred_work_area || 'Not set' }}</div>
          <div class="review-item"><strong>Country:</strong> {{ form.country_of_origin || 'Not set' }}</div>
        </div>
      </template>
    </WizardStepper>
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'blank', middleware: ['auth'] })

const { apiFetch } = useApi()
const authStore = useAuthStore()

const step = ref(0)
const loading = ref(false)
const error = ref('')
const skills = ['formwork', 'rebar', 'general', 'finishing', 'skeleton', 'electrical', 'plumbing', 'painting', 'waterproofing']
const areas = ['north', 'center', 'south', 'flexible']

const steps = [
  { title: 'Personal Info' },
  { title: 'Background' },
  { title: 'Skills' },
  { title: 'Preferences' },
  { title: 'Documents' },
  { title: 'Agency' },
  { title: 'Review' },
]

const form = reactive({
  full_name: '',
  id_number: '',
  country_of_origin: '',
  languages: [] as string[],
  date_of_arrival: '',
  primary_skill: '',
  secondary_skill: '',
  previous_experience: '',
  preferred_work_area: '',
  available_daily: false,
  available_contract: false,
  staffing_agency_id: null as number | null,
})

const languagesStr = computed({
  get: () => form.languages.join(', '),
  set: (val: string) => { form.languages = val.split(',').map(s => s.trim()).filter(Boolean) },
})

const canProceed = computed(() => {
  if (step.value === 0) return !!form.full_name
  return true
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/wizards/worker', { method: 'POST', body: { ...form } }) as any
    authStore.setUser(res.data.user)
    await navigateTo('/worker')
  } catch (e: any) {
    error.value = e?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.wizard-page { min-height: 100vh; padding: 2rem; }
.wizard-page h1 { text-align: center; color: white; font-size: 1.5rem; margin-bottom: 2rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field.full { grid-column: 1 / -1; }
.field label { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.field input, .field select, .field textarea {
  padding: 0.65rem 0.75rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
  border-radius: 8px; color: white; font-size: 0.9rem;
}
.radio-group { display: flex; gap: 1rem; flex-wrap: wrap; }
.radio-group label { display: flex; align-items: center; gap: 0.3rem; color: rgba(255,255,255,0.7); }
.hint { color: rgba(255,255,255,0.5); font-style: italic; grid-column: 1 / -1; }
.review-section { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 1.5rem; }
.review-section h3 { color: white; margin: 0 0 1rem; }
.review-item { color: rgba(255,255,255,0.7); margin-bottom: 0.5rem; }
.review-item strong { color: rgba(255,255,255,0.9); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; text-align: center; margin-top: 1rem; }
</style>
