<template>
  <div class="portal-page">
    <h1>Billing Information</h1>
    <p class="readonly-notice">This information is read-only. Contact Connex administration for billing changes.</p>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="billing">
      <div class="billing-overview">
        <div class="stat-card">
          <div class="stat-label">Current Balance</div>
          <div class="stat-value">{{ billing.current_balance ? formatAmount(billing.current_balance) : 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Billed</div>
          <div class="stat-value">{{ billing.total_billed ? formatAmount(billing.total_billed) : 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Paid</div>
          <div class="stat-value">{{ billing.total_paid ? formatAmount(billing.total_paid) : 'N/A' }}</div>
        </div>
      </div>

      <div v-if="billing.invoices && billing.invoices.length" class="invoices-section">
        <h2>Invoices</h2>
        <div v-for="inv in billing.invoices" :key="inv.id" class="invoice-card">
          <div class="invoice-info">
            <div class="invoice-num">{{ inv.invoice_number || 'Invoice #' + inv.id }}</div>
            <div class="invoice-meta">
              <span>{{ inv.date || inv.created_at }}</span>
              <span v-if="inv.period">Period: {{ inv.period }}</span>
            </div>
          </div>
          <div class="invoice-right">
            <div class="invoice-amount">{{ inv.amount ? formatAmount(inv.amount) : 'N/A' }}</div>
            <span class="status-badge" :class="inv.status">{{ inv.status }}</span>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="empty">No billing data available.</div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'agency', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const billing = ref<any>(null)

function formatAmount(amount: number) {
  return new Intl.NumberFormat('en-IL', { style: 'currency', currency: 'ILS' }).format(amount)
}

onMounted(async () => {
  try {
    const res = await apiFetch('/agency/billing') as any
    billing.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load billing info'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 0.5rem; }
.readonly-notice { color: rgba(255,255,255,0.4); font-size: 0.85rem; margin-bottom: 1.5rem; font-style: italic; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }
.billing-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 1.25rem; }
.stat-label { color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-bottom: 0.5rem; }
.stat-value { color: white; font-size: 1.5rem; font-weight: 700; }
.invoices-section h2 { color: white; font-size: 1.1rem; margin-bottom: 0.75rem; }
.invoice-card { background: rgba(255,255,255,0.05); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.invoice-num { color: white; font-weight: 500; margin-bottom: 0.2rem; }
.invoice-meta { display: flex; gap: 1rem; color: rgba(255,255,255,0.4); font-size: 0.75rem; }
.invoice-right { text-align: right; }
.invoice-amount { color: white; font-weight: 700; margin-bottom: 0.25rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.paid { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.overdue { background: rgba(239,68,68,0.2); color: #f87171; }
</style>
