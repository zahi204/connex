<template>
  <div class="cx-page">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
      <h1 class="cx-page-title" style="margin-bottom:0;">{{ $t('nav.notifications') }}</h1>
      <button class="cx-btn cx-btn-secondary" :disabled="markingAll" @click="doMarkAllRead">
        {{ markingAll ? $t('common.loading') : $t('notifications.mark_all_read') }}
      </button>
    </div>

    <!-- Category filter chips -->
    <div class="cx-filter-chips" style="margin-bottom:1.25rem;">
      <button
        v-for="cat in categories"
        :key="cat.value"
        class="cx-filter-chip"
        :class="{ active: activeCategory === cat.value }"
        @click="activeCategory = cat.value"
      >{{ cat.label }}</button>
    </div>

    <SharedLoadingState v-if="loading" :rows="5" />
    <div v-else-if="loadError" class="cx-toast cx-toast-error" style="position:static;">{{ loadError }}</div>
    <SharedEmptyState v-else-if="!filtered.length" icon="🔔" :title="$t('notifications.empty')" />

    <div v-else style="display:flex;flex-direction:column;gap:0.5rem;">
      <div
        v-for="n in filtered"
        :key="n.id"
        class="cx-card"
        :class="{ 'cx-card-accent': !n.read_at }"
        style="cursor:pointer;"
        @click="doMarkAsRead(n)"
      >
        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
          <span v-if="!n.read_at" class="cx-led cx-led-blue" style="margin-top:4px;flex-shrink:0;" />
          <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:var(--cx-text-primary);margin-bottom:0.2rem;">
              {{ n.title || n.type }}
            </div>
            <div style="color:var(--cx-text-muted);font-size:var(--cx-font-sm);margin-bottom:0.25rem;">
              {{ n.message || n.body }}
            </div>
            <div style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">
              {{ formatRelative(n.created_at) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { list, markAsRead, markAllRead, notifications } = useNotifications()
const loading = ref(true)
const loadError = ref('')
const markingAll = ref(false)
const activeCategory = ref('all')

const categories = [
  { value: 'all',        label: t('notifications.all') },
  { value: 'assignment', label: t('notifications.assignment') },
  { value: 'payment',    label: t('notifications.payment') },
  { value: 'training',   label: t('notifications.training') },
  { value: 'system',     label: t('notifications.system') },
]

const filtered = computed(() => {
  if (activeCategory.value === 'all') return notifications.value
  return notifications.value.filter(n =>
    (n.type || '').toLowerCase().includes(activeCategory.value)
  )
})

function formatRelative(dt: string) {
  if (!dt) return ''
  const d = new Date(dt)
  const diff = Date.now() - d.getTime()
  const mins = Math.floor(diff / 60_000)
  if (mins < 1)  return 'Just now'
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24)  return `${hrs}h ago`
  return d.toLocaleDateString()
}

async function doMarkAsRead(n: any) {
  if (!n.read_at) await markAsRead(n.id)
}

async function doMarkAllRead() {
  markingAll.value = true
  await markAllRead()
  markingAll.value = false
}

onMounted(async () => {
  try {
    await list()
  } catch (e: any) {
    loadError.value = e?.data?.message || t('notifications.load_failed')
  } finally {
    loading.value = false
  }
})
</script>
