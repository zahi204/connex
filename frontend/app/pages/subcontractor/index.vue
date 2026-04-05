<template>
  <div class="cx-page">
    <h1 class="cx-page-title">Subcontractor Dashboard</h1>

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
          {{ profile.company_name || profile.full_name }}
        </span>
      </div>

      <!-- Bento Grid -->
      <div class="cx-bento">
        <!-- Rating -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.rating ?? '--' }}</div>
          <div class="cx-bento-label">Rating</div>
        </div>

        <!-- Current Assignment -->
        <div class="cx-bento-item">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <span
              class="cx-led"
              :class="profile.current_assignment ? 'cx-led-green' : 'cx-led-yellow'"
            />
            <span class="cx-mono cx-text-accent" style="font-weight: 700;">
              {{ profile.current_assignment || 'None' }}
            </span>
          </div>
          <div class="cx-bento-label">Current Assignment</div>
        </div>

        <!-- Availability Date -->
        <div class="cx-bento-item">
          <div class="cx-bento-value" style="font-size: 1.25rem;">
            {{ profile.availability_date || profile.availability || '--' }}
          </div>
          <div class="cx-bento-label">Availability Date</div>
        </div>

        <!-- Worker Count -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ profile.worker_count ?? 0 }}</div>
          <div class="cx-bento-label">Worker Count</div>
        </div>
      </div>

      <!-- Quick Action -->
      <div style="margin-top: 1.5rem;">
        <NuxtLink to="/subcontractor/availability" class="cx-btn cx-btn-primary">
          Update Availability
        </NuxtLink>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'subcontractor', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const profile = ref<any>(null)

const statusLedClass = computed(() => {
  switch (profile.value?.status) {
    case 'available': case 'active': return 'cx-led-green'
    case 'assigned': return 'cx-led-blue'
    case 'pending': return 'cx-led-yellow'
    case 'suspended': case 'rejected': return 'cx-led-red'
    default: return 'cx-led-yellow'
  }
})

onMounted(async () => {
  try {
    const res = await apiFetch('/subcontractor/profile') as any
    profile.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load dashboard'
  } finally {
    loading.value = false
  }
})
</script>
