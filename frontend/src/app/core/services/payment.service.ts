import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { 
  Payment, 
  PaymentListResponse, 
  PaymentResponse 
} from '../models/payment.model';

@Injectable({
  providedIn: 'root'
})
export class PaymentService {
  private apiUrl = `${environment.apiUrl}/payments`;

  constructor(private http: HttpClient) {}

  // Obtener todos los pagos (con paginación)
  getAll(page: number = 1): Observable<PaymentListResponse> {
    return this.http.get<PaymentListResponse>(`${this.apiUrl}?page=${page}`);
  }

  // Obtener un pago por ID
  getOne(id: number): Observable<PaymentResponse> {
    return this.http.get<PaymentResponse>(`${this.apiUrl}/${id}`);
  }

  // Crear nuevo pago
  create(data: Partial<Payment>): Observable<PaymentResponse> {
    return this.http.post<PaymentResponse>(this.apiUrl, data);
  }

  // Actualizar pago
  update(id: number, data: Partial<Payment>): Observable<PaymentResponse> {
    return this.http.put<PaymentResponse>(`${this.apiUrl}/${id}`, data);
  }

  // Eliminar pago
  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  // Obtener pagos por reserva
  getByBooking(bookingId: number): Observable<PaymentListResponse> {
    return this.http.get<PaymentListResponse>(`${this.apiUrl}?booking_id=${bookingId}`);
  }

  // Marcar pago como completado
  markAsCompleted(id: number, transactionId?: string): Observable<PaymentResponse> {
    return this.http.patch<PaymentResponse>(`${this.apiUrl}/${id}/complete`, { transaction_id: transactionId });
  }

  // Marcar pago como reembolsado
  markAsRefunded(id: number, reason?: string): Observable<PaymentResponse> {
    return this.http.patch<PaymentResponse>(`${this.apiUrl}/${id}/refund`, { reason });
  }

  getMonthlyRevenue(): Observable<number[]> {
    return this.http.get<number[]>(`${this.apiUrl}/monthly-revenue`);
  }

  getTotalRevenue(): Observable<{ total: number }> {
    return this.http.get<{ total: number }>(`${this.apiUrl}/total-revenue`);
  }

  getRevenueComparison(): Observable<{ percentage: number; trend: string; current_year: number; previous_year: number }> {
    return this.http.get<{ percentage: number; trend: string; current_year: number; previous_year: number }>(`${this.apiUrl}/revenue-comparison`);
  }

  getMonthlyComparison(): Observable<{ percentage: number; trend: string; current_month: number; previous_month: number }> {
    return this.http.get<{ percentage: number; trend: string; current_month: number; previous_month: number }>(`${this.apiUrl}/monthly-comparison`);
  }
}