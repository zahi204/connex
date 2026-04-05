<template>
  <div class="role-page">
    <div class="role-container">
      <h1>{{ $t('auth.select_role') }}</h1>
      <p class="cx-text-muted" style="margin: 0 0 2rem; font-weight: 600;">Choose how you'll use Connex</p>

      <div class="roles-grid">
        <button
          v-for="role in roles"
          :key="role.key"
          class="role-card cx-card"
          :class="{ selected: selectedRole === role.key }"
          @click="selectedRole = role.key"
        >
          <div class="role-icon">{{ role.icon }}</div>
          <div class="role-name">{{ $t(`auth.roles.${role.key}`) }}</div>
          <div class="role-desc cx-text-muted">{{ role.desc }}</div>
          <div v-if="selectedRole === role.key" class="selected-indicator">
            <span class="cx-led cx-led-green" />
          </div>
        </button>
      </div>

      <div v-if="error" class="cx-error" style="margin-bottom: 1.25rem; text-align: center;">{{ error }}</div>

      <button
        class="cx-btn cx-btn-primary"
        style="width: 100%;"
        :disabled="!selectedRole || loading"
        @click="handleContinue"
      >
        {{ loading ? $t('common.loading') : $t('common.submit') }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'blank',
  middleware: ['auth'],
})

const { selectRole } = useAuth()

const selectedRole = ref<string | null>(null)
const loading = ref(false)
const error = ref('')

const roles = [
  { key: 'worker', icon: '👷', desc: 'Find work on construction projects' },
  { key: 'developer', icon: '🏗️', desc: 'Manage projects and hire resources' },
  { key: 'subcontractor', icon: '🔧', desc: 'Bid on projects and manage teams' },
  { key: 'agency', icon: '👥', desc: 'Supply and manage workers' },
]

const handleContinue = async () => {
  if (!selectedRole.value) return

  error.value = ''
  loading.value = true
  try {
    await selectRole(selectedRole.value)
    await navigateTo(`/wizard/${selectedRole.value}`)
  } catch (e: any) {
    error.value = e?.data?.message || e?.message || 'Failed to select role'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.role-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: var(--cx-bg-primary);
}

.role-container {
  text-align: center;
  width: 100%;
  max-width: 640px;
}

.role-container h1 {
  font-size: var(--cx-font-xl);
  font-weight: 900;
  color: var(--cx-text-primary);
  margin: 0 0 0.5rem;
}

.roles-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 2rem;
}

.role-card {
  cursor: pointer;
  text-align: center;
  padding: 1.75rem 1.25rem;
  position: relative;
  min-height: 140px;
}

.role-card:hover {
  border-color: var(--cx-accent);
}

.role-card.selected {
  border-color: var(--cx-accent);
  background: var(--cx-accent-soft);
  box-shadow: 0 0 20px var(--cx-accent-glow);
}

.role-icon {
  font-size: 2.25rem;
  margin-bottom: 0.75rem;
}

.role-name {
  color: var(--cx-text-primary);
  font-weight: 700;
  font-size: var(--cx-font-sm);
  margin-bottom: 0.4rem;
}

.role-desc {
  font-size: var(--cx-font-xs);
  line-height: 1.4;
}

.selected-indicator {
  position: absolute;
  top: 0.75rem;
  inset-inline-end: 0.75rem;
}
</style>
