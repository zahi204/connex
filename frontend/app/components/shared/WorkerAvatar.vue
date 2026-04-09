<template>
  <div class="cx-avatar" :class="`cx-avatar-${size}`" :style="wrapStyle">
    <img v-if="photoUrl" :src="photoUrl" :alt="name" class="cx-avatar-img" />
    <div v-else class="cx-avatar-initials" :style="{ fontSize: initialsSize }">{{ initials }}</div>
    <span v-if="status" class="cx-avatar-led" :class="ledClass" />
  </div>
</template>

<script setup lang="ts">
import { useStatusColors } from '~/composables/useStatusColors'

const props = withDefaults(defineProps<{
  name?: string
  photoUrl?: string | null
  status?: string | null
  size?: 'sm' | 'md' | 'lg' | 'xl'
}>(), {
  name: '',
  photoUrl: null,
  status: null,
  size: 'md',
})

const { workerStatusLedClass } = useStatusColors()

const initials = computed(() => {
  if (!props.name) return '?'
  return props.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
})

const initialsSize = computed(() => ({
  sm: '0.7rem', md: '0.9rem', lg: '1.2rem', xl: '1.5rem',
}[props.size]))

const ledClass = computed(() => props.status ? workerStatusLedClass(props.status) : 'cx-led-gray')

const wrapStyle = computed(() => ({
  sm: 'width:36px;height:36px',
  md: 'width:48px;height:48px',
  lg: 'width:72px;height:72px',
  xl: 'width:96px;height:96px',
}[props.size]))
</script>
