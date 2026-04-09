<template>
  <span class="cx-badge" :class="badgeClass" style="display:inline-flex;align-items:center;gap:0.35rem;">
    <span v-if="showLed" class="cx-led" :class="ledClass" style="width:7px;height:7px;flex-shrink:0;" />
    {{ label }}
  </span>
</template>

<script setup lang="ts">
import { useStatusColors } from '~/composables/useStatusColors'

const props = withDefaults(defineProps<{
  status: string
  type?: 'worker' | 'payment' | 'approval' | 'assignment'
  showLed?: boolean
}>(), {
  type: 'worker',
  showLed: true,
})

const { workerStatusBadgeClass, workerStatusLedClass, paymentStatusBadgeClass, paymentStatusLedClass, approvalStatusBadgeClass, assignmentStatusBadgeClass } = useStatusColors()

const badgeClass = computed(() => {
  switch (props.type) {
    case 'payment':    return paymentStatusBadgeClass(props.status)
    case 'approval':   return approvalStatusBadgeClass(props.status)
    case 'assignment': return assignmentStatusBadgeClass(props.status)
    default:           return workerStatusBadgeClass(props.status)
  }
})

const ledClass = computed(() => {
  switch (props.type) {
    case 'payment': return paymentStatusLedClass(props.status)
    default:        return workerStatusLedClass(props.status)
  }
})

const label = computed(() => props.status.replace(/_/g, ' '))
</script>
