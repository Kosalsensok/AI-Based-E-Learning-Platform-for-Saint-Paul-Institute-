<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import dayjs from 'dayjs'

const props = defineProps<{
  activeSessions: Array<any>
  logs: Array<any>
  failedLogs: Array<any>
  lockedUsers: Array<any>
  rolesPermissions: Array<any>
  securityPolicies: {
    session_expiration_hours: number
    max_failed_attempts: number
    require_2fa_admin: boolean
    require_2fa_teacher: boolean
  }
}>()

const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null
const initialTab = (urlParams?.get('tab') as any) || 'roles'

const currentTab = ref<'roles' | 'sessions' | 'history' | 'failed' | 'policies'>(
  ['roles', 'sessions', 'history', 'failed', 'policies'].includes(initialTab) ? initialTab : 'roles'
)

// Filter states for Active Sessions
const sessionSearch = ref('')
const sessionRoleFilter = ref('')
const sessionStatusFilter = ref('active')

// Filter states for Login History
const historySearch = ref('')
const historyStatusFilter = ref('')

const filteredSessions = computed(() => {
  return (props.activeSessions || []).filter(session => {
    const matchesSearch = !sessionSearch.value || 
      session.user?.name?.toLowerCase().includes(sessionSearch.value.toLowerCase()) ||
      session.user?.email?.toLowerCase().includes(sessionSearch.value.toLowerCase()) ||
      session.ip_address?.includes(sessionSearch.value)
    
    const matchesRole = !sessionRoleFilter.value || session.user?.role === sessionRoleFilter.value
    const matchesStatus = !sessionStatusFilter.value || (sessionStatusFilter.value === 'active' ? !session.is_revoked : session.is_revoked)

    return matchesSearch && matchesRole && matchesStatus
  })
})

const filteredHistory = computed(() => {
  return (props.logs || []).filter(log => {
    const matchesSearch = !historySearch.value ||
      log.user?.name?.toLowerCase().includes(historySearch.value.toLowerCase()) ||
      log.email?.toLowerCase().includes(historySearch.value.toLowerCase()) ||
      log.ip_address?.includes(historySearch.value)

    const matchesStatus = !historyStatusFilter.value || log.status === historyStatusFilter.value

    return matchesSearch && matchesStatus
  })
})

const formatTimeRemaining = (dateString: string) => {
  if (!dateString) return 'Expired'
  const diffHours = dayjs(dateString).diff(dayjs(), 'hour')
  if (diffHours <= 0) return 'Expiring soon'
  return `${diffHours}h remaining`
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return dayjs(dateString).format('DD MMM YYYY, HH:mm')
}

// Session revocation
const revokeSession = (sessionId: number) => {
  if (confirm('Are you sure you want to revoke this session? The user will be logged out.')) {
    router.post(`/admin/auth-logs/revoke/${sessionId}`)
  }
}

// User unlock
const unlockUser = (userId: number) => {
  if (confirm('Unlock this user account and reset failed login attempts?')) {
    router.post(`/admin/auth-logs/unlock/${userId}`)
  }
}

// Policy form
const policyForm = useForm({
  session_expiration_hours: props.securityPolicies?.session_expiration_hours || 24,
  max_failed_attempts: props.securityPolicies?.max_failed_attempts || 5,
  require_2fa_admin: props.securityPolicies?.require_2fa_admin ?? true,
  require_2fa_teacher: props.securityPolicies?.require_2fa_teacher ?? false,
})

const savePolicies = () => {
  policyForm.post('/admin/auth-logs/policies', {
    preserveScroll: true
  })
}
</script>

<template>
  <AdminLayout title="Authentication Module">
    <div class="space-y-6">

      <!-- Module Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-800/40 p-6 rounded-2xl border border-slate-800 backdrop-blur-xl">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-2xl shadow-lg shadow-indigo-500/10">
            🔐
          </div>
          <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Authentication Module</h1>
            <p class="text-xs text-slate-400 mt-1">Manage platform security, active user sessions, login logs, account lockouts, and role permissions.</p>
          </div>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-700 text-xs font-semibold text-emerald-400">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Security Protection Active
        </div>
      </div>

      <!-- Navigation Tabs (Matching Screenshot & Prototype 3.2 Ordering) -->
      <div class="flex border-b border-slate-800 overflow-x-auto custom-scrollbar">
        <button
          @click="currentTab = 'roles'"
          :class="[
            currentTab === 'roles'
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/5'
              : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700',
            'px-5 py-3 text-xs border-b-2 transition-all flex items-center gap-2 whitespace-nowrap font-medium'
          ]"
        >
          <span>🛡️ Roles & Permissions</span>
        </button>

        <button
          @click="currentTab = 'sessions'"
          :class="[
            currentTab === 'sessions'
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/5'
              : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700',
            'px-5 py-3 text-xs border-b-2 transition-all flex items-center gap-2 whitespace-nowrap font-medium'
          ]"
        >
          <span>🌐 Active Sessions</span>
          <span class="px-1.5 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 text-[10px] font-bold">
            {{ activeSessions?.length || 0 }}
          </span>
        </button>

        <button
          @click="currentTab = 'history'"
          :class="[
            currentTab === 'history'
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/5'
              : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700',
            'px-5 py-3 text-xs border-b-2 transition-all flex items-center gap-2 whitespace-nowrap font-medium'
          ]"
        >
          <span>📋 Login History</span>
        </button>

        <button
          @click="currentTab = 'failed'"
          :class="[
            currentTab === 'failed'
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/5'
              : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700',
            'px-5 py-3 text-xs border-b-2 transition-all flex items-center gap-2 whitespace-nowrap font-medium'
          ]"
        >
          <span>⚠️ Failed Login Attempts</span>
          <span v-if="lockedUsers?.length" class="px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold">
            {{ lockedUsers.length }} Locked
          </span>
        </button>

        <button
          @click="currentTab = 'policies'"
          :class="[
            currentTab === 'policies'
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/5'
              : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700',
            'px-5 py-3 text-xs border-b-2 transition-all flex items-center gap-2 whitespace-nowrap font-medium'
          ]"
        >
          <span>⚙️ Security Policies</span>
        </button>
      </div>

      <!-- TAB 1: Active Sessions (Prototype 3.2 Table) -->
      <div v-if="currentTab === 'sessions'" class="space-y-4">
        <!-- Controls Header -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-800/30 p-4 rounded-xl border border-slate-800">
          <div class="flex flex-1 items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 max-w-sm">
              <input
                v-model="sessionSearch"
                type="text"
                placeholder="Search user, email or IP..."
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            <!-- Role Filter -->
            <select
              v-model="sessionRoleFilter"
              class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-indigo-500"
            >
              <option value="">Role [All]</option>
              <option value="admin">Admin</option>
              <option value="teacher">Teacher</option>
              <option value="student">Student</option>
            </select>

            <!-- Status Filter -->
            <select
              v-model="sessionStatusFilter"
              class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-indigo-500"
            >
              <option value="active">Status [Active]</option>
              <option value="revoked">Status [Revoked]</option>
            </select>
          </div>

          <div class="text-xs text-slate-400 font-medium">
            Showing {{ filteredSessions.length }} active session(s)
          </div>
        </div>

        <!-- Prototype 3.2 Sessions Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4">ID</th>
                <th class="py-3.5 px-4">User</th>
                <th class="py-3.5 px-4">Role</th>
                <th class="py-3.5 px-4">Device / IP</th>
                <th class="py-3.5 px-4">Expires</th>
                <th class="py-3.5 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-xs">
              <tr v-for="(session, idx) in filteredSessions" :key="session.id" class="hover:bg-slate-800/30 transition-colors">
                <td class="py-3.5 px-4 font-mono text-slate-400">
                  {{ String(idx + 1).padStart(2, '0') }}
                </td>

                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <img
                      :src="session.user?.avatar || `https://ui-avatars.com/api/?name=${session.user?.name || 'User'}&background=6366f1&color=fff`"
                      class="w-7 h-7 rounded-full border border-slate-700"
                    />
                    <div>
                      <div class="font-semibold text-white">{{ session.user?.name || 'Unknown' }}</div>
                      <div class="text-[11px] text-slate-400">{{ session.user?.email || '-' }}</div>
                    </div>
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      session.user?.role === 'admin' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' :
                      session.user?.role === 'teacher' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' :
                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                      'px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize inline-block'
                    ]"
                  >
                    {{ session.user?.role || 'User' }}
                  </span>
                </td>

                <td class="py-3.5 px-4">
                  <div class="font-medium text-slate-200 truncate max-w-xs" :title="session.user_agent">
                    {{ session.user_agent ? (session.user_agent.includes('Mobile') ? 'Mobile App / PWA' : 'Chrome Browser') : 'PWA / IP' }}
                  </div>
                  <div class="text-[11px] font-mono text-slate-400">{{ session.ip_address || '127.0.0.1' }}</div>
                </td>

                <td class="py-3.5 px-4 font-mono font-medium text-slate-300">
                  {{ formatTimeRemaining(session.expires_at) }}
                </td>

                <td class="py-3.5 px-4 text-right">
                  <button
                    @click="revokeSession(session.id)"
                    class="px-3 py-1.5 text-xs font-semibold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-xl transition-all"
                  >
                    Revoke
                  </button>
                </td>
              </tr>

              <tr v-if="filteredSessions.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  No active sessions matching filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 2: Roles & Permissions -->
      <div v-if="currentTab === 'roles'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="item in rolesPermissions" :key="item.role" class="bg-slate-800/40 border border-slate-800 rounded-2xl p-6 space-y-4 flex flex-col justify-between backdrop-blur-xl">
          <div>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-bold text-white">{{ item.role }} Role</h3>
              <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full">
                {{ item.user_count }} User(s)
              </span>
            </div>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">{{ item.description }}</p>

            <div class="mt-4 pt-4 border-t border-slate-800/80 space-y-2">
              <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Granted Permissions:</div>
              <ul class="space-y-1.5">
                <li v-for="perm in item.permissions" :key="perm" class="text-xs text-slate-300 flex items-center gap-2">
                  <span class="text-emerald-400">✓</span> {{ perm }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 3: Login History -->
      <div v-if="currentTab === 'history'" class="space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-800/30 p-4 rounded-xl border border-slate-800">
          <input
            v-model="historySearch"
            type="text"
            placeholder="Search login history..."
            class="w-full md:w-72 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none"
          />
          <select
            v-model="historyStatusFilter"
            class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300"
          >
            <option value="">Status [All]</option>
            <option value="success">Success</option>
            <option value="failed">Failed</option>
          </select>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase">
                <th class="py-3 px-4">Date & Time</th>
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">IP Address</th>
                <th class="py-3 px-4">Device / Browser</th>
                <th class="py-3 px-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-xs">
              <tr v-for="log in filteredHistory" :key="log.id" class="hover:bg-slate-800/30">
                <td class="py-3 px-4 text-slate-300 font-mono">{{ formatDate(log.created_at) }}</td>
                <td class="py-3 px-4">
                  <div class="font-semibold text-white">{{ log.user?.name || log.email }}</div>
                  <div class="text-[11px] text-slate-400">{{ log.email }}</div>
                </td>
                <td class="py-3 px-4 font-mono text-slate-300">{{ log.ip_address || '127.0.0.1' }}</td>
                <td class="py-3 px-4 text-slate-300">{{ log.device || 'Desktop Chrome' }}</td>
                <td class="py-3 px-4">
                  <span
                    :class="[
                      log.status === 'success' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-red-500/20 text-red-300 border-red-500/30',
                      'px-2 py-0.5 rounded-full text-[10px] font-semibold border capitalize'
                    ]"
                  >
                    {{ log.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="filteredHistory.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-500">No login logs recorded yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 4: Failed Attempts & Lockouts -->
      <div v-if="currentTab === 'failed'" class="space-y-6">
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 flex items-start gap-4">
          <span class="text-amber-400 text-2xl">⚠️</span>
          <div>
            <h3 class="text-sm font-bold text-amber-300">Failed Login Limits & Locked Accounts</h3>
            <p class="text-xs text-amber-200/80 mt-1">Accounts are locked automatically when failed login attempts exceed threshold limits.</p>
          </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase">
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">Role</th>
                <th class="py-3 px-4">Failed Attempts</th>
                <th class="py-3 px-4">Lock Status</th>
                <th class="py-3 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-xs">
              <tr v-for="user in lockedUsers" :key="user.id" class="hover:bg-slate-800/30">
                <td class="py-3 px-4 font-semibold text-white">{{ user.name }} ({{ user.email }})</td>
                <td class="py-3 px-4 text-slate-300 capitalize">{{ user.role }}</td>
                <td class="py-3 px-4 font-mono font-bold text-amber-400">{{ user.login_attempts }} / {{ securityPolicies?.max_failed_attempts || 5 }}</td>
                <td class="py-3 px-4">
                  <span v-if="user.locked_until" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                    Locked until {{ formatDate(user.locked_until) }}
                  </span>
                  <span v-else class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    Attempts Warning
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <button
                    @click="unlockUser(user.id)"
                    class="px-3 py-1.5 text-xs font-semibold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl transition-all"
                  >
                    Unlock Account
                  </button>
                </td>
              </tr>
              <tr v-if="!lockedUsers || lockedUsers.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-500">No locked user accounts. All user accounts are operating normally.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 5: Security Policies -->
      <div v-if="currentTab === 'policies'" class="max-w-2xl bg-slate-800/40 border border-slate-800 rounded-2xl p-6 space-y-6 backdrop-blur-xl">
        <h3 class="text-base font-bold text-white border-b border-slate-800 pb-3">Platform Security Policies</h3>

        <form @submit.prevent="savePolicies" class="space-y-5 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Session Expiration Lifetime (Hours)</label>
            <input
              v-model="policyForm.session_expiration_hours"
              type="number"
              min="1" max="720"
              class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-indigo-500"
            />
            <p class="text-[11px] text-slate-500 mt-1">Automatic JWT token expiration time in hours.</p>
          </div>

          <div>
            <label class="block font-semibold text-slate-300 mb-1">Maximum Failed Login Attempts Threshold</label>
            <input
              v-model="policyForm.max_failed_attempts"
              type="number"
              min="1" max="20"
              class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-indigo-500"
            />
            <p class="text-[11px] text-slate-500 mt-1">Number of failed logins allowed before temporary account lockout.</p>
          </div>

          <div class="space-y-3 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="policyForm.require_2fa_admin"
                type="checkbox"
                class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-0"
              />
              <span class="text-slate-200 font-medium">Require Two-Factor Authentication (2FA) for Administrators</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="policyForm.require_2fa_teacher"
                type="checkbox"
                class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-0"
              />
              <span class="text-slate-200 font-medium">Require Two-Factor Authentication (2FA) for Teachers</span>
            </label>
          </div>

          <div class="pt-4 border-t border-slate-800 flex justify-end">
            <button
              type="submit"
              :disabled="policyForm.processing"
              class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition-all disabled:opacity-50"
            >
              Save Security Policies
            </button>
          </div>
        </form>
      </div>

    </div>
  </AdminLayout>
</template>
