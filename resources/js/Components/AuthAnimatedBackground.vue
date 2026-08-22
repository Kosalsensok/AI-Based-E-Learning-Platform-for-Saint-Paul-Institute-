<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

let animationFrameId: number | null = null
let isAnimating = false
let width = 0
let height = 0
let dpr = 1

// Mouse Tracking for Smooth Interactive Flare
const mouse = {
  x: -1000,
  y: -1000,
  targetX: -1000,
  targetY: -1000,
  active: false,
}

// Click Shockwave System
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

// Optimized 2D Simplex Noise for 60FPS Fluid Wave Motion
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
    maxRadius: 360,
    intensity: 1.0,
    speed: 5.0,
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

// Active Living 60FPS Manus AI Motion Engine
const render = (time: number) => {
  if (!isAnimating || !canvasRef.value) return
  animationFrameId = requestAnimationFrame(render)

  const ctx = canvasRef.value.getContext('2d', { alpha: true })
  if (!ctx) return

  ctx.save()
  ctx.scale(dpr, dpr)
  ctx.clearRect(0, 0, width, height)

  const timeSec = time * 0.001

  // 1. Render Ambient Living Nebula Glowing Clouds in Dark Mode
  if (isDark) {
    // Left Nebula Flare: Cyber Emerald / Teal
    const leftX = width * 0.12 + Math.sin(timeSec * 0.45) * 90
    const leftY = height * 0.65 + Math.cos(timeSec * 0.35) * 70
    const leftGrad = ctx.createRadialGradient(leftX, leftY, 0, leftX, leftY, Math.max(width, height) * 0.42)
    leftGrad.addColorStop(0, 'rgba(16, 185, 129, 0.13)')
    leftGrad.addColorStop(0.5, 'rgba(6, 182, 212, 0.06)')
    leftGrad.addColorStop(1, 'rgba(16, 185, 129, 0)')
    ctx.fillStyle = leftGrad
    ctx.fillRect(0, 0, width, height)

    // Right Nebula Flare: Warm Amber / Sunset Gold
    const rightX = width * 0.88 + Math.cos(timeSec * 0.4) * 90
    const rightY = height * 0.30 + Math.sin(timeSec * 0.45) * 70
    const rightGrad = ctx.createRadialGradient(rightX, rightY, 0, rightX, rightY, Math.max(width, height) * 0.42)
    rightGrad.addColorStop(0, 'rgba(245, 158, 11, 0.12)')
    rightGrad.addColorStop(0.5, 'rgba(244, 63, 94, 0.05)')
    rightGrad.addColorStop(1, 'rgba(245, 158, 11, 0)')
    ctx.fillStyle = rightGrad
    ctx.fillRect(0, 0, width, height)

    // Top Center Indigo Pulse
    const topX = width * 0.5 + Math.sin(timeSec * 0.3) * 60
    const topY = height * 0.1
    const topGrad = ctx.createRadialGradient(topX, topY, 0, topX, topY, Math.max(width, height) * 0.35)
    topGrad.addColorStop(0, 'rgba(99, 102, 241, 0.09)')
    topGrad.addColorStop(1, 'rgba(99, 102, 241, 0)')
    ctx.fillStyle = topGrad
    ctx.fillRect(0, 0, width, height)
  }

  // Smooth mouse lerping
  mouse.x += (mouse.targetX - mouse.x) * 0.1
  mouse.y += (mouse.targetY - mouse.y) * 0.1

  // Update Shockwaves
  for (let i = shockwaves.length - 1; i >= 0; i--) {
    const sw = shockwaves[i]
    sw.radius += sw.speed
    sw.intensity *= 0.965
    if (sw.radius > sw.maxRadius || sw.intensity < 0.02) {
      shockwaves.splice(i, 1)
    }
  }

  // Manus Dot-Matrix Grid Configuration (24px Spacing)
  const spacing = 24
  const cols = Math.ceil(width / spacing) + 1
  const rows = Math.ceil(height / spacing) + 1
  const offsetX = (width % spacing) / 2
  const offsetY = (height % spacing) / 2

  // Visible, organic flowing wave speed
  const flowTime1 = timeSec * 0.22
  const flowTime2 = timeSec * 0.35

  const mouseRadius = 240
  const mouseRadiusSq = mouseRadius * mouseRadius

  // 2. Render Every Dot with Continuous Fluid Waves
  for (let r = 0; r < rows; r++) {
    const origY = r * spacing + offsetY
    const normY = origY / height

    for (let c = 0; c < cols; c++) {
      const origX = c * spacing + offsetX
      const normX = origX / width

      // Dual-Frequency Simplex Noise for Continuous Waves
      const nVal1 = noise2D(origX * 0.0028 + flowTime1, origY * 0.0028 - flowTime1 * 0.5)
      const nVal2 = noise2D(origX * 0.0055 - flowTime2 * 0.6, origY * 0.0055 + flowTime2 * 0.7)
      const combinedNoise = (nVal1 * 0.65 + nVal2 * 0.35 + 1) * 0.5 // 0.0 -> 1.0

      // Dynamic Particle Brightness & Radius (Visibly Active Shimmer)
      let alpha = isDark 
        ? 0.12 + Math.pow(combinedNoise, 2.0) * 0.65 
        : 0.09 + Math.pow(combinedNoise, 1.8) * 0.45

      let radius = isDark 
        ? 0.9 + combinedNoise * 0.95 
        : 0.85 + combinedNoise * 0.65

      let currentX = origX
      let currentY = origY

      // Cursor Proximity Force & Radiant Glow
      if (mouse.active) {
        const dx = origX - mouse.x
        const dy = origY - mouse.y
        const distSq = dx * dx + dy * dy

        if (distSq < mouseRadiusSq && distSq > 0) {
          const dist = Math.sqrt(distSq)
          const factor = Math.pow(1 - dist / mouseRadius, 2)
          
          alpha += factor * (isDark ? 0.75 : 0.5)
          radius += factor * 1.4

          const push = factor * 3.5
          currentX += (dx / dist) * push
          currentY += (dy / dist) * push
        }
      }

      // Shockwave Wavefront Interaction
      for (const sw of shockwaves) {
        const swDx = origX - sw.x
        const swDy = origY - sw.y
        const swDist = Math.sqrt(swDx * swDx + swDy * swDy)
        const waveDiff = Math.abs(swDist - sw.radius)

        if (waveDiff < 45) {
          const waveFactor = (1 - waveDiff / 45) * sw.intensity
          alpha += waveFactor * 0.85
          radius += waveFactor * 1.6
          if (swDist > 0) {
            currentX += (swDx / swDist) * (waveFactor * 6.0)
            currentY += (swDy / swDist) * (waveFactor * 6.0)
          }
        }
      }

      alpha = Math.min(Math.max(alpha, 0.05), 0.95)

      // Manus Multi-Zone Organic Color Mapping
      if (isDark) {
        const colorBias = normX + (combinedNoise - 0.5) * 0.4

        if (alpha > 0.45 && mouse.active && Math.hypot(origX - mouse.x, origY - mouse.y) < 75) {
          // Pure Crisp Diamond White Spotlight
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
        } else if (colorBias < 0.35) {
          // Glowing Cyber Emerald / Mint / Neon Aqua (Left Wing)
          const greenRatio = Math.sin(timeSec * 0.7 + origY * 0.01) * 0.5 + 0.5
          const rCol = Math.round(16 + greenRatio * 20)
          const gCol = Math.round(185 + greenRatio * 45)
          const bCol = Math.round(160 + (1 - greenRatio) * 60)
          ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
        } else if (colorBias > 0.65) {
          // Glowing Warm Amber / Gold / Sunset Coral (Right Wing)
          const warmRatio = Math.cos(timeSec * 0.65 + origX * 0.01) * 0.5 + 0.5
          const rCol = Math.round(245 + warmRatio * 10)
          const gCol = Math.round(135 + warmRatio * 45)
          const bCol = Math.round(20 + warmRatio * 75)
          ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
        } else {
          // Center: Crystal Ice Blue & Soft Pearl
          ctx.fillStyle = `rgba(225, 235, 255, ${alpha})`
        }
      } else {
        // Light Mode: High-precision Slate & Accent Colors
        const colorBias = normX + (combinedNoise - 0.5) * 0.35
        if (colorBias < 0.35) {
          ctx.fillStyle = `rgba(13, 148, 136, ${Math.min(alpha * 1.1, 0.75)})`
        } else if (colorBias > 0.65) {
          ctx.fillStyle = `rgba(217, 119, 6, ${Math.min(alpha * 1.1, 0.75)})`
        } else {
          ctx.fillStyle = `rgba(71, 85, 105, ${Math.min(alpha * 1.1, 0.85)})`
        }
      }

      // Draw Circular Dot
      ctx.beginPath()
      ctx.arc(currentX, currentY, radius, 0, Math.PI * 2)
      ctx.fill()
    }
  }

  // 3. Ambient Edge Vignette
  if (isDark) {
    const vignette = ctx.createRadialGradient(
      width / 2,
      height / 2,
      Math.min(width, height) * 0.28,
      width / 2,
      height / 2,
      Math.max(width, height) * 0.78
    )
    vignette.addColorStop(0, 'rgba(7, 7, 9, 0)')
    vignette.addColorStop(0.65, 'rgba(7, 7, 9, 0.35)')
    vignette.addColorStop(1, 'rgba(7, 7, 9, 0.94)')
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
