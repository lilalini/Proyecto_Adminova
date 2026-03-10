  import { Injectable } from '@angular/core';
  import { HttpClient } from '@angular/common/http';
  import { Observable } from 'rxjs';
  import { environment } from '../../../environments/environment';
  import { Accommodation, AccommodationListResponse, AccommodationResponse } from '../models/accommodation.model';

  @Injectable({
    providedIn: 'root'
  })
  export class AccommodationService {
    private apiUrl = `${environment.apiUrl}/accommodations`;

    constructor(private http: HttpClient) {}

    // Obtener todos los alojamientos
    getAll(): Observable<AccommodationListResponse> {
      return this.http.get<AccommodationListResponse>(this.apiUrl);
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
  }