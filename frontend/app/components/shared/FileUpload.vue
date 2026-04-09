<template>
  <div>
    <div
      class="cx-file-drop"
      :class="{ 'cx-drag-over': isDragging }"
      @click="triggerInput"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
    >
      <div class="cx-file-drop-icon">📎</div>
      <div class="cx-file-drop-text">
        <span v-if="selectedFile">{{ selectedFile.name }}</span>
        <span v-else>{{ $t('upload.drop_or_click') }}</span>
      </div>
      <div class="cx-file-drop-hint">{{ $t('upload.hint') }}</div>
      <input ref="inputRef" type="file" :accept="accept" style="display:none;" @change="onFileSelected" />
    </div>

    <div v-if="uploading" class="cx-upload-progress" style="margin-top:0.75rem;">
      <div class="cx-upload-progress-bar" :style="{ width: progress + '%' }" />
    </div>

    <div v-if="errorMsg" class="cx-toast cx-toast-error" style="position:static;margin-top:0.75rem;">
      {{ errorMsg }}
    </div>
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  uploadUrl: string
  accept?: string
  extraFields?: Record<string, string>
}>(), {
  accept: '.pdf,.jpg,.jpeg,.png',
  extraFields: () => ({}),
})

const emit = defineEmits<{
  (e: 'uploaded', response: any): void
}>()

const { apiFetch } = useApi()
const isDragging = ref(false)
const selectedFile = ref<File | null>(null)
const uploading = ref(false)
const progress = ref(0)
const errorMsg = ref('')
const inputRef = ref<HTMLInputElement | null>(null)

function triggerInput() {
  inputRef.value?.click()
}

function onFileSelected(e: Event) {
  const target = e.target as HTMLInputElement
  if (target.files?.[0]) pickFile(target.files[0])
}

function onDrop(e: DragEvent) {
  isDragging.value = false
  if (e.dataTransfer?.files?.[0]) pickFile(e.dataTransfer.files[0])
}

function pickFile(f: File) {
  selectedFile.value = f
  upload(f)
}

async function upload(file: File) {
  uploading.value = true
  progress.value = 10
  errorMsg.value = ''
  try {
    const formData = new FormData()
    formData.append('file', file)
    for (const [k, v] of Object.entries(props.extraFields)) {
      formData.append(k, v)
    }

    // apiFetch with FormData — drop Content-Type so browser sets boundary
    const res = await $fetch(props.uploadUrl, {
      method: 'POST',
      body: formData,
      credentials: 'include',
    })

    progress.value = 100
    emit('uploaded', res)
  } catch (e: any) {
    errorMsg.value = e?.data?.message || 'Upload failed'
    progress.value = 0
  } finally {
    uploading.value = false
    setTimeout(() => { progress.value = 0 }, 1200)
  }
}
</script>
