export async function downloadVideo(lessonId: number, url: string, onProgress?: (p: number) => void) {
  const root = await navigator.storage.getDirectory()
  const fh = await root.getFileHandle(`lesson-${lessonId}.mp4`, { create: true })
  const w = await fh.createWritable()
  const res = await fetch(url)
  const total = +(res.headers.get('content-length') || 0)
  const reader = res.body!.getReader()
  let got = 0
  for (;;) {
    const { done, value } = await reader.read()
    if (done) break
    await w.write(value); got += value.length
    if (total && onProgress) onProgress(Math.round((got / total) * 100))
  }
  await w.close()
}

export async function getOfflineVideoUrl(lessonId: number) {
  try {
    const root = await navigator.storage.getDirectory()
    const fh = await root.getFileHandle(`lesson-${lessonId}.mp4`)
    return URL.createObjectURL(await fh.getFile())
  } catch { return null }
}
