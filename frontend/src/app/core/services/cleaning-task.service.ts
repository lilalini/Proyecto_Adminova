import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface CleaningTask {
  id: number;
  accommodation_id: number;
  booking_id: number;
  assigned_to_user_id: number | null;
  created_by_user_id: number;
  task_type: string;
  priority: 'low' | 'medium' | 'high' | 'urgent';
  title: string;
  description: string | null;
  checklist: string[] | null;
  scheduled_date: string;
  completed_at: string | null;
  duration_minutes: number | null;
  photos: string[] | null;
  notes: string | null;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled' | 'verified';
  verified_by_user_id: number | null;
  verified_at: string | null;
  accommodation?: {
    id: number;
    title: string;
  };
  created_at: string;
  updated_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class CleaningTaskService {
  private apiUrl = `${environment.apiUrl}/cleaning-tasks`;

  constructor(private http: HttpClient) {}

  // Listar todas las tareas
  getAll(): Observable<{ data: CleaningTask[] }> {
    return this.http.get<{ data: CleaningTask[] }>(this.apiUrl);
  }

  // Obtener una tarea específica
  get(id: number): Observable<{ data: CleaningTask }> {
    return this.http.get<{ data: CleaningTask }>(`${this.apiUrl}/${id}`);
  }

  // Crear nueva tarea
  create(data: Partial<CleaningTask>): Observable<{ data: CleaningTask }> {
    return this.http.post<{ data: CleaningTask }>(this.apiUrl, data);
  }

  // Actualizar tarea
  update(id: number, data: Partial<CleaningTask>): Observable<{ data: CleaningTask }> {
    return this.http.put<{ data: CleaningTask }>(`${this.apiUrl}/${id}`, data);
  }

  // Eliminar tarea
  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  // Asignar tarea a un staff
  assign(id: number, userId: number): Observable<{ data: CleaningTask }> {
    return this.http.post<{ data: CleaningTask }>(`${this.apiUrl}/${id}/assign`, { user_id: userId });
  }

  // Verificar tarea (supervisor)
  verify(id: number): Observable<{ data: CleaningTask }> {
    return this.http.post<{ data: CleaningTask }>(`${this.apiUrl}/${id}/verify`, {});
  }

  // Obtener tareas de hoy (filtro por scheduled_date)
  getTodayTasks(): Observable<{ data: CleaningTask[] }> {
    const today = new Date().toISOString().split('T')[0];
    return this.http.get<{ data: CleaningTask[] }>(`${this.apiUrl}?scheduled_date=${today}`);
  }

  // Obtener tareas pendientes
  getPendingTasks(): Observable<{ data: CleaningTask[] }> {
    return this.http.get<{ data: CleaningTask[] }>(`${this.apiUrl}?status=pending`);
  }
}