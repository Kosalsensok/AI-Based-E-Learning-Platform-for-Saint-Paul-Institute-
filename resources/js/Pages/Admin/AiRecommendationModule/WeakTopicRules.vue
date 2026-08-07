<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import type { WeakTopicItem, WeakTopicDetectionConfig } from './types'

const props = defineProps<{
  weakTopics: WeakTopicItem[]
  config: WeakTopicDetectionConfig
}>()

const emit = defineEmits<{
  (e: 'saveDetectionConfig', cfg: WeakTopicDetectionConfig): void
  (e: 'addMapping'): void
  (e: 'editMapping', item: WeakTopicItem): void
  (e: 'deleteMapping', item: WeakTopicItem): void
  (e: 'toggleMappingStatus', item: WeakTopicItem): void
  (e: 'createReviewClass'): void
  (e: 'addPracticeQuiz'): void
  (e: 'exportWeakTopics'): void
}>()

const thresholdPercent = ref(props.config.threshold_percent)
const minQuestions = ref(props.config.min_questions)
const consecutiveFailures = ref(props.config.consecutive_failures)
const autoRemedialTask = ref(props.config.auto_remedial_task)
const showFocusBadge = ref(props.config.show_focus_badge)

const activeDropdownId = ref<number | null>(null)

function toggleDropdown(id: number) {
  if (activeDropdownId.value === id) {
    activeDropdownId.value = null
  } else {
    activeDropdownId.value = id
  }
}

function handleOutsideClick(e: MouseEvent) {
  const target = e.target as HTMLElement
  if (!target.closest('.dropdown-container')) {
    activeDropdownId.value = null
  }
}

onMounted(() => {
  window.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  window.removeEventListener('click', handleOutsideClick)
})

function handleSaveConfig() {
  emit('saveDetectionConfig', {
    threshold_percent: thresholdPercent.value,
    min_questions: minQuestions.value,
    consecutive_failures: consecutiveFailures.value,
    auto_remedial_task: autoRemedialTask.value,
    show_focus_badge: showFocusBadge.value,
  })
}
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner & Automatic Detection Threshold Settings -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <div>
          <h3 class="text-base font-black text-white flex items-center gap-2.5">
            <div class="p-2 rounded-xl bg-gradient-to-br from-rose-500/20 to-purple-500/20 border border-rose-500/30 text-rose-300">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#F43F5E" stroke-width="1.8" />
                <circle cx="12" cy="12" r="5" fill="#F43F5E" fill-opacity="0.4" stroke="#FB7185" stroke-width="1.5" />
                <circle cx="12" cy="12" r="1.5" fill="#FFF" />
              </svg>
            </div>
            <span>WEAK TOPIC RULES & REMEDIAL MAPPING</span>
          </h3>
          <p class="text-slate-400 text-xs mt-0.5">Detect student weak topics automatically → map remedial content → suggest review drills</p>
        </div>
      </div>

      <!-- Detection Settings Card with Clear Editable Inputs -->
      <div class="bg-[#121827] p-4.5 rounded-xl border border-slate-700/80 space-y-4">
        <h4 class="font-bold text-xs text-purple-300 uppercase tracking-wide flex items-center gap-2">
          <svg class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" fill="#C084FC" fill-opacity="0.3" stroke="#A855F7" stroke-width="1.8" />
          </svg>
          <span>AUTOMATIC DETECTION THRESHOLD SETTINGS</span>
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Threshold 1: Weak Topic Score -->
          <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-700/80 space-y-1.5">
            <label class="block text-slate-300 font-semibold text-[11px]">Weak Topic Threshold:</label>
            <div class="flex items-center gap-2">
              <span class="text-slate-400 text-xs">Topic score below</span>
              <input
                v-model="thresholdPercent"
                type="number"
                class="w-20 bg-[#0d1222] border-2 border-red-500/50 focus:border-red-400 rounded-xl px-2.5 py-1 text-center font-black text-sm text-red-400 shadow-inner focus:outline-none transition-all"
              />
              <span class="text-slate-300 font-bold">%</span>
            </div>
          </div>

          <!-- Threshold 2: Minimum Questions -->
          <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-700/80 space-y-1.5">
            <label class="block text-slate-300 font-semibold text-[11px]">Minimum Questions:</label>
            <div class="flex items-center gap-2">
              <span class="text-slate-400 text-xs">At least</span>
              <input
                v-model="minQuestions"
                type="number"
                class="w-20 bg-[#0d1222] border-2 border-purple-500/50 focus:border-purple-400 rounded-xl px-2.5 py-1 text-center font-black text-sm text-purple-300 shadow-inner focus:outline-none transition-all"
              />
              <span class="text-slate-300 font-semibold">questions / topic</span>
            </div>
          </div>

          <!-- Threshold 3: Consecutive Failures -->
          <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-700/80 space-y-1.5">
            <label class="block text-slate-300 font-semibold text-[11px]">Consecutive Failures:</label>
            <div class="flex items-center gap-2">
              <input
                v-model="consecutiveFailures"
                type="number"
                class="w-20 bg-[#0d1222] border-2 border-amber-500/50 focus:border-amber-400 rounded-xl px-2.5 py-1 text-center font-black text-sm text-amber-300 shadow-inner focus:outline-none transition-all"
              />
              <span class="text-slate-300 font-semibold">fails trigger flag</span>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-800">
          <div class="flex items-center gap-4 text-slate-200">
            <label class="flex items-center gap-2 cursor-pointer font-medium hover:text-white">
              <input v-model="autoRemedialTask" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Auto-create Remedial Task</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-medium hover:text-white">
              <input v-model="showFocusBadge" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Focus Badge on Student UI ("Focus More")</span>
            </label>
          </div>

          <button
            @click="handleSaveConfig"
            class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
          >
            <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
              <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
              <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            <span>Save Detection Settings</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Weak Topic -> Remedial Content Mapping Table -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <!-- Table Header Toolbar with Grouped Action Buttons -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          <span>WEAK TOPIC → REMEDIAL CONTENT MAPPING</span>
        </h4>

        <!-- Grouped Action Buttons: + Add Mapping & Export Weak Topics -->
        <div class="flex items-center gap-2">
          <button
            @click="emit('exportWeakTopics')"
            class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700/80 rounded-xl text-slate-300 hover:text-white font-semibold flex items-center gap-1.5 transition-all"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none">
              <path d="M4 16V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V16M12 12V4M12 4L8 8M12 4L16 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>Export Weak Topics</span>
          </button>

          <button
            @click="emit('addMapping')"
            class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-md shadow-purple-600/30 transition-all active:scale-95"
          >
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
              <path d="M12 4V20M4 12H20" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
            </svg>
            <span>Add Mapping</span>
          </button>
        </div>
      </div>

      <!-- Mapping Table -->
      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3.5">Weak Topic & Condition</th>
              <th class="p-3.5">Target Course</th>
              <th class="p-3.5">Auto-Recommended Remedial Content</th>
              <th class="p-3.5 w-24">Status</th>
              <th class="p-3.5 text-right w-36">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="w in weakTopics" :key="w.id" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5">
                <div class="font-bold text-white text-sm">{{ w.topic }}</div>
                <div class="text-[11px] text-red-400 font-mono">Topic Score &lt; 50%</div>
              </td>
              <td class="p-3.5 font-semibold text-slate-300">{{ w.course }}</td>
              <td class="p-3.5">
                <div class="flex flex-wrap gap-1.5">
                  <div
                    v-for="(rc, rcIdx) in w.remedial_content"
                    :key="rcIdx"
                    class="px-2.5 py-1 rounded-lg bg-purple-950/60 border border-purple-500/30 text-purple-200 text-[11px] font-medium"
                  >
                    {{ rc }}
                  </div>
                </div>
              </td>
              <td class="p-3.5">
                <button
                  @click="emit('toggleMappingStatus', w)"
                  title="Click to toggle status"
                  class="px-2.5 py-1 rounded-full text-[10px] font-bold border transition-all inline-flex items-center gap-1.5 shadow-sm bg-emerald-500/15 text-emerald-400 border-emerald-500/40 hover:bg-emerald-500/25"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span>On</span>
                </button>
              </td>
              <!-- Full CRUD Actions: Edit + Delete + 3-Dots Dropdown -->
              <td class="p-3.5 text-right relative">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Flaticon Edit Button -->
                  <button
                    @click="emit('editMapping', w)"
                    title="Edit Mapping"
                    class="p-1.5 bg-blue-950/60 hover:bg-blue-900/80 text-blue-400 hover:text-blue-300 rounded-lg transition-all border border-blue-500/30 shadow-sm"
                  >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                      <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M18.5 2.5C19.33 1.67 20.67 1.67 21.5 2.5C22.33 3.33 22.33 4.67 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" fill="#0284C7" stroke="#38BDF8" stroke-width="1.5" />
                    </svg>
                  </button>

                  <!-- Flaticon Delete Button -->
                  <button
                    @click="emit('deleteMapping', w)"
                    title="Delete Mapping"
                    class="p-1.5 bg-rose-950/60 hover:bg-rose-900/80 text-rose-400 hover:text-rose-300 rounded-lg transition-all border border-rose-500/30 shadow-sm"
                  >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                      <path d="M19 7L18.133 19.142C18.0516 20.2813 17.1028 21.1429 15.9611 21.1429H8.03893C6.89722 21.1429 5.94843 20.2813 5.86701 19.142L5 7" stroke="#F87171" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M10 11V17M14 11V17" stroke="#FCA5A5" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M9 7V4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V7M4 7H20" stroke="#F87171" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                  </button>

                  <!-- Flaticon 3-Dots Dropdown Menu -->
                  <div class="relative inline-block text-left dropdown-container">
                    <button
                      @click.stop="toggleDropdown(w.id)"
                      title="More Options"
                      class="p-1.5 bg-purple-950/60 hover:bg-purple-900/80 text-purple-300 hover:text-white rounded-lg transition-all border border-purple-500/30 shadow-sm"
                    >
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="5" r="2" fill="#C084FC" />
                        <circle cx="12" cy="12" r="2" fill="#C084FC" />
                        <circle cx="12" cy="19" r="2" fill="#C084FC" />
                      </svg>
                    </button>

                    <!-- Popover Menu -->
                    <div
                      v-if="activeDropdownId === w.id"
                      class="absolute right-0 mt-1 w-44 rounded-xl bg-slate-900 border border-slate-700 shadow-2xl z-50 py-1 text-left text-xs divide-y divide-slate-800"
                    >
                      <button
                        @click="emit('toggleMappingStatus', w); activeDropdownId = null"
                        class="w-full px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-amber-300 flex items-center gap-2 font-medium"
                      >
                        <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none">
                          <circle cx="12" cy="12" r="9" stroke="#F59E0B" stroke-width="1.8" />
                          <path d="M12 7V12" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>Toggle Status (On/Off)</span>
                      </button>

                      <button
                        @click="emit('deleteMapping', w); activeDropdownId = null"
                        class="w-full px-3 py-2 text-rose-400 hover:bg-rose-950/60 flex items-center gap-2 font-medium"
                      >
                        <svg class="w-3.5 h-3.5 text-rose-400" viewBox="0 0 24 24" fill="none">
                          <path d="M19 7L18.13 19.14C18.05 20.28 17.1 21.14 15.96 21.14H8.04C6.9 21.14 5.95 20.28 5.87 19.14L5 7" stroke="#F87171" stroke-width="1.8" />
                        </svg>
                        <span>Delete Mapping</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Live Weak Topic Detection Dashboard (This Week) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
        <svg class="w-4 h-4 text-cyan-400" viewBox="0 0 24 24" fill="none">
          <path d="M22 12H18L15 21L9 3L6 12H2" stroke="#22D3EE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span>LIVE WEAK TOPIC DETECTION DASHBOARD (THIS WEEK)</span>
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3.5">Topic Title</th>
              <th class="p-3.5">Affected Students</th>
              <th class="p-3.5">Avg Score</th>
              <th class="p-3.5">Trend Status</th>
              <th class="p-3.5 text-right">Auto-Action Triggered</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="w in weakTopics" :key="w.id" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5 font-bold text-white text-sm">{{ w.topic }}</td>
              <td class="p-3.5 font-bold text-amber-300">{{ w.affected_students }} Students</td>
              <td class="p-3.5 font-black text-red-400 text-sm">{{ w.avg_score }}%</td>
              <!-- Highlighted Trend Badges for Easy Visual Scan -->
              <td class="p-3.5">
                <span
                  v-if="w.trend === 'down'"
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-500/15 border border-red-500/40 text-red-300 inline-flex items-center gap-1.5 shadow-sm"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                  <span>↓ Declining</span>
                </span>
                <span
                  v-else
                  class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/15 border border-amber-500/40 text-amber-300 inline-flex items-center gap-1.5"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                  <span>→ Stable</span>
                </span>
              </td>
              <td class="p-3.5 text-right font-bold text-emerald-400">{{ w.auto_action }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- AI Suggestion Box -->
      <div class="bg-gradient-to-r from-purple-950/60 via-indigo-950/60 to-slate-900 border border-purple-500/50 p-4.5 rounded-2xl shadow-2xl space-y-3">
        <div class="flex items-center gap-2 text-purple-300 font-bold text-xs uppercase tracking-wide">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8" />
            <path d="M12 6V12L16 14" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
          </svg>
          <span>AI RECOMMENDATION SUGGESTION FOR TEACHERS</span>
        </div>

        <p class="text-slate-200 leading-relaxed font-medium">
          "Loops in C is weak for <strong class="text-red-400">85 students</strong> (Avg Score: 45%). Consider scheduling a live review session or adding an extra drill quiz for Module 3."
        </p>

        <div class="flex flex-wrap items-center gap-3 pt-2.5 border-t border-purple-500/30">
          <!-- Flaticon Megaphone Button for Create Review Class -->
          <button
            @click="emit('createReviewClass')"
            class="px-4.5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
          >
            <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
              <path d="M22 12C22 12 19 8 12 8H7L3 12H7V18C7 18 12 18 12 18L22 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M12 18V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            <span>Create Review Class</span>
          </button>

          <!-- Flaticon Plus Document Button for Add Practice Quiz -->
          <button
            @click="emit('addPracticeQuiz')"
            class="px-4.5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white font-semibold rounded-xl flex items-center gap-2 transition-all active:scale-95 shadow-sm"
          >
            <svg class="w-4 h-4 text-teal-400" viewBox="0 0 24 24" fill="none">
              <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.8" />
              <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.8" />
              <path d="M12 11V17M9 14H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <span>Add Practice Quiz</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
