<template>
  <div class="blank-layout">
    <div class="lang-bar">
      <SharedLanguageSwitcher />
    </div>
    <slot />
  </div>
</template>

<style scoped>
.blank-layout {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.lang-bar {
  position: fixed;
  top: 1rem;
  left: 1rem;
  right: 1rem;
  display: flex;
  justify-content: flex-end;
  z-index: 10;
}
</style>
