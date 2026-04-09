<template>
  <div class="wizard-page cx-flow-page">
    <div class="cx-glass-panel cx-wizard-shell">
      <h1 class="cx-wizard-title">Subcontractor registration</h1>
      <SharedWizardStepper
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
          <div class="field"><label>Company / Business Name *</label><input v-model="form.name" type="text" /></div>
          <div class="field"><label>Registration Number</label><input v-model="form.registration_number" type="text" /></div>
          <div class="field"><label>Email</label><input v-model="form.email" type="email" /></div>
        </div>
      </template>
      <template #step-1>
        <div class="form-grid">
          <div class="field full">
            <label>Specializations</label>
            <div class="checkbox-group">
              <label v-for="spec in specializationOptions" :key="spec">
                <input type="checkbox" :value="spec" v-model="form.specializations" /> {{ spec }}
              </label>
            </div>
          </div>
          <div class="field full">
            <label>Operating Areas</label>
            <div class="checkbox-group">
              <label v-for="area in areaOptions" :key="area">
                <input type="checkbox" :value="area" v-model="form.operating_areas" /> {{ area }}
              </label>
            </div>
          </div>
        </div>
      </template>
      <template #step-2>
        <div class="form-grid">
          <div class="field"><label>Number of Workers</label><input v-model.number="form.number_of_workers" type="number" min="0" /></div>
          <div class="field"><label>Years of Experience</label><input v-model.number="form.years_of_experience" type="number" min="0" /></div>
        </div>
      </template>
      <template #step-3>
        <div class="form-grid">
          <div class="field full"><label>Notable Projects</label><textarea v-model="form.notable_projects" rows="4" placeholder="Describe your notable past projects..." /></div>
        </div>
      </template>
      <template #step-4>
        <div class="form-grid">
          <p class="hint">Document uploads (business license, insurance, certifications) will be available after registration.</p>
        </div>
      </template>
      <template #step-5>
        <div class="review-section">
          <h3>Review Your Information</h3>
          <div class="review-item"><strong>Name:</strong> {{ form.name }}</div>
          <div class="review-item"><strong>Registration #:</strong> {{ form.registration_number || 'Not set' }}</div>
          <div class="review-item"><strong>Email:</strong> {{ form.email || 'Not set' }}</div>
          <div class="review-item"><strong>Specializations:</strong> {{ form.specializations.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Operating Areas:</strong> {{ form.operating_areas.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Workers:</strong> {{ form.number_of_workers || 'Not set' }}</div>
          <div class="review-item"><strong>Experience:</strong> {{ form.years_of_experience ? form.years_of_experience + ' years' : 'Not set' }}</div>
        </div>
      </template>
      </SharedWizardStepper>
      <div v-if="error" class="cx-error error-message">{{ error }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'blank', middleware: ['auth'] })

const { apiFetch } = useApi()
const authStore = useAuthStore()

const step = ref(0)
const loading = ref(false)
const error = ref('')

const specializationOptions = ['formwork', 'rebar', 'electrical', 'plumbing', 'painting', 'waterproofing', 'finishing', 'demolition', 'scaffolding']
const areaOptions = ['north', 'center', 'south', 'nationwide']

const steps = [
  { title: 'Basic Info' },
  { title: 'Expertise' },
  { title: 'Capacity' },
  { title: 'Projects' },
  { title: 'Documents' },
  { title: 'Review' },
]

const form = reactive({
  name: '',
  registration_number: '',
  email: '',
  specializations: [] as string[],
  operating_areas: [] as string[],
  number_of_workers: null as number | null,
  years_of_experience: null as number | null,
  notable_projects: '',
})

const canProceed = computed(() => {
  if (step.value === 0) return !!form.name
  return true
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/wizards/subcontractor', { method: 'POST', body: { ...form } }) as any
    authStore.setUserFromApi(res.data.user)
    await navigateTo('/subcontractor')
  } catch (e: any) {
    error.value = e?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.hint { grid-column: 1 / -1; font-style: italic; }
.review-section {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid var(--cx-border);
  border-radius: var(--cx-radius-lg);
  padding: 1.5rem;
}
.error-message { margin-top: 1rem; }
</style>
