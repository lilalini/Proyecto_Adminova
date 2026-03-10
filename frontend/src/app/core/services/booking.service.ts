import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Booking {
  id: number;
  accommodation_id: number;
  guest_id: number;
  check_in: string;
  check_out: string;
  total_price: number;
  status: string;
  created_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class BookingService {
  private apiUrl = `${environment.apiUrl}/bookings`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<{ data: Booking[] }> {
    return this.http.get<{ data: Booking[] }>(this.apiUrl);
  }
}