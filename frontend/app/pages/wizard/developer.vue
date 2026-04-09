<template>
  <div class="wizard-page cx-flow-page">
    <div class="cx-glass-panel cx-wizard-shell">
      <h1 class="cx-wizard-title">Developer registration</h1>
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
          <div class="field"><label>Company Name *</label><input v-model="form.company_name" type="text" /></div>
          <div class="field"><label>Registration Number</label><input v-model="form.registration_number" type="text" /></div>
          <div class="field"><label>Email</label><input v-model="form.email" type="email" /></div>
        </div>
      </template>
      <template #step-1>
        <div class="form-grid">
          <div class="field full"><label>Company Description</label><textarea v-model="form.company_description" rows="4" /></div>
          <div class="field">
            <label>Company Size</label>
            <select v-model="form.company_size">
              <option value="">Select...</option>
              <option v-for="size in companySizes" :key="size" :value="size">{{ size }}</option>
            </select>
          </div>
        </div>
      </template>
      <template #step-2>
        <div class="form-grid">
          <div class="field full">
            <label>Areas of Operation</label>
            <div class="checkbox-group">
              <label v-for="area in operationAreas" :key="area">
                <input type="checkbox" :value="area" v-model="form.areas_of_operation" /> {{ area }}
              </label>
            </div>
          </div>
          <div class="field full">
            <label>Specializations</label>
            <div class="checkbox-group">
              <label v-for="spec in specializations" :key="spec">
                <input type="checkbox" :value="spec" v-model="form.specializations" /> {{ spec }}
              </label>
            </div>
          </div>
        </div>
      </template>
      <template #step-3>
        <div class="form-grid">
          <div class="field"><label>Contact Person Name</label><input v-model="form.contact_person_name" type="text" /></div>
          <div class="field"><label>Contact Person Role</label><input v-model="form.contact_person_role" type="text" /></div>
          <div class="field"><label>Contact Phone</label><input v-model="form.contact_person_phone" type="tel" /></div>
          <div class="field"><label>Contact Email</label><input v-model="form.contact_person_email" type="email" /></div>
        </div>
      </template>
      <template #step-4>
        <div class="form-grid">
          <p class="hint">Document uploads (company registration, licenses) will be available after registration.</p>
        </div>
      </template>
      <template #step-5>
        <div class="review-section">
          <h3>Review Your Information</h3>
          <div class="review-item"><strong>Company:</strong> {{ form.company_name }}</div>
          <div class="review-item"><strong>Registration #:</strong> {{ form.registration_number || 'Not set' }}</div>
          <div class="review-item"><strong>Email:</strong> {{ form.email || 'Not set' }}</div>
          <div class="review-item"><strong>Size:</strong> {{ form.company_size || 'Not set' }}</div>
          <div class="review-item"><strong>Areas:</strong> {{ form.areas_of_operation.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Specializations:</strong> {{ form.specializations.join(', ') || 'None selected' }}</div>
          <div class="review-item"><strong>Contact:</strong> {{ form.contact_person_name || 'Not set' }}</div>
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

const companySizes = ['1-10', '11-50', '51-200', '201-500', '500+']
const operationAreas = ['north', 'center', 'south', 'nationwide']
const specializations = ['residential', 'commercial', 'industrial', 'infrastructure', 'renovation']

const steps = [
  { title: 'Company Info' },
  { title: 'Details' },
  { title: 'Operations' },
  { title: 'Contact' },
  { title: 'Documents' },
  { title: 'Review' },
]

const form = reactive({
  company_name: '',
  registration_number: '',
  email: '',
  company_description: '',
  company_size: '',
  areas_of_operation: [] as string[],
  specializations: [] as string[],
  contact_person_name: '',
  contact_person_role: '',
  contact_person_phone: '',
  contact_person_email: '',
})

const canProceed = computed(() => {
  if (step.value === 0) return !!form.company_name
  return true
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/wizards/developer', { method: 'POST', body: { ...form } }) as any
    authStore.setUserFromApi(res.data.user)
    await navigateTo('/developer')
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
