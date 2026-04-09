<template>
  <div class="portal-layout">
    <aside class="cx-sidebar">
      <div class="cx-sidebar-brand">
        <div class="cx-sidebar-logo">C</div>
        <span class="cx-sidebar-title">Connex</span>
      </div>
      <nav>
        <NuxtLink to="/developer" class="cx-nav-item">{{ $t('nav.dashboard') }}</NuxtLink>
        <NuxtLink to="/developer/profile" class="cx-nav-item">{{ $t('nav.company_profile') }}</NuxtLink>
        <NuxtLink to="/developer/projects" class="cx-nav-item">{{ $t('nav.projects') }}</NuxtLink>
        <NuxtLink to="/developer/projects/create" class="cx-nav-item">{{ $t('nav.submit_project') }}</NuxtLink>
        <NuxtLink to="/developer/boq" class="cx-nav-item">{{ $t('nav.boq_requests') }}</NuxtLink>
        <NuxtLink to="/developer/notifications" class="cx-nav-item">{{ $t('nav.notifications') }}</NuxtLink>
      </nav>
    </aside>
    <main class="main-content">
      <header class="cx-topbar">
        <SharedLanguageSwitcher />
        <button class="cx-btn cx-btn-ghost btn-sm" @click="logout">{{ $t('common.logout') }}</button>
      </header>
      <div class="page-content">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
const { logout } = useAuth()
</script>

<style scoped>
.portal-layout { display: flex; min-height: 100vh; background: var(--cx-bg-primary); }
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  min-height: 0;
}
.page-content {
  flex: 1 1 auto;
  min-height: 0;
  overflow-y: auto;
  background: var(--cx-bg-primary);
}
.btn-sm { min-height: 40px; padding: 0 1rem; font-size: var(--cx-font-xs); }
</style>
