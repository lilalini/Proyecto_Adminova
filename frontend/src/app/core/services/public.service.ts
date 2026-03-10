import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

// ==================== INTERFACES ====================

export interface Owner {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
}

export interface Media {
  id: number;
  url: string;
  type: string;
  is_primary: boolean;
}

export interface Review {
  id: number;
  guest_name: string;
  rating: number;
  comment: string;
  created_at: string;
}

export interface Availability {
  id: number;
  date: string;
  price: number;
  available: boolean;
  min_stay: number;
}

export interface Accommodation {
  id: number;
  title: string;
  slug: string;
  description: string;
  base_price: number;
  cleaning_fee?: number;
  security_deposit?: number;
  bedrooms: number;
  bathrooms: number;
  max_guests: number;
  size_m2: number;
  address: string;
  city: string;
  country: string;
  latitude?: string;
  longitude?: string;
  amenities?: string[];
  status: 'published' | 'draft' | 'inactive';
  owner?: Owner;
  media?: Media[];
  reviews?: Review[];
  created_at: string;
  updated_at: string;
}

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

// ==================== SERVICIO ====================

@Injectable({
  providedIn: 'root'
})
export class PublicService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  // ========== ALOJAMIENTOS ==========
  
  getAccommodations(): Observable<ApiResponse<Accommodation[]>> {
    return this.http.get<ApiResponse<Accommodation[]>>(`${this.apiUrl}/accommodations/public`);
  }

  getAccommodation(id: number): Observable<ApiResponse<Accommodation>> {
    return this.http.get<ApiResponse<Accommodation>>(`${this.apiUrl}/accommodations/${id}/public`);
  }

  // ========== DISPONIBILIDAD ==========
  
  getAvailability(accommodationId: number): Observable<ApiResponse<Availability[]>> {
    return this.http.get<ApiResponse<Availability[]>>(`${this.apiUrl}/availability/public/${accommodationId}`);
  }

  checkAvailability(accommodationId: number, checkIn: string, checkOut: string): Observable<{ available: boolean; price: number }> {
    return this.http.post<{ available: boolean; price: number }>(`${this.apiUrl}/availability/check`, {
      accommodation_id: accommodationId,
      check_in: checkIn,
      check_out: checkOut
    });
  }

  // ========== RESEÑAS ==========
  
  getReviews(accommodationId: number): Observable<ApiResponse<Review[]>> {
    return this.http.get<ApiResponse<Review[]>>(`${this.apiUrl}/reviews/public/${accommodationId}`);
  }

  createReview(accommodationId: number, review: { rating: number; comment: string }): Observable<ApiResponse<Review>> {
    return this.http.post<ApiResponse<Review>>(`${this.apiUrl}/reviews`, {
      accommodation_id: accommodationId,
      ...review
    });
  }
}