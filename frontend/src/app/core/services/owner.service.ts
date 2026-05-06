import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Owner, OwnerListResponse, OwnerResponse } from '../models/owner.model';

@Injectable({
  providedIn: 'root'
})
export class OwnerService {
  private apiUrl = `${environment.apiUrl}/owners`;

  constructor(private http: HttpClient) {}

  getAll(page: number = 1): Observable<OwnerListResponse> {
    return this.http.get<OwnerListResponse>(`${this.apiUrl}?page=${page}`);
  }

  getOne(id: number): Observable<OwnerResponse> {
    return this.http.get<OwnerResponse>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Owner>): Observable<OwnerResponse> {
    return this.http.post<OwnerResponse>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Owner>): Observable<OwnerResponse> {
    return this.http.put<OwnerResponse>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }
}