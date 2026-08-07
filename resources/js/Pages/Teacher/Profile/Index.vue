<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  profile: {
    name: string
    email: string
    major: string
    department: string
    bio: string
    phone: string
    language: string
    email_notifications: boolean
    app_notifications: boolean
  }
}>()

const showEditModal = ref(false)
const showPasswordModal = ref(false)

const profileForm = useForm({
  name: props.profile?.name || 'Mr. Sophea',
  email: props.profile?.email || 'sophea@lms.com',
  bio: props.profile?.bio || 'Instructor with 5+ years experience in programming.',
  phone: props.profile?.phone || '+855 12 345 678',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submitProfile = () => {
  profileForm.post(route('teacher.profile.update'), {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

const submitPassword = () => {
  passwordForm.post(route('teacher.profile.password'), {
    onSuccess: () => {
      showPasswordModal.value = false
      passwordForm.reset()
    }
  })
}
</script>

<template>
  <TeacherLayout title="My Profile">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Profile Header Card -->
      <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col sm:flex-row items-center gap-6">
        <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold text-3xl flex items-center justify-center shadow-lg shadow-blue-500/30 flex-shrink-0">
          {{ profile?.name ? profile.name.charAt(0) : 'S' }}
        </div>

        <div class="flex-1 text-center sm:text-left space-y-1">
          <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ profile?.name || 'Mr. Sophea' }}</h1>
          <p class="text-xs font-semibold text-blue-600 dark:text-blue-400">Instructor • {{ profile?.department }} • {{ profile?.major }}</p>
          <p class="text-xs text-slate-500">{{ profile?.email }} • {{ profile?.phone }}</p>
          <p class="text-xs text-slate-600 dark:text-slate-300 pt-2 italic">"{{ profile?.bio }}"</p>
        </div>

        <div class="flex flex-col gap-2 w-full sm:w-auto">
          <button
            @click="showEditModal = true"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow transition"
          >
            ✏️ Edit Profile
          </button>
          <button
            @click="showPasswordModal = true"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold"
          >
            🔒 Change Password
          </button>
        </div>
      </div>

      <!-- Settings Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Notification Preferences -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
          <h2 class="text-base font-bold text-slate-800 dark:text-white">🔔 Notification Settings</h2>
          <div class="space-y-3 text-xs">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-bold text-slate-700 dark:text-slate-200">Email Notifications</p>
                <p class="text-slate-400">Receive student questions & system announcements via email</p>
              </div>
              <input type="checkbox" checked class="w-4 h-4 accent-blue-600" />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-bold text-slate-700 dark:text-slate-200">In-App Push Alerts</p>
                <p class="text-slate-400">Receive real-time popups when student submits quiz</p>
              </div>
              <input type="checkbox" checked class="w-4 h-4 accent-blue-600" />
            </div>
          </div>
        </div>

        <!-- Language & Display Settings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
          <h2 class="text-base font-bold text-slate-800 dark:text-white">🌐 Language & Display</h2>
          <div class="space-y-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">System Language</label>
              <select class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-semibold">
                <option value="km">🇰🇭 Khmer (ភាសាខ្មែរ)</option>
                <option value="en">🇬🇧 English</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Profile Details</h3>
          <div class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold mb-1">Full Name</label>
              <input v-model="profileForm.name" type="text" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Email</label>
              <input v-model="profileForm.email" type="email" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Bio</label>
              <textarea v-model="profileForm.bio" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button @click="showEditModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
            <button @click="submitProfile" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Save Changes</button>
          </div>
        </div>
      </div>

      <!-- Password Modal -->
      <div v-if="showPasswordModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">Change Account Password</h3>
          <div class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold mb-1">Current Password</label>
              <input v-model="passwordForm.current_password" type="password" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">New Password</label>
              <input v-model="passwordForm.password" type="password" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button @click="showPasswordModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
            <button @click="submitPassword" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Update Password</button>
          </div>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>
