<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.team') }}</h1>

    <SharedLoadingState v-if="loading" :rows="6" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;">{{ error }}</div>
    <SharedEmptyState v-else-if="!team" icon="👥" :title="$t('team.no_team')" />

    <template v-else>
      <!-- Team header card -->
      <div class="cx-card" style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:flex-start;gap:1.25rem;flex-wrap:wrap;">
          <div style="flex:1;min-width:0;">
            <h2 style="font-family:var(--cx-font-display);font-size:1.4rem;font-weight:800;margin:0 0 0.5rem;color:var(--cx-text-primary);">
              {{ team.name }}
            </h2>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
              <SharedStatusBadge v-if="team.status" :status="team.status" type="worker" />
              <span v-if="team.current_project" class="cx-badge cx-badge-blue">
                {{ team.current_project.name }}
              </span>
            </div>
          </div>
          <div v-if="team.leader" style="text-align:right;">
            <div class="cx-info-label">{{ $t('team.leader') }}</div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.3rem;">
              <SharedWorkerAvatar :name="team.leader.full_name" size="sm" status="available" />
              <span style="font-weight:600;color:var(--cx-text-primary);">{{ team.leader.full_name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Members grid -->
      <h2 class="cx-section-title">{{ $t('team.members') }}</h2>
      <SharedEmptyState v-if="!team.members?.length" icon="👤" :title="$t('team.no_members')" />
      <div v-else class="cx-bento" style="--cx-bento-min:200px;">
        <div
          v-for="m in team.members"
          :key="m.id"
          class="cx-card"
          :class="{ 'cx-card-accent': m.is_leader }"
          style="display:flex;flex-direction:column;gap:0.6rem;"
        >
          <div style="display:flex;align-items:center;gap:0.75rem;">
            <SharedWorkerAvatar :name="m.full_name" size="md" />
            <div style="min-width:0;">
              <div style="font-weight:700;color:var(--cx-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ m.full_name }}
              </div>
              <div style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">
                {{ m.primary_skill ?? '—' }}
              </div>
            </div>
          </div>
          <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <span v-if="m.is_leader" class="cx-badge cx-badge-blue">{{ $t('team.leader') }}</span>
            <span v-if="m.role" class="cx-badge cx-badge-gray">{{ m.role }}</span>
          </div>
          <div class="cx-info-row" style="margin-top:auto;">
            <div class="cx-info-label">{{ $t('team.joined') }}</div>
            <div class="cx-info-value cx-mono">{{ m.joined_at ?? '—' }}</div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchTeam, team, teamLoading: loading } = useWorkerPortal()
const error = ref('')

onMounted(async () => {
  try {
    await fetchTeam()
  } catch (e: any) {
    error.value = e?.data?.message || t('team.load_failed')
  }
})
</script>
