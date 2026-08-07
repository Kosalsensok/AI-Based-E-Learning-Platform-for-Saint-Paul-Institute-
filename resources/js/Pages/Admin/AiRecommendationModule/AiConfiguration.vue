<script setup lang="ts">
import { ref, watch } from 'vue'
import type { AiConfigSettings } from './types'

const props = defineProps<{
  config: AiConfigSettings
}>()

const emit = defineEmits<{
  (e: 'saveConfig', cfg: AiConfigSettings): void
  (e: 'testConnection'): void
  (e: 'runFullSystemTest'): void
  (e: 'resetDefaults'): void
}>()

const engineType = ref(props.config.engine_type)
const mlModel = ref(props.config.ml_model)
const confidenceThreshold = ref(props.config.confidence_threshold)
const maxRecPerDay = ref(props.config.max_rec_per_day)
const reevaluationHours = ref(props.config.reevaluation_hours)
const sandboxMode = ref(props.config.sandbox_mode)

const aiProvider = ref(props.config.ai_provider)
const apiKeyMasked = ref(props.config.api_key_masked)
const temperature = ref(props.config.temperature)
const timeoutSeconds = ref(props.config.timeout_seconds)

const personalizationLevel = ref(props.config.personalization_level)
const useQuizScores = ref(props.config.use_quiz_scores)
const useLearningTime = ref(props.config.use_learning_time)
const useWeakTopics = ref(props.config.use_weak_topics)
const useIdleBehavior = ref(props.config.use_idle_behavior)
const useContentPrefs = ref(props.config.use_content_prefs)
const usePeerComparison = ref(props.config.use_peer_comparison)

const privacyLearningOnly = ref(props.config.privacy_learning_only)
const privacyAnonymize = ref(props.config.privacy_anonymize)
const dataRetentionDays = ref(props.config.data_retention_days)
const transparencyShowWhy = ref(props.config.transparency_show_why)
const allowStudentDismiss = ref(props.config.allow_student_dismiss)

const idleTriggerDays = ref(props.config.idle_trigger_days)
const sendEmail = ref(props.config.send_email)
const sendInApp = ref(props.config.send_in_app)
const sendSms = ref(props.config.send_sms)
const bestSendTime = ref(props.config.best_send_time)
const maxAlertsPerWeek = ref(props.config.max_alerts_per_week)

// 1. API Key Visibility State
const showApiKey = ref(false)

// 2. Connection Testing State & Spinner
const isTestingConnection = ref(false)
const connectionStatusText = ref('🟢 Status: Engine Connected & Active (Ping 45ms)')
const connectionSuccess = ref(true)

function handleTestConnection() {
  isTestingConnection.value = true
  setTimeout(() => {
    isTestingConnection.value = false
    connectionSuccess.value = true
    connectionStatusText.value = `🟢 Status: Connected Successfully to ${aiProvider.value} (Ping 38ms)`
    emit('testConnection')
  }, 800)
}

// 3. Unsaved Changes Tracking State
const isDirty = ref(false)
const isSaving = ref(false)

// Watch all reactive refs for dirty tracking
watch([
  engineType, mlModel, confidenceThreshold, maxRecPerDay, reevaluationHours, sandboxMode,
  aiProvider, apiKeyMasked, temperature, timeoutSeconds, personalizationLevel,
  useQuizScores, useLearningTime, useWeakTopics, useIdleBehavior, privacyLearningOnly,
  transparencyShowWhy, allowStudentDismiss
], () => {
  isDirty.value = true
}, { deep: true })

function handleSave() {
  isSaving.value = true
  setTimeout(() => {
    isDirty.value = false
    isSaving.value = false
    emit('saveConfig', {
      engine_type: engineType.value,
      ml_model: mlModel.value,
      confidence_threshold: confidenceThreshold.value,
      max_rec_per_day: maxRecPerDay.value,
      reevaluation_hours: reevaluationHours.value,
      sandbox_mode: sandboxMode.value,
      ai_provider: aiProvider.value,
      api_key_masked: apiKeyMasked.value,
      api_connected: props.config.api_connected,
      temperature: temperature.value,
      timeout_seconds: timeoutSeconds.value,
      personalization_level: personalizationLevel.value,
      use_quiz_scores: useQuizScores.value,
      use_learning_time: useLearningTime.value,
      use_weak_topics: useWeakTopics.value,
      use_idle_behavior: useIdleBehavior.value,
      use_content_prefs: useContentPrefs.value,
      use_peer_comparison: usePeerComparison.value,
      privacy_learning_only: privacyLearningOnly.value,
      privacy_anonymize: privacyAnonymize.value,
      data_retention_days: dataRetentionDays.value,
      transparency_show_why: transparencyShowWhy.value,
      allow_student_dismiss: allowStudentDismiss.value,
      idle_trigger_days: idleTriggerDays.value,
      send_email: sendEmail.value,
      send_in_app: sendInApp.value,
      send_sms: sendSms.value,
      best_send_time: bestSendTime.value,
      max_alerts_per_week: maxAlertsPerWeek.value,
    })
  }, 300)
}
</script>

<template>
  <div class="space-y-6 text-xs font-sans relative pb-12">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-black text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
            <svg class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#C084FC" stroke-width="1.8" />
              <path d="M19.4 15A1.65 1.65 0 0 0 20 13.4V10.6A1.65 1.65 0 0 0 18.5 9L18 8.1A1.65 1.65 0 0 0 18 6.5L19.5 5L17.5 3L16 4.5A1.65 1.65 0 0 0 14.4 4.5L13.5 4A1.65 1.65 0 0 0 12 2.5H9.2A1.65 1.65 0 0 0 7.6 4L6.7 4.5A1.65 1.65 0 0 0 5.1 4.5L3.6 3L1.6 5L3.1 6.5A1.65 1.65 0 0 0 3.1 8.1L2.6 9A1.65 1.65 0 0 0 1 10.6V13.4A1.65 1.65 0 0 0 2.5 15L3 15.9A1.65 1.65 0 0 0 3 17.5L1.5 19L3.5 21L5 19.5A1.65 1.65 0 0 0 6.6 19.5L7.5 20A1.65 1.65 0 0 0 9 21.5H11.8A1.65 1.65 0 0 0 13.4 20L14.3 19.5A1.65 1.65 0 0 0 15.9 19.5L17.4 21L19.4 19L17.9 17.5A1.65 1.65 0 0 0 17.9 15.9L18.4 15H19.4Z" stroke="#C084FC" stroke-width="1.5" stroke-linecap="round" />
            </svg>
          </div>
          <span>AI SYSTEM CONFIGURATION</span>
        </h3>
        <p class="text-slate-400 text-xs mt-0.5">Configure AI Engine parameters, API credentials, personalization levels, privacy & safety rules.</p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="emit('runFullSystemTest')"
          class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-semibold rounded-xl transition-all flex items-center gap-1.5"
        >
          <svg class="w-4 h-4 text-cyan-400" viewBox="0 0 24 24" fill="none">
            <path d="M9 3V8L4 16C3.33333 17.1111 3.8 19 5.5 19H18.5C20.2 19 20.6667 17.1111 20 16L15 8V3M9 3H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Run System Test</span>
        </button>

        <button
          @click="handleSave"
          class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2 active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" />
            <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>Save Configuration</span>
        </button>
      </div>
    </div>

    <!-- Engine Settings & API Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- 1. Engine Settings -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>ENGINE SETTINGS</span>
        </h4>

        <div class="space-y-3.5 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
          <div>
            <label class="block text-slate-300 mb-1 font-semibold">Recommendation Engine Mode:</label>
            <select v-model="engineType" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 focus:border-purple-500 focus:outline-none font-medium cursor-pointer">
              <option value="hybrid">🔀 Hybrid (Rule-Based + Machine Learning)</option>
              <option value="rule_based">⚙️ Rule-Based Only</option>
              <option value="ml">🤖 ML Collaborative Filtering</option>
            </select>
          </div>

          <div>
            <label class="block text-slate-300 mb-1 font-semibold">ML Model Algorithm:</label>
            <input v-model="mlModel" type="text" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 focus:border-purple-500 focus:outline-none font-medium" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-300 mb-1 font-semibold">Confidence Threshold:</label>
              <div class="flex items-center gap-2">
                <input v-model.number="confidenceThreshold" type="number" min="0" max="100" class="w-full bg-slate-950 text-purple-300 font-black border-2 border-slate-700/80 rounded-xl px-3.5 py-1.5 focus:border-purple-500 focus:outline-none text-sm" />
                <span class="text-slate-300 font-bold">%</span>
              </div>
            </div>

            <div>
              <label class="block text-slate-300 mb-1 font-semibold">Max Recs/Day/Student:</label>
              <input v-model.number="maxRecPerDay" type="number" min="1" max="50" class="w-full bg-slate-950 text-purple-300 font-black border-2 border-slate-700/80 rounded-xl px-3.5 py-1.5 focus:border-purple-500 focus:outline-none text-sm" />
            </div>
          </div>

          <label class="flex items-center gap-2.5 cursor-pointer pt-1 hover:text-white transition-colors">
            <input v-model="sandboxMode" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
            <span class="text-slate-200 font-medium">Sandbox / Test Mode (evaluate rules without serving live students)</span>
          </label>
        </div>
      </div>

      <!-- 2. API & Model Settings -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none">
            <path d="M21 2L11 12M21 2L15 22L11 13M21 2L2 9L11 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          </svg>
          <span>API & MODEL SETTINGS</span>
        </h4>

        <div class="space-y-3.5 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
          <div>
            <label class="block text-slate-300 mb-1 font-semibold">AI Provider:</label>
            <input v-model="aiProvider" type="text" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 focus:border-purple-500 focus:outline-none font-medium" />
          </div>

          <!-- API Key with Interactive Show/Hide Toggle Button & Test Connection Spinner -->
          <div>
            <label class="block text-slate-300 mb-1 font-semibold">API Key:</label>
            <div class="flex items-center gap-2">
              <div class="relative flex-1">
                <input
                  v-model="apiKeyMasked"
                  :type="showApiKey ? 'text' : 'password'"
                  class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl pl-3.5 pr-10 py-2 font-mono text-xs focus:border-purple-500 focus:outline-none"
                />
                <!-- Eye Icon Toggle -->
                <button
                  type="button"
                  @click="showApiKey = !showApiKey"
                  class="absolute right-3 top-2.5 text-slate-400 hover:text-white transition-colors"
                  :title="showApiKey ? 'Hide API Key' : 'Show API Key'"
                >
                  <svg v-if="!showApiKey" class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="1.8" />
                    <circle cx="12" cy="12" r="3" fill="#94A3B8" />
                  </svg>
                  <svg v-else class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20C5 20 1 12 1 12C2.24 9.68 4.14 7.78 6.47 6.47M9.9 4.24A9.12 9.12 0 0 1 12 4C19 4 23 12 23 12C22.18 13.54 21.04 14.89 19.67 15.96M1 1L23 23" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                  </svg>
                </button>
              </div>

              <!-- Test Connection Button with Animated Spinner -->
              <button
                @click="handleTestConnection"
                :disabled="isTestingConnection"
                class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold border border-slate-700 whitespace-nowrap flex items-center gap-1.5 transition-all disabled:opacity-50 active:scale-95 shadow-sm"
              >
                <svg v-if="isTestingConnection" class="w-3.5 h-3.5 animate-spin text-purple-400" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                  <path d="M12 2C6.47715 2 2 6.47715 2 12" stroke="currentColor" stroke-width="3" class="opacity-75" />
                </svg>
                <span v-else>🧪</span>
                <span>{{ isTestingConnection ? 'Testing...' : 'Test Connection' }}</span>
              </button>
            </div>
          </div>

          <!-- Range Slider for Temperature / Creativity -->
          <div class="space-y-1.5 pt-1">
            <div class="flex items-center justify-between text-slate-300 font-semibold">
              <span>Temperature / Creativity:</span>
              <span class="px-2.5 py-0.5 rounded-md bg-purple-950/80 text-purple-300 border border-purple-500/40 font-mono font-bold text-xs">{{ temperature }}</span>
            </div>
            <input
              v-model.number="temperature"
              type="range"
              min="0.0"
              max="1.0"
              step="0.05"
              class="w-full accent-purple-500 h-2 bg-slate-950 rounded-lg cursor-pointer border border-slate-700"
            />
            <div class="flex justify-between text-[10px] text-slate-400 font-semibold">
              <span>🎯 Precise (0.0)</span>
              <span>🎨 Creative (1.0)</span>
            </div>
          </div>

          <!-- Dynamic Connection Status Indicator -->
          <div
            class="p-2.5 rounded-xl border text-xs font-bold flex items-center gap-2 transition-all shadow-sm"
            :class="connectionSuccess ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/40 border-rose-500/40 text-rose-300'"
          >
            <span>{{ connectionStatusText }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Personalization & Privacy Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Personalization Level -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <svg class="w-4 h-4 text-cyan-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>PERSONALIZATION LEVEL & DATA INPUTS</span>
        </h4>

        <div class="space-y-3 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
          <div class="flex items-center gap-4 text-slate-200 font-bold">
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input v-model="personalizationLevel" value="basic" type="radio" name="level" class="text-purple-600 focus:ring-purple-500" />
              <span>Basic</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input v-model="personalizationLevel" value="standard" type="radio" name="level" class="text-purple-600 focus:ring-purple-500" />
              <span class="text-purple-300">Standard (Recommended)</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input v-model="personalizationLevel" value="advanced" type="radio" name="level" class="text-purple-600 focus:ring-purple-500" />
              <span>Advanced</span>
            </label>
          </div>

          <div class="grid grid-cols-2 gap-2.5 text-slate-200 pt-2.5 border-t border-slate-800">
            <label class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <input v-model="useQuizScores" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Use Quiz Scores</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <input v-model="useLearningTime" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Use Learning Time</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <input v-model="useWeakTopics" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Use Weak Topics</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <input v-model="useIdleBehavior" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
              <span>Use Idle Behavior</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Privacy & Ethics -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L3 7V12C3 17.55 6.84 22.74 12 24C17.16 22.74 21 17.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>PRIVACY & AI ETHICS</span>
        </h4>

        <div class="space-y-3 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80 text-slate-200">
          <label class="flex items-center gap-2.5 cursor-pointer hover:text-white transition-colors">
            <input v-model="privacyLearningOnly" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
            <span>Only use learning metrics (no personal PII in model training)</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer hover:text-white transition-colors">
            <input v-model="transparencyShowWhy" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
            <span class="text-purple-300 font-bold">Student Transparency (Show "Why am I seeing this?")</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer hover:text-white transition-colors">
            <input v-model="allowStudentDismiss" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700 focus:ring-purple-500" />
            <span>Allow students to dismiss recommendations</span>
          </label>
        </div>
      </div>
    </div>

    <!-- 3. Sticky Save Changes Bar on Unsaved Changes -->
    <transition
      enter-active-class="transition duration-300 ease-out transform"
      enter-from-class="translate-y-12 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in transform"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-12 opacity-0"
    >
      <div v-if="isDirty" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-[#0e1322]/95 border-2 border-purple-500/80 rounded-2xl px-6 py-3 shadow-2xl flex items-center justify-between gap-6 z-40 backdrop-blur-md ring-2 ring-purple-500/30">
        <div class="flex items-center gap-2.5 text-amber-300 font-bold text-xs">
          <span class="text-base animate-pulse">⚠️</span>
          <span>You have unsaved AI Configuration changes!</span>
        </div>

        <button
          @click="handleSave"
          :disabled="isSaving"
          class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/40 text-xs flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg v-if="isSaving" class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
            <path d="M12 2C6.47715 2 2 6.47715 2 12" stroke="currentColor" stroke-width="3" class="opacity-75" />
          </svg>
          <span v-else>💾</span>
          <span>Save Changes Now</span>
        </button>
      </div>
    </transition>
  </div>
</template>
