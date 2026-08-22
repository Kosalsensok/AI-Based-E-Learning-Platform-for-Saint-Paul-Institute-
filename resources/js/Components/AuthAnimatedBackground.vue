<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

let animationFrameId: number | null = null
let isAnimating = false
let width = 0
let height = 0
let dpr = 1

// Mouse Tracking for Interactive Spotlight
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
    maxRadius: 380,
    intensity: 1.0,
    speed: 5.5,
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

// 60FPS Manus Liquid Shadow & Floating Particle Engine
const render = (time: number) => {
  if (!isAnimating || !canvasRef.value) return
  animationFrameId = requestAnimationFrame(render)

  const ctx = canvasRef.value.getContext('2d', { alpha: true })
  if (!ctx) return

  ctx.save()
  ctx.scale(dpr, dpr)
  ctx.clearRect(0, 0, width, height)

  const t = time * 0.001

  // 1. Dynamic Liquid Shadow Blobs (Morphing Organic Light-Shadow Clouds)
  // Blob 1: Cyber Emerald / Mint (Bottom-Left sweeping upward)
  const b1x = width * 0.18 + Math.sin(t * 0.5) * width * 0.12
  const b1y = height * 0.68 + Math.cos(t * 0.4) * height * 0.14
  const b1r = Math.max(width, height) * 0.42 + Math.sin(t * 0.6) * 40

  // Blob 2: Warm Amber / Sunset Gold (Top-Right sweeping downward)
  const b2x = width * 0.82 + Math.cos(t * 0.45) * width * 0.12
  const b2y = height * 0.32 + Math.sin(t * 0.5) * height * 0.14
  const b2r = Math.max(width, height) * 0.42 + Math.cos(t * 0.55) * 40

  // Blob 3: Neon Aqua / Electric Cyan (Top-Left floating across)
  const b3x = width * 0.28 + Math.cos(t * 0.35) * width * 0.1
  const b3y = height * 0.22 + Math.sin(t * 0.45) * height * 0.1
  const b3r = Math.max(width, height) * 0.32

  // Blob 4: Sunset Coral / Magenta (Bottom-Right floating)
  const b4x = width * 0.72 + Math.sin(t * 0.4) * width * 0.1
  const b4y = height * 0.78 + Math.cos(t * 0.5) * height * 0.1
  const b4r = Math.max(width, height) * 0.32

  // Blob 5: Indigo Core Nebula (Center pulse)
  const b5x = width * 0.5 + Math.sin(t * 0.25) * 60
  const b5y = height * 0.45 + Math.cos(t * 0.3) * 50
  const b5r = Math.max(width, height) * 0.36

  if (isDark) {
    // Render Soft Ambient Liquid Shadows
    const g1 = ctx.createRadialGradient(b1x, b1y, 0, b1x, b1y, b1r)
    g1.addColorStop(0, 'rgba(16, 185, 129, 0.14)')
    g1.addColorStop(0.5, 'rgba(6, 182, 212, 0.06)')
    g1.addColorStop(1, 'rgba(16, 185, 129, 0)')
    ctx.fillStyle = g1
    ctx.fillRect(0, 0, width, height)

    const g2 = ctx.createRadialGradient(b2x, b2y, 0, b2x, b2y, b2r)
    g2.addColorStop(0, 'rgba(245, 158, 11, 0.13)')
    g2.addColorStop(0.5, 'rgba(244, 63, 94, 0.05)')
    g2.addColorStop(1, 'rgba(245, 158, 11, 0)')
    ctx.fillStyle = g2
    ctx.fillRect(0, 0, width, height)

    const g5 = ctx.createRadialGradient(b5x, b5y, 0, b5x, b5y, b5r)
    g5.addColorStop(0, 'rgba(99, 102, 241, 0.09)')
    g5.addColorStop(1, 'rgba(99, 102, 241, 0)')
    ctx.fillStyle = g5
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

  // Manus Dot Grid Configuration (24px Spacing)
  const spacing = 24
  const cols = Math.ceil(width / spacing) + 1
  const rows = Math.ceil(height / spacing) + 1
  const offsetX = (width % spacing) / 2
  const offsetY = (height % spacing) / 2

  const mouseRadius = 240
  const mouseRadiusSq = mouseRadius * mouseRadius

  // 2. Render Living Floating Particle Matrix
  for (let r = 0; r < rows; r++) {
    const baseY = r * spacing + offsetY

    for (let c = 0; c < cols; c++) {
      const baseX = c * spacing + offsetX

      // Organic Floating Displacement (Physical Wave Motion in 2D Space)
      const floatAngle = t * 0.85 + c * 0.28 + r * 0.32
      const floatX = Math.sin(floatAngle) * 2.6
      const floatY = Math.cos(floatAngle * 0.9) * 2.6

      let currentX = baseX + floatX
      let currentY = baseY + floatY

      // Distance to Liquid Shadow Blobs
      const d1 = Math.hypot(currentX - b1x, currentY - b1y) / b1r
      const d2 = Math.hypot(currentX - b2x, currentY - b2y) / b2r
      const d3 = Math.hypot(currentX - b3x, currentY - b3y) / b3r
      const d4 = Math.hypot(currentX - b4x, currentY - b4y) / b4r
      const d5 = Math.hypot(currentX - b5x, currentY - b5y) / b5r

      // Blob Influence Factors (0.0 to 1.0)
      const w1 = Math.max(0, 1 - d1)
      const w2 = Math.max(0, 1 - d2)
      const w3 = Math.max(0, 1 - d3)
      const w4 = Math.max(0, 1 - d4)
      const w5 = Math.max(0, 1 - d5)

      // Total Shadow Glow Intensity
      const totalGlow = Math.min(1.0, w1 * 1.3 + w2 * 1.3 + w3 * 0.9 + w4 * 0.9 + w5 * 0.8)

      // Dynamic Dot Brightness & Size
      let alpha = isDark ? 0.10 + totalGlow * 0.72 : 0.08 + totalGlow * 0.5
      let radius = isDark ? 0.85 + totalGlow * 1.1 : 0.8 + totalGlow * 0.8

      // Cursor Proximity Interaction
      if (mouse.active) {
        const dx = currentX - mouse.x
        const dy = currentY - mouse.y
        const distSq = dx * dx + dy * dy

        if (distSq < mouseRadiusSq && distSq > 0) {
          const dist = Math.sqrt(distSq)
          const factor = Math.pow(1 - dist / mouseRadius, 2)
          
          alpha += factor * (isDark ? 0.8 : 0.5)
          radius += factor * 1.5

          const push = factor * 4.0
          currentX += (dx / dist) * push
          currentY += (dy / dist) * push
        }
      }

      // Shockwave Ripples
      for (const sw of shockwaves) {
        const swDx = currentX - sw.x
        const swDy = currentY - sw.y
        const swDist = Math.hypot(swDx, swDy)
        const waveDiff = Math.abs(swDist - sw.radius)

        if (waveDiff < 40) {
          const waveFactor = (1 - waveDiff / 40) * sw.intensity
          alpha += waveFactor * 0.85
          radius += waveFactor * 1.6
          if (swDist > 0) {
            currentX += (swDx / swDist) * (waveFactor * 6.0)
            currentY += (swDy / swDist) * (waveFactor * 6.0)
          }
        }
      }

      alpha = Math.min(Math.max(alpha, 0.04), 0.95)

      // 3. Dynamic Organic Color Blending (Morphing Colors from Shadow Blobs)
      if (isDark) {
        if (alpha > 0.45 && mouse.active && Math.hypot(currentX - mouse.x, currentY - mouse.y) < 70) {
          // Diamond White Core under Cursor
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
        } else {
          // Calculate blended color from blobs
          const emeraldScore = w1 * 1.4 + w3 * 0.8
          const amberScore = w2 * 1.4 + w4 * 0.8

          if (emeraldScore > amberScore && emeraldScore > 0.15) {
            // Cyber Emerald / Mint / Neon Teal
            const rCol = Math.round(16 + (w3 / (emeraldScore + 0.01)) * 20)
            const gCol = Math.round(185 + (w1 / (emeraldScore + 0.01)) * 45)
            const bCol = Math.round(160 + (w3 / (emeraldScore + 0.01)) * 60)
            ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
          } else if (amberScore > 0.15) {
            // Warm Amber / Sunset Gold / Coral
            const rCol = Math.round(245 + (w4 / (amberScore + 0.01)) * 10)
            const gCol = Math.round(140 + (w2 / (amberScore + 0.01)) * 40)
            const bCol = Math.round(25 + (w4 / (amberScore + 0.01)) * 70)
            ctx.fillStyle = `rgba(${rCol}, ${gCol}, ${bCol}, ${alpha})`
          } else {
            // Crystal White / Soft Blue Core
            ctx.fillStyle = `rgba(225, 235, 255, ${alpha})`
          }
        }
      } else {
        // Light Mode: Precision Slate & Teal/Amber Accents
        const emeraldScore = w1 + w3
        const amberScore = w2 + w4
        if (emeraldScore > amberScore && emeraldScore > 0.2) {
          ctx.fillStyle = `rgba(13, 148, 136, ${Math.min(alpha * 1.1, 0.75)})`
        } else if (amberScore > 0.2) {
          ctx.fillStyle = `rgba(217, 119, 6, ${Math.min(alpha * 1.1, 0.75)})`
        } else {
          ctx.fillStyle = `rgba(71, 85, 105, ${Math.min(alpha * 1.1, 0.85)})`
        }
      }

      // Draw Smooth Circular Particle
      ctx.beginPath()
      ctx.arc(currentX, currentY, radius, 0, Math.PI * 2)
      ctx.fill()
    }
  }

  // 4. Ambient Vignette Edge Depth
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
