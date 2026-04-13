import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Document {
  id: number;
  title: string;
  document_type: string;
  file_name: string;
  file_path: string;
  file_size: number;
  mime_type: string;
  created_at: string;
}

@Injectable({ providedIn: 'root' })
export class DocumentService {
  private apiUrl = `${environment.apiUrl}/documents`;

  constructor(private http: HttpClient) {}

  getGuestDocuments(guestId: number): Observable<{data: Document[]}> {
    return this.http.get<{data: Document[]}>(
      `${this.apiUrl}?documentable_type=App\\Models\\Guest&documentable_id=${guestId}`
    );
  }

  downloadDocument(documentId: number): void {
    const token = localStorage.getItem('token');
    window.open(`${this.apiUrl}/${documentId}/download?token=${token}`, '_blank');
  }
}