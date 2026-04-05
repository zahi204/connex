<template>
  <div class="wizard-page cx-page">
    <h1 class="cx-page-title" style="text-align: center;">Worker Registration</h1>
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
          <div class="field"><label class="cx-label">Full Name *</label><input v-model="form.full_name" type="text" class="cx-input" /></div>
          <div class="field"><label class="cx-label">ID Number</label><input v-model="form.id_number" type="text" class="cx-input cx-mono" /></div>
        </div>
      </template>
      <template #step-1>
        <div class="form-grid">
          <div class="field"><label class="cx-label">Country of Origin</label><input v-model="form.country_of_origin" type="text" class="cx-input" /></div>
          <div class="field"><label class="cx-label">Languages (comma separated)</label><input v-model="languagesStr" type="text" class="cx-input" /></div>
          <div class="field"><label class="cx-label">Date of Arrival</label><input v-model="form.date_of_arrival" type="date" class="cx-input cx-mono" /></div>
        </div>
      </template>
      <template #step-2>
        <div class="form-grid">
          <div class="field">
            <label class="cx-label">Primary Skill</label>
            <select v-model="form.primary_skill" class="cx-select">
              <option value="">Select...</option>
              <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div class="field">
            <label class="cx-label">Secondary Skill</label>
            <select v-model="form.secondary_skill" class="cx-select">
              <option value="">Select...</option>
              <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div class="field full"><label class="cx-label">Previous Experience</label><textarea v-model="form.previous_experience" rows="3" class="cx-textarea" /></div>
        </div>
      </template>
      <template #step-3>
        <div class="form-grid">
          <div class="field full">
            <label class="cx-label">Preferred Work Area</label>
            <div class="radio-group">
              <label v-for="a in areas" :key="a" class="radio-option"><input type="radio" v-model="form.preferred_work_area" :value="a" /> <span class="cx-uppercase">{{ a }}</span></label>
            </div>
          </div>
          <div class="field"><label class="toggle-label"><input type="checkbox" v-model="form.available_daily" /> Available for Daily Work</label></div>
          <div class="field"><label class="toggle-label"><input type="checkbox" v-model="form.available_contract" /> Available for Contract Work</label></div>
        </div>
      </template>
      <template #step-4>
        <div class="form-grid">
          <p class="cx-text-muted" style="grid-column: 1 / -1; font-weight: 600;">Document uploads will be available after registration.</p>
        </div>
      </template>
      <template #step-5>
        <div class="form-grid">
          <p class="cx-text-muted" style="grid-column: 1 / -1; font-weight: 600;">If you were referred by a staffing agency, select it here. Otherwise, skip this step.</p>
        </div>
      </template>
      <template #step-6>
        <div class="review-section">
          <h3>Review Your Information</h3>
          <div class="review-row"><span class="cx-label" style="margin: 0;">Name</span><span class="cx-text-primary">{{ form.full_name }}</span></div>
          <div class="review-row"><span class="cx-label" style="margin: 0;">Skill</span><span class="cx-badge-orange">{{ form.primary_skill || 'Not set' }}</span></div>
          <div class="review-row"><span class="cx-label" style="margin: 0;">Area</span><span class="cx-text-secondary">{{ form.preferred_work_area || 'Not set' }}</span></div>
          <div class="review-row"><span class="cx-label" style="margin: 0;">Country</span><span class="cx-text-secondary">{{ form.country_of_origin || 'Not set' }}</span></div>
        </div>
      </template>
    </WizardStepper>
    <div v-if="error" class="cx-error" style="text-align: center; margin-top: 1rem;">{{ error }}</div>
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
.wizard-page { min-height: 100vh; background: var(--cx-bg-primary); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.field { display: flex; flex-direction: column; gap: 0.35rem; }
.field.full { grid-column: 1 / -1; }
.radio-group { display: flex; gap: 1.25rem; flex-wrap: wrap; margin-top: 0.25rem; }
.radio-option {
  display: flex; align-items: center; gap: 0.5rem;
  color: var(--cx-text-secondary); font-size: var(--cx-font-sm); font-weight: 600;
  cursor: pointer; min-height: var(--cx-tap-min);
}
.toggle-label {
  display: flex; align-items: center; gap: 0.5rem;
  color: var(--cx-text-secondary); font-size: var(--cx-font-sm); font-weight: 600;
  cursor: pointer; min-height: var(--cx-tap-min);
}
.review-section { padding: 0.5rem 0; }
.review-section h3 { color: var(--cx-text-primary); font-weight: 800; margin: 0 0 1.25rem; }
.review-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.75rem 0; border-bottom: 1px solid var(--cx-border);
}
</style>
