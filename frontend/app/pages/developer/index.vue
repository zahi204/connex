<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('dashboard.developer.title') }}</h1>

    <div v-if="loading" class="cx-loading">{{ $t('common.loading') }}</div>
    <div v-else-if="error" class="cx-error">{{ error }}</div>

    <template v-else-if="profile">
      <!-- Company Header -->
      <div class="cx-card" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <span
          class="cx-led"
          :class="statusLedClass"
        />
        <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
          {{ profile.company_name || profile.full_name }}
        </span>
        <span
          :class="statusBadgeClass"
          style="margin-inline-start: auto;"
        >
          {{ profile.status ?? $t('common.unknown') }}
        </span>
      </div>

      <!-- Bento Grid -->
      <div class="cx-bento">
        <!-- Active Projects -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.active_projects_count ?? 0 }}</div>
          <div class="cx-bento-label">{{ $t('dashboard.developer.active_projects') }}</div>
        </div>

        <!-- Pending Approvals -->
        <div class="cx-bento-item">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <span
              class="cx-led"
              :class="(profile.pending_items_count ?? 0) > 0 ? 'cx-led-yellow' : 'cx-led-green'"
            />
            <span class="cx-bento-value" style="margin-bottom: 0;">
              {{ profile.pending_items_count ?? 0 }}
            </span>
          </div>
          <div class="cx-bento-label">{{ $t('dashboard.developer.pending_approvals') }}</div>
        </div>

        <!-- BOQ Requests -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.boq_requests_count ?? 0 }}</div>
          <div class="cx-bento-label">{{ $t('dashboard.developer.boq_requests') }}</div>
        </div>

        <!-- Latest Project Status -->
        <div class="cx-bento-item">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <span
              class="cx-led"
              :class="projectStatusLedClass"
            />
            <span class="cx-mono cx-text-accent" style="font-weight: 700; text-transform: uppercase;">
              {{ profile.latest_project_status || $t('common.none') }}
            </span>
          </div>
          <div class="cx-bento-label">{{ $t('dashboard.developer.latest_project_status') }}</div>
        </div>
      </div>

      <!-- Quick Action -->
      <div style="margin-top: 1.5rem;">
        <NuxtLink to="/developer/projects/create" class="cx-btn cx-btn-primary">
          {{ $t('dashboard.developer.submit_new_project') }}
        </NuxtLink>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'developer', middleware: ['auth'] })

const { t } = useI18n()
const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)

const statusLedClass = computed(() => {
  switch (profile.value?.status) {
    case 'active': case 'approved': return 'cx-led-green'
    case 'pending': return 'cx-led-yellow'
    case 'suspended': case 'rejected': return 'cx-led-red'
    default: return 'cx-led-yellow'
  }
})

const statusBadgeClass = computed(() => {
  switch (profile.value?.status) {
    case 'active': case 'approved': return 'cx-badge-green'
    case 'pending': return 'cx-badge-yellow'
    case 'suspended': case 'rejected': return 'cx-badge-red'
    default: return 'cx-badge-gray'
  }
})

const projectStatusLedClass = computed(() => {
  const s = profile.value?.latest_project_status
  switch (s) {
    case 'active': case 'in_progress': return 'cx-led-green'
    case 'pending': case 'review': return 'cx-led-yellow'
    case 'completed': return 'cx-led-blue'
    case 'cancelled': case 'rejected': return 'cx-led-red'
    default: return 'cx-led-yellow'
  }
})

onMounted(async () => {
  try {
    const res = await apiFetch('/developer/profile') as any
    profile.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || t('dashboard.developer.load_failed')
  } finally {
    loading.value = false
  }
})
</script>
