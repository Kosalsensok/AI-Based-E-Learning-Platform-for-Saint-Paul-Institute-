<script setup lang="ts">
import { ref, watch } from 'vue'
import type { AiRuleItem } from '../types'

const props = defineProps<{
  show: boolean
  ruleToEdit?: AiRuleItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', ruleData: any): void
}>()

const ruleId = ref<number | null>(null)
const ruleName = ref('Advance High Performer')
const category = ref('Performance')
const description = ref('When student scores high, move them forward automatically.')
const metric = ref('Quiz Score')
const operator = ref('>= Greater or Equal')
const scoreValue = ref(80)
const actionType = ref('Advance to next module')
const messageTemplate = ref('Great job! You are ready for the next module 🎉')
const priority = ref('P1')
const cooldownHours = ref(24)
const maxFires = ref(3)

watch(() => props.show, (newVal) => {
  if (newVal) {
    if (props.ruleToEdit) {
      ruleId.value = props.ruleToEdit.id
      ruleName.value = props.ruleToEdit.name
      category.value = props.ruleToEdit.category
      actionType.value = props.ruleToEdit.action_text
      priority.value = props.ruleToEdit.priority
      messageTemplate.value = props.ruleToEdit.message_template || ''
      cooldownHours.value = props.ruleToEdit.cooldown_hours || 24
      maxFires.value = props.ruleToEdit.max_fires || 3
    } else {
      ruleId.value = null
      ruleName.value = ''
      category.value = 'Performance'
      actionType.value = 'Advance to next module'
      priority.value = 'P1'
      messageTemplate.value = ''
      cooldownHours.value = 24
      maxFires.value = 3
    }
  }
})

function handleSave() {
  emit('save', {
    id: ruleId.value,
    name: ruleName.value || 'New Custom Rule',
    category: category.value,
    description: description.value,
    condition_text: `${metric.value} ${operator.value} ${scoreValue.value}%`,
    action_text: actionType.value,
    priority: priority.value,
    status: 'active',
    fired_count: props.ruleToEdit ? props.ruleToEdit.fired_count : 0,
    cooldown_hours: cooldownHours.value,
    max_fires: maxFires.value,
    notify_student: true,
    notify_teacher: false,
    message_template: messageTemplate.value,
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Header with Flaticon Vector Icon -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
            <!-- Flaticon Pencil / Plus Icon -->
            <svg v-if="ruleToEdit" class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
              <path d="M18.5 2.5C19.33 1.67 20.67 1.67 21.5 2.5C22.33 3.33 22.33 4.67 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" fill="#A855F7" stroke="#C084FC" stroke-width="1.5" />
            </svg>
            <svg v-else class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M12 4V20M4 12H20" stroke="#C084FC" stroke-width="2.2" stroke-linecap="round" />
            </svg>
          </div>
          <span>{{ ruleToEdit ? 'EDIT AI RULE' : 'CREATE NEW AI RULE' }}</span>
        </h3>

        <!-- Close Button -->
        <button
          @click="emit('close')"
          class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <!-- Form Inputs Container -->
      <div class="space-y-3.5 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
        <!-- Rule Name Input -->
        <div>
          <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none">
              <path d="M4 7H20M4 12H15M4 17H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <span>Rule Name:</span>
          </label>
          <input
            v-model="ruleName"
            type="text"
            placeholder="e.g. Advance High Performer"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none transition-all placeholder:text-slate-500 font-medium"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <!-- Category Select -->
          <div>
            <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-teal-400" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
              </svg>
              <span>Category:</span>
            </label>
            <select
              v-model="category"
              class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer"
            >
              <option value="Performance">Performance</option>
              <option value="Engagement">Engagement</option>
              <option value="Completion">Completion</option>
              <option value="Behavior">Behavior</option>
            </select>
          </div>

          <!-- Priority Select -->
          <div>
            <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none">
                <path d="M4 15L12 9L20 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <span>Priority Level:</span>
            </label>
            <select
              v-model="priority"
              class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer"
            >
              <option value="P1">P1 (Highest Win)</option>
              <option value="P2">P2</option>
              <option value="P3">P3</option>
              <option value="P4">P4</option>
              <option value="P5">P5 (Lowest)</option>
            </select>
          </div>
        </div>

        <!-- IF Condition Trigger Section -->
        <div class="p-3.5 bg-slate-900/90 rounded-xl border border-teal-500/30 space-y-2">
          <span class="font-bold text-teal-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-400" viewBox="0 0 24 24" fill="none">
              <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="#14B8A6" fill-opacity="0.3" stroke="#2DD4BF" stroke-width="1.5" />
            </svg>
            <span>IF CONDITION (Trigger):</span>
          </span>
          <div class="grid grid-cols-3 gap-2">
            <input v-model="metric" type="text" class="bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs focus:border-teal-500 focus:outline-none" />
            <input v-model="operator" type="text" class="bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs focus:border-teal-500 focus:outline-none" />
            <input v-model="scoreValue" type="number" class="bg-slate-950 text-teal-300 font-bold border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs focus:border-teal-500 focus:outline-none" />
          </div>
        </div>

        <!-- THEN Action Response Section -->
        <div class="p-3.5 bg-slate-900/90 rounded-xl border border-purple-500/30 space-y-2.5">
          <span class="font-bold text-purple-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
              <polygon points="5 3 19 12 5 21 5 3" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.5" />
            </svg>
            <span>THEN ACTION (Response):</span>
          </span>
          <input v-model="actionType" type="text" class="w-full bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs focus:border-purple-500 focus:outline-none" />
          
          <div>
            <label class="block text-slate-400 mb-1 font-semibold text-[11px] flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none">
                <path d="M21 15C21 15.5 20.5 16 20 16H7L3 20V5C3 4.5 3.5 4 4 4H20C20.5 4 21 4.5 21 5V15Z" stroke="currentColor" stroke-width="1.8" />
              </svg>
              <span>Notification Message Template:</span>
            </label>
            <input v-model="messageTemplate" type="text" placeholder="e.g. Great job! Keep going..." class="w-full bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs focus:border-purple-500 focus:outline-none" />
          </div>
        </div>
      </div>

      <!-- Action Buttons Footer -->
      <div class="flex justify-end gap-2.5 border-t border-slate-700/60 pt-4">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl font-semibold transition-all border border-slate-700"
        >
          Cancel
        </button>

        <!-- Save Button with Flaticon Save Icon -->
        <button
          @click="handleSave"
          class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          </svg>
          <span>{{ ruleToEdit ? 'Save Changes' : 'Save & Activate Rule' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
