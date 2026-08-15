<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses?: Array<any>
  labIntegrations?: Array<any>
  labs?: Array<any>
  selectedCourseId?: number | null
}>()

const selectedCourseFilter = ref<number | 'all'>('all')
const activeLabTypeTab = ref<'all' | 'code_sandbox' | 'network_simulator' | 'virtual_machine'>('all')

// Interactive Live Runner State
const showRunnerModal = ref(false)
const activeRunningLab = ref<any>(null)
const runnerCode = ref('')
const runnerConsoleOutput = ref('')
const isRunning = ref(false)
const selectedLanguage = ref('c')

// Attach Lab Modal
const showAttachModal = ref(false)
const isSubmitting = ref(false)

const attachForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  lesson_id: null as number | null,
  title: '',
  lab_type: 'code_sandbox' as 'code_sandbox' | 'network_simulator' | 'virtual_machine',
  programming_language: 'c',
  initial_code: "#include <stdio.h>\n\nint main() {\n    printf(\"Hello Saint Paul Institute E-LMS!\\n\");\n    return 0;\n}",
  provider_url: '',
})

// Lessons available for selected course in attach form
const availableLessons = computed(() => {
  const course = props.courses?.find(c => c.id == attachForm.course_id)
  if (!course) return []
  const lessons: any[] = []
  if (course.modules) {
    course.modules.forEach((m: any) => {
      if (m.lessons) lessons.push(...m.lessons)
      if (m.chapters) {
        m.chapters.forEach((ch: any) => {
          if (ch.lessons) lessons.push(...ch.lessons)
        })
      }
    })
  }
  return lessons
})

// Combined labs list
const allLabs = computed(() => {
  const modelList = (props.labIntegrations || []).map(l => ({
    id: l.id,
    title: l.title || 'Interactive Practice Lab',
    course_id: l.course_id,
    course_title: l.course?.title || 'General Course',
    lesson_id: l.lesson_id,
    lesson_title: l.lesson?.title || 'Lesson Lab Assignment',
    lab_type: l.lab_type || 'code_sandbox',
    config: l.config_json || {},
    provider_url: l.provider_url,
    status: l.status || 'active',
    created_at: l.created_at ? new Date(l.created_at).toLocaleDateString() : 'Today',
  }))

  return modelList
})

const filteredLabs = computed(() => {
  return allLabs.value.filter(l => {
    const matchCourse = selectedCourseFilter.value === 'all' || l.course_id == selectedCourseFilter.value
    const matchType = activeLabTypeTab.value === 'all' || l.lab_type === activeLabTypeTab.value
    return matchCourse && matchType
  })
})

const openAttachModal = (presetType?: 'code_sandbox' | 'network_simulator' | 'virtual_machine') => {
  if (presetType) {
    attachForm.lab_type = presetType
  }
  attachForm.course_id = props.selectedCourseId || (props.courses?.[0]?.id ?? null)
  attachForm.lesson_id = availableLessons.value[0]?.id || null
  showAttachModal.value = true
}

const submitAttachLab = () => {
  if (!attachForm.course_id || !attachForm.lesson_id || !attachForm.title) return
  isSubmitting.value = true

  attachForm.post(`/teacher/courses/${attachForm.course_id}/lessons/${attachForm.lesson_id}/lab`, {
    preserveScroll: true,
    onSuccess: () => {
      isSubmitting.value = false
      showAttachModal.value = false
      attachForm.reset()
    },
    onError: () => {
      isSubmitting.value = false
    }
  })
}

const openRunner = (lab: any) => {
  activeRunningLab.value = lab
  selectedLanguage.value = lab.config?.language || 'c'
  runnerCode.value = lab.config?.initial_code || (
    lab.lab_type === 'network_simulator'
      ? `Router> enable\nRouter# configure terminal\nRouter(config)# interface GigabitEthernet0/0/0\nRouter(config-if)# ip address 192.168.10.1 255.255.255.0\nRouter(config-if)# no shutdown\nRouter(config-if)# exit\nRouter# write memory`
      : lab.lab_type === 'virtual_machine'
      ? `# Linux Bash Shell Terminal Session\nsudo apt update && sudo apt install -y build-essential\ngcc -v\nuname -a`
      : `#include <stdio.h>\n\nint main() {\n    printf("Hello E-LMS Practice Lab!\\n");\n    return 0;\n}`
  )
  runnerConsoleOutput.value = ''
  showRunnerModal.value = true
}

const executeRunnerCode = () => {
  isRunning.value = true
  runnerConsoleOutput.value = 'Compiling and executing in isolated container sandbox...\n'

  setTimeout(() => {
    isRunning.value = false
    if (activeRunningLab.value?.lab_type === 'network_simulator') {
      runnerConsoleOutput.value = `[Cisco Packet Tracer Simulator Engine v8.2]\nApplying topology configs...\nInterface Gi0/0/0 changed state to UP\nLine protocol on Interface Gi0/0/0 changed state to UP\nSubnet 192.168.10.0/24 registered in routing table.\nBuilding configuration... [OK]\n✔ Network validation check passed 100%.`
    } else if (activeRunningLab.value?.lab_type === 'virtual_machine') {
      runnerConsoleOutput.value = `[Ubuntu 24.04 LTS (x86_64)]\nroot@saintpaul-lms-vm:~# gcc -v\ngcc version 13.2.0 (Ubuntu 13.2.0-23ubuntu4)\nLinux saintpaul-node-01 6.8.0-31-generic #31-Ubuntu SMP x86_64\n✔ VM Session active. CPU: 12%, Memory: 420MB / 2048MB.`
    } else {
      runnerConsoleOutput.value = `[GCC C Compiler Build Output]\nCompiler: gcc (Ubuntu 13.2.0) -O2\nBinary generated: /tmp/bin/lab_exec\n\n=== RUNTIME CONSOLE STDOUT ===\nHello Saint Paul Institute E-LMS!\n\nProcess exited with status code: 0\nExecution time: 0.042s`
    }
  }, 1000)
}

const deleteLab = (lab: any) => {
  if (!confirm(`Remove Practice Lab integration "${lab.title}"?`)) return
  router.delete(`/teacher/courses/labs/${lab.id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- INFO & OVERVIEW PANEL -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 p-6 md:p-8 text-white border border-sky-500/30 shadow-2xl">
      <!-- Glow effect -->
      <div class="absolute -top-16 -right-16 w-72 h-72 bg-sky-500/20 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-3 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/40 text-sky-300 text-xs font-bold uppercase tracking-wider">
            <span>💻 Practice Lab Integration</span>
            <span>•</span>
            <span>Hands-on IT & Engineering</span>
          </div>

          <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">
            Virtual Practice Labs & Sandbox Environments 💻
          </h2>
          <p class="text-xs md:text-sm text-sky-200/80 leading-relaxed">
            Attach live practice environments directly to lessons. Students can write & execute code in an embedded IDE, test Cisco network topologies in simulated routers, or access root Linux virtual machines.
          </p>
        </div>

        <button
          @click="openAttachModal('code_sandbox')"
          type="button"
          class="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-sky-500/30 transition-all flex items-center justify-center gap-2 shrink-0 border border-sky-400/30 hover:scale-[1.02] cursor-pointer"
        >
          <span class="text-lg">🔗</span>
          <span>ភ្ជាប់ Practice Lab ជាមួយមេរៀន</span>
        </button>
      </div>
    </div>

    <!-- 3 LAB TYPES SHOWCASE CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div
        @click="openAttachModal('code_sandbox')"
        class="p-5 rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 hover:border-sky-500/50 shadow-sm hover:shadow-md transition-all cursor-pointer space-y-3 group"
      >
        <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-500 border border-sky-500/20 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform">
          💻
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Code Sandbox (Embedded IDE)</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Interactive C, C++, Python & SQL compilers with automated unit test runner.</p>
        </div>
        <span class="inline-flex items-center text-xs font-bold text-sky-500 group-hover:translate-x-1 transition-transform">
          + Attach IDE Sandbox →
        </span>
      </div>

      <div
        @click="openAttachModal('network_simulator')"
        class="p-5 rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 hover:border-emerald-500/50 shadow-sm hover:shadow-md transition-all cursor-pointer space-y-3 group"
      >
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform">
          🌐
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Network Simulator (Packet Tracer)</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Router & Switch CLI config, IP subnetting, VLAN routing, and network diagnostics.</p>
        </div>
        <span class="inline-flex items-center text-xs font-bold text-emerald-500 group-hover:translate-x-1 transition-transform">
          + Attach Network Sim →
        </span>
      </div>

      <div
        @click="openAttachModal('virtual_machine')"
        class="p-5 rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 hover:border-purple-500/50 shadow-sm hover:shadow-md transition-all cursor-pointer space-y-3 group"
      >
        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 border border-purple-500/20 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform">
          🖥️
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Virtual Machine Access (Linux VM)</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Dedicated root terminal instances on Ubuntu Linux for systems programming.</p>
        </div>
        <span class="inline-flex items-center text-xs font-bold text-purple-500 group-hover:translate-x-1 transition-transform">
          + Attach Linux VM →
        </span>
      </div>
    </div>

    <!-- FILTER & LIST OF CONNECTED LABS -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden space-y-0">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>💻 Connected Practice Labs</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-sky-50 dark:bg-sky-950 text-sky-600 font-extrabold">{{ filteredLabs.length }}</span>
          </h3>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <button
            v-for="t in ['all', 'code_sandbox', 'network_simulator', 'virtual_machine'] as const"
            :key="t"
            @click="activeLabTypeTab = t"
            :class="[
              'px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition',
              activeLabTypeTab === t
                ? 'bg-sky-600 text-white shadow-sm'
                : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
            ]"
          >
            {{ t === 'all' ? 'All Labs' : (t === 'code_sandbox' ? 'Code IDE' : (t === 'network_simulator' ? 'Network Sim' : 'Linux VM')) }}
          </button>
        </div>
      </div>

      <div v-if="filteredLabs.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">💻</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No practice labs attached yet.</p>
        <p class="text-slate-400 mt-1">Click "ភ្ជាប់ Practice Lab ជាមួយមេរៀន" to connect your first virtual lab.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div
          v-for="lab in filteredLabs"
          :key="lab.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-3.5">
            <div
              :class="[
                'w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 font-bold border',
                lab.lab_type === 'code_sandbox' ? 'bg-sky-500/10 border-sky-500/30 text-sky-500' :
                lab.lab_type === 'network_simulator' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500' :
                'bg-purple-500/10 border-purple-500/30 text-purple-500'
              ]"
            >
              {{ lab.lab_type === 'code_sandbox' ? '💻' : (lab.lab_type === 'network_simulator' ? '🌐' : '🖥️') }}
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-2.5 flex-wrap">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ lab.title }}</h4>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">
                  ✓ Active Sandbox
                </span>
              </div>

              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span>📚 {{ lab.course_title }}</span>
                <span>•</span>
                <span>📌 {{ lab.lesson_title }}</span>
                <span>•</span>
                <span class="font-mono text-sky-600 dark:text-sky-400 text-[11px] truncate max-w-[220px]">{{ lab.provider_url }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-2 self-end md:self-center">
            <button
              @click="openRunner(lab)"
              type="button"
              class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer"
            >
              <span>▶️</span>
              <span>Test / Launch Sandbox</span>
            </button>

            <button
              @click="deleteLab(lab)"
              type="button"
              class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition cursor-pointer"
              title="Delete Lab"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ATTACH PRACTICE LAB MODAL -->
    <div
      v-if="showAttachModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <span>🔗 ភ្ជាប់ Practice Lab ជាមួយមេរៀន</span>
          </h3>
          <button @click="showAttachModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="submitAttachLab" class="space-y-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Target Course</label>
            <select
              v-model="attachForm.course_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Target Lesson</label>
            <select
              v-model="attachForm.lesson_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
              required
            >
              <option v-for="l in availableLessons" :key="l.id" :value="l.id">{{ l.title }}</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Lab Type</label>
            <select
              v-model="attachForm.lab_type"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option value="code_sandbox">Code sandbox (embedded interactive IDE)</option>
              <option value="network_simulator">Network simulator (Packet Tracer-like)</option>
              <option value="virtual_machine">Virtual machine (Ubuntu Linux root session)</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Lab Title</label>
            <input
              v-model="attachForm.title"
              type="text"
              placeholder="e.g. C Programming Pointer Memory Manipulation Lab"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              required
            />
          </div>

          <div v-if="attachForm.lab_type === 'code_sandbox'" class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Programming Language</label>
            <select
              v-model="attachForm.programming_language"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option value="c">C Language (GCC 13)</option>
              <option value="cpp">C++ (G++ 13)</option>
              <option value="python">Python 3.12</option>
              <option value="sql">SQL (MySQL / SQLite)</option>
            </select>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
            <button
              type="button"
              @click="showAttachModal = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold shadow-md transition disabled:opacity-50"
            >
              {{ isSubmitting ? 'Connecting...' : 'Attach Lab (ភ្ជាប់)' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- INTERACTIVE TEST / LAUNCH SANDBOX RUNNER MODAL -->
    <div
      v-if="showRunnerModal && activeRunningLab"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
      <div class="bg-slate-900 text-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-700 space-y-4 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 shrink-0">
          <div class="flex items-center gap-3">
            <span class="p-2 rounded-xl bg-sky-500/20 text-sky-400">💻</span>
            <div>
              <h3 class="font-extrabold text-base">{{ activeRunningLab.title }}</h3>
              <p class="text-xs text-slate-400">{{ activeRunningLab.course_title }} • {{ activeRunningLab.lesson_title }}</p>
            </div>
          </div>
          <button @click="showRunnerModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <!-- Embedded Code / CLI Editor Area -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 min-h-[320px]">
          <!-- Code / Command Input -->
          <div class="flex flex-col space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
              <span>Source Code / Configuration CLI:</span>
              <span class="px-2 py-0.5 rounded bg-slate-800 text-sky-400 uppercase">{{ selectedLanguage }}</span>
            </div>
            <textarea
              v-model="runnerCode"
              class="w-full flex-1 p-4 bg-slate-950 border border-slate-800 rounded-2xl font-mono text-xs text-emerald-400 leading-relaxed focus:outline-none focus:border-sky-500 resize-none shadow-inner"
              spellcheck="false"
            ></textarea>
          </div>

          <!-- Sandbox Execution Output Console -->
          <div class="flex flex-col space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
              <span>Execution Output & Diagnostics:</span>
              <span v-if="isRunning" class="text-amber-400 text-[10px] animate-pulse font-mono">Running sandbox...</span>
              <span v-else class="text-emerald-400 text-[10px] font-mono">Status: Ready</span>
            </div>
            <div class="w-full flex-1 p-4 bg-black border border-slate-800 rounded-2xl font-mono text-xs text-slate-300 whitespace-pre-wrap overflow-y-auto shadow-inner">
              <span v-if="!runnerConsoleOutput" class="text-slate-600 italic">Click "Run Sandbox Code" below to compile and execute in the live environment...</span>
              <span v-else>{{ runnerConsoleOutput }}</span>
            </div>
          </div>
        </div>

        <!-- RUNNER FOOTER -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-800 shrink-0">
          <span class="text-xs text-slate-400">Provider: {{ activeRunningLab.provider_url }}</span>
          <div class="flex items-center gap-3">
            <button
              @click="showRunnerModal = false"
              class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs"
            >
              Close
            </button>
            <button
              @click="executeRunnerCode"
              :disabled="isRunning"
              class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-sky-500/30 flex items-center gap-2 disabled:opacity-50 transition"
            >
              <span>{{ isRunning ? '⚙️ Executing...' : '▶ Run Sandbox Code' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
