import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface LoyaltyPoint {
  id: number;
  points: number;
  type: 'earned' | 'redeemed' | 'expired' | 'adjusted';
  description: string;
  created_at: string;
  expiry_date: string | null;
}

export interface LoyaltyPointResponse {
  data: LoyaltyPoint[];
  meta?: {
    current_page: number;
    last_page: number;
    total: number;
  };
}

@Injectable({
  providedIn: 'root'
})
export class LoyaltyPointService {
  private apiUrl = `${environment.apiUrl}/loyalty-points`;

  constructor(private http: HttpClient) {}

  getBalance(guestId: number): Observable<{ guest_id: number; balance: number }> {
    return this.http.get<{ guest_id: number; balance: number }>(`${this.apiUrl}/balance/${guestId}`);
  }

  getHistory(guestId: number, page: number = 1): Observable<LoyaltyPointResponse> {
    return this.http.get<LoyaltyPointResponse>(`${this.apiUrl}?guest_id=${guestId}&page=${page}`);
  }

  getGuestByUserId(userId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/guests/by-user/${userId}`);
  }
}