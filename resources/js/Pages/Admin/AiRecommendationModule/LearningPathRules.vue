<script setup lang="ts">
import { ref } from 'vue'
import type { LearningPathNode } from './types'

const props = defineProps<{
  nodes: LearningPathNode[]
}>()

const emit = defineEmits<{
  (e: 'addNode'): void
  (e: 'reorderNodes'): void
  (e: 'previewPath'): void
  (e: 'savePath'): void
  (e: 'editDecisionNode'): void
  (e: 'editNode', node: LearningPathNode): void
  (e: 'deleteNode', node: LearningPathNode): void
}>()

const selectedCourse = ref('C Programming Basics')
const pathMode = ref('adaptive')

const rulePrereq = ref(true)
const rulePostTest = ref(true)
const ruleAdaptive = ref(true)
const ruleRemedialNode = ref(true)
const ruleSkipPreTest = ref(false)

// Interactive Reactive Nodes List
const nodesList = ref<LearningPathNode[]>([
  { id: 1, step_number: 1, title: 'Basics of C Programming', status: 'completed' },
  { id: 2, step_number: 2, title: 'Operators & Expressions in C', status: 'in_progress' },
  { id: 3, step_number: 3, title: 'Conditional Statements', status: 'next' },
  { id: 4, step_number: 4, title: 'Loops in C (For & While)', status: 'locked' },
])

function handleAddNode() {
  const newNum = nodesList.value.length + 1
  nodesList.value.push({
    id: Date.now(),
    step_number: newNum,
    title: `Module ${newNum}: Advanced Topics`,
    status: 'locked'
  })
  emit('addNode')
}

function handleDeleteNode(node: LearningPathNode) {
  nodesList.value = nodesList.value.filter(n => n.id !== node.id)
  emit('deleteNode', node)
}
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-black text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 text-indigo-300">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
              <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="#818CF8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <span>ADAPTIVE LEARNING PATH RULES</span>
        </h3>
        <p class="text-slate-400 text-xs mt-0.5">Define module sequencing and adaptive branching — fast-track high performers, supply remedial drills for struggling students.</p>
      </div>

      <div class="flex flex-wrap items-center gap-2.5">
        <select v-model="selectedCourse" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer">
          <option value="C Programming Basics">Course: C Programming Basics</option>
          <option value="Tourism Fundamentals">Course: Tourism Fundamentals</option>
          <option value="English Literature">Course: English Literature</option>
        </select>

        <select v-model="pathMode" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer">
          <option value="adaptive">Mode: 🔀 Adaptive Branching</option>
          <option value="linear">Mode: ➡️ Linear Sequential</option>
        </select>
      </div>
    </div>

    <!-- Node Flow Visualizer Card -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <!-- Section Header with Clear Primary vs Secondary Action Hierarchy -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <h4 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          <span>VISUAL LEARNING PATH SEQUENCE ({{ selectedCourse }})</span>
        </h4>

        <div class="flex items-center gap-2">
          <!-- Secondary Action: Preview Student Path (Outline Style with Flaticon Eye Icon) -->
          <button
            @click="emit('previewPath')"
            class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700/80 rounded-xl text-slate-300 hover:text-white font-semibold text-xs flex items-center gap-1.5 transition-all"
          >
            <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none">
              <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="1.8" />
              <circle cx="12" cy="12" r="3" fill="#F59E0B" fill-opacity="0.5" />
            </svg>
            <span>Preview Student Path</span>
          </button>

          <!-- Primary Action: + Add Node (Vivid Purple Button) -->
          <button
            @click="handleAddNode"
            class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-md shadow-purple-600/30 transition-all active:scale-95 ring-1 ring-purple-400/40"
          >
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
              <path d="M12 4V20M4 12H20" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
            </svg>
            <span>Add Node</span>
          </button>
        </div>
      </div>

      <!-- Path Tree Visual Block with Drag Handles & Hover Actions -->
      <div class="p-5 bg-[#121827] rounded-xl border border-slate-700/80 space-y-3 font-sans">
        <template v-for="(node, idx) in nodesList" :key="node.id">
          <!-- Node Card Block with Drag Handle and Hover Actions -->
          <div
            class="group flex items-center gap-3 p-3 rounded-xl border transition-all relative"
            :class="
              node.status === 'completed' ? 'bg-emerald-950/30 border-emerald-500/40' :
              (node.status === 'in_progress' ? 'bg-amber-950/30 border-amber-500/40' :
              (node.status === 'next' ? 'bg-purple-950/30 border-purple-500/40' : 'bg-slate-900/60 border-slate-700/80 opacity-75'))
            "
          >
            <!-- Drag Handle Grip -->
            <div class="text-slate-600 group-hover:text-slate-400 cursor-grab font-bold tracking-tighter text-xs px-1 select-none" title="Drag to reorder">
              ⋮⋮
            </div>

            <!-- Step Number Badge -->
            <span
              class="w-7 h-7 rounded-full font-black text-xs flex items-center justify-center shrink-0"
              :class="
                node.status === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' :
                (node.status === 'in_progress' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' :
                (node.status === 'next' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : 'bg-slate-800 text-slate-400'))
              "
            >
              {{ idx + 1 }}
            </span>

            <!-- Node Title & Status Tag -->
            <div class="flex-1 min-w-0 flex items-center justify-between">
              <div>
                <span class="font-bold text-white text-xs">{{ node.title }}</span>
                <span
                  class="ml-2.5 px-2 py-0.5 rounded text-[10px] font-semibold inline-block"
                  :class="
                    node.status === 'completed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                    (node.status === 'in_progress' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' :
                    (node.status === 'next' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-slate-800 text-slate-400'))
                  "
                >
                  {{ node.status === 'completed' ? 'Completed' : (node.status === 'in_progress' ? 'In Progress' : (node.status === 'next' ? 'Next Up' : 'Locked')) }}
                </span>
              </div>

              <!-- Quick Hover Action Buttons -->
              <div class="flex items-center gap-1 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                  @click="emit('editNode', node)"
                  title="Edit Node"
                  class="p-1.5 bg-blue-950/60 hover:bg-blue-900/80 text-blue-400 rounded-lg border border-blue-500/30 shadow-sm"
                >
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" />
                    <path d="M18.5 2.5C19.33 1.67 20.67 1.67 21.5 2.5C22.33 3.33 22.33 4.67 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" fill="#0284C7" stroke="#38BDF8" stroke-width="1.5" />
                  </svg>
                </button>
                <button
                  @click="handleDeleteNode(node)"
                  title="Delete Node"
                  class="p-1.5 bg-rose-950/60 hover:bg-rose-900/80 text-rose-400 rounded-lg border border-rose-500/30 shadow-sm"
                >
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                    <path d="M19 7L18.133 19.142C18.0516 20.2813 17.1028 21.1429 15.9611 21.1429H8.03893C6.89722 21.1429 5.94843 20.2813 5.86701 19.142L5 7" stroke="#F87171" stroke-width="1.8" stroke-linecap="round" />
                    <path d="M10 11V17M14 11V17" stroke="#FCA5A5" stroke-width="1.8" stroke-linecap="round" />
                    <path d="M9 7V4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V7M4 7H20" stroke="#F87171" stroke-width="1.8" stroke-linecap="round" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Connector Arrow -->
          <div class="text-center text-slate-500 font-bold text-xs py-0.5">↓</div>

          <!-- Decision Node Box (Inserted before Node 4) -->
          <div v-if="idx === 2" class="p-4 bg-gradient-to-r from-purple-950/80 via-indigo-950/80 to-slate-900 border-2 border-purple-500/80 rounded-xl space-y-3 shadow-xl my-2">
            <div class="flex items-center justify-between border-b border-purple-500/40 pb-2">
              <span class="font-black text-xs text-purple-300 flex items-center gap-2">
                <span class="text-slate-400 cursor-grab font-bold tracking-tighter text-xs" title="Drag to reorder">⋮⋮</span>
                <span class="text-amber-400">◇</span>
                <span>DECISION NODE: Post-Test Score Evaluation</span>
              </span>
              <button
                @click="emit('editDecisionNode')"
                class="px-3 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition-all shadow"
              >
                <svg class="w-3 h-3 text-purple-200" viewBox="0 0 24 24" fill="none">
                  <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="currentColor" stroke-width="1.8" />
                </svg>
                <span>Edit Decision Node</span>
              </button>
            </div>

            <!-- Explicit Branch Visual Targets with Clear Visual Arrows -->
            <div class="space-y-2 text-xs font-mono">
              <!-- Branch 1: High Score -->
              <div class="p-2.5 bg-emerald-950/50 border border-emerald-500/40 rounded-xl flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-emerald-300">├── IF Score ≥ 80%</span>
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[11px] font-bold flex items-center gap-1.5">
                  <span>➔</span>
                  <span>Advance directly to Step 4: Loops in C</span>
                </span>
              </div>

              <!-- Branch 2: Mid Score -->
              <div class="p-2.5 bg-amber-950/50 border border-amber-500/40 rounded-xl flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-amber-300">├── IF Score 50 – 79%</span>
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[11px] font-bold flex items-center gap-1.5">
                  <span>➔</span>
                  <span>Go to Step 4: Loops + Assign Extra Practice Drill</span>
                </span>
              </div>

              <!-- Branch 3: Low Score -->
              <div class="p-2.5 bg-red-950/50 border border-red-500/40 rounded-xl flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-red-300">└── IF Score &lt; 50%</span>
                <span class="px-2.5 py-0.5 rounded-md bg-red-500/20 text-red-300 border border-red-500/40 text-[11px] font-bold flex items-center gap-1.5">
                  <span>➔</span>
                  <span>Insert Remedial Node: Conditions Review ➔ Go to Step 4</span>
                </span>
              </div>
            </div>
          </div>

          <div v-if="idx === 2" class="text-center text-slate-500 font-bold text-xs py-0.5">↓</div>
        </template>
      </div>
    </div>

    <!-- Path Rules Toggles Panel -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
        <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
          <path d="M12 7V13L16 15" stroke="currentColor" stroke-width="1.8" />
        </svg>
        <span>LEARNING PATH ENFORCEMENT RULES</span>
      </h4>

      <div class="space-y-3 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
        <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
          <input v-model="rulePrereq" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
          <span class="text-slate-200 font-medium">Strict prerequisites (must complete previous node before starting next)</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
          <input v-model="rulePostTest" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
          <span class="text-slate-200 font-medium">Post-test completion required to unlock next node</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
          <input v-model="ruleAdaptive" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
          <span class="text-slate-200 font-medium">Adaptive branching enabled (dynamic path adjustment based on score)</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
          <input v-model="ruleRemedialNode" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
          <span class="text-slate-200 font-medium">Auto-insert remedial node on quiz failure (&lt;50%)</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
          <input v-model="ruleSkipPreTest" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
          <span class="text-slate-200 font-medium">Allow skip via pre-test (score ≥ 90% skips introductory module)</span>
        </label>
      </div>

      <!-- Single Consolidated Save All Path Changes Button -->
      <div class="flex justify-end pt-2 border-t border-slate-800">
        <button
          @click="emit('savePath')"
          class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          </svg>
          <span>Save All Path Changes</span>
        </button>
      </div>
    </div>
  </div>
</template>
