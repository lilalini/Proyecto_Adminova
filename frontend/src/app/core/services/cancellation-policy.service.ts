import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CancellationPolicy, CancellationPolicyListResponse } from '../models/cancellation-policy.model';

@Injectable({
  providedIn: 'root'
})
export class CancellationPolicyService {
  private apiUrl = `${environment.apiUrl}/cancellation-policies`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<CancellationPolicyListResponse> {
    return this.http.get<CancellationPolicyListResponse>(this.apiUrl);
  }

  getOne(id: number): Observable<{ data: CancellationPolicy }> {
    return this.http.get<{ data: CancellationPolicy }>(`${this.apiUrl}/${id}`);
  }
}