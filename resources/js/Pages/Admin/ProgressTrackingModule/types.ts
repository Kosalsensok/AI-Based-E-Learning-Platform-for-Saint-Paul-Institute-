export interface StudentProgressProfile {
  id: string
  name: string
  avatar?: string
  major: string
  course: string
  overall_progress: number
  learning_time: string
  quiz_avg: number
  assignments_submitted: number
  assignments_total: number
  cert_status: 'Eligible' | 'Pending' | 'Granted'
  modules: Array<{
    id: number
    title: string
    progress: number
    status: 'Completed' | 'In Progress' | 'Locked'
    chapters: Array<{
      id: number
      title: string
      status: 'completed' | 'in_progress' | 'locked'
      video_watched_percent: number
      pdf_opened: boolean
      slide_read: boolean
      quiz_score?: number
    }>
  }>
}

export interface CourseCompletionItem {
  id: number
  title: string
  teacher: string
  enrolled: number
  completed: number
  completed_percent: number
  in_progress: number
  in_progress_percent: number
  avg_score: number
  major: string
  semester: string
}

export interface ModuleAnalyticsItem {
  id: number
  title: string
  course: string
  completed_percent: number
  status_color: 'green' | 'yellow' | 'red'
  avg_days_to_complete: number
  expected_days: number
  dropout_rate: number
  difficulty_flag: boolean
}

export interface LearningTimeData {
  daily_avg_mins: number
  weekly_avg_hours: string
  video_split_percent: number
  document_split_percent: number
  daily_hours: Array<{ day: string; hours: number }>
  peak_hours: string
  top_active_students: Array<{
    name: string
    major: string
    time_spent: string
    completed_items: number
    xp_points: number
  }>
}

export interface AtRiskStudentItem {
  id: string
  name: string
  avatar?: string
  course: string
  major: string
  risk_level: 'high' | 'medium' | 'safe'
  risk_factors: string[]
  idle_days: number
  quiz_avg: number
  last_active: string
}

export interface WeeklyProgressMetrics {
  wau: { current: number; growth: number }
  study_hours: { current: string; growth: number }
  videos_watched: { current: number; growth: number }
  quizzes_taken: { current: number; growth: number }
  heatmap: Array<{
    hour: string
    days: Array<{ day: string; level: 'low' | 'med' | 'high' }>
  }>
  ai_insight: string
}

export interface MajorProgressItem {
  id: number
  major: string
  faculty: string
  enrolled: number
  active_this_week: number
  active_percent: number
  at_risk_count: number
  at_risk_percent: number
  avg_progress: number
}
