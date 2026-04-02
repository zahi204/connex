<template>
  <div class="portal-page">
    <h1>Payment History</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!payments.length" class="empty">No payment records found.</div>
      <div v-else class="payments-list">
        <div v-for="p in payments" :key="p.id" class="payment-card">
          <div class="payment-info">
            <div class="payment-desc">{{ p.description || 'Payment' }}</div>
            <div class="payment-meta">
              <span v-if="p.date">{{ p.date }}</span>
              <span v-if="p.project_name">{{ p.project_name }}</span>
            </div>
          </div>
          <div class="payment-right">
            <div class="payment-amount">{{ p.amount ? formatAmount(p.amount) : 'N/A' }}</div>
            <span class="status-badge" :class="p.status">{{ p.status }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const payments = ref<any[]>([])

function formatAmount(amount: number) {
  return new Intl.NumberFormat('en-IL', { style: 'currency', currency: 'ILS' }).format(amount)
}

onMounted(async () => {
  try {
    const res = await apiFetch('/worker/payments') as any
    payments.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load payments'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }
.payments-list { display: flex; flex-direction: column; gap: 0.5rem; }
.payment-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.payment-desc { color: white; font-weight: 500; margin-bottom: 0.2rem; }
.payment-meta { display: flex; gap: 1rem; color: rgba(255,255,255,0.4); font-size: 0.75rem; }
.payment-right { text-align: right; }
.payment-amount { color: white; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.25rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.paid { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.processing { background: rgba(59,130,246,0.2); color: #93c5fd; }
</style>
