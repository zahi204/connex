<template>
  <div class="blank-layout">
    <div class="lang-bar">
      <SharedLanguageSwitcher />
    </div>
    <div class="blank-layout__content">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.blank-layout {
  min-height: 100vh;
  min-height: 100dvh;
  background: var(--cx-blank-gradient);
  font-family: var(--cx-font-sans);
  color: var(--cx-text-primary);
}

.blank-layout__content {
  min-height: 100vh;
  min-height: 100dvh;
  padding-top: 4.75rem;
  box-sizing: border-box;
}

.lang-bar {
  position: fixed;
  top: 1rem;
  inset-inline: 1rem;
  display: flex;
  justify-content: flex-end;
  z-index: 10;
  pointer-events: none;
}

.lang-bar :deep(.language-switcher) {
  pointer-events: auto;
}
</style>
