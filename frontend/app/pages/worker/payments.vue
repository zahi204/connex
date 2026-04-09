<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.payments') }}</h1>

    <SharedLoadingState v-if="loading" :rows="5" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;">{{ error }}</div>

    <template v-else-if="paymentsData">
      <!-- Summary bento -->
      <div class="cx-bento" style="margin-bottom:1.5rem;">
        <SharedStatCard
          :label="$t('payments.month_status')"
          :value="paymentsData.summary?.current_month_status ?? '—'"
          :led="paymentLed(paymentsData.summary?.current_month_status)"
        />
        <SharedStatCard
          :label="$t('payments.last_payment')"
          :value="paymentsData.summary?.last_payment_amount != null
            ? formatAmount(paymentsData.summary.last_payment_amount) + ' · ' + (paymentsData.summary.last_payment_date ?? '')
            : '—'"
        />
        <SharedStatCard
          :label="$t('payments.year_total')"
          :value="paymentsData.summary?.total_paid_year != null
            ? formatAmount(paymentsData.summary.total_paid_year)
            : '—'"
        />
      </div>

      <!-- Payments list -->
      <div v-if="!paymentRows.length">
        <SharedEmptyState icon="💳" :title="$t('payments.empty')" />
      </div>
      <div v-else class="cx-card" style="padding:0;overflow:hidden;">
        <table class="cx-table">
          <thead>
            <tr>
              <th>{{ $t('payments.description') }}</th>
              <th>{{ $t('payments.date') }}</th>
              <th style="text-align:right;">{{ $t('payments.amount') }}</th>
              <th>{{ $t('payments.status') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in paymentRows" :key="p.id">
              <td style="color:var(--cx-text-secondary);">{{ p.notes || p.payment_type || '—' }}</td>
              <td class="cx-mono" style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">{{ p.payment_date ?? '—' }}</td>
              <td class="cx-mono" style="text-align:right;font-weight:700;color:var(--cx-text-primary);">{{ formatAmount(p.amount) }}</td>
              <td><SharedStatusBadge :status="'paid'" type="payment" /></td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <SharedEmptyState v-else icon="💳" :title="$t('payments.empty')" />
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchPayments, paymentsData, paymentsLoading: loading } = useWorkerPortal()
const { paymentStatusLedClass } = useStatusColors()
const error = ref('')

const paymentRows = computed(() => {
  const d = paymentsData.value?.payments
  if (Array.isArray(d)) return d
  if (d?.data && Array.isArray(d.data)) return d.data
  return []
})

function formatAmount(amount: number) {
  return new Intl.NumberFormat('he-IL', { style: 'currency', currency: 'ILS' }).format(amount)
}

function paymentLed(status?: string) {
  return paymentStatusLedClass(status ?? '')
}

onMounted(async () => {
  try {
    await fetchPayments()
  } catch (e: any) {
    error.value = e?.data?.message || t('payments.load_failed')
  }
})
</script>
