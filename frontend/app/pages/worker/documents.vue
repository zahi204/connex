<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('nav.documents') }}</h1>

    <!-- Upload section -->
    <div class="cx-card" style="margin-bottom:1.5rem;">
      <h2 class="cx-section-title">{{ $t('documents.upload') }}</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:1rem;">
        <div>
          <label class="cx-label">{{ $t('documents.name') }}</label>
          <input v-model="uploadName" class="cx-input" type="text" :placeholder="$t('documents.name_placeholder')" />
        </div>
        <div>
          <label class="cx-label">{{ $t('documents.type') }}</label>
          <select v-model="uploadType" class="cx-select">
            <option v-for="d in docTypeOptions" :key="d.value" :value="d.value">{{ d.label }}</option>
          </select>
        </div>
      </div>
      <SharedFileUpload
        v-if="uploadName && uploadType"
        :upload-url="`${baseURL}/api/v1/worker/documents`"
        :extra-fields="{ name: uploadName, document_type: uploadType }"
        @uploaded="onUploaded"
      />
      <p v-else class="cx-label" style="color:var(--cx-text-muted);">{{ $t('documents.fill_fields_first') }}</p>
    </div>

    <!-- Documents list -->
    <h2 class="cx-section-title">{{ $t('documents.my_documents') }}</h2>

    <SharedLoadingState v-if="loading" :rows="4" />
    <div v-else-if="error" class="cx-toast cx-toast-error" style="position:static;">{{ error }}</div>
    <SharedEmptyState v-else-if="!documents.length" icon="📄" :title="$t('documents.empty')" />

    <div v-else class="cx-card" style="padding:0;overflow:hidden;">
      <table class="cx-table">
        <thead>
          <tr>
            <th>{{ $t('documents.name') }}</th>
            <th>{{ $t('documents.type') }}</th>
            <th>{{ $t('documents.uploaded_at') }}</th>
            <th>{{ $t('documents.status') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documents" :key="doc.id">
            <td style="font-weight:600;color:var(--cx-text-primary);">{{ doc.name || doc.document_type }}</td>
            <td class="cx-mono" style="font-size:var(--cx-font-xs);">{{ doc.document_type ?? '—' }}</td>
            <td style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);">{{ doc.created_at ? new Date(doc.created_at).toLocaleDateString() : '—' }}</td>
            <td><SharedStatusBadge :status="doc.status ?? 'pending'" type="approval" /></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Upload success toast -->
    <Transition name="fade">
      <div v-if="uploadToast" class="cx-toast cx-toast-success">✓ {{ uploadToast }}</div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { t } = useI18n()
const { fetchDocuments, documents, documentsLoading: loading } = useWorkerPortal()
const { apiFetch, baseURL } = useApi()
const error = ref('')
const uploadName = ref('')
const uploadType = ref('')
const uploadToast = ref('')
const docTypeOptions = ref<any[]>([])

onMounted(async () => {
  try {
    await fetchDocuments()
    const res = await apiFetch('/reference/document_types') as any
    docTypeOptions.value = res?.data ?? []
    if (docTypeOptions.value.length) uploadType.value = docTypeOptions.value[0].value
  } catch (e: any) {
    error.value = e?.data?.message || t('documents.load_failed')
  }
})

async function onUploaded(_res: any) {
  uploadName.value = ''
  uploadToast.value = t('documents.uploaded_approval')
  setTimeout(() => { uploadToast.value = '' }, 4000)
  await fetchDocuments()
}
</script>
