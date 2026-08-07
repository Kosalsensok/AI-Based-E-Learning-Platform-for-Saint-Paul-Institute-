<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  users: Array<any>
  faculties: Array<any>
  departments: Array<any>
  majors: Array<any>
  filters: {
    role?: string
    status?: string
  }
}>()

const activeTab = ref<string>(props.filters?.role || (props.filters?.status === 'suspended' ? 'suspended' : 'all'))
const search = ref('')
const selectedRole = ref(props.filters?.role || '')
const selectedMajor = ref<number | ''>('')
const selectedStatus = ref(props.filters?.status || '')

const selectedUserIds = ref<number[]>([])
const dialogOpen = ref(false)
const editMode = ref(false)
const selectedUser = ref<any>(null)

const form = useForm({
  name: '',
  name_kh: '',
  email: '',
  phone: '',
  role: 'student',
  faculty_id: null as number | null,
  department_id: null as number | null,
  major_id: null as number | null,
  status: 'active',
  password: '',
})

// Cascading options
const filteredDepartments = computed(() => {
  if (!form.faculty_id) return props.departments || []
  return (props.departments || []).filter(d => d.faculty_id === form.faculty_id)
})

const filteredMajors = computed(() => {
  if (!form.department_id) return props.majors || []
  return (props.majors || []).filter(m => m.department_id === form.department_id)
})

watch(() => form.faculty_id, () => {
  form.department_id = null
  form.major_id = null
})

watch(() => form.department_id, () => {
  form.major_id = null
})

// Tab navigation handler
const selectTab = (tab: string) => {
  activeTab.value = tab
  if (tab === 'all') {
    selectedRole.value = ''
    selectedStatus.value = ''
  } else if (tab === 'admin') {
    selectedRole.value = 'admin'
    selectedStatus.value = ''
  } else if (tab === 'teacher') {
    selectedRole.value = 'teacher'
    selectedStatus.value = ''
  } else if (tab === 'student') {
    selectedRole.value = 'student'
    selectedStatus.value = ''
  } else if (tab === 'suspended') {
    selectedRole.value = ''
    selectedStatus.value = 'suspended'
  }
}

// Filtered user list
const filteredUsers = computed(() => {
  return (props.users || []).filter(user => {
    const matchesSearch = !search.value ||
      user.name?.toLowerCase().includes(search.value.toLowerCase()) ||
      user.name_kh?.toLowerCase().includes(search.value.toLowerCase()) ||
      user.email?.toLowerCase().includes(search.value.toLowerCase()) ||
      user.student_code?.toLowerCase().includes(search.value.toLowerCase())

    const matchesRole = !selectedRole.value || user.role === selectedRole.value
    const matchesMajor = !selectedMajor.value || user.major_id === selectedMajor.value
    const matchesStatus = !selectedStatus.value || user.status === selectedStatus.value

    return matchesSearch && matchesRole && matchesMajor && matchesStatus
  })
})

const toggleSelectAll = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.checked) {
    selectedUserIds.value = filteredUsers.value.map(u => u.id)
  } else {
    selectedUserIds.value = []
  }
}

const openCreate = () => {
  editMode.value = false
  selectedUser.value = null
  form.reset()
  dialogOpen.value = true
}

const openEdit = (user: any) => {
  editMode.value = true
  selectedUser.value = user
  form.name = user.name || ''
  form.name_kh = user.name_kh || ''
  form.email = user.email || ''
  form.phone = user.phone || ''
  form.role = user.role || 'student'
  form.faculty_id = user.major?.department?.faculty_id || null
  form.department_id = user.major?.department_id || null
  form.major_id = user.major_id || null
  form.status = user.status || 'active'
  form.password = ''
  dialogOpen.value = true
}

const submitForm = () => {
  if (editMode.value && selectedUser.value) {
    form.put(route('admin.users.update', selectedUser.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        dialogOpen.value = false
      }
    })
  } else {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => {
        dialogOpen.value = false
      }
    })
  }
}

const performBulkAction = (action: 'activate' | 'suspend' | 'delete') => {
  if (selectedUserIds.value.length === 0) {
    alert('Please select at least one user.')
    return
  }
  if (!confirm(`Are you sure you want to perform "${action}" on ${selectedUserIds.value.length} user(s)?`)) return

  router.post(route('admin.users.bulk'), {
    ids: selectedUserIds.value,
    action: action
  }, {
    preserveScroll: true,
    onSuccess: () => {
      selectedUserIds.value = []
    }
  })
}

const exportUsersCSV = () => {
  const headers = ['ID', 'Name Khmer', 'Name English', 'Email', 'Role', 'Major', 'Status', 'Phone']
  const rows = filteredUsers.value.map(u => [
    u.id,
    `"${u.name_kh || ''}"`,
    `"${u.name || ''}"`,
    u.email,
    u.role,
    `"${u.major?.name || ''}"`,
    u.status,
    u.phone || ''
  ])

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `users_export_${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
</script>

<template>
  <AdminLayout title="User Management">
    <div class="space-y-6">

      <!-- Header & Quick Actions -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-800/40 p-6 rounded-2xl border border-slate-800 backdrop-blur-xl">
        <div>
          <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            👥 USER MANAGEMENT
          </h1>
          <p class="text-xs text-slate-400 mt-1">Manage all Administrators, Teachers, and Students profiles and permissions.</p>
        </div>

        <button
          @click="openCreate"
          class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2"
        >
          <span>➕ Add User</span>
        </button>
      </div>

      <!-- Filter Tabs (Prototype 4) -->
      <div class="flex border-b border-slate-800 overflow-x-auto">
        <button
          v-for="t in [
            { key: 'all', label: 'All Users' },
            { key: 'admin', label: 'Admins' },
            { key: 'teacher', label: 'Teachers' },
            { key: 'student', label: 'Students' },
            { key: 'suspended', label: 'Suspended' }
          ]"
          :key="t.key"
          @click="selectTab(t.key)"
          :class="[
            activeTab === t.key
              ? 'border-indigo-500 text-indigo-400 font-bold bg-indigo-500/10'
              : 'border-transparent text-slate-400 hover:text-slate-200',
            'px-5 py-3 text-xs border-b-2 transition-all font-medium whitespace-nowrap'
          ]"
        >
          {{ t.label }}
        </button>
      </div>

      <!-- Filter Controls & Bulk Action Bar (Prototype 4) -->
      <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-800 space-y-4">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Search -->
          <div class="flex-1 min-w-[200px]">
            <input
              v-model="search"
              type="text"
              placeholder="Search by Name, Email, Student ID..."
              class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
            />
          </div>

          <!-- Role Filter -->
          <select v-model="selectedRole" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option value="">Role [All ▼]</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
          </select>

          <!-- Major Filter -->
          <select v-model="selectedMajor" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option value="">Major [All ▼]</option>
            <option v-for="m in majors" :key="m.id" :value="m.id">{{ m.name }}</option>
          </select>

          <!-- Status Filter -->
          <select v-model="selectedStatus" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option value="">Status [All ▼]</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
            <option value="pending">Pending</option>
          </select>
        </div>

        <!-- Bulk Action & Export buttons -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-800">
          <div class="flex items-center gap-2">
            <button
              @click="exportUsersCSV"
              class="px-3 py-1.5 text-xs font-semibold bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition-all"
            >
              📥 Export Excel/CSV
            </button>
          </div>

          <div class="flex items-center gap-2" v-if="selectedUserIds.length > 0">
            <span class="text-xs text-slate-400 font-semibold">{{ selectedUserIds.length }} selected:</span>
            <button
              @click="performBulkAction('activate')"
              class="px-3 py-1.5 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl hover:bg-emerald-500/20"
            >
              Bulk Activate
            </button>
            <button
              @click="performBulkAction('suspend')"
              class="px-3 py-1.5 text-xs font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl hover:bg-amber-500/20"
            >
              Bulk Suspend
            </button>
          </div>
        </div>
      </div>

      <!-- Prototype 4 Users Table -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
              <th class="py-3.5 px-4 w-10">
                <input type="checkbox" @change="toggleSelectAll" class="rounded bg-slate-900 border-slate-700" />
              </th>
              <th class="py-3.5 px-4">ID</th>
              <th class="py-3.5 px-4">Name</th>
              <th class="py-3.5 px-4">Role</th>
              <th class="py-3.5 px-4">Major / Dept</th>
              <th class="py-3.5 px-4">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-for="u in filteredUsers" :key="u.id" class="hover:bg-slate-800/30 transition-colors">
              <td class="py-3.5 px-4">
                <input type="checkbox" :value="u.id" v-model="selectedUserIds" class="rounded bg-slate-900 border-slate-700" />
              </td>

              <td class="py-3.5 px-4 font-mono text-slate-400">
                {{ String(u.id).padStart(2, '0') }}
              </td>

              <td class="py-3.5 px-4">
                <div class="font-semibold text-white">{{ u.name }}</div>
                <div class="text-[11px] text-indigo-400" v-if="u.name_kh">{{ u.name_kh }}</div>
                <div class="text-[11px] text-slate-400">{{ u.email }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span
                  :class="[
                    u.role === 'admin' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' :
                    u.role === 'teacher' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' :
                    'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                    'px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize inline-block'
                  ]"
                >
                  {{ u.role }}
                </span>
              </td>

              <td class="py-3.5 px-4 text-slate-300">
                <div class="font-medium">{{ u.major?.name || '—' }}</div>
                <div class="text-[11px] text-slate-500">{{ u.major?.department?.name || '' }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span
                  :class="[
                    u.status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' :
                    u.status === 'suspended' ? 'bg-red-500/20 text-red-300 border-red-500/30' :
                    u.status === 'pending' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' :
                    'bg-slate-700 text-slate-300',
                    'px-2.5 py-1 rounded-full text-[10px] font-bold border capitalize inline-block'
                  ]"
                >
                  {{ u.status || 'active' }}
                </span>
              </td>

              <td class="py-3.5 px-4 text-right">
                <button
                  @click="openEdit(u)"
                  class="px-2.5 py-1.5 text-xs font-semibold text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 rounded-xl mr-2"
                >
                  Edit Profile
                </button>
              </td>
            </tr>

            <tr v-if="filteredUsers.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-500">
                No users found matching current filters.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Prototype 4: Add / Edit User Modal -->
      <div v-if="dialogOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-xl shadow-2xl space-y-5">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              ➕ {{ editMode ? 'EDIT USER PROFILE' : 'ADD / CREATE NEW USER' }}
            </h3>
            <button @click="dialogOpen = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Full Name Khmer</label>
                <input v-model="form.name_kh" type="text" placeholder="ឈ្មោះជាភាសាខ្មែរ" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white" />
              </div>

              <div>
                <label class="block font-semibold text-slate-300 mb-1">Full Name English *</label>
                <input v-model="form.name" type="text" placeholder="John Doe" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Email *</label>
                <input v-model="form.email" type="email" placeholder="user@elms.edu" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white" />
              </div>

              <div>
                <label class="block font-semibold text-slate-300 mb-1">Phone Number</label>
                <input v-model="form.phone" type="text" placeholder="+855 12 345 678" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Role *</label>
                <select v-model="form.role" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                  <option value="student">Student</option>
                  <option value="teacher">Teacher</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div>
                <label class="block font-semibold text-slate-300 mb-1">Status *</label>
                <select v-model="form.status" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
            </div>

            <!-- Cascading Academic Selections -->
            <div class="p-4 bg-slate-800/40 rounded-2xl border border-slate-800 space-y-3">
              <div class="text-[11px] font-bold text-slate-400 uppercase">Academic Assignment</div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                  <label class="block text-[11px] font-medium text-slate-300 mb-1">Faculty</label>
                  <select v-model="form.faculty_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-2.5 py-1.5 text-white">
                    <option :value="null">Select Faculty</option>
                    <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="block text-[11px] font-medium text-slate-300 mb-1">Department</label>
                  <select v-model="form.department_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-2.5 py-1.5 text-white">
                    <option :value="null">Select Dept</option>
                    <option v-for="d in filteredDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="block text-[11px] font-medium text-slate-300 mb-1">Major</label>
                  <select v-model="form.major_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-2.5 py-1.5 text-white">
                    <option :value="null">Select Major</option>
                    <option v-for="m in filteredMajors" :key="m.id" :value="m.id">{{ m.name }}</option>
                  </select>
                </div>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 mb-1">
                Password <span v-if="editMode" class="text-slate-500 font-normal">(Leave blank to keep unchanged)</span>
              </label>
              <input v-model="form.password" type="password" placeholder="••••••••" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white" />
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
              <button type="button" @click="dialogOpen = false" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
              <button type="submit" :disabled="form.processing" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg">
                Save User
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
