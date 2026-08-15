<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  courseVideos?: Array<any>
  videos?: Array<any>
  selectedCourseId?: number | null
}>()

const searchQuery = ref('')
const selectedCourseFilter = ref<number | 'all'>('all')
const statusFilter = ref<'All' | 'ready' | 'processing' | 'uploading' | 'failed'>('All')
const isDragging = ref(false)

// Combined videos list from Eloquent models & legacy
const allVideos = computed(() => {
  const modelList = (props.courseVideos || []).map(v => ({
    id: v.id,
    title: v.title,
    course_id: v.course_id,
    course_title: v.course?.title || 'General Course',
    lesson_title: v.lesson?.title || 'Unassigned Lesson',
    duration: v.duration || '15:20',
    file_size: v.file_size || '120 MB',
    status: v.status || 'ready',
    cloudinary_url: v.cloudinary_url || '/storage/videos/sample.mp4',
    created_at: v.created_at ? new Date(v.created_at).toLocaleDateString() : 'Today',
    transcode_progress: v.transcode_progress ?? 100,
  }))

  return modelList
})

const filteredVideos = computed(() => {
  return allVideos.value.filter(v => {
    const matchSearch = v.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        v.course_title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCourse = selectedCourseFilter.value === 'all' || v.course_id == selectedCourseFilter.value
    const matchStatus = statusFilter.value === 'All' || v.status === statusFilter.value
    return matchSearch && matchCourse && matchStatus
  })
})

// Upload Video Form & Modal
const showUploadModal = ref(false)
const uploadProgress = ref(0)
const isUploading = ref(false)

const uploadForm = useForm({
  course_id: props.selectedCourseId || (props.courses?.[0]?.id ?? null),
  lesson_id: null as number | null,
  title: '',
  video: null as File | null,
  cloudinary_url: '',
  duration: '18:45',
})

// Video Player Modal
const activePlayingVideo = ref<any>(null)
const showPlayerModal = ref(false)

const openPlayer = (video: any) => {
  activePlayingVideo.value = video
  showPlayerModal.value = true
}

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    uploadForm.video = file
    if (!uploadForm.title) {
      uploadForm.title = file.name.replace(/\.[^/.]+$/, "")
    }
  }
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  if (e.dataTransfer && e.dataTransfer.files.length > 0) {
    const file = e.dataTransfer.files[0]
    uploadForm.video = file
    if (!uploadForm.title) {
      uploadForm.title = file.name.replace(/\.[^/.]+$/, "")
    }
    showUploadModal.value = true
  }
}

const submitUpload = () => {
  if (!uploadForm.course_id || !uploadForm.title) return
  isUploading.value = true
  uploadProgress.value = 15

  const progressInterval = setInterval(() => {
    if (uploadProgress.value < 90) {
      uploadProgress.value += 15
    }
  }, 200)

  uploadForm.post(`/teacher/courses/${uploadForm.course_id}/videos`, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      clearInterval(progressInterval)
      uploadProgress.value = 100
      setTimeout(() => {
        isUploading.value = false
        showUploadModal.value = false
        uploadForm.reset()
      }, 500)
    },
    onError: () => {
      clearInterval(progressInterval)
      isUploading.value = false
    }
  })
}

// Delete video
const deleteVideo = (video: any) => {
  if (!confirm(`Delete video "${video.title}"?`)) return
  router.delete(`/teacher/courses/videos/${video.id}`, {
    preserveScroll: true
  })
}

// Trigger webhook simulation / transcode ready
const triggerReady = (video: any) => {
  router.post(`/teacher/courses/videos/${video.id}/status`, {
    status: 'ready',
    transcode_progress: 100
  }, {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- UPLOAD DROPZONE BANNER (MP4, Max 2GB) -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="[
        'rounded-3xl border-2 border-dashed p-6 md:p-8 text-center transition-all cursor-pointer relative overflow-hidden',
        isDragging
          ? 'border-blue-500 bg-blue-500/10 scale-[1.01]'
          : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-gray-850 hover:bg-slate-100 dark:hover:bg-gray-800'
      ]"
      @click="showUploadModal = true"
    >
      <div class="max-w-md mx-auto space-y-3 pointer-events-none">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-3xl shadow-inner border border-blue-500/20">
          📹
        </div>
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
            Upload Video Lesson (Cloudinary Transcoding)
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Drag & drop MP4, MOV, or WEBM video here, or <span class="text-blue-500 font-bold underline">click to browse</span>
          </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-200/70 dark:bg-gray-700 text-[10px] font-bold text-slate-600 dark:text-slate-300">
          <span>Max File Size: 2GB</span>
          <span>•</span>
          <span>Adaptive Bitrate (1080p / 720p / 480p)</span>
        </div>
      </div>
    </div>

    <!-- CONTROLS & FILTER BAR -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3 flex-wrap">
        <!-- Status filter buttons -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-gray-700 p-1 rounded-xl">
          <button
            v-for="s in ['All', 'ready', 'processing', 'uploading'] as const"
            :key="s"
            @click="statusFilter = s"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-bold capitalize transition',
              statusFilter === s 
                ? 'bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400 shadow-sm'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'
            ]"
          >
            {{ s }}
          </button>
        </div>

        <!-- Course filter select -->
        <select
          v-model="selectedCourseFilter"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white font-semibold"
        >
          <option value="all">All Courses</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>
      </div>

      <div class="flex items-center gap-3">
        <div class="relative w-full md:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search videos by title..."
            class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
          <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
        </div>

        <button
          @click="showUploadModal = true"
          type="button"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition whitespace-nowrap cursor-pointer flex items-center gap-1.5"
        >
          <span>+</span>
          <span>Upload Video</span>
        </button>
      </div>
    </div>

    <!-- VIDEO ITEMS LIST -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
          <span>🎥 Video Library</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-600 font-extrabold">{{ filteredVideos.length }}</span>
        </h3>
        <span class="text-xs text-slate-400">Processing flow: Upload → Cloudinary transcode → Webhook Ready</span>
      </div>

      <div v-if="filteredVideos.length === 0" class="p-12 text-center text-slate-400 text-xs">
        <div class="text-4xl mb-2">🎬</div>
        <p class="font-bold text-slate-700 dark:text-slate-200">No videos match your criteria.</p>
        <p class="text-slate-400 mt-1">Upload an MP4 video or adjust your filters above.</p>
      </div>

      <div v-else class="divide-y divide-slate-100 dark:divide-gray-700">
        <div
          v-for="video in filteredVideos"
          :key="video.id"
          class="p-4 hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex items-start gap-4">
            <!-- Video Thumbnail / Play Icon -->
            <div 
              @click="openPlayer(video)"
              class="w-16 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center relative group/play cursor-pointer shadow shrink-0 overflow-hidden"
            >
              <span class="text-xl group-hover/play:scale-125 transition-transform">▶️</span>
              <div class="absolute bottom-1 right-1 text-[9px] bg-black/80 px-1 rounded text-slate-200 font-mono">
                {{ video.duration }}
              </div>
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-2.5 flex-wrap">
                <h4 
                  @click="openPlayer(video)"
                  class="font-bold text-sm text-slate-900 dark:text-white hover:text-blue-600 cursor-pointer"
                >
                  {{ video.title }}
                </h4>

                <!-- STATUS BADGE -->
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border flex items-center gap-1',
                    video.status === 'ready'
                      ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'
                      : video.status === 'processing'
                      ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20 animate-pulse'
                      : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'
                  ]"
                >
                  <span v-if="video.status === 'ready'">✓ Ready</span>
                  <span v-else-if="video.status === 'processing'">⚙️ Processing ({{ video.transcode_progress }}%)</span>
                  <span v-else>⬆️ Uploading ({{ video.transcode_progress }}%)</span>
                </span>
              </div>

              <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span>📚 {{ video.course_title }}</span>
                <span>•</span>
                <span>📦 {{ video.file_size }}</span>
                <span>•</span>
                <span>Uploaded: {{ video.created_at }}</span>
                <span>•</span>
                <span class="text-blue-500 font-mono text-[11px] truncate max-w-[200px]">{{ video.cloudinary_url }}</span>
              </div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-2 self-end md:self-center">
            <button
              v-if="video.status !== 'ready'"
              @click="triggerReady(video)"
              type="button"
              class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 font-bold text-[11px] hover:bg-amber-500/20 cursor-pointer"
              title="Simulate Cloudinary Webhook finish"
            >
              Simulate Ready
            </button>

            <button
              @click="openPlayer(video)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs transition cursor-pointer"
            >
              Preview
            </button>

            <button
              @click="deleteVideo(video)"
              type="button"
              class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition cursor-pointer"
              title="Delete Video"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- UPLOAD MODAL -->
    <div
      v-if="showUploadModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-gray-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <span>📹 Upload Video to Cloudinary</span>
          </h3>
          <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="submitUpload" class="space-y-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Course</label>
            <select
              v-model="uploadForm.course_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white font-medium"
            >
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Video Title</label>
            <input
              v-model="uploadForm.title"
              type="text"
              placeholder="e.g. 01_Introduction_to_C_Programming.mp4"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
              required
            />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Select Video File (MP4, Max 2GB)</label>
            <input
              type="file"
              accept="video/mp4,video/webm,video/mov,video/avi"
              @change="handleFileSelect"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white"
            />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 dark:text-slate-300">Or Cloudinary Video URL (Optional)</label>
            <input
              v-model="uploadForm.cloudinary_url"
              type="url"
              placeholder="https://res.cloudinary.com/.../video.mp4"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-xl text-slate-900 dark:text-white"
            />
          </div>

          <!-- Progress Bar -->
          <div v-if="isUploading" class="space-y-1 pt-2">
            <div class="flex justify-between text-[11px] font-bold text-blue-600 dark:text-blue-400">
              <span>Uploading & Transcoding...</span>
              <span>{{ uploadProgress }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
              <div class="bg-blue-600 h-full transition-all duration-200" :style="{ width: uploadProgress + '%' }"></div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
            <button
              type="button"
              @click="showUploadModal = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isUploading"
              class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-md transition disabled:opacity-50"
            >
              {{ isUploading ? 'Uploading...' : 'Start Upload' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- PLAYER PREVIEW MODAL -->
    <div
      v-if="showPlayerModal && activePlayingVideo"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
      <div class="bg-slate-900 text-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-700 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h3 class="font-extrabold text-base">{{ activePlayingVideo.title }}</h3>
            <p class="text-xs text-slate-400">{{ activePlayingVideo.course_title }} • Duration: {{ activePlayingVideo.duration }}</p>
          </div>
          <button @click="showPlayerModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="rounded-2xl overflow-hidden bg-black aspect-video flex items-center justify-center">
          <video
            controls
            autoplay
            class="w-full h-full object-contain"
            :src="activePlayingVideo.cloudinary_url"
          >
            Your browser does not support HTML5 video player.
          </video>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
          <span>Cloudinary CDN Optimized Delivery</span>
          <button
            @click="showPlayerModal = false"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold"
          >
            Close Player
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
