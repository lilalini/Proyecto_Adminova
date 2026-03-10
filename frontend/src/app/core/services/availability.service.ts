import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Availability {
  id: number;
  accommodation_id: number;
  date: string;
  price: number;
  available: boolean;
  min_stay: number;
}

@Injectable({
  providedIn: 'root'
})
export class AvailabilityService {
  private apiUrl = `${environment.apiUrl}/availability-calendars`;

  constructor(private http: HttpClient) {}

  getByAccommodation(accommodationId: number): Observable<{ data: Availability[] }> {
    return this.http.get<{ data: Availability[] }>(`${this.apiUrl}?accommodation_id=${accommodationId}`);
  }

  checkAvailability(accommodationId: number, checkIn: string, checkOut: string): Observable<{ available: boolean; price: number }> {
    return this.http.post<{ available: boolean; price: number }>(`${this.apiUrl}/check`, {
      accommodation_id: accommodationId,
      check_in: checkIn,
      check_out: checkOut
    });
  }
}