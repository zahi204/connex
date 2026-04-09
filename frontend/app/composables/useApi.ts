/**
 * Laravel JsonResource serializes as `{ data: { ...attrs } }`.
 * Our API envelope is `{ success, message, data: ... }`.
 * This unwraps one JsonResource layer when present.
 */
export const unwrapJsonResource = <T>(payload: unknown): T | undefined => {
  if (payload === null || payload === undefined)
    return undefined
  if (typeof payload !== 'object' || Array.isArray(payload))
    return payload as T
  const obj = payload as Record<string, unknown>
  if (
    'data' in obj
    && obj.data !== null
    && typeof obj.data === 'object'
    && !Array.isArray(obj.data)
  ) {
    return obj.data as T
  }
  return payload as T
}

const decodeXsrfValue = (raw: string): string => {
  try {
    return decodeURIComponent(raw)
  }
  catch {
    return raw
  }
}

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBaseUrl as string
  const xsrfCookie = useCookie('XSRF-TOKEN')

  const apiFetch = $fetch.create({
    baseURL: `${baseURL}/api/v1`,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    async onRequest({ options }) {
      // After /sanctum/csrf-cookie, Set-Cookie updates the browser store immediately, but
      // Nuxt's useCookie ref often lags; prefer document.cookie on the client.
      let token: string | undefined
      if (import.meta.client) {
        const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
        if (match?.[1])
          token = decodeXsrfValue(match[1])
      }
      if (!token && xsrfCookie.value)
        token = decodeXsrfValue(xsrfCookie.value)

      if (token) {
        options.headers = new Headers(options.headers as HeadersInit)
        options.headers.set('X-XSRF-TOKEN', token)
      }
    },
    async onResponseError({ response }) {
      if (response.status === 401) {
        await navigateTo('/login')
      }
      if (response.status === 419) {
        await $fetch(`${baseURL}/sanctum/csrf-cookie`, { credentials: 'include' })
      }
    },
  })

  return { apiFetch, baseURL }
}
