<template>
  <div class="portal-page">
    <h1>My Team</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="team">
      <div class="team-header">
        <h2>{{ team.name }}</h2>
        <span v-if="team.leader" class="team-leader">Leader: {{ team.leader }}</span>
      </div>
      <div v-if="team.members && team.members.length" class="members-list">
        <div v-for="m in team.members" :key="m.id" class="member-card">
          <div class="member-name">{{ m.full_name }}</div>
          <div class="member-details">
            <span v-if="m.primary_skill">{{ m.primary_skill }}</span>
            <span v-if="m.role" class="member-role">{{ m.role }}</span>
          </div>
        </div>
      </div>
      <div v-else class="empty">No team members found.</div>
    </div>
    <div v-else class="empty">You are not currently assigned to a team.</div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const team = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/worker/team') as any
    team.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load team info'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.empty { color: var(--cx-text-muted); text-align: center; padding: 2rem; }
.team-header { margin-bottom: 1.5rem; }
.team-header h2 { color: var(--cx-text-primary); margin: 0 0 0.25rem; font-size: 1.2rem; }
.team-leader { color: var(--cx-text-muted); font-size: 0.85rem; }
.members-list { display: flex; flex-direction: column; gap: 0.5rem; }
.member-card { background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 10px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.member-name { color: var(--cx-text-primary); font-weight: 500; }
.member-details { display: flex; gap: 1rem; color: var(--cx-text-muted); font-size: 0.8rem; }
.member-role { background: rgba(139,92,246,0.2); color: #c4b5fd; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.7rem; }
</style>
