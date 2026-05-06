// notification.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private apiUrl = `${environment.apiUrl}/notifications`;

  constructor(private http: HttpClient) {}

  getAll(page: number = 1, perPage: number = 5): Observable<any> {
    return this.http.get(`${this.apiUrl}?page=${page}&per_page=${perPage}`);
  }

  markAsRead(id: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/${id}/mark-read`, {});
  }

  markAllAsRead(): Observable<any> {
    return this.http.get(`${this.apiUrl}/mark-all-read`);
  }
}