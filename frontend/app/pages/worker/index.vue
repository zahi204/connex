<template>
  <div class="cx-page">
    <h1 class="cx-page-title">Worker Dashboard</h1>

    <div v-if="loading" class="cx-loading">Loading...</div>
    <div v-else-if="error" class="cx-error">{{ error }}</div>

    <template v-else-if="profile">
      <!-- Status LED Banner -->
      <div class="cx-card" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <span
          class="cx-led"
          :class="statusLedClass"
        />
        <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
          {{ profile.status ?? 'Unknown' }}
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
          <div class="cx-bento-label">Professional Rating</div>
        </div>

        <!-- Training Score -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.training_score ?? '--' }}</div>
          <div class="cx-bento-label">Training Score</div>
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
              {{ profile.payment_status ?? 'N/A' }}
            </span>
          </div>
          <div class="cx-bento-label">Payment Status</div>
        </div>

        <!-- Current Assignment -->
        <div class="cx-bento-item">
          <div class="cx-bento-value" style="font-size: 1.25rem;">
            {{ profile.current_assignment || 'None' }}
          </div>
          <div class="cx-bento-label">Current Assignment</div>
        </div>
      </div>

      <!-- Quick Action -->
      <div style="margin-top: 1.5rem;">
        <NuxtLink to="/worker/assignments" class="cx-btn cx-btn-primary">
          View Assignments
        </NuxtLink>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)

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
  try {
    const res = await apiFetch('/worker/profile') as any
    profile.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load dashboard'
  } finally {
    loading.value = false
  }
})
</script>
