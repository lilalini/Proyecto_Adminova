  import { Injectable } from '@angular/core';
  import { HttpClient } from '@angular/common/http';
  import { Observable } from 'rxjs';
  import { environment } from '../../../environments/environment';
  import { Accommodation, AccommodationListResponse, AccommodationResponse,  ApiResponse } from '../models/accommodation.model';
 
  @Injectable({
    providedIn: 'root'
  })
  export class AccommodationService {
    private apiUrl = `${environment.apiUrl}/accommodations`;

    constructor(private http: HttpClient) {}

    // Obtener todos los alojamientos
    getAll(page: number = 1): Observable<AccommodationListResponse> {
    return this.http.get<AccommodationListResponse>(`${this.apiUrl}?page=${page}`);
    }

    // Obtener un alojamiento por ID
    getOne(id: number): Observable<AccommodationResponse> {
      return this.http.get<AccommodationResponse>(`${this.apiUrl}/${id}`);
    }

    // Crear nuevo alojamiento
    create(data: Partial<Accommodation>): Observable<AccommodationResponse> {
      return this.http.post<AccommodationResponse>(this.apiUrl, data);
    }

    // Actualizar alojamiento
    update(id: number, data: Partial<Accommodation>): Observable<AccommodationResponse> {
      return this.http.put<AccommodationResponse>(`${this.apiUrl}/${id}`, data);
    }

    // Eliminar alojamiento
    delete(id: number): Observable<{ message: string }> {
      return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
    }

    // Eliminar imagen de un alojamiento
    deleteImage(accommodationId: number, imageId: number): Observable<any> {
      return this.http.delete(`${this.apiUrl}/${accommodationId}/media/${imageId}`);
    }

    searchAccommodations(params: any): Observable<ApiResponse<Accommodation[]>> {
      return this.http.get<ApiResponse<Accommodation[]>>(`${this.apiUrl}/public`, { params });
    }

  }

  