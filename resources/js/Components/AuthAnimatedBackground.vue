<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import * as THREE from 'three'

const containerRef = ref<HTMLDivElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)
const isMobile = ref(false)
const isCanvasReady = ref(false)

let animationFrameId: number | null = null
let isAnimating = false
let startTime = 0
let scene: THREE.Scene | null = null
let camera: THREE.PerspectiveCamera | null = null
let renderer: THREE.WebGLRenderer | null = null
let observer: IntersectionObserver | null = null

// Three.js object references for animation loop
let mainGroup: THREE.Group | null = null
let innerSphere: THREE.Mesh | null = null
let outerWire: THREE.Mesh | null = null
let ringMesh: THREE.Mesh | null = null
let aiCoreGroup: THREE.Group | null = null
let outerTorus: THREE.Mesh | null = null
let innerNode: THREE.Mesh | null = null
let techMatrixGroup: THREE.Group | null = null
let glassCube1: THREE.Mesh | null = null
let glassCube2: THREE.Mesh | null = null
let particleCloud: THREE.Points | null = null

// Subtle mouse tracking for gentle 3D parallax
const mouse = { x: 0, y: 0, targetX: 0, targetY: 0 }

const handleMouseMove = (e: MouseEvent) => {
  const halfWidth = window.innerWidth / 2
  const halfHeight = window.innerHeight / 2
  mouse.targetX = (e.clientX - halfWidth) / halfWidth
  mouse.targetY = (e.clientY - halfHeight) / halfHeight
}

const animate = () => {
  if (!isAnimating) return
  animationFrameId = requestAnimationFrame(animate)
  const elapsedTime = (performance.now() - startTime) / 1000

  // Smooth mouse lerp
  mouse.x += (mouse.targetX - mouse.x) * 0.03
  mouse.y += (mouse.targetY - mouse.y) * 0.03

  if (mainGroup) {
    mainGroup.rotation.y = mouse.x * 0.08
    mainGroup.rotation.x = -mouse.y * 0.06
  }

  // Serene Floating Animations
  if (innerSphere) innerSphere.rotation.y = elapsedTime * 0.25
  if (outerWire) {
    outerWire.rotation.x = elapsedTime * 0.15
    outerWire.rotation.y = elapsedTime * 0.2
  }
  if (ringMesh) ringMesh.rotation.z = elapsedTime * 0.18
  if (aiCoreGroup) aiCoreGroup.position.y = 4.8 + Math.sin(elapsedTime * 1.0) * 0.15

  if (outerTorus) outerTorus.rotation.z = elapsedTime * 0.12
  if (innerNode) innerNode.rotation.y = elapsedTime * 0.3
  if (techMatrixGroup) techMatrixGroup.position.y = -4.8 + Math.sin(elapsedTime * 1.2 + 1) * 0.12

  if (glassCube1) {
    glassCube1.rotation.x += 0.003
    glassCube1.rotation.y += 0.004
  }
  if (glassCube2) {
    glassCube2.rotation.x -= 0.003
  }

  if (particleCloud) particleCloud.rotation.y = elapsedTime * 0.012

  if (renderer && scene && camera) {
    renderer.render(scene, camera)
  }
}

const startAnimation = () => {
  if (isAnimating || document.hidden || isMobile.value || !renderer) return
  isAnimating = true
  if (!startTime) startTime = performance.now()
  animate()
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

const checkMobile = () => {
  if (typeof window !== 'undefined') {
    isMobile.value = window.innerWidth < 768
  }
}

const initThree = () => {
  try {
    checkMobile()
    // Skip heavy 3D rendering on mobile devices, use ultra-light CSS gradient instead
    if (isMobile.value) return
    if (!canvasRef.value || !containerRef.value) return

    const width = containerRef.value.clientWidth || window.innerWidth
    const height = containerRef.value.clientHeight || window.innerHeight

    if (width === 0 || height === 0) return

    // Scene & Camera setup
    scene = new THREE.Scene()
    camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000)
    camera.position.set(0, 0, 22)

    // WebGL Renderer with Anti-aliasing & Soft Tone Mapping
    renderer = new THREE.WebGLRenderer({
      canvas: canvasRef.value,
      alpha: true,
      antialias: true,
      powerPreference: 'high-performance',
    })
    renderer.setSize(width, height)
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2))

    // Soft Lighting Setup (Gentle & Balanced)
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.75)
    scene.add(ambientLight)

    const dirLight = new THREE.DirectionalLight(0x60a5fa, 1.0)
    dirLight.position.set(10, 15, 15)
    scene.add(dirLight)

    const cyanPoint = new THREE.PointLight(0x38bdf8, 1.4, 35)
    cyanPoint.position.set(-12, 8, 8)
    scene.add(cyanPoint)

    const purplePoint = new THREE.PointLight(0x8b5cf6, 1.4, 35)
    purplePoint.position.set(12, -8, 8)
    scene.add(purplePoint)

    // Main 3D Group
    mainGroup = new THREE.Group()
    scene.add(mainGroup)

    // ==========================================
    // 1. SUBTLE HOLOGRAPHIC AI CORE (TOP-LEFT)
    // ==========================================
    aiCoreGroup = new THREE.Group()
    aiCoreGroup.position.set(-8.5, 4.8, -3)

    // Inner Soft Glowing Core
    const innerSphereGeo = new THREE.SphereGeometry(1.2, 24, 24)
    const innerSphereMat = new THREE.MeshStandardMaterial({
      color: 0x38bdf8,
      emissive: 0x0284c7,
      emissiveIntensity: 0.35,
      roughness: 0.4,
      metalness: 0.5,
      transparent: true,
      opacity: 0.65,
    })
    innerSphere = new THREE.Mesh(innerSphereGeo, innerSphereMat)
    aiCoreGroup.add(innerSphere)

    // Outer Delicate Wireframe Icosahedron
    const outerWireGeo = new THREE.IcosahedronGeometry(1.9, 1)
    const outerWireMat = new THREE.MeshBasicMaterial({
      color: 0x60a5fa,
      wireframe: true,
      transparent: true,
      opacity: 0.28,
    })
    outerWire = new THREE.Mesh(outerWireGeo, outerWireMat)
    aiCoreGroup.add(outerWire)

    // Subtle Orbital Ring
    const ringGeo = new THREE.TorusGeometry(2.4, 0.025, 16, 64)
    const ringMat = new THREE.MeshBasicMaterial({
      color: 0x38bdf8,
      transparent: true,
      opacity: 0.3,
    })
    ringMesh = new THREE.Mesh(ringGeo, ringMat)
    ringMesh.rotation.x = Math.PI / 3
    aiCoreGroup.add(ringMesh)

    mainGroup.add(aiCoreGroup)

    // ==========================================
    // 2. SUBTLE TECH RING MATRIX (BOTTOM-RIGHT)
    // ==========================================
    techMatrixGroup = new THREE.Group()
    techMatrixGroup.position.set(8.5, -4.8, -2)

    const outerTorusGeo = new THREE.TorusGeometry(2.6, 0.03, 16, 64)
    const outerTorusMat = new THREE.MeshBasicMaterial({
      color: 0x8b5cf6,
      wireframe: true,
      transparent: true,
      opacity: 0.25,
    })
    outerTorus = new THREE.Mesh(outerTorusGeo, outerTorusMat)
    outerTorus.rotation.x = Math.PI / 2.5
    techMatrixGroup.add(outerTorus)

    const innerNodeGeo = new THREE.OctahedronGeometry(0.8, 0)
    const innerNodeMat = new THREE.MeshStandardMaterial({
      color: 0x8b5cf6,
      roughness: 0.4,
      transparent: true,
      opacity: 0.5,
    })
    innerNode = new THREE.Mesh(innerNodeGeo, innerNodeMat)
    techMatrixGroup.add(innerNode)

    mainGroup.add(techMatrixGroup)

    // ==========================================
    // 3. ELEGANT GLASSMOPHISM ACCENTS
    // ==========================================
    const glassGroup = new THREE.Group()

    const cubeGeo = new THREE.BoxGeometry(1.4, 1.4, 1.4)
    const glassMat = new THREE.MeshPhysicalMaterial({
      color: 0x60a5fa,
      transparent: true,
      opacity: 0.22,
      roughness: 0.2,
      metalness: 0.1,
      transmission: 0.6,
      ior: 1.4,
    })

    // Glass Cube 1 (Upper Right Accent)
    glassCube1 = new THREE.Mesh(cubeGeo, glassMat)
    glassCube1.position.set(9.0, 5.5, -4)
    glassCube1.rotation.set(0.4, 0.6, 0.2)
    glassGroup.add(glassCube1)

    // Glass Cube 2 (Lower Left Accent)
    glassCube2 = new THREE.Mesh(cubeGeo, glassMat)
    glassCube2.position.set(-9.0, -5.5, -3)
    glassCube2.rotation.set(-0.3, 0.3, 0.5)
    glassGroup.add(glassCube2)

    mainGroup.add(glassGroup)

    // ==========================================
    // 4. SUBTLE DUST PARTICLES (LIGHT & SOFT)
    // ==========================================
    const particleCount = 70
    const particleGeo = new THREE.BufferGeometry()
    const particlePositions = new Float32Array(particleCount * 3)

    for (let i = 0; i < particleCount; i++) {
      particlePositions[i * 3] = (Math.random() - 0.5) * 40
      particlePositions[i * 3 + 1] = (Math.random() - 0.5) * 26
      particlePositions[i * 3 + 2] = (Math.random() - 0.5) * 16
    }

    particleGeo.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3))
    const particleMat = new THREE.PointsMaterial({
      color: 0x38bdf8,
      size: 0.1,
      transparent: true,
      opacity: 0.4,
    })
    particleCloud = new THREE.Points(particleGeo, particleMat)
    mainGroup.add(particleCloud)

    // Start Animation
    startTime = performance.now()
    startAnimation()
    isCanvasReady.value = true

    // IntersectionObserver to pause Three.js when scrolled off screen
    if (typeof IntersectionObserver !== 'undefined' && containerRef.value) {
      observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
          startAnimation()
        } else {
          stopAnimation()
        }
      }, { threshold: 0.05 })
      observer.observe(containerRef.value)
    }
  } catch (err) {
    console.warn('AuthAnimatedBackground 3D canvas skipped gracefully:', err)
  }
}

const handleResize = () => {
  try {
    const wasMobile = isMobile.value
    checkMobile()

    if (isMobile.value) {
      stopAnimation()
      if (renderer) {
        renderer.dispose()
        renderer = null
      }
      return
    }

    if (wasMobile && !isMobile.value && !renderer) {
      initThree()
      return
    }

    if (!containerRef.value || !camera || !renderer) return
    const width = containerRef.value.clientWidth || window.innerWidth
    const height = containerRef.value.clientHeight || window.innerHeight
    if (width === 0 || height === 0) return
    camera.aspect = width / height
    camera.updateProjectionMatrix()
    renderer.setSize(width, height)
  } catch (e) {}
}

onMounted(() => {
  checkMobile()
  initThree()
  window.addEventListener('mousemove', handleMouseMove, { passive: true })
  window.addEventListener('resize', handleResize, { passive: true })
  document.addEventListener('visibilitychange', handleVisibilityChange)
})

onUnmounted(() => {
  stopAnimation()
  window.removeEventListener('mousemove', handleMouseMove)
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (observer) {
    observer.disconnect()
    observer = null
  }
  try {
    if (renderer) {
      renderer.dispose()
      renderer = null
    }
    scene = null
    camera = null
    mainGroup = null
    innerSphere = null
    outerWire = null
    ringMesh = null
    aiCoreGroup = null
    outerTorus = null
    innerNode = null
    techMatrixGroup = null
    glassCube1 = null
    glassCube2 = null
    particleCloud = null
  } catch (e) {}
})
</script>

<template>
  <div ref="containerRef" class="absolute inset-0 w-full h-full pointer-events-none z-0 overflow-hidden select-none bg-gradient-to-br from-blue-950/20 via-slate-950/30 to-indigo-950/20">
    <!-- Desktop 3D Canvas with Smooth 700ms Fade-in to Prevent Popping -->
    <canvas
      v-if="!isMobile"
      ref="canvasRef"
      :class="['w-full h-full block transition-opacity duration-700 ease-out', isCanvasReady ? 'opacity-100' : 'opacity-0']"
    ></canvas>
    <!-- Mobile Lightweight Ambient CSS Gradient Fallback -->
    <div v-else class="w-full h-full absolute inset-0 bg-gradient-to-br from-blue-900/30 via-slate-900/50 to-indigo-950/40"></div>
  </div>
</template>
