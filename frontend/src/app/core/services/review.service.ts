import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { 
  Review, 
  ReviewListResponse, 
  ReviewResponse,
  CreateReviewData,
  RespondToReviewData
} from '../models/review.model';

@Injectable({
  providedIn: 'root'
})
export class ReviewService {
  private apiUrl = `${environment.apiUrl}/reviews`;

  constructor(private http: HttpClient) {}

  // Obtener reseñas por alojamiento (público)
  getByAccommodation(accommodationId: number, page: number = 1): Observable<ReviewListResponse> {
    return this.http.get<ReviewListResponse>(
      `${this.apiUrl}?accommodation_id=${accommodationId}&page=${page}`
    );
  }

  // Obtener todas las reseñas (admin) con paginación
  getAll(page: number = 1): Observable<ReviewListResponse> {
    return this.http.get<ReviewListResponse>(`${this.apiUrl}?page=${page}`);
  }

  // Obtener una reseña por ID
  getOne(id: number): Observable<ReviewResponse> {
    return this.http.get<ReviewResponse>(`${this.apiUrl}/${id}`);
  }

  // Crear nueva reseña (solo guests)
  create(data: CreateReviewData): Observable<ReviewResponse> {
    return this.http.post<ReviewResponse>(this.apiUrl, data);
  }

  // Actualizar reseña (admin o owner)
  update(id: number, data: Partial<Review>): Observable<ReviewResponse> {
    return this.http.put<ReviewResponse>(`${this.apiUrl}/${id}`, data);
  }

  // Eliminar reseña (admin)
  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  // Responder a una reseña (admin/owner)
  respond(id: number, data: RespondToReviewData): Observable<ReviewResponse> {
    return this.http.post<ReviewResponse>(`${this.apiUrl}/${id}/respond`, data);
  }

  // Publicar reseña (admin)
  publish(id: number): Observable<ReviewResponse> {
    return this.http.post<ReviewResponse>(`${this.apiUrl}/${id}/publish`, {});
  }

  // Rechazar reseña (admin)
  reject(id: number): Observable<ReviewResponse> {
    return this.http.post<ReviewResponse>(`${this.apiUrl}/${id}/reject`, {});
  }

  // Marcar reseña como útil (guest)
  markAsHelpful(id: number): Observable<ReviewResponse> {
    return this.http.post<ReviewResponse>(`${this.apiUrl}/${id}/helpful`, {});
  }
}