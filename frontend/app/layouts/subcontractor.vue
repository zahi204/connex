<template>
  <div class="portal-layout">
    <aside class="cx-sidebar">
      <div class="cx-sidebar-brand">
        <div class="cx-sidebar-logo">C</div>
        <span class="cx-sidebar-title">Connex</span>
      </div>
      <nav>
        <NuxtLink to="/subcontractor" class="cx-nav-item">Dashboard</NuxtLink>
        <NuxtLink to="/subcontractor/profile" class="cx-nav-item">Profile</NuxtLink>
        <NuxtLink to="/subcontractor/assignments" class="cx-nav-item">Assignments</NuxtLink>
        <NuxtLink to="/subcontractor/rating" class="cx-nav-item">Rating</NuxtLink>
        <NuxtLink to="/subcontractor/documents" class="cx-nav-item">Documents</NuxtLink>
        <NuxtLink to="/subcontractor/availability" class="cx-nav-item">Availability</NuxtLink>
        <NuxtLink to="/subcontractor/notifications" class="cx-nav-item">Notifications</NuxtLink>
      </nav>
    </aside>
    <main class="main-content">
      <header class="cx-topbar">
        <LanguageSwitcher />
        <button class="cx-btn cx-btn-danger btn-sm" @click="logout">LOGOUT</button>
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
.main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
.page-content { flex: 1; overflow-y: auto; }
.btn-sm { min-height: 40px; padding: 0 1rem; font-size: var(--cx-font-xs); }
</style>
