import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Booking, BookingListResponse, BookingResponse } from '../models/booking.model';

@Injectable({
  providedIn: 'root'
})
export class BookingService {
  private apiUrl = `${environment.apiUrl}/bookings`;

  constructor(private http: HttpClient) {}

  // Obtener todas las reservas (con paginación)
  getAll(page: number = 1): Observable<BookingListResponse> {
    return this.http.get<BookingListResponse>(`${this.apiUrl}?page=${page}`);
  }

  // Obtener una reserva por ID
  getOne(id: number): Observable<BookingResponse> {
    return this.http.get<BookingResponse>(`${this.apiUrl}/${id}`);
  }

  // Crear nueva reserva
/*  create(data: Partial<Booking>): Observable<BookingResponse> {
    return this.http.post<BookingResponse>(this.apiUrl, data);
  }*/

  create<T = BookingResponse>(data: Partial<Booking>): Observable<T> {
    return this.http.post<T>(this.apiUrl, data);
  }

  // Actualizar reserva
  update(id: number, data: Partial<Booking>): Observable<BookingResponse> {
    return this.http.put<BookingResponse>(`${this.apiUrl}/${id}`, data);
  }

  // Eliminar reserva
  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  // Obtener reservas por alojamiento (útil para calendarios)
  getByAccommodation(accommodationId: number): Observable<BookingListResponse> {
    return this.http.get<BookingListResponse>(`${this.apiUrl}?accommodation_id=${accommodationId}`);
  }

  // Obtener reservas por huésped
  getByGuest(guestId: number): Observable<BookingListResponse> {
    return this.http.get<BookingListResponse>(`${this.apiUrl}?guest_id=${guestId}`);
  }

  getMyBookings(): Observable<BookingListResponse> {
    return this.http.get<BookingListResponse>(`${this.apiUrl}/my`);
  }

    confirmPayment(bookingId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/${bookingId}/confirm-payment`, {});
  }

    cancelBooking(bookingId: number, reason?: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/${bookingId}/cancel`, { reason });
  }

}


