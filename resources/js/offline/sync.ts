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
