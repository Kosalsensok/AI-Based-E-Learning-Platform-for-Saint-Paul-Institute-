<script setup lang="ts">
const props = defineProps<{
  form: any
  backupHistory: Array<any>
  saveSettings: () => void
  runBackupNow: () => void
}>()

const emit = defineEmits(['openRestoreModal'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">🗄️ Backup & System Restore</h2>
        <p class="text-xs text-slate-400">Automate database & S3 storage snapshots to prevent data loss. High risk restore requires double confirmation.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Last Backup: {{ form.last_successful_backup_date || '26/05/2025 02:00 AM' }}</span>
    </div>

    <!-- Backup History Table -->
    <div class="overflow-x-auto rounded-xl border border-slate-700/60">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
          <tr>
            <th class="p-3">File Name</th>
            <th class="p-3">Type</th>
            <th class="p-3">Size</th>
            <th class="p-3">Date</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/60 bg-slate-900/40">
          <tr v-for="b in backupHistory" :key="b.id">
            <td class="p-3 font-mono text-white">{{ b.filename }}</td>
            <td class="p-3">{{ b.type }}</td>
            <td class="p-3 font-semibold">{{ b.size }}</td>
            <td class="p-3 text-slate-400">{{ b.date }}</td>
            <td class="p-3"><span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/20 text-emerald-300">Completed</span></td>
            <td class="p-3 text-right space-x-2">
              <button @click="emit('openRestoreModal')" type="button" class="text-amber-400 hover:underline font-semibold">🔄 Restore</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <button @click="runBackupNow" type="button" class="px-3.5 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl">▶️ Run Backup Now</button>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save Backup Policy</button>
    </div>
  </div>
</template>
