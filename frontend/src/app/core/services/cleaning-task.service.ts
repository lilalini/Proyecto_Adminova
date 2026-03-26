import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface CleaningTask {
  id: number;
  accommodation_id: number;
  accommodation_name?: string;
  booking_id?: number;
  scheduled_date: string;  // ← NOTA: se llama scheduled_date, no date
  status: 'pending' | 'in_progress' | 'completed' | 'verified';
  priority?: string;
  assigned_to_user_id?: number;
  assigned_to?: {
    id: number;
    name: string;
  };
  created_by_user_id?: number;
  notes?: string;
  verified_at?: string;
  created_at: string;
  updated_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class CleaningTaskService {
  private apiUrl = `${environment.apiUrl}/cleaning-tasks`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<{ data: CleaningTask[] }> {
    return this.http.get<{ data: CleaningTask[] }>(this.apiUrl, { params });
  }

  getTodayTasks(): Observable<{ data: CleaningTask[] }> {
    const today = new Date().toISOString().split('T')[0];
    return this.http.get<{ data: CleaningTask[] }>(`${this.apiUrl}?from=${today}&to=${today}`);
  }

  update(id: number, data: Partial<CleaningTask>): Observable<{ data: CleaningTask }> {
    return this.http.put<{ data: CleaningTask }>(`${this.apiUrl}/${id}`, data);
  }

  verify(id: number): Observable<{ data: CleaningTask }> {
    return this.http.post<{ data: CleaningTask }>(`${this.apiUrl}/${id}/verify`, {});
  }

  assign(id: number, userId: number): Observable<{ data: CleaningTask }> {
    return this.http.post<{ data: CleaningTask }>(`${this.apiUrl}/${id}/assign`, { assigned_to_user_id: userId });
  }
}