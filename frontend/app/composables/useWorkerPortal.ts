import { unwrapJsonResource } from '~/composables/useApi'

/**
 * Typed wrappers for all /worker/* portal endpoints.
 * Centralises the unwrapJsonResource handling used on the dashboard.
 */
export const useWorkerPortal = () => {
  const { apiFetch } = useApi()

  // ---- Profile ----
  const profile = ref<any>(null)
  const profileLoading = ref(false)
  const profileError = ref('')

  async function fetchProfile() {
    profileLoading.value = true
    profileError.value = ''
    try {
      const res = await apiFetch('/worker/profile') as any
      const raw = res?.data
      profile.value = unwrapJsonResource(raw) ?? raw ?? null
    } catch (e: any) {
      profileError.value = e?.data?.message || 'Failed to load profile'
    } finally {
      profileLoading.value = false
    }
    return profile.value
  }

  async function updateProfile(data: Record<string, any>) {
    await apiFetch('/worker/profile', { method: 'PUT', body: data })
    await fetchProfile()
  }

  // ---- Assignments ----
  const assignments = ref<any[]>([])
  const assignmentsMeta = ref<any>(null)
  const assignmentsLoading = ref(false)

  async function fetchAssignments(params?: Record<string, any>) {
    assignmentsLoading.value = true
    try {
      const res = await apiFetch('/worker/assignments', { query: params ?? {} }) as any
      const body = res?.data ?? res
      if (Array.isArray(body)) {
        assignments.value = body
      } else if (body?.data) {
        assignments.value = body.data
        assignmentsMeta.value = body.meta ?? null
      }
    } finally {
      assignmentsLoading.value = false
    }
    return assignments.value
  }

  // ---- Team ----
  const team = ref<any>(null)
  const teamLoading = ref(false)

  async function fetchTeam() {
    teamLoading.value = true
    try {
      const res = await apiFetch('/worker/team') as any
      team.value = res?.data ?? null
    } finally {
      teamLoading.value = false
    }
    return team.value
  }

  // ---- Training ----
  const training = ref<any[]>([])
  const trainingLoading = ref(false)

  async function fetchTraining() {
    trainingLoading.value = true
    try {
      const res = await apiFetch('/worker/training') as any
      const body = res?.data
      training.value = Array.isArray(body) ? body : []
    } finally {
      trainingLoading.value = false
    }
    return training.value
  }

  // ---- Documents ----
  const documents = ref<any[]>([])
  const documentsLoading = ref(false)

  async function fetchDocuments() {
    documentsLoading.value = true
    try {
      const res = await apiFetch('/worker/documents') as any
      documents.value = res?.data ?? []
    } finally {
      documentsLoading.value = false
    }
    return documents.value
  }

  // ---- Payments ----
  const paymentsData = ref<any>(null)
  const paymentsLoading = ref(false)

  async function fetchPayments() {
    paymentsLoading.value = true
    try {
      const res = await apiFetch('/worker/payments') as any
      paymentsData.value = res?.data ?? null
    } finally {
      paymentsLoading.value = false
    }
    return paymentsData.value
  }

  // ---- Availability ----
  async function updateAvailability(data: Record<string, any>) {
    await apiFetch('/worker/availability', { method: 'PUT', body: data })
  }

  return {
    // profile
    profile,
    profileLoading,
    profileError,
    fetchProfile,
    updateProfile,
    // assignments
    assignments,
    assignmentsMeta,
    assignmentsLoading,
    fetchAssignments,
    // team
    team,
    teamLoading,
    fetchTeam,
    // training
    training,
    trainingLoading,
    fetchTraining,
    // documents
    documents,
    documentsLoading,
    fetchDocuments,
    // payments
    paymentsData,
    paymentsLoading,
    fetchPayments,
    // availability
    updateAvailability,
  }
}
