export default defineNuxtConfig({
  compatibilityDate: '2026-03-26',
  future: {
    compatibilityVersion: 4,
  },

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000',
    },
  },

  modules: [
    '@pinia/nuxt',
    '@nuxtjs/i18n',
  ],

  i18n: {
    locales: [
      { code: 'he', name: 'עברית', dir: 'rtl', file: 'he.json' },
      { code: 'ar', name: 'العربية', dir: 'rtl', file: 'ar.json' },
      { code: 'en', name: 'English', dir: 'ltr', file: 'en.json' },
    ],
    defaultLocale: 'he',
    lazy: true,
    langDir: '../i18n/locales/',
    strategy: 'no_prefix',
  },

  nitro: {
    devProxy: {
      '/api': {
        target: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },

  devtools: { enabled: true },
})
