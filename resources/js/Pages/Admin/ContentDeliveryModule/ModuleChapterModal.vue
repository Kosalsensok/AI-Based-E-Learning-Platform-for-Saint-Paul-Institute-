<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { 
  BookOpen, 
  GripVertical, 
  Trash2, 
  Plus, 
  Lock, 
  Clock, 
  Sparkles, 
  X,
  Layers
} from 'lucide-vue-next'

const props = defineProps<{
  show: boolean
  mode: 'module' | 'chapter'
  item?: any
  totalModules?: number
}>()

const emit = defineEmits(['close', 'saved'])

const courseTitle = ref('C Programming Basics')
const moduleNumber = ref(1)
const moduleTitle = ref('')
const description = ref('')
const durationVal = ref(3)
const durationUnit = ref<'Hours' | 'Minutes'>('Hours')
const status = ref('Published')
const learningObjectives = ref<string[]>([
  'Understand history of C programming',
  'Setup GCC compiler and VS Code environment',
  'Write and compile first C program'
])

const chapterNumber = ref('1.1')
const chapterTitle = ref('')

// Initialize or update fields when item or show changes
watch(() => [props.show, props.item, props.mode], () => {
  if (!props.show) return

  if (props.mode === 'module') {
    moduleNumber.value = props.item?.module_number || (props.totalModules ? props.totalModules + 1 : 1)
    moduleTitle.value = props.item?.title || (props.item ? '' : 'Introduction to C Programming')
    description.value = props.item?.description || (props.item ? '' : 'This module covers history, setup environment, and first C program.')
    
    // Parse duration (e.g. "3 Hours" or "45 Minutes")
    const rawDuration = props.item?.estimated_duration || '3 Hours'
    const parts = rawDuration.split(' ')
    if (parts.length >= 2) {
      durationVal.value = parseInt(parts[0]) || 3
      durationUnit.value = parts[1].toLowerCase().includes('min') ? 'Minutes' : 'Hours'
    } else {
      durationVal.value = parseInt(rawDuration) || 3
      durationUnit.value = 'Hours'
    }

    status.value = props.item?.status || 'Published'
    
    if (props.item?.learning_objectives && Array.isArray(props.item.learning_objectives)) {
      learningObjectives.value = [...props.item.learning_objectives]
    } else if (!props.item) {
      learningObjectives.value = [
        'Understand history of C programming',
        'Setup GCC compiler and VS Code environment',
        'Write and compile first C program'
      ]
    }
  } else {
    chapterNumber.value = props.item?.chapter_number || '1.1'
    chapterTitle.value = props.item?.title || 'History & Setup Environment'
  }
}, { immediate: true })

const addObjective = () => {
  learningObjectives.value.push('')
}

const removeObjective = (index: number) => {
  learningObjectives.value.splice(index, 1)
}

const moveObjectiveUp = (index: number) => {
  if (index <= 0) return
  const temp = learningObjectives.value[index]
  learningObjectives.value[index] = learningObjectives.value[index - 1]
  learningObjectives.value[index - 1] = temp
}

const moveObjectiveDown = (index: number) => {
  if (index >= learningObjectives.value.length - 1) return
  const temp = learningObjectives.value[index]
  learningObjectives.value[index] = learningObjectives.value[index + 1]
  learningObjectives.value[index + 1] = temp
}

const saveButtonText = computed(() => {
  if (props.mode === 'module') {
    return props.item ? 'Update Module' : 'Save Module'
  } else {
    return props.item ? 'Update Chapter' : 'Save Chapter'
  }
})

const handleSave = () => {
  const formattedDuration = `${durationVal.value} ${durationUnit.value}`
  emit('saved', {
    mode: props.mode,
    id: props.item?.id,
    module_number: moduleNumber.value,
    title: props.mode === 'module' ? moduleTitle.value : chapterTitle.value,
    description: description.value,
    estimated_duration: formattedDuration,
    status: status.value,
    learning_objectives: learningObjectives.value.filter(o => o.trim() !== '')
  })
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 w-full max-w-xl overflow-hidden my-6 transform transition-all text-slate-100 animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Gradient Header -->
      <div class="bg-slate-950/90 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800/80">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold shrink-0">
            <Layers v-if="props.mode === 'module'" class="w-5 h-5" />
            <BookOpen v-else class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-base font-bold text-white tracking-wide khmer-title-clean">
              {{ props.mode === 'module' ? (props.item ? 'EDIT MODULE' : 'ADD NEW MODULE') : (props.item ? 'EDIT CHAPTER' : 'ADD NEW CHAPTER') }}
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Configure course structural hierarchy and learning objectives</p>
          </div>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-700 transition cursor-pointer">
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-4 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        
        <!-- Target Course (Read-Only State) -->
        <div class="space-y-1">
          <label class="block font-semibold text-slate-400 text-[11px] uppercase tracking-wider">Target Course (Read-only)</label>
          <div class="px-4 py-2.5 bg-slate-950/60 border border-slate-800/80 rounded-2xl text-slate-300 font-medium flex items-center justify-between opacity-85 select-none cursor-not-allowed">
            <span class="flex items-center gap-2 font-semibold text-slate-200">
              <BookOpen class="w-4 h-4 text-indigo-400" />
              <span>{{ courseTitle }}</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-800 text-slate-400 border border-slate-700/60 flex items-center gap-1 font-mono">
              <Lock class="w-3 h-3 text-slate-400" /> Active Course
            </span>
          </div>
        </div>

        <!-- MODULE MODE FORM -->
        <div v-if="props.mode === 'module'" class="space-y-4">
          
          <!-- Row 1: Module # (Auto-filled) + Module Title -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 mb-1">Module # <span class="text-indigo-400 text-[10px] font-normal">(Auto)</span>:</label>
              <input v-model.number="moduleNumber" type="number" min="1" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono font-bold focus:outline-none focus:border-indigo-500/60 transition" />
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-300 mb-1">Module Title:</label>
              <input v-model="moduleTitle" type="text" placeholder="e.g. Introduction to C Programming" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-semibold placeholder:text-slate-600 focus:outline-none focus:border-indigo-500/60 transition" />
            </div>
          </div>

          <!-- Row 2: Module Description -->
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Module Description:</label>
            <textarea v-model="description" rows="2" placeholder="Brief summary of module contents..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 text-xs placeholder:text-slate-600 focus:outline-none focus:border-indigo-500/60 khmer-text-clean transition"></textarea>
          </div>

          <!-- Row 3: Learning Objectives (With Drag Handle & Delete Button) -->
          <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80 space-y-3">
            <div class="flex justify-between items-center">
              <label class="font-bold text-slate-200 flex items-center gap-1.5">
                <Sparkles class="w-4 h-4 text-indigo-400" />
                <span>Learning Objectives</span>
              </label>
              <button @click="addObjective" class="px-3 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-xl text-[11px] font-bold transition flex items-center gap-1 cursor-pointer active:scale-95">
                <Plus class="w-3.5 h-3.5" /> Add Objective
              </button>
            </div>

            <div class="space-y-2">
              <div 
                v-for="(obj, idx) in learningObjectives" 
                :key="idx" 
                class="flex items-center gap-2 bg-slate-900/80 p-1.5 px-2.5 rounded-xl border border-slate-800/80 hover:border-slate-700/80 transition group"
              >
                <!-- Drag / Reorder Handle -->
                <div class="flex items-center gap-0.5 text-slate-600 group-hover:text-slate-400 shrink-0">
                  <button @click="moveObjectiveUp(idx)" :disabled="idx === 0" class="hover:text-indigo-400 disabled:opacity-20 cursor-pointer" title="Move Up">▲</button>
                  <GripVertical class="w-4 h-4 cursor-grab" />
                  <button @click="moveObjectiveDown(idx)" :disabled="idx === learningObjectives.length - 1" class="hover:text-indigo-400 disabled:opacity-20 cursor-pointer" title="Move Down">▼</button>
                </div>
                
                <span class="text-slate-400 font-mono font-bold text-[11px] shrink-0 w-4">{{ idx + 1 }}.</span>
                
                <input 
                  v-model="learningObjectives[idx]" 
                  type="text" 
                  placeholder="Enter objective statement..." 
                  class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-xs focus:outline-none focus:border-indigo-500/60 khmer-text-clean transition" 
                />

                <!-- Delete Objective Button -->
                <button 
                  @click="removeObjective(idx)" 
                  class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-600 hover:text-white text-rose-400 border border-rose-500/20 flex items-center justify-center shrink-0 transition active:scale-95 cursor-pointer"
                  title="Delete Objective"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>

              <p v-if="learningObjectives.length === 0" class="text-[11px] text-slate-500 italic text-center py-2">
                No learning objectives added yet. Click "+ Add Objective" to create one.
              </p>
            </div>
          </div>

          <!-- Row 4: Estimated Duration (Number + Unit) + Publish Status -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 mb-1 flex items-center gap-1">
                <Clock class="w-3.5 h-3.5 text-sky-400" />
                <span>Estimated Duration:</span>
              </label>
              <div class="flex items-center gap-2">
                <input 
                  v-model.number="durationVal" 
                  type="number" 
                  min="1" 
                  class="w-1/2 px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 font-mono font-bold focus:outline-none focus:border-indigo-500/60 transition" 
                />
                <select 
                  v-model="durationUnit" 
                  class="w-1/2 px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 font-bold focus:outline-none focus:border-indigo-500/60 transition cursor-pointer"
                >
                  <option value="Hours">Hours</option>
                  <option value="Minutes">Minutes</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 mb-1">Publish Status:</label>
              <select v-model="status" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 font-semibold focus:outline-none focus:border-indigo-500/60 transition cursor-pointer">
                <option value="Published">🟢 Published (Live)</option>
                <option value="Draft">🟡 Draft</option>
              </select>
            </div>
          </div>

        </div>

        <!-- CHAPTER MODE FORM -->
        <div v-else class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 mb-1">Chapter #:</label>
              <input v-model="chapterNumber" type="text" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono font-bold focus:outline-none focus:border-indigo-500/60 transition" />
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-300 mb-1">Chapter Title:</label>
              <input v-model="chapterTitle" type="text" placeholder="e.g. History & Setup Environment" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-semibold placeholder:text-slate-600 focus:outline-none focus:border-indigo-500/60 transition" />
            </div>
          </div>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800/80 flex items-center justify-between">
        <button 
          @click="$emit('close')" 
          class="px-5 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700/60 transition cursor-pointer active:scale-95"
        >
          Cancel
        </button>
        <button 
          @click="handleSave" 
          class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95"
        >
          {{ saveButtonText }}
        </button>
      </div>

    </div>
  </div>
</template>
