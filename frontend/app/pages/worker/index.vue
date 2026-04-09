<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('dashboard.worker.title') }}</h1>

    <div v-if="loading" class="cx-loading">{{ $t('common.loading') }}</div>

    <div v-else-if="needsOnboarding" class="onboarding-prompt cx-card" style="max-width: 520px; text-align: center; margin: 3rem auto; padding: 2.5rem;">
      <div style="font-size: 3rem; margin-bottom: 1rem;">👷</div>
      <h2 style="margin: 0 0 0.75rem;">{{ $t('dashboard.worker.complete_registration') }}</h2>
      <p class="cx-text-muted" style="margin: 0 0 1.5rem; font-weight: 600;">
        {{ $t('dashboard.worker.onboarding_text') }}
      </p>
      <NuxtLink to="/wizard/worker" class="cx-btn cx-btn-primary" style="width: 100%;">
        {{ $t('dashboard.worker.start_onboarding') }}
      </NuxtLink>
    </div>

    <div v-else-if="error" class="cx-error">{{ error }}</div>

    <template v-else-if="profile">
      <!-- Status LED Banner -->
      <div class="cx-card" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <span
          class="cx-led"
          :class="statusLedClass"
        />
        <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
          {{ profile.status ?? $t('common.unknown') }}
        </span>
        <span class="cx-text-muted" style="margin-inline-start: auto;">
          {{ profile.full_name }}
        </span>
      </div>

      <!-- Bento Grid -->
      <div class="cx-bento">
        <!-- Professional Rating -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.professional_rating ?? '--' }}</div>
          <div class="cx-bento-label">{{ $t('dashboard.worker.professional_rating') }}</div>
        </div>

        <!-- Training Score -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.training_score ?? '--' }}</div>
          <div class="cx-bento-label">{{ $t('dashboard.worker.training_score') }}</div>
        </div>

        <!-- Payment Status -->
        <div class="cx-bento-item">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <span
              class="cx-led"
              :class="paymentLedClass"
            />
            <span
              class="cx-mono"
              :class="paymentBadgeClass"
            >
              {{ profile.payment_status ?? $t('common.na') }}
            </span>
          </div>
          <div class="cx-bento-label">{{ $t('dashboard.worker.payment_status') }}</div>
        </div>

        <!-- Current Assignment -->
        <div class="cx-bento-item">
          <div class="cx-bento-value" style="font-size: 1.25rem;">
            {{ profile.current_assignment || $t('common.none') }}
          </div>
          <div class="cx-bento-label">{{ $t('dashboard.worker.current_assignment') }}</div>
        </div>
      </div>

      <!-- Quick Action -->
      <div style="margin-top: 1.5rem;">
        <NuxtLink to="/worker/assignments" class="cx-btn cx-btn-primary">
          {{ $t('dashboard.worker.view_assignments') }}
        </NuxtLink>
      </div>
    </template>

    <div v-else class="cx-empty" style="max-width: 36rem; margin: 2rem auto;">
      <p>{{ $t('dashboard.worker.empty_text') }}</p>
      <NuxtLink to="/wizard/worker" class="cx-btn cx-btn-primary" style="margin-top: 1rem;">
        {{ $t('dashboard.worker.complete_registration_cta') }}
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { unwrapJsonResource } from '~/composables/useApi'

definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { apiFetch } = useApi()
const authStore = useAuthStore()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)
const needsOnboarding = computed(() => authStore.needsOnboarding)

const statusLedClass = computed(() => {
  switch (profile.value?.status) {
    case 'available': return 'cx-led-green'
    case 'assigned': return 'cx-led-blue'
    case 'active': return 'cx-led-green'
    case 'pending': return 'cx-led-yellow'
    case 'suspended': return 'cx-led-red'
    default: return 'cx-led-yellow'
  }
})

const paymentLedClass = computed(() => {
  switch (profile.value?.payment_status) {
    case 'paid': case 'current': return 'cx-led-green'
    case 'pending': return 'cx-led-yellow'
    case 'overdue': case 'failed': return 'cx-led-red'
    default: return 'cx-led-yellow'
  }
})

const paymentBadgeClass = computed(() => {
  switch (profile.value?.payment_status) {
    case 'paid': case 'current': return 'cx-badge-green'
    case 'pending': return 'cx-badge-yellow'
    case 'overdue': case 'failed': return 'cx-badge-red'
    default: return 'cx-badge-gray'
  }
})

onMounted(async () => {
  if (authStore.needsOnboarding) {
    loading.value = false
    return
  }
  try {
    const res = await apiFetch('/worker/profile') as any
    const raw = res?.data
    profile.value = unwrapJsonResource(raw) ?? raw ?? null
  } catch (e: any) {
    const msg = (e?.data?.message || e?.message || '') as string
    const is404 = e?.statusCode === 404 || e?.status === 404 || /not found|profile/i.test(msg)
    if (is404 && authStore.user) {
      authStore.$patch({
        user: { ...authStore.user, needs_onboarding: true },
      })
    }
    else {
      error.value = msg || t('dashboard.worker.load_failed')
    }
  } finally {
    loading.value = false
  }
})
</script>
