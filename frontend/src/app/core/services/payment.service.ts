import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Payment {
  id: number;
  booking_id: number;
  amount: number;
  payment_method: string;
  status: string;
  created_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class PaymentService {
  private apiUrl = `${environment.apiUrl}/payments`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<{ data: Payment[] }> {
    return this.http.get<{ data: Payment[] }>(this.apiUrl);
  }
}