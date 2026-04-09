<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.training') }}</h1>

    <SharedLoadingState v-if="loading" :rows="5" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;">{{ error }}</div>
    <SharedEmptyState v-else-if="!training.length" icon="🎓" :title="$t('training.empty')" />

    <template v-else>
      <!-- Summary stats from latest result -->
      <div class="cx-bento" style="margin-bottom:1.5rem;">
        <SharedStatCard
          :label="$t('training.latest_score')"
          :value="latest?.professional_score ?? '—'"
        />
        <SharedStatCard
          :label="$t('training.classification')"
          :value="latest?.classification ?? '—'"
        />
        <SharedStatCard
          :label="$t('training.suitability')"
          :value="latest?.suitability ?? '—'"
          :led="suitabilityLed(latest?.suitability)"
        />
      </div>

      <!-- Cycle list -->
      <h2 class="cx-section-title">{{ $t('training.history') }}</h2>
      <div style="display:flex;flex-direction:column;gap:0.75rem;">
        <div v-for="r in training" :key="r.id" class="cx-card" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
          <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:var(--cx-text-primary);margin-bottom:0.25rem;">
              {{ r.cycle_name ?? $t('training.cycle') + ' #' + r.training_cycle_id }}
            </div>
            <div style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">
              {{ r.cycle_start ?? '?' }} → {{ r.cycle_end ?? '?' }}
            </div>
          </div>
          <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            <div class="cx-info-row" style="text-align:right;">
              <div class="cx-info-label">{{ $t('training.score') }}</div>
              <div class="cx-info-value cx-mono">{{ r.professional_score ?? '—' }}</div>
            </div>
            <SharedStatusBadge v-if="r.suitability" :status="r.suitability" type="worker" />
            <button
              class="cx-btn"
              :class="r.certificate_available ? 'cx-btn-secondary' : 'cx-btn-ghost'"
              :disabled="!r.certificate_available"
              :title="r.certificate_available ? undefined : $t('training.no_certificate')"
              @click="r.certificate_available && downloadCertificate(r)"
            >
              ⬇ {{ $t('training.certificate') }}
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchTraining, training, trainingLoading: loading } = useWorkerPortal()
const { apiFetch, baseURL } = useApi()
const error = ref('')

const latest = computed(() => training.value?.[0] ?? null)

function suitabilityLed(s?: string) {
  switch (s?.toLowerCase()) {
    case 'suitable':    return 'cx-led-green'
    case 'conditional': return 'cx-led-yellow'
    case 'unsuitable':  return 'cx-led-red'
    default:            return 'cx-led-gray'
  }
}

function downloadCertificate(r: any) {
  window.open(`${baseURL}/api/v1/worker/training/${r.id}/certificate`, '_blank')
}

onMounted(async () => {
  try {
    await fetchTraining()
  } catch (e: any) {
    error.value = e?.data?.message || t('training.load_failed')
  }
})
</script>
