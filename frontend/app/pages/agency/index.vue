<template>
  <div class="cx-page">
    <h1 class="cx-page-title">Agency Dashboard</h1>

    <div v-if="loading" class="cx-loading">Loading...</div>
    <div v-else-if="error" class="cx-error">{{ error }}</div>

    <template v-else-if="quality">
      <!-- Bento Grid -->
      <div class="cx-bento">
        <!-- Total Workers -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ quality.worker_count ?? 0 }}</div>
          <div class="cx-bento-label">Total Workers</div>
        </div>

        <!-- Average Quality Rating -->
        <div class="cx-bento-item">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <span
              class="cx-led"
              :class="ratingLedClass"
            />
            <span class="cx-bento-value" style="margin-bottom: 0;">
              {{ quality.average_rating ?? '--' }}
            </span>
          </div>
          <div class="cx-bento-label">Average Quality Rating</div>
        </div>

        <!-- Workers Trained -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">{{ quality.workers_trained ?? 0 }}</div>
          <div class="cx-bento-label">Workers Trained</div>
        </div>

        <!-- Outstanding Balance -->
        <div class="cx-bento-item">
          <div class="cx-bento-value">
            {{ formatNIS(quality.outstanding_balance) }}
          </div>
          <div class="cx-bento-label">Outstanding Balance</div>
        </div>
      </div>

      <!-- Quality Metrics Section -->
      <div class="cx-card" style="margin-top: 1.5rem;">
        <h2 style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; font-size: 1rem;">
          Quality Metrics
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
          <div>
            <div class="cx-bento-label">Training Pass Rate</div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
              <span
                class="cx-led"
                :class="passRateLedClass"
              />
              <span class="cx-mono cx-text-accent" style="font-size: 1.25rem; font-weight: 700;">
                {{ quality.training_pass_rate != null ? quality.training_pass_rate + '%' : '--' }}
              </span>
            </div>
          </div>
          <div>
            <div class="cx-bento-label">Quality Score</div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
              <span
                class="cx-led"
                :class="qualityScoreLedClass"
              />
              <span class="cx-mono cx-text-accent" style="font-size: 1.25rem; font-weight: 700;">
                {{ quality.quality_score ?? '--' }}
              </span>
            </div>
          </div>
          <div>
            <div class="cx-bento-label">Agency Status</div>
            <div style="margin-top: 0.25rem;">
              <span :class="statusBadgeClass">
                {{ quality.status ?? 'Unknown' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'agency', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const quality = ref<any>(null)

function formatNIS(value: number | null | undefined): string {
  if (value == null) return '--'
  return new Intl.NumberFormat('he-IL', {
    style: 'currency',
    currency: 'ILS',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}

const ratingLedClass = computed(() => {
  const r = quality.value?.average_rating
  if (r == null) return 'cx-led-yellow'
  if (r >= 4) return 'cx-led-green'
  if (r >= 3) return 'cx-led-yellow'
  return 'cx-led-red'
})

const passRateLedClass = computed(() => {
  const r = quality.value?.training_pass_rate
  if (r == null) return 'cx-led-yellow'
  if (r >= 80) return 'cx-led-green'
  if (r >= 60) return 'cx-led-yellow'
  return 'cx-led-red'
})

const qualityScoreLedClass = computed(() => {
  const s = quality.value?.quality_score
  if (s == null) return 'cx-led-yellow'
  if (s >= 80) return 'cx-led-green'
  if (s >= 60) return 'cx-led-yellow'
  return 'cx-led-red'
})

const statusBadgeClass = computed(() => {
  switch (quality.value?.status) {
    case 'active': case 'approved': return 'cx-badge-green'
    case 'pending': return 'cx-badge-yellow'
    case 'suspended': case 'rejected': return 'cx-badge-red'
    default: return 'cx-badge-gray'
  }
})

onMounted(async () => {
  try {
    const res = await apiFetch('/agency/quality') as any
    quality.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load dashboard'
  } finally {
    loading.value = false
  }
})
</script>
