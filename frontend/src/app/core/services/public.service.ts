import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Accommodation } from '../models/accommodation.model';
import { AvailabilityCalendar } from '../models/availability-calendar.model';
import { Review } from '../models/review.model';
import { Media } from '../models/media.model';
import { Owner } from '../models/owner.model';

export interface ApiResponse<T> {
  data: T;
  links?: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta?: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

@Injectable({
  providedIn: 'root'
})
export class PublicService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  // ========== ALOJAMIENTOS ==========
  
  getAccommodations(page: number = 1): Observable<ApiResponse<Accommodation[]>> {
    return this.http.get<ApiResponse<Accommodation[]>>(`${this.apiUrl}/accommodations/public?page=${page}`);
  }

  getAccommodation(id: number): Observable<ApiResponse<Accommodation>> {
    return this.http.get<ApiResponse<Accommodation>>(`${this.apiUrl}/accommodations/${id}/public`);
  }

    searchAccommodations(params: any): Observable<ApiResponse<Accommodation[]>> {
    return this.http.get<ApiResponse<Accommodation[]>>(`${this.apiUrl}/accommodations/public`, { params });
  }

  // ========== DISPONIBILIDAD ==========
  
  getAvailability(accommodationId: number): Observable<ApiResponse<AvailabilityCalendar[]>> {
    return this.http.get<ApiResponse<AvailabilityCalendar[]>>(`${this.apiUrl}/availability/public/${accommodationId}`);
  }

  checkAvailability(accommodationId: number, checkIn: string, checkOut: string): Observable<{ available: boolean; price: number; conflicts?: string[] }> {
    return this.http.post<{ available: boolean; price: number; conflicts?: string[] }>(`${this.apiUrl}/availability/check`, {
      accommodation_id: accommodationId,
      check_in: checkIn,
      check_out: checkOut
    });
  }

  // ========== RESEÑAS ==========
  
  getReviews(accommodationId: number, page: number = 1): Observable<ApiResponse<Review[]>> {
    return this.http.get<ApiResponse<Review[]>>(`${this.apiUrl}/reviews/public/${accommodationId}?page=${page}`);
  }

  createReview(accommodationId: number, review: { rating: number; comment: string }): Observable<ApiResponse<Review>> {
    return this.http.post<ApiResponse<Review>>(`${this.apiUrl}/reviews`, {
      accommodation_id: accommodationId,
      ...review
    });
  }
}