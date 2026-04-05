<template>
  <div class="wizard-stepper">
    <!-- Progress Bar -->
    <div class="progress-track">
      <div class="progress-fill" :style="{ width: `${(currentStep / (steps.length - 1)) * 100}%` }" />
    </div>

    <!-- Step Indicators -->
    <div class="steps-indicator">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="step-item"
        :class="{ active: index === currentStep, completed: index < currentStep }"
      >
        <div class="step-circle">
          <span v-if="index < currentStep">&#10003;</span>
          <span v-else class="cx-mono">{{ index + 1 }}</span>
        </div>
        <span class="step-label">{{ step.title }}</span>
      </div>
    </div>

    <!-- Step Content -->
    <div class="step-content cx-card">
      <slot :name="`step-${currentStep}`" />
    </div>

    <!-- Actions -->
    <div class="step-actions">
      <button
        v-if="currentStep > 0"
        class="cx-btn cx-btn-secondary"
        @click="$emit('prev')"
      >
        BACK
      </button>
      <div class="spacer" />
      <button
        v-if="currentStep < steps.length - 1"
        class="cx-btn cx-btn-primary"
        :disabled="!canProceed"
        @click="$emit('next')"
      >
        NEXT STEP
      </button>
      <button
        v-else
        class="cx-btn cx-btn-primary"
        :disabled="loading"
        @click="$emit('submit')"
      >
        {{ loading ? $t('common.loading') : 'CONFIRM & REGISTER' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  steps: Array<{ title: string }>
  currentStep: number
  canProceed?: boolean
  loading?: boolean
}>()

defineEmits(['next', 'prev', 'submit'])
</script>

<style scoped>
.wizard-stepper { max-width: 720px; margin: 0 auto; }

.progress-track {
  height: 4px;
  background: var(--cx-border);
  border-radius: 2px;
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--cx-accent);
  border-radius: 2px;
  transition: width 0.3s ease;
  box-shadow: 0 0 8px var(--cx-accent-glow);
}

.steps-indicator {
  display: flex;
  justify-content: center;
  gap: 0.35rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  opacity: 0.3;
  transition: opacity 0.2s;
}

.step-item.active,
.step-item.completed { opacity: 1; }

.step-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--cx-font-xs);
  font-weight: 800;
  background: var(--cx-bg-card);
  color: var(--cx-text-muted);
  border: 2px solid var(--cx-border);
}

.step-item.active .step-circle {
  background: var(--cx-accent);
  border-color: var(--cx-accent);
  color: var(--cx-text-inverse);
  box-shadow: 0 0 12px var(--cx-accent-glow);
}

.step-item.completed .step-circle {
  background: var(--cx-led-green);
  border-color: var(--cx-led-green);
  color: var(--cx-text-inverse);
}

.step-label {
  font-size: var(--cx-font-xs);
  font-weight: 700;
  color: var(--cx-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: none;
}

@media (min-width: 640px) {
  .step-label { display: inline; }
}

.step-content {
  min-height: 300px;
  padding: 1.5rem;
}

.step-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.spacer { flex: 1; }
</style>
