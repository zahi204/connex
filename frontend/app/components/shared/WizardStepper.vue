<template>
  <div class="wizard-stepper">
    <div class="steps-indicator">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="step-item"
        :class="{ active: index === currentStep, completed: index < currentStep }"
      >
        <div class="step-circle">
          <span v-if="index < currentStep">&#10003;</span>
          <span v-else>{{ index + 1 }}</span>
        </div>
        <span class="step-label">{{ step.title }}</span>
      </div>
    </div>

    <div class="step-content">
      <slot :name="`step-${currentStep}`" />
    </div>

    <div class="step-actions">
      <button
        v-if="currentStep > 0"
        class="btn-secondary"
        @click="$emit('prev')"
      >
        {{ $t('common.cancel') }}
      </button>
      <div class="spacer" />
      <button
        v-if="currentStep < steps.length - 1"
        class="btn-primary"
        :disabled="!canProceed"
        @click="$emit('next')"
      >
        Next
      </button>
      <button
        v-else
        class="btn-primary"
        :disabled="loading"
        @click="$emit('submit')"
      >
        {{ loading ? $t('common.loading') : $t('common.submit') }}
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
.wizard-stepper { max-width: 700px; margin: 0 auto; }
.steps-indicator { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap; }
.step-item { display: flex; align-items: center; gap: 0.5rem; opacity: 0.4; transition: opacity 0.2s; }
.step-item.active, .step-item.completed { opacity: 1; }
.step-circle {
  width: 32px; height: 32px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.85rem; font-weight: 600;
  background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
  border: 2px solid rgba(255,255,255,0.2);
}
.step-item.active .step-circle { background: #6366f1; border-color: #6366f1; color: white; }
.step-item.completed .step-circle { background: #22c55e; border-color: #22c55e; color: white; }
.step-label { font-size: 0.8rem; color: rgba(255,255,255,0.6); display: none; }
@media (min-width: 640px) { .step-label { display: inline; } }
.step-content { min-height: 300px; }
.step-actions { display: flex; gap: 1rem; margin-top: 2rem; }
.spacer { flex: 1; }
.btn-primary {
  padding: 0.75rem 2rem; background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
}
.btn-primary:disabled { opacity: 0.3; cursor: not-allowed; }
.btn-secondary {
  padding: 0.75rem 2rem; background: rgba(255,255,255,0.08);
  color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.15);
  border-radius: 10px; cursor: pointer;
}
</style>
