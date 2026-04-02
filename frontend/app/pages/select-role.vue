<template>
  <div class="role-page">
    <div class="role-container">
      <h1>{{ $t('auth.select_role') }}</h1>
      <p class="subtitle">Choose how you'll use Connex</p>

      <div class="roles-grid">
        <button
          v-for="role in roles"
          :key="role.key"
          class="role-card"
          :class="{ selected: selectedRole === role.key }"
          @click="selectedRole = role.key"
        >
          <div class="role-icon">{{ role.icon }}</div>
          <div class="role-name">{{ $t(`auth.roles.${role.key}`) }}</div>
          <div class="role-desc">{{ role.desc }}</div>
        </button>
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>

      <button
        class="btn-continue"
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
}

.role-container {
  text-align: center;
  width: 100%;
  max-width: 600px;
}

.role-container h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: white;
  margin: 0 0 0.5rem;
}

.subtitle {
  color: rgba(255, 255, 255, 0.5);
  margin: 0 0 2.5rem;
  font-size: 0.95rem;
}

.roles-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 2rem;
}

.role-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 1.5rem 1rem;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.role-card:hover {
  border-color: rgba(99, 102, 241, 0.4);
  background: rgba(255, 255, 255, 0.08);
}

.role-card.selected {
  border-color: #6366f1;
  background: rgba(99, 102, 241, 0.15);
}

.role-icon {
  font-size: 2rem;
  margin-bottom: 0.75rem;
}

.role-name {
  color: white;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.4rem;
}

.role-desc {
  color: rgba(255, 255, 255, 0.45);
  font-size: 0.8rem;
  line-height: 1.3;
}

.btn-continue {
  width: 100%;
  padding: 0.85rem;
  background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.1s;
}

.btn-continue:hover:not(:disabled) {
  opacity: 0.9;
}

.btn-continue:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.error-message {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-size: 0.85rem;
  margin-bottom: 1.25rem;
  text-align: center;
}
</style>
