export interface InsightItem {
  id?: string
  text: string
  actionText: string
  targetTab?: string
}

export interface OverviewMetrics {
  student_engagement: { value: number; change: number; status: string }
  teacher_performance: { value: number; change: number; status: string }
  course_completion: { value: number; change: number; status: string }
  quiz_pass_rate: { value: number; change: number; status: string }
  revenue_growth: { value: string; change: number; status: string }
  cert_issued: { value: number; change: number; status: string; count?: number }
  trends: {
    enrollment: number
    revenue: number
    completion: number
    pass_rate: number
  }
  insights: Array<string | InsightItem>
}

export interface StudentAnalyticsData {
  kpis: {
    total: number
    active: number
    active_percent: number
    retention: number
    at_risk: number
    at_risk_percent: number
  }
  enrollment_trend: Array<{ month: string; count: number }>
  by_major: Array<{ major: string; count: number; percent: number }>
  by_gender: { male_percent: number; female_percent: number }
  engagement_distribution: Array<{ label: string; range: string; count: number; percent: number; color: string }>
  top_students: Array<{ rank: number; name: string; major: string; progress: number; avg_score: number; hours: string }>
  retention_funnel: Array<{ stage: string; percent: number; count: number }>
}

export interface TeacherAnalyticsData {
  kpis: {
    total: number
    avg_rating: number
    avg_courses: number
    avg_monthly_earn: string
  }
  top_teachers: Array<{ rank: number; name: string; courses: number; students: number; rating: number; completion: number; revenue: string }>
  rating_distribution: Array<{ stars: string; count: number; percent: number }>
  matrix: Array<{ name: string; rating: number; completion: number; zone: 'star' | 'normal' | 'review' }>
  needing_attention: Array<{ name: string; course: string; rating: number; completion: number; reason: string }>
  ai_insights: string[]
}

export interface CourseAnalyticsData {
  kpis: {
    total: number
    active: number
    avg_completion: number
    total_revenue: string
  }
  top_popular: Array<{ rank: number; title: string; teacher: string; enrolled: number; completed_percent: number; rating: number; revenue: string }>
  completion_heatmap: Array<{
    course: string
    modules: Array<{ mod: string; score: number; flag?: string }>
  }>
  revenue_by_course: Array<{ course: string; revenue: string; amount: number }>
  needing_attention: Array<{ course: string; enrolled: number; completed_percent: number; rating: number; reason: string }>
  ai_insights: string[]
}

export interface QuizAnalyticsData {
  kpis: {
    total_quizzes: number
    pass_rate: number
    avg_score: number
    avg_time: string
  }
  score_distribution: Array<{ range: string; count: number; percent: number; flag?: boolean }>
  pass_by_type: Array<{ type: string; pass_rate: number; note?: string }>
  difficult_quizzes: Array<{ name: string; course: string; attempts: number; pass_rate: number; avg_score: number; status: 'danger' | 'warning' }>
  difficult_questions: Array<{ id: string; preview: string; type: string; correct_rate: number; difficulty: string }>
  improvement: Array<{ course: string; pre_test: number; post_test: number; growth: number }>
  ai_insights: string[]
}

export interface PaymentAnalyticsData {
  kpis: {
    gross_revenue: string
    net_revenue: string
    refund_amount: string
    refund_percent: number
    growth: number
    paid_txn: number
    pending_txn: number
    failed_txn: number
    failed_percent: number
    free_enrolls: number
  }
  revenue_trend_12m: Array<{ month: string; amount: number }>
  revenue_by_major: Array<{ major: string; amount: string; percent: number }>
  revenue_by_mode: Array<{ mode: string; amount: string; percent: number }>
  conversion_funnel: Array<{ stage: string; percent: number; count: number }>
  payment_methods: Array<{ name: string; percent: number; count: number }>
  refund_reasons: Array<{ reason: string; percent: number }>
}
