<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  systemLogs: Array<any>
  auditLogs: Array<any>
  clearSystemLogs: () => void
}>()

const emit = defineEmits(['openLogTrace'])
const activeLogsTab = ref<'system' | 'audit'>('system')
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">📜 System & Audit Logs</h2>
        <p class="text-xs text-slate-400">Track system technical errors, API failures, and administrative audit trails.</p>
      </div>

      <div class="flex items-center gap-2 bg-slate-900 p-1 rounded-xl border border-slate-700">
        <button @click="activeLogsTab = 'system'" type="button" :class="activeLogsTab === 'system' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400'" class="px-3 py-1 text-xs rounded-lg transition-all">System Logs</button>
        <button @click="activeLogsTab = 'audit'" type="button" :class="activeLogsTab === 'audit' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400'" class="px-3 py-1 text-xs rounded-lg transition-all">Audit Logs</button>
      </div>
    </div>

    <!-- System Technical Logs Table -->
    <div v-if="activeLogsTab === 'system'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div class="text-xs font-bold text-slate-300">Technical Exceptions & Service Failures</div>
        <button @click="clearSystemLogs" type="button" class="text-xs text-rose-400 hover:underline">🧹 Clear System Logs</button>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/60">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
            <tr>
              <th class="p-3">Time</th>
              <th class="p-3">Level</th>
              <th class="p-3">Service</th>
              <th class="p-3">Message</th>
              <th class="p-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 bg-slate-900/40">
            <tr v-for="log in systemLogs" :key="log.id">
              <td class="p-3 text-slate-400 font-mono text-[11px]">{{ log.time }}</td>
              <td class="p-3">
                <span :class="log.level === 'Error' ? 'bg-rose-500/20 text-rose-300' : log.level === 'Warning' ? 'bg-amber-500/20 text-amber-300' : 'bg-blue-500/20 text-blue-300'" class="px-2 py-0.5 text-[10px] font-bold rounded-full">
                  {{ log.level }}
                </span>
              </td>
              <td class="p-3 font-semibold text-white">{{ log.service }}</td>
              <td class="p-3 text-slate-300 truncate max-w-md">{{ log.message }}</td>
              <td class="p-3 text-right">
                <button v-if="log.stack_trace" @click="emit('openLogTrace', log)" type="button" class="text-blue-400 hover:underline">View Trace</button>
                <span v-else class="text-slate-500">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Audit Logs Table -->
    <div v-else class="space-y-4">
      <div class="text-xs font-bold text-slate-300">Administrative Activity & Security Changes</div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/60">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
            <tr>
              <th class="p-3">Date</th>
              <th class="p-3">User / Role</th>
              <th class="p-3">Action</th>
              <th class="p-3">Resource</th>
              <th class="p-3">IP Address</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 bg-slate-900/40">
            <tr v-for="a in auditLogs" :key="a.id">
              <td class="p-3 text-slate-400 font-mono text-[11px]">{{ a.date }}</td>
              <td class="p-3 font-semibold text-white">{{ a.user_name }} <span class="text-slate-500 text-[10px]">({{ a.role }})</span></td>
              <td class="p-3"><span class="px-2 py-0.5 text-[10px] bg-slate-800 text-blue-300 rounded font-mono">{{ a.action }}</span></td>
              <td class="p-3 text-slate-300">{{ a.resource }}</td>
              <td class="p-3 font-mono text-slate-400">{{ a.ip_address }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
