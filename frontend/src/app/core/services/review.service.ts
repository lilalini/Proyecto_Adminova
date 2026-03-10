import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Review {
  id: number;
  accommodation_id: number;
  guest_id: number;
  guest_name?: string;
  rating: number;
  comment: string;
  created_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class ReviewService {
  private apiUrl = `${environment.apiUrl}/reviews`;

  constructor(private http: HttpClient) {}

  getByAccommodation(accommodationId: number): Observable<{ data: Review[] }> {
    return this.http.get<{ data: Review[] }>(`${this.apiUrl}?accommodation_id=${accommodationId}`);
  }

  create(review: Partial<Review>): Observable<{ data: Review }> {
    return this.http.post<{ data: Review }>(this.apiUrl, review);
  }
}