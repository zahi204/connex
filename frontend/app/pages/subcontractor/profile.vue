<template>
  <div class="portal-page">
    <h1>My Profile</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="profile" class="profile-content">
      <div v-if="successMsg" class="success-message">{{ successMsg }}</div>
      <div class="profile-header">
        <h2>{{ profile.company_name || profile.full_name }}</h2>
        <button v-if="!editing" class="btn-edit" @click="startEdit">Edit Profile</button>
        <div v-else class="edit-actions">
          <button class="btn-save" :disabled="saving" @click="saveProfile">{{ saving ? 'Saving...' : 'Save' }}</button>
          <button class="btn-cancel" @click="cancelEdit">Cancel</button>
        </div>
      </div>

      <div v-if="!editing" class="info-grid">
        <div class="info-item"><span class="label">Company Name</span><span class="value">{{ profile.company_name || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Contact Name</span><span class="value">{{ profile.contact_name || profile.full_name || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Specialization</span><span class="value">{{ profile.specialization || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">License Number</span><span class="value">{{ profile.license_number || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Work Area</span><span class="value">{{ profile.work_area || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Status</span><span class="value">{{ profile.status }}</span></div>
      </div>

      <div v-else class="form-grid">
        <div class="field"><label>Company Name</label><input v-model="form.company_name" type="text" /></div>
        <div class="field"><label>Contact Name</label><input v-model="form.contact_name" type="text" /></div>
        <div class="field"><label>Specialization</label><input v-model="form.specialization" type="text" /></div>
        <div class="field"><label>License Number</label><input v-model="form.license_number" type="text" /></div>
        <div class="field"><label>Work Area</label><input v-model="form.work_area" type="text" /></div>
        <p class="hint">Changes will be submitted for approval.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'subcontractor', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const saving = ref(false)
const editing = ref(false)
const successMsg = ref('')
const profile = ref<any>(null)
const form = reactive<any>({})

onMounted(async () => {
  try {
    const res = await apiFetch('/subcontractor/profile') as any
    profile.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load profile'
  } finally {
    loading.value = false
  }
})

function startEdit() {
  Object.assign(form, { ...profile.value })
  editing.value = true
  successMsg.value = ''
}

function cancelEdit() {
  editing.value = false
}

async function saveProfile() {
  saving.value = true
  error.value = ''
  try {
    const res = await apiFetch('/subcontractor/profile', { method: 'PUT', body: { ...form } }) as any
    profile.value = res.data
    editing.value = false
    successMsg.value = 'Profile update submitted for approval.'
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to save profile'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.success-message { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; }
.profile-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.profile-header h2 { color: white; margin: 0; font-size: 1.2rem; }
.btn-edit, .btn-save, .btn-cancel { padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
.btn-edit { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
.btn-save { background: rgba(34,197,94,0.2); color: #86efac; border: 1px solid rgba(34,197,94,0.3); }
.btn-cancel { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.15); }
.edit-actions { display: flex; gap: 0.5rem; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.info-item { background: rgba(255,255,255,0.05); border-radius: 8px; padding: 1rem; }
.info-item .label { display: block; color: rgba(255,255,255,0.5); font-size: 0.75rem; margin-bottom: 0.25rem; }
.info-item .value { color: white; font-size: 0.95rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field label { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.field input { padding: 0.65rem 0.75rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: white; font-size: 0.9rem; }
.hint { color: rgba(255,255,255,0.4); font-style: italic; grid-column: 1 / -1; font-size: 0.85rem; }
</style>
