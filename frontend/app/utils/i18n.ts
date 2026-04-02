export const RTL_LOCALES = ['he', 'ar']

export const isRtl = (locale: string): boolean => {
  return RTL_LOCALES.includes(locale)
}

export const getDirection = (locale: string): 'rtl' | 'ltr' => {
  return isRtl(locale) ? 'rtl' : 'ltr'
}
