import { openDB, type DBSchema, type IDBPDatabase } from 'idb'

interface LMSDB extends DBSchema {
  courses:   { key: number; value: any }
  lessons:   { key: number; value: any; indexes: { 'by-course': number } }
  contents:  { key: string; value: { url: string; blob: Blob } }
  quizzes:   { key: number; value: any; indexes: { 'by-course': number } }
  progress:  { key: string; value: any }
  syncQueue: { key: string; value: { id: string; type: string; payload: any } }
  downloads: { key: number; value: { lesson_id: number; file: string } }
  academicCache: { key: string; value: { key: string; data: any; cachedAt: number } }
}

let _db: IDBPDatabase<LMSDB> | null = null
export async function db() {
  if (_db) return _db
  _db = await openDB<LMSDB>('elms-offline', 2, {
    upgrade(d, oldVersion) {
      if (oldVersion < 1) {
        d.createObjectStore('courses', { keyPath: 'id' })
        d.createObjectStore('lessons', { keyPath: 'id' }).createIndex('by-course', 'course_id')
        d.createObjectStore('contents', { keyPath: 'url' })
        d.createObjectStore('quizzes', { keyPath: 'id' }).createIndex('by-course', 'course_id')
        d.createObjectStore('progress', { keyPath: 'key' })
        d.createObjectStore('syncQueue', { keyPath: 'id' })
        d.createObjectStore('downloads', { keyPath: 'lesson_id' })
      }
      if (oldVersion < 2) {
        if (!d.objectStoreNames.contains('academicCache')) {
          d.createObjectStore('academicCache', { keyPath: 'key' })
        }
      }
    },
  })
  return _db
}
