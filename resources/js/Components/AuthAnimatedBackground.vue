<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

let animationFrameId: number | null = null
let isAnimating = false
let width = 0
let height = 0
let dpr = 1

// Mouse Tracking for Smooth Soft Proximity Interaction
const mouse = {
  x: -1000,
  y: -1000,
  targetX: -1000,
  targetY: -1000,
  active: false,
}

// Gentle Click Shockwave System
interface Shockwave {
  x: number
  y: number
  radius: number
  maxRadius: number
  intensity: number
  speed: number
}
const shockwaves: Shockwave[] = []

// Dark Mode Detection
let isDark = true
let themeObserver: MutationObserver | null = null

const updateTheme = () => {
  if (typeof document !== 'undefined') {
    isDark = document.documentElement.classList.contains('dark')
  }
}

// Optimized 2D Simplex Noise for Ultra-Smooth 60FPS Fluid Vector Field
const F2 = 0.5 * (Math.sqrt(3.0) - 1.0)
const G2 = (3.0 - Math.sqrt(3.0)) / 6.0

const perm = new Uint8Array(512)
const gradP = new Float32Array(512 * 2)

const initNoise = () => {
  const p = new Uint8Array(256)
  for (let i = 0; i < 256; i++) p[i] = i
  for (let i = 255; i > 0; i--) {
    const r = Math.floor(Math.random() * (i + 1))
    const t = p[i]
    p[i] = p[r]
    p[r] = t
  }
  for (let i = 0; i < 512; i++) {
    perm[i] = p[i & 255]
    const angle = (perm[i] / 256) * Math.PI * 2
    gradP[i * 2] = Math.cos(angle)
    gradP[i * 2 + 1] = Math.sin(angle)
  }
}

const noise2D = (xin: number, yin: number): number => {
  let n0 = 0
  let n1 = 0
  let n2 = 0
  const s = (xin + yin) * F2
  const i = Math.floor(xin + s)
  const j = Math.floor(yin + s)
  const t = (i + j) * G2
  const X0 = i - t
  const Y0 = j - t
  const x0 = xin - X0
  const y0 = yin - Y0

  let i1 = 0
  let j1 = 0
  if (x0 > y0) {
    i1 = 1
    j1 = 0
  } else {
    i1 = 0
    j1 = 1
  }

  const x1 = x0 - i1 + G2
  const y1 = y0 - j1 + G2
  const x2 = x0 - 1.0 + 2.0 * G2
  const y2 = y0 - 1.0 + 2.0 * G2

  const ii = i & 255
  const jj = j & 255

  let t0 = 0.5 - x0 * x0 - y0 * y0
  if (t0 >= 0) {
    const gi0 = perm[ii + perm[jj]]
    t0 *= t0
    n0 = t0 * t0 * (gradP[gi0 * 2] * x0 + gradP[gi0 * 2 + 1] * y0)
  }

  let t1 = 0.5 - x1 * x1 - y1 * y1
  if (t1 >= 0) {
    const gi1 = perm[ii + i1 + perm[jj + j1]]
    t1 *= t1
    n1 = t1 * t1 * (gradP[gi1 * 2] * x1 + gradP[gi1 * 2 + 1] * y1)
  }

  let t2 = 0.5 - x2 * x2 - y2 * y2
  if (t2 >= 0) {
    const gi2 = perm[ii + 1 + perm[jj + 1]]
    t2 *= t2
    n2 = t2 * t2 * (gradP[gi2 * 2] * x2 + gradP[gi2 * 2 + 1] * y2)
  }

  return 70.0 * (n0 + n1 + n2)
}

const handleMouseMove = (e: MouseEvent) => {
  if (!containerRef.value) return
  const rect = containerRef.value.getBoundingClientRect()
  mouse.targetX = e.clientX - rect.left
  mouse.targetY = e.clientY - rect.top
  mouse.active = true
}

const handleMouseLeave = () => {
  mouse.active = false
  mouse.targetX = -1000
  mouse.targetY = -1000
}

const handleTouchMove = (e: TouchEvent) => {
  if (!containerRef.value || e.touches.length === 0) return
  const rect = containerRef.value.getBoundingClientRect()
  mouse.targetX = e.touches[0].clientX - rect.left
  mouse.targetY = e.touches[0].clientY - rect.top
  mouse.active = true
}

const handleTouchEnd = () => {
  mouse.active = false
}

const handleClick = (e: MouseEvent) => {
  if (!containerRef.value) return
  const rect = containerRef.value.getBoundingClientRect()
  shockwaves.push({
    x: e.clientX - rect.left,
    y: e.clientY - rect.top,
    radius: 0,
    maxRadius: 280,
    intensity: 0.6,
    speed: 3.5,
  })
}

const resize = () => {
  if (!containerRef.value || !canvasRef.value) return
  width = containerRef.value.clientWidth || window.innerWidth
  height = containerRef.value.clientHeight || window.innerHeight
  dpr = Math.min(window.devicePixelRatio || 1, 2)

  canvasRef.value.width = width * dpr
  canvasRef.value.height = height * dpr
  canvasRef.value.style.width = `${width}px`
  canvasRef.value.style.height = `${height}px`
}

// 60FPS Subtle & Calm Manus AI Motion Engine
const render = (time: number) => {
  if (!isAnimating || !canvasRef.value) return
  animationFrameId = requestAnimationFrame(render)

  const ctx = canvasRef.value.getContext('2d', { alpha: true })
  if (!ctx) return

  ctx.save()
  ctx.scale(dpr, dpr)
  ctx.clearRect(0, 0, width, height)

  // Smooth mouse lerping
  mouse.x += (mouse.targetX - mouse.x) * 0.08
  mouse.y += (mouse.targetY - mouse.y) * 0.08

  // Update Shockwaves
  for (let i = shockwaves.length - 1; i >= 0; i--) {
    const sw = shockwaves[i]
    sw.radius += sw.speed
    sw.intensity *= 0.97
    if (sw.radius > sw.maxRadius || sw.intensity < 0.02) {
      shockwaves.splice(i, 1)
    }
  }

  // Manus Ultra-Fine Grid Configuration (22px Spacing)
  const spacing = 22
  const cols = Math.ceil(width / spacing) + 1
  const rows = Math.ceil(height / spacing) + 1
  const offsetX = (width % spacing) / 2
  const offsetY = (height % spacing) / 2

  // Ultra-gentle slow time drift (ចលនាយឺតៗ ស្រទន់)
  const timeSec = time * 0.001
  const flowTime1 = timeSec * 0.045
  const flowTime2 = timeSec * 0.065

  const mouseRadius = 200
  const mouseRadiusSq = mouseRadius * mouseRadius

  // Render Every Dot with Subtle Organic Waves
  for (let r = 0; r < rows; r++) {
    const origY = r * spacing + offsetY

    for (let c = 0; c < cols; c++) {
      const origX = c * spacing + offsetX
      const normX = origX / width

      // 1. Dual-Frequency Simplex Noise for Subtle Gentle Drift
      const nVal1 = noise2D(origX * 0.0022 + flowTime1, origY * 0.0022 - flowTime1 * 0.4)
      const nVal2 = noise2D(origX * 0.0045 - flowTime2 * 0.5, origY * 0.0045 + flowTime2 * 0.6)
      const combinedNoise = (nVal1 * 0.65 + nVal2 * 0.35 + 1) * 0.5 // 0.0 -> 1.0

      // Delicate Base Alpha & Radius
      let alpha = isDark ? 0.06 + Math.pow(combinedNoise, 2.5) * 0.42 : 0.05 + Math.pow(combinedNoise, 2.2) * 0.32
      let radius = isDark ? 0.75 + combinedNoise * 0.55 : 0.7 + combinedNoise * 0.45

      let currentX = origX
      let currentY = origY

      // 2. Gentle Cursor Proximity Illumination
      if (mouse.active) {
        const dx = origX - mouse.x
        const dy = origY - mouse.y
        const distSq = dx * dx + dy * dy

        if (distSq < mouseRadiusSq && distSq > 0) {
          const dist = Math.sqrt(distSq)
          const factor = Math.pow(1 - dist / mouseRadius, 2)
          
          // Soft Illumination Boost
          alpha += factor * (isDark ? 0.55 : 0.4)
          radius += factor * 0.85

          // Very subtle smooth displacement
          const push = factor * 2.0
          currentX += (dx / dist) * push
          currentY += (dy / dist) * push
        }
      }

      // 3. Gentle Shockwave Interaction
      for (const sw of shockwaves) {
        const swDx = origX - sw.x
        const swDy = origY - sw.y
        const swDist = Math.sqrt(swDx * swDx + swDy * swDy)
        const waveDiff = Math.abs(swDist - sw.radius)

        if (waveDiff < 35) {
          const waveFactor = (1 - waveDiff / 35) * sw.intensity
          alpha += waveFactor * 0.45
          radius += waveFactor * 0.7
          if (swDist > 0) {
            currentX += (swDx / swDist) * (waveFactor * 3.5)
            currentY += (swDy / swDist) * (waveFactor * 3.5)
          }
        }
      }

      alpha = Math.min(Math.max(alpha, 0.03), 0.85)

      // 4. Manus Exact Organic Palette Mapping
      if (isDark) {
        const colorBias = normX + (combinedNoise - 0.5) * 0.3

        if (alpha > 0.4 && mouse.active && Math.hypot(origX - mouse.x, origY - mouse.y) < 70) {
          // Pure Crisp White under Cursor
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
        } else if (colorBias < 0.35) {
          // Subtle Cyber Emerald / Mint / Aqua (Left Wing)
          const greenRatio = Math.sin(timeSec * 0.3 + origY * 0.008) * 0.5 + 0.5
          const rCol = Math.round(16 + greenRatio * 15)
          const gCol = Math.round(180 + greenRatio * 35)
          const bCol = Math.round(150 + (1 - greenRatio) * 50)
          ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
        } else if (colorBias > 0.65) {
          // Subtle Warm Amber / Gold / Coral (Right Wing)
          const warmRatio = Math.cos(timeSec * 0.25 + origX * 0.008) * 0.5 + 0.5
          const rCol = Math.round(245 + warmRatio * 10)
          const gCol = Math.round(140 + warmRatio * 40)
          const bCol = Math.round(30 + warmRatio * 60)
          ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
        } else {
          // Center: Sleek Subtle Crystal Mist
          ctx.fillStyle = `rgba(225, 235, 255, ${alpha})`
        }
      } else {
        // Light Mode: Subtle High-Precision Slate
        const colorBias = normX + (combinedNoise - 0.5) * 0.3
        if (colorBias < 0.35) {
          ctx.fillStyle = `rgba(13, 148, 136, ${alpha * 0.9})`
        } else if (colorBias > 0.65) {
          ctx.fillStyle = `rgba(217, 119, 6, ${alpha * 0.9})`
        } else {
          ctx.fillStyle = `rgba(71, 85, 105, ${alpha})`
        }
      }

      // Draw Smooth Tiny Circular Dot
      ctx.beginPath()
      ctx.arc(currentX, currentY, radius, 0, Math.PI * 2)
      ctx.fill()
    }
  }

  // 5. Manus Ambient Edge Soft Vignette
  if (isDark) {
    const vignette = ctx.createRadialGradient(
      width / 2,
      height / 2,
      Math.min(width, height) * 0.3,
      width / 2,
      height / 2,
      Math.max(width, height) * 0.8
    )
    vignette.addColorStop(0, 'rgba(7, 7, 9, 0)')
    vignette.addColorStop(0.7, 'rgba(7, 7, 9, 0.3)')
    vignette.addColorStop(1, 'rgba(7, 7, 9, 0.92)')
    ctx.fillStyle = vignette
    ctx.fillRect(0, 0, width, height)
  }

  ctx.restore()
}

const startAnimation = () => {
  if (isAnimating) return
  isAnimating = true
  animationFrameId = requestAnimationFrame(render)
}

const stopAnimation = () => {
  if (animationFrameId !== null) {
    cancelAnimationFrame(animationFrameId)
    animationFrameId = null
  }
  isAnimating = false
}

const handleVisibilityChange = () => {
  if (document.hidden) {
    stopAnimation()
  } else {
    startAnimation()
  }
}

onMounted(() => {
  initNoise()
  updateTheme()
  resize()

  themeObserver = new MutationObserver(() => {
    updateTheme()
  })
  themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })

  window.addEventListener('mousemove', handleMouseMove, { passive: true })
  window.addEventListener('mouseleave', handleMouseLeave)
  window.addEventListener('touchmove', handleTouchMove, { passive: true })
  window.addEventListener('touchend', handleTouchEnd)
  window.addEventListener('click', handleClick, { passive: true })
  window.addEventListener('resize', resize, { passive: true })
  document.addEventListener('visibilitychange', handleVisibilityChange)

  startAnimation()
})

onUnmounted(() => {
  stopAnimation()
  if (themeObserver) {
    themeObserver.disconnect()
    themeObserver = null
  }
  window.removeEventListener('mousemove', handleMouseMove)
  window.removeEventListener('mouseleave', handleMouseLeave)
  window.removeEventListener('touchmove', handleTouchMove)
  window.removeEventListener('touchend', handleTouchEnd)
  window.removeEventListener('click', handleClick)
  window.removeEventListener('resize', resize)
  document.removeEventListener('visibilitychange', handleVisibilityChange)
})
</script>

<template>
  <div ref="containerRef" class="absolute inset-0 w-full h-full pointer-events-none z-0 overflow-hidden select-none bg-transparent">
    <canvas ref="canvasRef" class="w-full h-full block"></canvas>
  </div>
</template>
