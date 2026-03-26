import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { 
  AvailabilityCalendar, 
  AvailabilityCalendarListResponse,
  AvailabilityCheckRequest,
  AvailabilityCheckResponse 
} from '../models/availability-calendar.model';

@Injectable({
  providedIn: 'root'
})
export class AvailabilityService {
  private apiUrl = `${environment.apiUrl}/availability-calendars`;

  constructor(private http: HttpClient) {}

  // Obtener disponibilidad por alojamiento
  getByAccommodation(accommodationId: number): Observable<AvailabilityCalendarListResponse> {
    return this.http.get<AvailabilityCalendarListResponse>(`${this.apiUrl}?accommodation_id=${accommodationId}`);
  }

  // Verificar disponibilidad en un rango de fechas
  checkAvailability(accommodationId: number, checkIn: string, checkOut: string): Observable<AvailabilityCheckResponse> {
    const request: AvailabilityCheckRequest = {
      accommodation_id: accommodationId,
      check_in: checkIn,
      check_out: checkOut
    };
    return this.http.post<AvailabilityCheckResponse>(`${this.apiUrl}/check`, request);
  }

  // Crear o actualizar disponibilidad
  updateRange(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/update-range`, data);
  }

  // Obtener disponibilidad por rango de fechas
  getByDateRange(accommodationId: number, startDate: string, endDate: string): Observable<AvailabilityCalendarListResponse> {
    return this.http.get<AvailabilityCalendarListResponse>(
      `${this.apiUrl}?accommodation_id=${accommodationId}&start=${startDate}&end=${endDate}`
    );
  }
}