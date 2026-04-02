<template>
  <div class="wizard-page">
    <h1>Staffing Agency Registration</h1>
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
    authStore.setUser(res.data.user)
    await navigateTo('/agency')
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
.checkbox-group { display: flex; gap: 1rem; flex-wrap: wrap; }
.checkbox-group label { display: flex; align-items: center; gap: 0.3rem; color: rgba(255,255,255,0.7); }
.hint { color: rgba(255,255,255,0.5); font-style: italic; grid-column: 1 / -1; }
.review-section { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 1.5rem; }
.review-section h3 { color: white; margin: 0 0 1rem; }
.review-item { color: rgba(255,255,255,0.7); margin-bottom: 0.5rem; }
.review-item strong { color: rgba(255,255,255,0.9); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; text-align: center; margin-top: 1rem; }
</style>
