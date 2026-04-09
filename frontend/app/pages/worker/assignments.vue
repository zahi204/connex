<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.assignments') }}</h1>

    <!-- Filters -->
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
      <select v-model="filterStatus" class="cx-select" style="min-width:140px;">
        <option value="">{{ $t('directory.all') }}</option>
        <option value="new">New</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <input v-model="filterFrom" class="cx-input" type="date" :placeholder="$t('assignments.from')" style="min-width:150px;" />
      <input v-model="filterTo" class="cx-input" type="date" :placeholder="$t('assignments.to')" style="min-width:150px;" />
      <button class="cx-btn cx-btn-secondary" @click="applyFilters">{{ $t('common.filter') }}</button>
    </div>

    <SharedLoadingState v-if="loading" :rows="5" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;">{{ error }}</div>
    <SharedEmptyState v-else-if="!assignments.length" icon="📋" :title="$t('assignments.empty')" />

    <div v-else class="cx-card" style="padding:0;overflow:hidden;">
      <table class="cx-table">
        <thead>
          <tr>
            <th>{{ $t('assignments.project') }}</th>
            <th>{{ $t('assignments.role') }}</th>
            <th>{{ $t('assignments.dates') }}</th>
            <th>{{ $t('assignments.status') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="a in assignments"
            :key="a.id"
            style="cursor:pointer;"
            @click="openDrawer(a)"
          >
            <td style="font-weight:600;color:var(--cx-text-primary);">{{ a.project?.name ?? '—' }}</td>
            <td style="color:var(--cx-text-muted);font-size:var(--cx-font-sm);">{{ a.engagement_type ?? '—' }}</td>
            <td style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">
              {{ a.start_date ?? '?' }} → {{ a.estimated_end_date ?? '?' }}
            </td>
            <td><SharedStatusBadge :status="a.status ?? 'unknown'" type="assignment" /></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Drawer -->
    <SharedDrawer v-model="drawerOpen" :title="selected?.project?.name ?? $t('assignments.details')">
      <template v-if="selected">
        <div style="display:flex;flex-direction:column;gap:1.25rem;">
          <div class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.status') }}</div>
            <div class="cx-info-value"><SharedStatusBadge :status="selected.status ?? 'unknown'" type="assignment" /></div>
          </div>
          <div class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.project') }}</div>
            <div class="cx-info-value">{{ selected.project?.name }}</div>
          </div>
          <div v-if="selected.project?.description" class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.description') }}</div>
            <div class="cx-info-value" style="white-space:pre-wrap;line-height:1.6;">{{ selected.project.description }}</div>
          </div>
          <div class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.type') }}</div>
            <div class="cx-info-value">{{ selected.engagement_type ?? '—' }}</div>
          </div>
          <div class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.dates') }}</div>
            <div class="cx-info-value cx-mono">{{ selected.start_date ?? '?' }} → {{ selected.estimated_end_date ?? '?' }}</div>
          </div>
          <div v-if="selected.work_description" class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.work_description') }}</div>
            <div class="cx-info-value" style="white-space:pre-wrap;">{{ selected.work_description }}</div>
          </div>
          <div v-if="selected.notes" class="cx-info-row">
            <div class="cx-info-label">{{ $t('assignments.notes') }}</div>
            <div class="cx-info-value" style="white-space:pre-wrap;">{{ selected.notes }}</div>
          </div>
        </div>
      </template>
    </SharedDrawer>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchAssignments, assignments, assignmentsLoading: loading } = useWorkerPortal()
const error = ref('')
const filterStatus = ref('')
const filterFrom = ref('')
const filterTo = ref('')
const drawerOpen = ref(false)
const selected = ref<any>(null)

onMounted(() => applyFilters())

async function applyFilters() {
  error.value = ''
  try {
    const q: Record<string, any> = {}
    if (filterStatus.value) q.status = filterStatus.value
    if (filterFrom.value) q.from = filterFrom.value
    if (filterTo.value) q.to = filterTo.value
    await fetchAssignments(q)
  } catch (e: any) {
    error.value = e?.data?.message || t('assignments.load_failed')
  }
}

function openDrawer(a: any) {
  selected.value = a
  drawerOpen.value = true
}
</script>
