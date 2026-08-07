<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  form: any
  saveSettings: () => void
  onMaintenanceToggle: (val: boolean) => void
  cancelMaintenance: () => void
}>()

const emit = defineEmits(['openBrandingModal'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">🏢 General Settings</h2>
        <p class="text-xs text-slate-400">Configure E.LMS identity, organization profile, timezone, contact details, and maintenance mode.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/30">Active Config</span>
    </div>

    <!-- Organization Identity -->
    <div class="space-y-4">
      <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">🏫 Organization Identity</h3>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">System Name</label>
          <input v-model="form.site_name" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Short Name</label>
          <input v-model="form.site_short_name" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Institution Name</label>
          <input v-model="form.institution_name" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Website URL</label>
          <input v-model="form.website_url" type="url" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Support Email</label>
          <input v-model="form.contact_email" type="email" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Support Phone</label>
          <input v-model="form.contact_phone" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <!-- Logo & Favicon Upload -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/60 flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-200">System Logo</div>
            <div class="text-[11px] text-slate-400">PNG/SVG max 2MB</div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="emit('openBrandingModal')" type="button" class="px-2.5 py-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700">🖼️ Upload Logo</button>
            <button @click="emit('openBrandingModal')" type="button" class="px-2 py-1.5 text-xs text-blue-400 hover:underline">Preview</button>
          </div>
        </div>
        <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/60 flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-200">Favicon Icon</div>
            <div class="text-[11px] text-slate-400">ICO/PNG 32x32</div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="emit('openBrandingModal')" type="button" class="px-2.5 py-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700">🖼️ Upload Icon</button>
            <button @click="emit('openBrandingModal')" type="button" class="px-2 py-1.5 text-xs text-blue-400 hover:underline">Preview</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Regional Defaults -->
    <div class="border-t border-slate-700/60 pt-6 space-y-4">
      <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">🌏 Regional Defaults</h3>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Timezone</label>
          <select v-model="form.timezone" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="Asia/Phnom_Penh">Asia/Phnom_Penh (GMT+7)</option>
            <option value="Asia/Bangkok">Asia/Bangkok (GMT+7)</option>
            <option value="UTC">UTC (GMT+0)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Default Currency</label>
          <select v-model="form.default_currency" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="USD">USD – US Dollar ($)</option>
            <option value="KHR">KHR – Khmer Riel (៛)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Secondary Currency</label>
          <select v-model="form.secondary_currency" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="KHR">KHR – Khmer Riel (៛)</option>
            <option value="USD">USD – US Dollar ($)</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Date Format</label>
          <select v-model="form.date_format" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="DD/MM/YYYY">DD/MM/YYYY (e.g. 30/05/2025)</option>
            <option value="YYYY-MM-DD">YYYY-MM-DD (e.g. 2025-05-30)</option>
            <option value="MM/DD/YYYY">MM/DD/YYYY (e.g. 05/30/2025)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Time Format</label>
          <select v-model="form.time_format" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="12-hour AM/PM">12-hour AM/PM (e.g. 10:30 AM)</option>
            <option value="24-hour">24-hour (e.g. 22:30)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- System Behavior -->
    <div class="border-t border-slate-700/60 pt-6 space-y-4">
      <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">🛠️ System Behavior</h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Default User Role</label>
          <select v-model="form.default_user_role" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
          </select>
        </div>
        
        <div class="space-y-3 pt-2">
          <label class="flex items-center gap-3 cursor-pointer">
            <input v-model="form.require_email_verification" type="checkbox" class="w-4 h-4 rounded text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
            <span class="text-xs text-slate-300">Require email verification for new accounts</span>
          </label>

          <label class="flex items-center gap-3 cursor-pointer">
            <input v-model="form.allow_registration" type="checkbox" class="w-4 h-4 rounded text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
            <span class="text-xs text-slate-300">Allow new student self-registration</span>
          </label>
        </div>
      </div>

      <!-- Maintenance Mode Alert Box -->
      <div class="p-4 rounded-xl border bg-slate-900/80 flex items-center justify-between" :class="form.maintenance_mode ? 'border-amber-500/50 bg-amber-950/20' : 'border-slate-700/60'">
        <div>
          <div class="text-xs font-bold flex items-center gap-2" :class="form.maintenance_mode ? 'text-amber-400' : 'text-slate-200'">
            <span>⚠️ Maintenance Mode</span>
            <span v-if="form.maintenance_mode" class="px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-300 rounded font-bold">SYSTEM OFF FOR USERS</span>
          </div>
          <div class="text-[11px] text-slate-400 mt-0.5">When ON, students & teachers see maintenance page. Super Admins retain full access.</div>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="form.maintenance_mode ? cancelMaintenance() : onMaintenanceToggle(true)"
            type="button"
            :class="form.maintenance_mode ? 'bg-amber-600 hover:bg-amber-500 text-white' : 'bg-slate-700 hover:bg-slate-600 text-slate-200'"
            class="px-3 py-1.5 text-xs font-semibold rounded-xl transition-all"
          >
            {{ form.maintenance_mode ? 'Turn OFF Maintenance' : 'Turn ON Maintenance' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Action Buttons -->
    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <button @click="emit('openBrandingModal')" type="button" class="px-3.5 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700/60 rounded-xl border border-slate-600">
        👁 Preview Branding
      </button>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">
        💾 Save General Settings
      </button>
    </div>
  </div>
</template>
