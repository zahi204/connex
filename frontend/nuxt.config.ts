export default defineNuxtConfig({
  compatibilityDate: '2026-03-26',
  future: {
    compatibilityVersion: 4,
  },

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000',
    },
  },

  css: [
    '~/assets/css/design-system.css',
  ],

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
        target: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
    },
  },

  devServer: {
    host: '127.0.0.1',
    port: 3000,
  },

  devtools: { enabled: true },
})
