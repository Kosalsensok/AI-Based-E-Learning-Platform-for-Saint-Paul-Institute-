import '../css/app.css'
import '@fontsource-variable/inter'
import '@fontsource/noto-sans-khmer/400.css'
import '@fontsource/noto-sans-khmer/700.css'

import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query'
import ui from '@nuxt/ui/vue-plugin'
import { registerSW } from 'virtual:pwa-register'
import { flushQueue } from '@/offline/sync'

import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import ToastService from 'primevue/toastservice'
import 'primeicons/primeicons.css'
import { initTheme } from '@/composables/useTheme'

initTheme()

registerSW({ immediate: true })
window.addEventListener('online', flushQueue)

createInertiaApp({
  title: (t) => t ? `${t} - E-LMS` : 'E-LMS',
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`,
    import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(createPinia())
      .use(VueQueryPlugin)
      .use(ui)
      .use(PrimeVue, { theme: { preset: Aura } })
      .use(ToastService)
      .mount(el)
  },
  progress: { color: '#1e40af' },
})
