import { useToast } from 'primevue/usetoast'

export function useAppToast() {
  const toast = useToast()

  const success = (detail: string, summary: string = 'ជោគជ័យ', life: number = 3500) => {
    try {
      toast.add({
        severity: 'success',
        summary,
        detail,
        life,
      })
    } catch (e) {}
  }

  const error = (detail: string, summary: string = 'បរាជ័យ', life: number = 4500) => {
    try {
      toast.add({
        severity: 'error',
        summary,
        detail,
        life,
      })
    } catch (e) {}
  }

  const info = (detail: string, summary: string = 'ដំណឹង', life: number = 3500) => {
    try {
      toast.add({
        severity: 'info',
        summary,
        detail,
        life,
      })
    } catch (e) {}
  }

  const warn = (detail: string, summary: string = 'ការព្រមាន', life: number = 4000) => {
    try {
      toast.add({
        severity: 'warn',
        summary,
        detail,
        life,
      })
    } catch (e) {}
  }

  return {
    raw: toast,
    success,
    error,
    info,
    warn,
  }
}
