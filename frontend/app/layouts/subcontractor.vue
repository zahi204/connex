<template>
  <div class="portal-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">C</div>
        <span class="brand">Connex</span>
      </div>
      <nav class="sidebar-nav">
        <NuxtLink to="/subcontractor" class="nav-item">Dashboard</NuxtLink>
        <NuxtLink to="/subcontractor/profile" class="nav-item">Profile</NuxtLink>
        <NuxtLink to="/subcontractor/assignments" class="nav-item">Assignments</NuxtLink>
        <NuxtLink to="/subcontractor/rating" class="nav-item">Rating</NuxtLink>
        <NuxtLink to="/subcontractor/documents" class="nav-item">Documents</NuxtLink>
        <NuxtLink to="/subcontractor/availability" class="nav-item">Availability</NuxtLink>
        <NuxtLink to="/subcontractor/notifications" class="nav-item">Notifications</NuxtLink>
      </nav>
    </aside>
    <main class="main-content">
      <header class="topbar">
        <div class="topbar-end">
          <LanguageSwitcher />
          <button class="btn-logout" @click="logout">Logout</button>
        </div>
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
.portal-layout { display: flex; min-height: 100vh; }
.sidebar { width: 240px; background: rgba(0,0,0,0.3); border-inline-end: 1px solid rgba(255,255,255,0.08); padding: 1.5rem 0; flex-shrink: 0; }
.sidebar-header { display: flex; align-items: center; gap: 0.75rem; padding: 0 1.25rem; margin-bottom: 2rem; }
.logo { width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem; }
.brand { color: white; font-weight: 700; font-size: 1.1rem; }
.sidebar-nav { display: flex; flex-direction: column; gap: 0.25rem; }
.nav-item { display: block; padding: 0.65rem 1.25rem; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem; transition: all 0.15s; border-inline-start: 3px solid transparent; }
.nav-item:hover { color: white; background: rgba(255,255,255,0.05); }
.nav-item.router-link-active { color: white; background: rgba(99,102,241,0.15); border-inline-start-color: #6366f1; }
.main-content { flex: 1; display: flex; flex-direction: column; }
.topbar { display: flex; justify-content: flex-end; padding: 0.75rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
.topbar-end { display: flex; align-items: center; gap: 1rem; }
.btn-logout { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); padding: 0.4rem 1rem; border-radius: 8px; font-size: 0.8rem; cursor: pointer; }
.page-content { flex: 1; padding: 1.5rem; overflow-y: auto; }
</style>
