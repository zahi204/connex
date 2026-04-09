<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.profile') }}</h1>

    <SharedLoadingState v-if="loading" :rows="6" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;margin-bottom:1rem;">{{ error }}</div>

    <template v-else-if="profile">
      <!-- Toast -->
      <Transition name="fade">
        <div v-if="toastMsg" class="cx-toast cx-toast-success">✓ {{ toastMsg }}</div>
      </Transition>

      <!-- Hero card -->
      <div class="cx-card" style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <SharedWorkerAvatar :name="profile.full_name" :photo-url="profile.photo_url" :status="profile.status" size="xl" />
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            <div>
              <h2 style="font-family:var(--cx-font-display);font-size:1.5rem;font-weight:800;margin:0 0 0.3rem;color:var(--cx-text-primary);">
                {{ profile.full_name }}
              </h2>
              <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                <SharedStatusBadge :status="profile.status ?? 'unknown'" type="worker" />
                <SharedRatingStars :rating="profile.professional_rating" :max="10" />
              </div>
            </div>
            <div style="margin-inline-start:auto;">
              <button v-if="!editing" class="cx-btn cx-btn-secondary" @click="startEdit">
                {{ $t('common.actions') }} ✎
              </button>
              <div v-else style="display:flex;gap:0.5rem;">
                <button class="cx-btn cx-btn-primary" :disabled="saving" @click="saveProfile">
                  {{ saving ? $t('common.loading') : $t('common.save') }}
                </button>
                <button class="cx-btn cx-btn-ghost" @click="cancelEdit">{{ $t('common.cancel') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- View mode: info grid -->
      <div v-if="!editing" class="cx-bento" style="--cx-bento-min:220px;">
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.id_number') }}</div>
          <div class="cx-info-value cx-mono">{{ profile.id_number || '—' }}</div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.primary_skill') }}</div>
          <div class="cx-info-value">{{ profile.primary_skill || '—' }}</div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.secondary_skill') }}</div>
          <div class="cx-info-value">{{ profile.secondary_skill || '—' }}</div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.country_of_origin') }}</div>
          <div class="cx-info-value">{{ profile.country_of_origin || '—' }}</div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.languages') }}</div>
          <div class="cx-info-value">
            <span v-if="profile.languages?.length" style="display:flex;gap:0.4rem;flex-wrap:wrap;">
              <span v-for="lang in profile.languages" :key="lang" class="cx-chip">{{ lang }}</span>
            </span>
            <span v-else>—</span>
          </div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.work_area') }}</div>
          <div class="cx-info-value">{{ profile.preferred_work_area || '—' }}</div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.availability') }}</div>
          <div class="cx-info-value" style="display:flex;gap:0.75rem;">
            <span :class="profile.available_daily ? 'cx-badge-green' : 'cx-badge-gray'" class="cx-badge">Daily</span>
            <span :class="profile.available_contract ? 'cx-badge-green' : 'cx-badge-gray'" class="cx-badge">Contract</span>
          </div>
        </div>
        <div class="cx-card cx-info-row">
          <div class="cx-info-label">{{ $t('profile.payment_status') }}</div>
          <div class="cx-info-value">
            <SharedStatusBadge :status="profile.payment_status ?? 'unknown'" type="payment" />
          </div>
        </div>
      </div>

      <!-- Edit mode: form -->
      <div v-else class="cx-card">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
          <div>
            <label class="cx-label">{{ $t('profile.full_name') }}</label>
            <input v-model="form.full_name" class="cx-input" type="text" />
          </div>
          <div>
            <label class="cx-label">{{ $t('profile.primary_skill') }}</label>
            <select v-model="form.primary_skill" class="cx-select">
              <option v-for="s in skillOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>
          <div>
            <label class="cx-label">{{ $t('profile.secondary_skill') }}</label>
            <select v-model="form.secondary_skill" class="cx-select">
              <option value="">—</option>
              <option v-for="s in skillOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>
          <div>
            <label class="cx-label">{{ $t('profile.work_area') }}</label>
            <select v-model="form.preferred_work_area" class="cx-select">
              <option v-for="a in areaOptions" :key="a.value" :value="a.value">{{ a.label }}</option>
            </select>
          </div>
          <div>
            <label class="cx-label">{{ $t('profile.languages') }}</label>
            <input v-model="languagesStr" class="cx-input" type="text" :placeholder="$t('profile.languages_hint')" />
          </div>
          <div style="grid-column:1/-1;">
            <p class="cx-label" style="color:var(--cx-text-muted);font-style:italic;">{{ $t('profile.approval_hint') }}</p>
          </div>
        </div>
      </div>
    </template>

    <SharedEmptyState v-else icon="👷" :title="$t('profile.no_profile')" />
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchProfile, profile, profileLoading: loading, profileError: error, updateProfile } = useWorkerPortal()
const { apiFetch } = useApi()

const editing = ref(false)
const saving = ref(false)
const toastMsg = ref('')
const form = reactive<any>({})
const skillOptions = ref<any[]>([])
const areaOptions = ref<any[]>([])

const languagesStr = computed({
  get: () => (form.languages || []).join(', '),
  set: (val: string) => { form.languages = val.split(',').map((s: string) => s.trim()).filter(Boolean) },
})

onMounted(async () => {
  await fetchProfile()
  try {
    const [sRes, aRes] = await Promise.all([
      apiFetch('/reference/skills') as any,
      apiFetch('/reference/work_areas') as any,
    ])
    skillOptions.value = sRes?.data ?? []
    areaOptions.value = aRes?.data ?? []
  } catch { /* reference data not critical */ }
})

function startEdit() {
  Object.assign(form, { ...profile.value })
  editing.value = true
  toastMsg.value = ''
}

function cancelEdit() {
  editing.value = false
}

async function saveProfile() {
  saving.value = true
  try {
    await updateProfile({ ...form })
    editing.value = false
    toastMsg.value = t('profile.submitted_approval')
    setTimeout(() => { toastMsg.value = '' }, 4000)
  } catch (e: any) {
    error.value = e?.data?.message || t('profile.save_failed')
  } finally {
    saving.value = false
  }
}
</script>
