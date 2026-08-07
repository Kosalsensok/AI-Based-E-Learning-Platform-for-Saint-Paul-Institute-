export interface VttCue {
  id: number
  startTime: string
  endTime: string
  textEn: string
  textKh: string
}

export function parseVttString(vttText: string): VttCue[] {
  const cues: VttCue[] = []
  const blocks = vttText.split(/\n\s*\n/)
  
  const timestampRegex = /(\d{2}:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}\.\d{3})\s*-->\s*(\d{2}:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}\.\d{3})/

  let idCounter = 1
  for (const block of blocks) {
    const lines = block.strip ? block.strip().split('\n') : block.trim().split('\n')
    let timeIndex = -1
    for (let i = 0; i < lines.length; i++) {
      if (timestampRegex.test(lines[i])) {
        timeIndex = i
        break
      }
    }

    if (timeIndex !== -1) {
      const match = lines[timeIndex].match(timestampRegex)
      const startTime = match ? match[1] : '00:00.000'
      const endTime = match ? match[2] : '00:05.000'
      const textLines = lines.slice(timeIndex + 1).join('\n')

      cues.push({
        id: idCounter++,
        startTime,
        endTime,
        textEn: textLines,
        textKh: ''
      })
    }
  }

  return cues
}

export function exportToVttFormat(cues: VttCue[], lang: 'en' | 'kh' = 'kh'): string {
  let output = 'WEBVTT\n\n'

  for (const cue of cues) {
    const text = lang === 'kh' ? (cue.textKh || cue.textEn) : cue.textEn
    output += `${cue.startTime} --> ${cue.endTime}\n${text}\n\n`
  }

  return output.trim() + '\n'
}

export function createVttBlobUrl(vttContent: string): string {
  const blob = new Blob([vttContent], { type: 'text/vtt;charset=utf-8' })
  return URL.createObjectURL(blob)
}
