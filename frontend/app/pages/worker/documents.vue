<template>
  <div class="portal-page">
    <h1>My Documents</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="!documents.length" class="empty">No documents found.</div>
      <div v-else class="documents-list">
        <div v-for="doc in documents" :key="doc.id" class="doc-card">
          <div class="doc-info">
            <div class="doc-name">{{ doc.name || doc.type }}</div>
            <div class="doc-meta">
              <span v-if="doc.type">{{ doc.type }}</span>
              <span v-if="doc.uploaded_at">Uploaded: {{ doc.uploaded_at }}</span>
            </div>
          </div>
          <span class="status-badge" :class="doc.status">{{ doc.status || 'uploaded' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const documents = ref<any[]>([])

onMounted(async () => {
  try {
    const res = await apiFetch('/worker/documents') as any
    documents.value = res.data || []
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load documents'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: white; font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: rgba(255,255,255,0.5); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 0.75rem; border-radius: 10px; }
.empty { color: rgba(255,255,255,0.5); text-align: center; padding: 2rem; }
.documents-list { display: flex; flex-direction: column; gap: 0.5rem; }
.doc-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
.doc-name { color: white; font-weight: 500; margin-bottom: 0.2rem; }
.doc-meta { display: flex; gap: 1rem; color: rgba(255,255,255,0.4); font-size: 0.75rem; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.status-badge.verified { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-badge.pending { background: rgba(234,179,8,0.2); color: #facc15; }
.status-badge.rejected { background: rgba(239,68,68,0.2); color: #f87171; }
.status-badge.uploaded { background: rgba(59,130,246,0.2); color: #93c5fd; }
</style>
