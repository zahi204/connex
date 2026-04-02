export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBaseUrl as string

  const apiFetch = $fetch.create({
    baseURL: `${baseURL}/api/v1`,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    async onRequest({ options }) {
      const xsrfToken = useCookie('XSRF-TOKEN')
      if (xsrfToken.value) {
        options.headers = {
          ...options.headers,
          'X-XSRF-TOKEN': xsrfToken.value,
        }
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
