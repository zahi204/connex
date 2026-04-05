<template>
  <div class="portal-layout">
    <aside class="cx-sidebar">
      <div class="cx-sidebar-brand">
        <div class="cx-sidebar-logo">C</div>
        <span class="cx-sidebar-title">Connex</span>
      </div>
      <nav>
        <NuxtLink to="/worker" class="cx-nav-item">Dashboard</NuxtLink>
        <NuxtLink to="/worker/profile" class="cx-nav-item">Profile</NuxtLink>
        <NuxtLink to="/worker/assignments" class="cx-nav-item">Assignments</NuxtLink>
        <NuxtLink to="/worker/team" class="cx-nav-item">My Team</NuxtLink>
        <NuxtLink to="/worker/training" class="cx-nav-item">Training</NuxtLink>
        <NuxtLink to="/worker/documents" class="cx-nav-item">Documents</NuxtLink>
        <NuxtLink to="/worker/payments" class="cx-nav-item">Payments</NuxtLink>
        <NuxtLink to="/worker/notifications" class="cx-nav-item">Notifications</NuxtLink>
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
