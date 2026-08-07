export interface Stats {
  total_questions: number
  active_questions: number
  draft_questions: number
  courses_linked: number
  total_quizzes: number
  published_quizzes: number
  draft_quizzes: number
  scheduled_quizzes: number
  total_assignments: number
  pending_assignments: number
  graded_assignments: number
  overdue_assignments: number
  avg_pass_rate: number
  total_attempts: number
}

export interface QuestionItem {
  id: string
  question: string
  type: string
  type_label: string
  difficulty: string
  marks: number
  course: string
  module: string
  chapter: string
  status: string
  options?: string[]
  correct_answer?: string
  explanation?: string
  rubric?: string
  word_limit?: string
  pairs?: Array<{ left: string; right: string }>
  sample_solution?: string
  created_by: string
}

export interface QuizItem {
  id: number
  title: string
  type: string
  type_label: string
  badge_color?: string
  course: string
  module: string
  questions_count: number
  total_marks: number
  time_limit_minutes: number
  passing_score: number
  max_attempts?: number
  attempts_allowed?: number
  status: string
  status_label?: string
  attempts_count?: number
  avg_score?: number
  created_by: string
}

export interface AssignmentItem {
  id: string
  title: string
  course: string
  module: string
  chapter: string
  due_date: string
  submitted_count?: number
  submissions_count?: number
  total_students: number
  max_marks: number
  status: string
  status_label: string
  file_types: string
  created_by: string
}

export interface ResultItem {
  id: string
  student: string
  email: string
  avatar: string | null
  quiz: string
  type: string
  course: string
  score: number
  passing_score: number
  passed: boolean
  time_spent: string
  submitted_at: string
  graded_by: string
}
