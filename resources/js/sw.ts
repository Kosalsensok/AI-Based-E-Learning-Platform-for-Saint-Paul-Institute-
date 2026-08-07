/// <reference lib="webworker" />
import { precacheAndRoute, cleanupOutdatedCaches } from 'workbox-precaching'
import { registerRoute } from 'workbox-routing'
import { NetworkFirst, CacheFirst } from 'workbox-strategies'
import { BackgroundSyncPlugin } from 'workbox-background-sync'

declare const self: ServiceWorkerGlobalScope

precacheAndRoute(self.__WB_MANIFEST)
cleanupOutdatedCaches()

registerRoute(({ url }) => url.pathname.startsWith('/api/v1/offline'),
  new NetworkFirst({ cacheName: 'api-offline', networkTimeoutSeconds: 5 }))

registerRoute(({ url }) => url.pathname.startsWith('/storage/'),
  new CacheFirst({ cacheName: 'media-cache' }))

const bgSync = new BackgroundSyncPlugin('elms-sync', { maxRetentionTime: 24 * 60 })
registerRoute(({ url }) => url.pathname.startsWith('/api/v1/sync'),
  new NetworkFirst({ plugins: [bgSync] }), 'POST')

self.addEventListener('message', (e) => { if (e.data === 'SKIP_WAITING') self.skipWaiting() })
