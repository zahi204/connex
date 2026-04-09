<template>
  <div class="wizard-page cx-flow-page">
    <div class="cx-glass-panel cx-wizard-shell">
      <h1 class="cx-wizard-title">Staffing agency registration</h1>
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
          <div class="field"><label>Agency Name *</label><input v-model="form.agency_name" type="text" /></div>
          <div class="field"><label>Registration Number</label><input v-model="form.registration_number" type="text" /></div>
          <div class="field"><label>Email</label><input v-model="form.email" type="email" /></div>
        </div>
      </template>
      <template #step-1>
        <div class="form-grid">
          <div class="field"><label>Contact Person Name</label><input v-model="form.contact_person_name" type="text" /></div>
          <div class="field"><label>Contact Person Role</label><input v-model="form.contact_person_role" type="text" /></div>
          <div class="field"><label>Contact Phone</label><input v-model="form.contact_person_phone" type="tel" /></div>
          <div class="field"><label>Contact Email</label><input v-model="form.contact_person_email" type="email" /></div>
        </div>
      </template>
      <template #step-2>
        <div class="form-grid">
          <div class="field full">
            <label>Worker Types Supplied</label>
            <div class="checkbox-group">
              <label v-for="wt in workerTypeOptions" :key="wt">
                <input type="checkbox" :value="wt" v-model="form.worker_types" /> {{ wt }}
              </label>
            </div>
          </div>
          <div class="field full">
            <label>Countries of Origin</label>
            <div class="checkbox-group">
              <label v-for="country in countryOptions" :key="country">
                <input type="checkbox" :value="country" v-model="form.countries_of_origin" /> {{ country }}
              </label>
            </div>
          </div>
        </div>
      </template>
      <template #step-3>
        <div class="form-grid">
          <div class="field"><label>Average Worker Capacity</label><input v-model.number="form.average_capacity" type="number" min="0" /></div>
          <div class="field"><label>Monthly Throughput</label><input v-model.number="form.monthly_throughput" type="number" min="0" /></div>
        </div>
      </template>
      <template #step-4>
        <div class="form-grid">
          <p class="hint">Document uploads (agency license, agreements) will be available after registration.</p>
        </div>
      </template>
      <template #step-5>
        <div class="review-section">
          <h3>Review Your Information</h3>
          <div class="review-item"><strong>Agency:</strong> {{ form.agency_name }}</div>
          <div class="review-item"><strong>Registration #:</strong> {{ form.registration_number || 'Not set' }}</div>
          <div class="review-item"><strong>Email:</strong> {{ form.email || 'Not set' }}</div>
          <div class="review-item"><strong>Contact:</strong> {{ form.contact_person_name || 'Not set' }}</div>
          <div class="review-item"><strong>Worker Types:</strong> {{ form.worker_types.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Countries:</strong> {{ form.countries_of_origin.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Capacity:</strong> {{ form.average_capacity || 'Not set' }}</div>
          <div class="review-item"><strong>Monthly Throughput:</strong> {{ form.monthly_throughput || 'Not set' }}</div>
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

const workerTypeOptions = ['general', 'skilled', 'semi-skilled', 'specialized', 'foreman']
const countryOptions = ['Moldova', 'Ukraine', 'Turkey', 'China', 'Thailand', 'Sri Lanka', 'India', 'Philippines', 'Other']

const steps = [
  { title: 'Agency Info' },
  { title: 'Contact' },
  { title: 'Workers' },
  { title: 'Capacity' },
  { title: 'Documents' },
  { title: 'Review' },
]

const form = reactive({
  agency_name: '',
  registration_number: '',
  email: '',
  contact_person_name: '',
  contact_person_role: '',
  contact_person_phone: '',
  contact_person_email: '',
  worker_types: [] as string[],
  countries_of_origin: [] as string[],
  average_capacity: null as number | null,
  monthly_throughput: null as number | null,
})

const canProceed = computed(() => {
  if (step.value === 0) return !!form.agency_name
  return true
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/wizards/agency', { method: 'POST', body: { ...form } }) as any
    authStore.setUserFromApi(res.data.user)
    await navigateTo('/agency')
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
