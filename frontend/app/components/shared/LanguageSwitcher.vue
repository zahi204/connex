<template>
  <div class="language-switcher">
    <button
      v-for="loc in availableLocales"
      :key="loc.code"
      :class="{ active: currentLocale === loc.code }"
      @click="switchLocale(loc.code as LocaleCode)"
    >
      {{ loc.name }}
    </button>
  </div>
</template>

<script setup lang="ts">
const { locale, locales, setLocale } = useI18n()

const currentLocale = computed(() => locale.value)

const availableLocales = computed(() =>
  (locales.value as Array<{ code: string; name: string; dir: string }>)
)

type LocaleCode = 'he' | 'ar' | 'en'

const switchLocale = async (code: LocaleCode) => {
  await setLocale(code)
  const dir = availableLocales.value.find(l => l.code === code)?.dir || 'ltr'
  document.documentElement.setAttribute('dir', dir)
  document.documentElement.setAttribute('lang', code)
}
</script>

<style scoped>
.language-switcher {
  display: flex;
  gap: 0.25rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 0.25rem;
  backdrop-filter: blur(10px);
}

.language-switcher button {
  padding: 0.4rem 0.75rem;
  border: none;
  border-radius: 6px;
  background: transparent;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.2s;
}

.language-switcher button:hover {
  color: white;
  background: rgba(255, 255, 255, 0.1);
}

.language-switcher button.active {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}
</style>
