import { ref } from 'vue'

export const isDark = ref(false)

export function initTheme() {
  if (typeof window === 'undefined') return
  const stored = localStorage.getItem('theme')
  if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  } else {
    isDark.value = false
    document.documentElement.classList.remove('dark')
  }
}

export function useTheme() {
  // Ensure theme is properly synced when composable is used
  if (typeof window !== 'undefined' && typeof document !== 'undefined') {
    const hasDark = document.documentElement.classList.contains('dark')
    if (hasDark !== isDark.value) {
      isDark.value = hasDark
    }
  }

  const toggleTheme = () => {
    isDark.value = !isDark.value
    if (isDark.value) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }

  const setTheme = (theme: 'dark' | 'light') => {
    isDark.value = theme === 'dark'
    if (isDark.value) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }

  return {
    isDark,
    toggleTheme,
    setTheme,
  }
}
