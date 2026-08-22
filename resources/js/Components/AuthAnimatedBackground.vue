<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

let animationFrameId: number | null = null
let isAnimating = false
let width = 0
let height = 0
let dpr = 1

// Mouse Tracking
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

// Grid Node Interface
interface MatrixDot {
  col: number
  row: number
  baseX: number
  baseY: number
  isStar: boolean
  twinklePhase: number
  twinkleSpeed: number
  colorType: 'emerald' | 'amber' | 'white' | 'cyan'
}

let dots: MatrixDot[] = []

// Dark Mode State
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
    maxRadius: 400,
    intensity: 1.0,
    speed: 6.0,
  })
}

// Initialize and Rebuild the Matrix Grid
const spacing = 22

const buildGrid = () => {
  dots = []
  const cols = Math.ceil(width / spacing) + 1
  const rows = Math.ceil(height / spacing) + 1
  const offsetX = (width % spacing) / 2
  const offsetY = (height % spacing) / 2

  for (let r = 0; r < rows; r++) {
    const y = r * spacing + offsetY
    for (let c = 0; c < cols; c++) {
      const x = c * spacing + offsetX
      const normX = x / (width || 1)

      // 10% of nodes are distinct bright twinkling stars / nodes
      const isStar = Math.random() < 0.09

      // Color mapping
      let colorType: 'emerald' | 'amber' | 'white' | 'cyan' = 'white'
      if (normX < 0.32) {
        colorType = Math.random() < 0.6 ? 'emerald' : 'cyan'
      } else if (normX > 0.68) {
        colorType = 'amber'
      }

      dots.push({
        col: c,
        row: r,
        baseX: x,
        baseY: y,
        isStar,
        twinklePhase: Math.random() * Math.PI * 2,
        twinkleSpeed: 0.8 + Math.random() * 1.6,
        colorType,
      })
    }
  }
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

  buildGrid()
}

// 60FPS Manus Twinkling & Traveling Matrix Wave Engine
const render = (time: number) => {
  if (!isAnimating || !canvasRef.value) return
  animationFrameId = requestAnimationFrame(render)

  const ctx = canvasRef.value.getContext('2d', { alpha: true })
  if (!ctx) return

  ctx.save()
  ctx.scale(dpr, dpr)
  ctx.clearRect(0, 0, width, height)

  const t = time * 0.001

  // Smooth mouse tracking
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

  // 1. Render Subtle Ambient Lighting Flares in Dark Mode
  if (isDark) {
    // Left Emerald Flare
    const g1 = ctx.createRadialGradient(
      width * 0.15 + Math.sin(t * 0.4) * 60,
      height * 0.65 + Math.cos(t * 0.3) * 50,
      0,
      width * 0.15,
      height * 0.65,
      Math.max(width, height) * 0.4
    )
    g1.addColorStop(0, 'rgba(16, 185, 129, 0.12)')
    g1.addColorStop(0.5, 'rgba(6, 182, 212, 0.04)')
    g1.addColorStop(1, 'rgba(16, 185, 129, 0)')
    ctx.fillStyle = g1
    ctx.fillRect(0, 0, width, height)

    // Right Amber Flare
    const g2 = ctx.createRadialGradient(
      width * 0.85 + Math.cos(t * 0.35) * 60,
      height * 0.35 + Math.sin(t * 0.4) * 50,
      0,
      width * 0.85,
      height * 0.35,
      Math.max(width, height) * 0.4
    )
    g2.addColorStop(0, 'rgba(245, 158, 11, 0.11)')
    g2.addColorStop(0.5, 'rgba(244, 63, 94, 0.04)')
    g2.addColorStop(1, 'rgba(245, 158, 11, 0)')
    ctx.fillStyle = g2
    ctx.fillRect(0, 0, width, height)
  }

  const mouseRadius = 220
  const mouseRadiusSq = mouseRadius * mouseRadius
  const centerX = width / 2
  const centerY = height / 2

  // 2. Render Animated Dot Matrix with Traveling Waves & Twinkling Nodes
  for (let i = 0; i < dots.length; i++) {
    const dot = dots[i]

    // Traveling Wave 1 (Diagonal sweeping pulse)
    const wave1 = Math.sin(dot.col * 0.12 + dot.row * 0.08 - t * 1.8)
    
    // Traveling Wave 2 (Radial breathing pulse from center)
    const distCenter = Math.hypot(dot.baseX - centerX, dot.baseY - centerY)
    const wave2 = Math.sin(distCenter * 0.012 - t * 2.2)

    // Combined Wave Factor (0.0 to 1.0)
    const waveFactor = (wave1 * 0.55 + wave2 * 0.45 + 1) * 0.5

    // Base Alpha Calculation
    let alpha = isDark ? 0.08 + Math.pow(waveFactor, 2.0) * 0.35 : 0.06 + Math.pow(waveFactor, 1.8) * 0.25
    let radius = isDark ? 0.85 + waveFactor * 0.55 : 0.8 + waveFactor * 0.4

    // Individual Twinkling Node Pulsing (Sparkling Stars like Manus)
    if (dot.isStar) {
      const twinkle = (Math.sin(t * dot.twinkleSpeed + dot.twinklePhase) + 1) * 0.5
      alpha += Math.pow(twinkle, 2.5) * (isDark ? 0.65 : 0.45)
      radius += twinkle * 1.1
    }

    let currentX = dot.baseX
    let currentY = dot.baseY

    // Cursor Proximity Flare & Illumination
    if (mouse.active) {
      const dx = dot.baseX - mouse.x
      const dy = dot.baseY - mouse.y
      const distSq = dx * dx + dy * dy

      if (distSq < mouseRadiusSq && distSq > 0) {
        const dist = Math.sqrt(distSq)
        const factor = Math.pow(1 - dist / mouseRadius, 2)
        
        alpha += factor * (isDark ? 0.8 : 0.55)
        radius += factor * 1.5

        const push = factor * 3.5
        currentX += (dx / dist) * push
        currentY += (dy / dist) * push
      }
    }

    // Shockwave Interaction
    for (const sw of shockwaves) {
      const swDx = dot.baseX - sw.x
      const swDy = dot.baseY - sw.y
      const swDist = Math.hypot(swDx, swDy)
      const waveDiff = Math.abs(swDist - sw.radius)

      if (waveDiff < 40) {
        const swFactor = (1 - waveDiff / 40) * sw.intensity
        alpha += swFactor * 0.85
        radius += swFactor * 1.6
        if (swDist > 0) {
          currentX += (swDx / swDist) * (swFactor * 6.0)
          currentY += (swDy / swDist) * (swFactor * 6.0)
        }
      }
    }

    alpha = Math.min(Math.max(alpha, 0.04), 0.95)

    // Color Rendering
    if (isDark) {
      if (alpha > 0.5 && mouse.active && Math.hypot(dot.baseX - mouse.x, dot.baseY - mouse.y) < 70) {
        // Pure Diamond White Core under Cursor
        ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`
      } else if (dot.colorType === 'emerald') {
        ctx.fillStyle = `rgba(16, 185, 129, ${alpha})`
      } else if (dot.colorType === 'cyan') {
        ctx.fillStyle = `rgba(6, 182, 212, ${alpha})`
      } else if (dot.colorType === 'amber') {
        ctx.fillStyle = `rgba(245, 158, 11, ${alpha})`
      } else {
        ctx.fillStyle = `rgba(225, 235, 255, ${alpha})`
      }
    } else {
      if (dot.colorType === 'emerald' || dot.colorType === 'cyan') {
        ctx.fillStyle = `rgba(13, 148, 136, ${Math.min(alpha * 1.2, 0.8)})`
      } else if (dot.colorType === 'amber') {
        ctx.fillStyle = `rgba(217, 119, 6, ${Math.min(alpha * 1.2, 0.8)})`
      } else {
        ctx.fillStyle = `rgba(71, 85, 105, ${Math.min(alpha * 1.2, 0.9)})`
      }
    }

    // Draw Smooth Circular Dot
    ctx.beginPath()
    ctx.arc(currentX, currentY, radius, 0, Math.PI * 2)
    ctx.fill()
  }

  // 3. Ambient Vignette Edge Depth
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
