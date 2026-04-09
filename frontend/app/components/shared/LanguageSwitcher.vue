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
  display: inline-flex;
  gap: 0.125rem;
  background: rgba(26, 34, 54, 0.6);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--cx-border);
  border-radius: var(--cx-radius-md);
  padding: 0.2rem;
}

.language-switcher button {
  padding: 0.4rem 0.85rem;
  border: none;
  border-radius: calc(var(--cx-radius-md) - 4px);
  background: transparent;
  color: var(--cx-text-muted);
  cursor: pointer;
  font-family: inherit;
  font-size: 0.78rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  transition: all 0.2s;
}

.language-switcher button:hover {
  color: var(--cx-text-primary);
}

.language-switcher button.active {
  background: var(--cx-primary-soft);
  color: var(--cx-primary);
  box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.25), 0 0 12px rgba(56, 189, 248, 0.15);
}
</style>
