<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.availability') }}</h1>

    <SharedLoadingState v-if="loading" :rows="4" />
    <div v-else-if="loadError" class="cx-toast cx-toast-error" style="position:static;margin-bottom:1rem;">{{ loadError }}</div>

    <Transition name="fade">
      <div v-if="toastMsg" class="cx-toast cx-toast-success">✓ {{ toastMsg }}</div>
    </Transition>

    <div class="cx-card" style="max-width:560px;">
      <h2 class="cx-section-title">{{ $t('availability.update') }}</h2>

      <!-- Daily toggle -->
      <div style="margin-bottom:1.25rem;">
        <label class="cx-toggle">
          <input v-model="form.available_daily" type="checkbox" />
          <span class="cx-toggle-label">{{ $t('availability.available_daily') }}</span>
        </label>
      </div>

      <!-- Contract toggle -->
      <div style="margin-bottom:1.25rem;">
        <label class="cx-toggle">
          <input v-model="form.available_contract" type="checkbox" />
          <span class="cx-toggle-label">{{ $t('availability.available_contract') }}</span>
        </label>
      </div>

      <!-- Not available until -->
      <div style="margin-bottom:1.25rem;">
        <label class="cx-label">{{ $t('availability.not_available_until') }}</label>
        <input v-model="form.not_available_until" class="cx-input" type="date" />
        <p class="cx-label" style="color:var(--cx-text-muted);margin-top:0.35rem;">{{ $t('availability.not_available_hint') }}</p>
      </div>

      <!-- Status override -->
      <div style="margin-bottom:1.5rem;">
        <label class="cx-label">{{ $t('availability.status') }}</label>
        <select v-model="form.status" class="cx-select">
          <option value="">{{ $t('availability.no_override') }}</option>
          <option value="available">Available</option>
          <option value="waiting">Waiting</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <p class="cx-label" style="color:var(--cx-text-muted);font-style:italic;margin-bottom:1rem;">{{ $t('availability.approval_hint') }}</p>

      <button class="cx-btn cx-btn-primary" :disabled="saving" @click="submit">
        {{ saving ? $t('common.loading') : $t('common.submit') }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchProfile, profile, profileLoading: loading, updateAvailability } = useWorkerPortal()
const loadError = ref('')
const saving = ref(false)
const toastMsg = ref('')

const form = reactive({
  available_daily: false,
  available_contract: false,
  not_available_until: '',
  status: '',
})

onMounted(async () => {
  try {
    await fetchProfile()
    if (profile.value) {
      form.available_daily = !!profile.value.available_daily
      form.available_contract = !!profile.value.available_contract
    }
  } catch (e: any) {
    loadError.value = e?.data?.message || t('availability.load_failed')
  }
})

async function submit() {
  saving.value = true
  try {
    const payload: Record<string, any> = {
      available_daily: form.available_daily,
      available_contract: form.available_contract,
    }
    if (form.not_available_until) payload.not_available_until = form.not_available_until
    if (form.status) payload.status = form.status

    await updateAvailability(payload)
    toastMsg.value = t('availability.submitted_approval')
    setTimeout(() => { toastMsg.value = '' }, 4000)
  } catch (e: any) {
    loadError.value = e?.data?.message || t('availability.save_failed')
  } finally {
    saving.value = false
  }
}
</script>
