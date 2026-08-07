export interface AiRuleItem {
  id: number
  code: string
  name: string
  category: 'Performance' | 'Engagement' | 'Completion' | 'Behavior'
  condition_text: string
  action_text: string
  priority: 'P1' | 'P2' | 'P3' | 'P4' | 'P5'
  status: 'active' | 'disabled'
  fired_count: number
  cooldown_hours: number
  max_fires: number
  notify_student: boolean
  notify_teacher: boolean
  message_template?: string
}

export interface WeakTopicItem {
  id: number
  topic: string
  course: string
  affected_students: number
  avg_score: number
  trend: 'down' | 'flat' | 'up'
  auto_action: string
  remedial_content: string[]
  status: 'active' | 'disabled'
}

export interface WeakTopicDetectionConfig {
  threshold_percent: number
  min_questions: number
  consecutive_failures: number
  auto_remedial_task: boolean
  show_focus_badge: boolean
}

export interface LearningPathNode {
  id: number
  step_number: number
  title: string
  status: 'completed' | 'in_progress' | 'next' | 'locked' | 'decision'
  remedial_flag?: boolean
  branches?: Array<{
    condition: string
    target_node: string
    action_note: string
  }>
}

export interface RecommendationLogItem {
  id: number
  timestamp: string
  student_name: string
  student_id: string
  rule_code: string
  rule_name: string
  recommendation_text: string
  status: 'Done' | 'Accepted' | 'Sent' | 'Ignored'
}

export interface EffectivenessMetric {
  rule_type: string
  acceptance_rate: number
  status_tag: 'Excellent' | 'Good' | 'Fair' | 'Needs Tuning'
}

export interface AiConfigSettings {
  engine_type: 'rule_based' | 'ml' | 'hybrid'
  ml_model: string
  confidence_threshold: number
  max_rec_per_day: number
  reevaluation_hours: number
  sandbox_mode: boolean
  ai_provider: string
  api_key_masked: string
  api_connected: boolean
  temperature: number
  timeout_seconds: number
  personalization_level: 'basic' | 'standard' | 'advanced'
  use_quiz_scores: boolean
  use_learning_time: boolean
  use_weak_topics: boolean
  use_idle_behavior: boolean
  use_content_prefs: boolean
  use_peer_comparison: boolean
  privacy_learning_only: boolean
  privacy_anonymize: boolean
  data_retention_days: number
  transparency_show_why: boolean
  allow_student_dismiss: boolean
  idle_trigger_days: number
  send_email: boolean
  send_in_app: boolean
  send_sms: boolean
  best_send_time: string
  max_alerts_per_week: number
}

export interface StudentRecommendationPreview {
  student_name: string
  current_path_step: string
  weak_topics: Array<{ topic: string; score: number }>
  ai_card: {
    title: string
    why_text: string
    next_action: string
  }
}
