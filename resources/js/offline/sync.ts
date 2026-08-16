import { db } from './db'

export async function queueSync(type: 'progress' | 'quiz-attempt', payload: any) {
  const d = await db()
  await d.add('syncQueue', { id: crypto.randomUUID(), type, payload })
  if (navigator.onLine) await flushQueue()
}

export async function flushQueue() {
  const d = await db()
  for (const item of await d.getAll('syncQueue')) {
    try {
      const res = await fetch(`/api/v1/sync/${item.type}`, {
        method: 'POST', credentials: 'include',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(item.payload),
      })
      if (res.ok) await d.delete('syncQueue', item.id)
      else if (res.status !== 0) await d.delete('syncQueue', item.id)
    } catch { break }
  }
}

/**
 * Cache Academic / General data into IndexedDB
 */
export async function setCachedData(key: string, data: any) {
  try {
    const d = await db()
    await d.put('academicCache', {
      key,
      data,
      cachedAt: Date.now(),
    })
  } catch (e) {
    console.warn('Offline cache write failed:', e)
  }
}

/**
 * Get Cached data from IndexedDB
 */
export async function getCachedData<T = any>(key: string): Promise<T | null> {
  try {
    const d = await db()
    const record = await d.get('academicCache', key)
    return record ? (record.data as T) : null
  } catch (e) {
    return null
  }
}

/**
 * Stale-While-Revalidate (SWR) Pattern:
 * 1. Returns cached local data immediately if available for instantaneous UI render.
 * 2. Fetches fresh data in background from server and updates local cache.
 * 3. Calls onUpdated callback when fresh data arrives.
 */
export async function fetchWithSWR<T = any>(
  key: string,
  fetcher: () => Promise<T>,
  onUpdated?: (freshData: T) => void
): Promise<T | null> {
  const cached = await getCachedData<T>(key)

  // Asynchronously revalidate in background
  if (typeof window !== 'undefined' && navigator.onLine) {
    fetcher()
      .then(async (fresh) => {
        if (fresh !== undefined && fresh !== null) {
          await setCachedData(key, fresh)
          if (onUpdated) {
            onUpdated(fresh)
          }
        }
      })
      .catch((err) => {
        console.warn(`SWR background revalidation failed for ${key}:`, err)
      })
  }

  // If we had cache, return it immediately; otherwise await server response
  if (cached !== null) {
    return cached
  }

  try {
    const fresh = await fetcher()
    if (fresh !== undefined && fresh !== null) {
      await setCachedData(key, fresh)
    }
    return fresh
  } catch (err) {
    return null
  }
}
