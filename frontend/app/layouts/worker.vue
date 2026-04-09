<template>
  <div class="portal-layout">
    <aside class="cx-sidebar">
      <div class="cx-sidebar-brand">
        <div class="cx-sidebar-logo">C</div>
        <span class="cx-sidebar-title">Connex</span>
      </div>
      <nav>
        <NuxtLink to="/worker" class="cx-nav-item">{{ $t('nav.dashboard') }}</NuxtLink>
        <NuxtLink to="/worker/profile" class="cx-nav-item">{{ $t('nav.profile') }}</NuxtLink>
        <NuxtLink to="/worker/assignments" class="cx-nav-item">{{ $t('nav.assignments') }}</NuxtLink>
        <NuxtLink to="/worker/team" class="cx-nav-item">{{ $t('nav.team') }}</NuxtLink>
        <NuxtLink to="/worker/training" class="cx-nav-item">{{ $t('nav.training') }}</NuxtLink>
        <NuxtLink to="/worker/documents" class="cx-nav-item">{{ $t('nav.documents') }}</NuxtLink>
        <NuxtLink to="/worker/payments" class="cx-nav-item">{{ $t('nav.payments') }}</NuxtLink>
        <NuxtLink to="/worker/availability" class="cx-nav-item">{{ $t('nav.availability') }}</NuxtLink>
        <NuxtLink to="/worker/directory" class="cx-nav-item">{{ $t('directory.title') }}</NuxtLink>
        <NuxtLink to="/worker/notifications" class="cx-nav-item cx-notification-badge">
          {{ $t('nav.notifications') }}
          <span v-if="unreadCount > 0" class="cx-notification-count">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
        </NuxtLink>
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
const { unreadCount, startPolling, stopPolling } = useNotifications()

onMounted(() => startPolling(30_000))
onUnmounted(() => stopPolling())
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

/* Notification badge on nav item */
.cx-nav-item.cx-notification-badge {
  position: relative;
  display: flex;
  align-items: center;
}
</style>
