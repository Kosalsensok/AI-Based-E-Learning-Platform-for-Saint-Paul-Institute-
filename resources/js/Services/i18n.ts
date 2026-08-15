import { ref, reactive } from 'vue'
import enTranslations from '../locales/en.json'
import kmTranslations from '../locales/km.json'

export type LanguageCode = 'km' | 'en'

const translations: Record<LanguageCode, Record<string, string>> = {
  en: enTranslations,
  km: kmTranslations
}

const savedLocale = typeof window !== 'undefined' ? (localStorage.getItem('elms_lang') as LanguageCode) : null
const currentLocale = ref<LanguageCode>(savedLocale === 'en' ? 'en' : 'km')

export const i18n = {
  locale: currentLocale,

  t(key: string, defaultText?: string): string {
    const lang = currentLocale.value
    return translations[lang]?.[key] || defaultText || key
  },

  setLanguage(lang: LanguageCode) {
    currentLocale.value = lang
    if (typeof window !== 'undefined') {
      document.documentElement.lang = lang
      localStorage.setItem('elms_lang', lang)
    }
  },

  toggleLanguage() {
    this.setLanguage(currentLocale.value === 'km' ? 'en' : 'km')
  }
}
