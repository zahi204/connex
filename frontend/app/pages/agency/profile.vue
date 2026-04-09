<template>
  <div class="portal-page">
    <h1>Agency Profile</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="profile" class="profile-content">
      <div v-if="successMsg" class="success-message">{{ successMsg }}</div>
      <div class="profile-header">
        <h2>{{ profile.agency_name }}</h2>
        <button v-if="!editing" class="btn-edit" @click="startEdit">Edit Profile</button>
        <div v-else class="edit-actions">
          <button class="btn-save" :disabled="saving" @click="saveProfile">{{ saving ? 'Saving...' : 'Save' }}</button>
          <button class="btn-cancel" @click="cancelEdit">Cancel</button>
        </div>
      </div>

      <div v-if="!editing" class="info-grid">
        <div class="info-item"><span class="label">Agency Name</span><span class="value">{{ profile.agency_name || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Registration Number</span><span class="value">{{ profile.registration_number || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Contact Name</span><span class="value">{{ profile.contact_name || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Contact Email</span><span class="value">{{ profile.contact_email || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Address</span><span class="value">{{ profile.address || 'N/A' }}</span></div>
        <div class="info-item"><span class="label">Status</span><span class="value">{{ profile.status }}</span></div>
      </div>

      <div v-else class="form-grid">
        <div class="field"><label>Agency Name</label><input v-model="form.agency_name" type="text" /></div>
        <div class="field"><label>Registration Number</label><input v-model="form.registration_number" type="text" /></div>
        <div class="field"><label>Contact Name</label><input v-model="form.contact_name" type="text" /></div>
        <div class="field"><label>Contact Email</label><input v-model="form.contact_email" type="email" /></div>
        <div class="field full"><label>Address</label><input v-model="form.address" type="text" /></div>
        <p class="hint">Changes will be submitted for approval.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'agency', middleware: ['auth'] })

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
    const res = await apiFetch('/agency/profile') as any
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
    const res = await apiFetch('/agency/profile', { method: 'PUT', body: { ...form } }) as any
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
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.success-message { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: var(--cx-led-green); padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; }
.profile-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.profile-header h2 { color: var(--cx-text-primary); margin: 0; font-size: 1.2rem; }
.btn-edit, .btn-save, .btn-cancel { padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
.btn-edit { background: rgba(59,130,246,0.2); color: var(--cx-primary); border: 1px solid rgba(59,130,246,0.3); }
.btn-save { background: rgba(34,197,94,0.2); color: var(--cx-led-green); border: 1px solid rgba(34,197,94,0.3); }
.btn-cancel { background: var(--cx-bg-muted); color: var(--cx-text-secondary); border: 1px solid var(--cx-border); }
.edit-actions { display: flex; gap: 0.5rem; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.info-item { background: var(--cx-bg-card); border-radius: 8px; padding: 1rem; }
.info-item .label { display: block; color: var(--cx-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem; }
.info-item .value { color: var(--cx-text-primary); font-size: 0.95rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.25rem; }
.field.full { grid-column: 1 / -1; }
.field label { color: var(--cx-text-secondary); font-size: 0.85rem; }
.field input { padding: 0.65rem 0.75rem; background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 8px; color: var(--cx-text-primary); font-size: 0.9rem; }
.hint { color: var(--cx-text-muted); font-style: italic; grid-column: 1 / -1; font-size: 0.85rem; }
</style>
