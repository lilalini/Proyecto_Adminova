import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { 
  Guest, 
  GuestListResponse, 
  GuestResponse 
} from '../models/guest.model';

@Injectable({
  providedIn: 'root'
})
export class GuestService {
  private apiUrl = `${environment.apiUrl}/guests`;

  constructor(private http: HttpClient) {}

  // Obtener todos los huéspedes (con paginación)
  getAll(page: number = 1): Observable<GuestListResponse> {
    return this.http.get<GuestListResponse>(`${this.apiUrl}?page=${page}`);
  }

  // Obtener un huésped por ID
  getOne(id: number): Observable<GuestResponse> {
    return this.http.get<GuestResponse>(`${this.apiUrl}/${id}`);
  }

  // Crear nuevo huésped
  create(data: Partial<Guest>): Observable<GuestResponse> {
    return this.http.post<GuestResponse>(this.apiUrl, data);
  }

  // Actualizar huésped
  update(id: number, data: Partial<Guest>): Observable<GuestResponse> {
    return this.http.put<GuestResponse>(`${this.apiUrl}/${id}`, data);
  }

  // Eliminar huésped
  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  // Buscar huésped por email
  getByEmail(email: string): Observable<GuestListResponse> {
    return this.http.get<GuestListResponse>(`${this.apiUrl}?email=${email}`);
  }

  // Obtener huéspedes de una reserva específica (si existe relación)
  getByBooking(bookingId: number): Observable<GuestListResponse> {
    return this.http.get<GuestListResponse>(`${this.apiUrl}?booking_id=${bookingId}`);
  }

    getByUserId(userId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/by-user/${userId}`);
  }

    isProfileComplete(userId: number): Observable<{ complete: boolean; missing_fields: string[] }> {
      return this.http.get<{ complete: boolean; missing_fields: string[] }>(`${this.apiUrl}/profile-complete/${userId}`);
  }

  updateByUserId(userId: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/by-user/${userId}`, data);
  }
}