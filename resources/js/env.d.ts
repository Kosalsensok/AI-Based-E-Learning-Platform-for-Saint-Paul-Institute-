/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  // eslint-disable-next-scope-block
  const component: DefineComponent<{}, {}, any>
  export default component
}
