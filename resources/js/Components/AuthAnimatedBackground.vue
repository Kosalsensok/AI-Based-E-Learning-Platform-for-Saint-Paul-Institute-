<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

let animationFrameId: number | null = null
let isAnimating = false
let width = 0
let height = 0
let dpr = 1

// Mouse Tracking for Interactive Illumination
const mouse = {
  x: -1000,
  y: -1000,
  targetX: -1000,
  targetY: -1000,
  active: false,
}

// Click Ripple Waves
interface Ripple {
  x: number
  y: number
  radius: number
  maxRadius: number
  opacity: number
}
const ripples: Ripple[] = []

// Dark Mode State Observer
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
  ripples.push({
    x: e.clientX - rect.left,
    y: e.clientY - rect.top,
    radius: 0,
    maxRadius: 280,
    opacity: 0.8,
  })
}

// Resize & High-DPI Canvas Buffer
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

// Animation Loop (60 FPS Interactive Dot Matrix Engine)
const render = (time: number) => {
  if (!isAnimating || !canvasRef.value) return
  animationFrameId = requestAnimationFrame(render)

  const ctx = canvasRef.value.getContext('2d', { alpha: true })
  if (!ctx) return

  ctx.save()
  ctx.scale(dpr, dpr)
  ctx.clearRect(0, 0, width, height)

  // Smooth mouse lerp
  if (mouse.active) {
    mouse.x += (mouse.targetX - mouse.x) * 0.12
    mouse.y += (mouse.targetY - mouse.y) * 0.12
  } else {
    mouse.x += (mouse.targetX - mouse.x) * 0.05
    mouse.y += (mouse.targetY - mouse.y) * 0.05
  }

  // 1. Ambient Moving Flares (Manus Signature Lighting)
  const t = time * 0.0008
  const amberX = width * 0.85 + Math.sin(t * 0.7) * 80
  const amberY = height * 0.15 + Math.cos(t * 0.6) * 60
  const tealX = width * 0.15 + Math.cos(t * 0.8) * 80
  const tealY = height * 0.85 + Math.sin(t * 0.5) * 60
  const violetX = width * 0.2 + Math.sin(t * 0.5) * 60
  const violetY = height * 0.35 + Math.cos(t * 0.7) * 50

  // Draw Subtle Ambient Glows
  if (isDark) {
    // Top-Right Warm Amber / Orange Flare
    const gradAmber = ctx.createRadialGradient(amberX, amberY, 0, amberX, amberY, 480)
    gradAmber.addColorStop(0, 'rgba(245, 158, 11, 0.13)')
    gradAmber.addColorStop(0.5, 'rgba(234, 88, 12, 0.06)')
    gradAmber.addColorStop(1, 'rgba(0, 0, 0, 0)')
    ctx.fillStyle = gradAmber
    ctx.fillRect(0, 0, width, height)

    // Bottom-Left Cyber Emerald / Teal Flare
    const gradTeal = ctx.createRadialGradient(tealX, tealY, 0, tealX, tealY, 520)
    gradTeal.addColorStop(0, 'rgba(20, 184, 166, 0.11)')
    gradTeal.addColorStop(0.5, 'rgba(16, 185, 129, 0.05)')
    gradTeal.addColorStop(1, 'rgba(0, 0, 0, 0)')
    ctx.fillStyle = gradTeal
    ctx.fillRect(0, 0, width, height)

    // Deep Indigo / Violet center flare
    const gradViolet = ctx.createRadialGradient(violetX, violetY, 0, violetX, violetY, 400)
    gradViolet.addColorStop(0, 'rgba(99, 102, 241, 0.10)')
    gradViolet.addColorStop(1, 'rgba(0, 0, 0, 0)')
    ctx.fillStyle = gradViolet
    ctx.fillRect(0, 0, width, height)
  } else {
    // Light Mode Soft Blue / Indigo Glows
    const gradLight = ctx.createRadialGradient(width * 0.75, height * 0.25, 0, width * 0.75, height * 0.25, 500)
    gradLight.addColorStop(0, 'rgba(59, 130, 246, 0.08)')
    gradLight.addColorStop(1, 'rgba(255, 255, 255, 0)')
    ctx.fillStyle = gradLight
    ctx.fillRect(0, 0, width, height)
  }

  // 2. Update Ripple Waves
  for (let i = ripples.length - 1; i >= 0; i--) {
    const r = ripples[i]
    r.radius += 4.5
    r.opacity *= 0.96
    if (r.radius > r.maxRadius || r.opacity < 0.02) {
      ripples.splice(i, 1)
    }
  }

  // 3. Grid Settings
  const spacing = 26
  const cols = Math.ceil(width / spacing) + 1
  const rows = Math.ceil(height / spacing) + 1
  const offsetX = (width % spacing) / 2
  const offsetY = (height % spacing) / 2

  const mouseRadius = 220
  const mouseRadiusSq = mouseRadius * mouseRadius

  // 4. Render Interactive Dot Matrix
  for (let r = 0; r < rows; r++) {
    const y = r * spacing + offsetY
    for (let c = 0; c < cols; c++) {
      const x = c * spacing + offsetX

      // Organic subtle shimmer wave
      const wave = Math.sin(t * 1.5 + x * 0.012 + y * 0.012) * 0.03
      let alpha = isDark ? 0.16 + wave : 0.12 + wave
      let radius = isDark ? 1.05 : 1.0

      // Interactive Cursor Proximity Boost
      const dx = x - mouse.x
      const dy = y - mouse.y
      const distSq = dx * dx + dy * dy

      if (distSq < mouseRadiusSq) {
        const dist = Math.sqrt(distSq)
        const factor = Math.pow(1 - dist / mouseRadius, 2)
        alpha += factor * (isDark ? 0.65 : 0.45)
        radius += factor * 1.4
      }

      // Ripple Wave Interaction
      for (const rip of ripples) {
        const rdx = x - rip.x
        const rdy = y - rip.y
        const rDist = Math.sqrt(rdx * rdx + rdy * rdy)
        const diff = Math.abs(rDist - rip.radius)
        if (diff < 35) {
          const ripFactor = (1 - diff / 35) * rip.opacity
          alpha += ripFactor * 0.6
          radius += ripFactor * 1.2
        }
      }

      // Clamp Alpha
      alpha = Math.min(Math.max(alpha, 0.04), 0.95)

      // Dynamic Color Shading based on Screen Position (Manus Multi-Tone Dot Grid)
      if (isDark) {
        // Color influence calculation
        const amberWeight = Math.max(0, 1 - Math.hypot(x - amberX, y - amberY) / 550)
        const tealWeight = Math.max(0, 1 - Math.hypot(x - tealX, y - tealY) / 550)
        const mouseWeight = distSq < mouseRadiusSq ? Math.pow(1 - Math.sqrt(distSq) / mouseRadius, 2) : 0

        if (mouseWeight > 0.3) {
          // Bright Diamond White/Cyan when near cursor
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
        } else if (amberWeight > 0.25) {
          // Warm Amber Tone
          ctx.fillStyle = `rgba(251, 191, 36, ${alpha})`
        } else if (tealWeight > 0.25) {
          // Cyber Emerald Tone
          ctx.fillStyle = `rgba(45, 212, 191, ${alpha})`
        } else {
          // Pure Crisp Slate/White Tone
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
        }
      } else {
        // Light Mode: Slate / Indigo Tone
        ctx.fillStyle = `rgba(51, 65, 85, ${alpha})`
      }

      // Draw Crisp Round Dot
      ctx.beginPath()
      ctx.arc(x, y, radius, 0, Math.PI * 2)
      ctx.fill()
    }
  }

  // 5. Soft Vignette Edge Falloff (Dark Mode Luxury Edge Shadow)
  if (isDark) {
    const vignette = ctx.createRadialGradient(
      width / 2,
      height / 2,
      Math.min(width, height) * 0.3,
      width / 2,
      height / 2,
      Math.max(width, height) * 0.75
    )
    vignette.addColorStop(0, 'rgba(7, 7, 9, 0)')
    vignette.addColorStop(0.7, 'rgba(7, 7, 9, 0.4)')
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
  updateTheme()
  resize()

  // Track Theme Changes (Dark/Light Switcher)
  themeObserver = new MutationObserver(() => {
    updateTheme()
  })
  themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })

  // Listeners for Interactive Motion & Canvas Resize
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
  <div ref="containerRef" class="absolute inset-0 w-full h-full pointer-events-none z-0 overflow-hidden select-none">
    <canvas ref="canvasRef" class="w-full h-full block"></canvas>
  </div>
</template>
