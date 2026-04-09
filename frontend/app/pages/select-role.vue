<template>
  <div class="role-page cx-flow-page">
    <div class="role-container cx-glass-panel">
      <h1 class="cx-wizard-title">{{ $t('auth.select_role') }}</h1>
      <p class="cx-text-muted subtitle">Choose how you'll use Connex</p>

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
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 0.5rem;
}

.role-container {
  text-align: center;
  width: 100%;
  max-width: 640px;
}

.subtitle {
  margin: 0 0 2rem;
  font-weight: 500;
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
  min-height: 150px;
  background: var(--cx-bg-card);
  border: 1px solid var(--cx-border);
  transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s, background 0.25s;
}

.role-card:hover {
  border-color: var(--cx-border-accent);
  transform: translateY(-2px);
  box-shadow: var(--cx-shadow-glow);
}

.role-card.selected {
  border-color: transparent;
  background: linear-gradient(var(--cx-bg-card), var(--cx-bg-card)) padding-box,
              var(--cx-gradient-accent) border-box;
  border: 1px solid transparent;
  box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.35), var(--cx-shadow-glow);
}

.role-icon {
  font-size: 2.25rem;
  margin-bottom: 0.75rem;
  filter: drop-shadow(0 0 12px rgba(56, 189, 248, 0.25));
}

.role-name {
  font-family: var(--cx-font-display);
  color: var(--cx-text-primary);
  font-weight: 700;
  font-size: var(--cx-font-sm);
  margin-bottom: 0.4rem;
  letter-spacing: -0.01em;
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
