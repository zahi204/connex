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
        {{ $t('common.back') }}
      </button>
      <div class="spacer" />
      <button
        v-if="currentStep < steps.length - 1"
        class="cx-btn cx-btn-primary"
        :disabled="!canProceed"
        @click="$emit('next')"
      >
        {{ $t('common.next_step') }}
      </button>
      <button
        v-else
        class="cx-btn cx-btn-primary"
        :disabled="loading"
        @click="$emit('submit')"
      >
        {{ loading ? $t('common.loading') : $t('wizard.confirm_register') }}
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
  background: rgba(255, 255, 255, 0.06);
  border-radius: 9999px;
  margin-bottom: 1.5rem;
  overflow: hidden;
  position: relative;
}

.progress-fill {
  height: 100%;
  background: var(--cx-gradient-accent);
  border-radius: 9999px;
  transition: width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  box-shadow: 0 0 12px rgba(56, 189, 248, 0.5);
}

.steps-indicator {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--cx-text-muted);
  transition: color 0.2s;
}

.step-item.active,
.step-item.completed { color: var(--cx-text-primary); }

.step-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--cx-font-mono);
  font-size: var(--cx-font-xs);
  font-weight: 600;
  background: var(--cx-bg-card);
  color: var(--cx-text-muted);
  border: 1px solid var(--cx-border-strong);
  transition: all 0.25s;
}

.step-item.active .step-circle {
  background: var(--cx-gradient-accent);
  border-color: transparent;
  color: var(--cx-text-inverse);
  box-shadow: 0 0 16px rgba(56, 189, 248, 0.45);
}

.step-item.completed .step-circle {
  background: var(--cx-led-green);
  border-color: transparent;
  color: var(--cx-text-inverse);
  box-shadow: 0 0 12px rgba(52, 211, 153, 0.4);
}

.step-label {
  font-size: var(--cx-font-xs);
  font-weight: 500;
  text-transform: none;
  letter-spacing: 0;
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
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.spacer { flex: 1; }
</style>
