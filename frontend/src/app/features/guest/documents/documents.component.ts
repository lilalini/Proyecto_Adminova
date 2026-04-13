import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { DatePipe } from '@angular/common';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { AuthService } from '../../../core/services/auth.service';
import { environment } from '../../../../environments/environment';
import { first } from 'rxjs/operators';

interface Document {
  id: number;
  title: string;
  document_type: string;
  file_name: string;
  file_path: string;
  file_size: number;
  mime_type: string;
  created_at: string;
}

@Component({
  selector: 'app-documents',
  templateUrl: './documents.component.html',
  standalone: true,
  imports: [DatePipe, IconSvgComponent]
})
export class DocumentsComponent implements OnInit {
  documents: Document[] = [];
  loading = true;
  error = '';

  constructor(
    private http: HttpClient,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadDocuments();
  }

  loadDocuments(): void {
    // Obtener el usuario actual del observable
    this.authService.currentUser$.pipe(first()).subscribe({
      next: (user) => {
        if (!user) {
          this.error = 'No se encontró información del usuario';
          this.loading = false;
          return;
        }

        const userId = user.id;
        
        this.http.get<{data: Document[]}>(`${environment.apiUrl}/documents?documentable_type=App\\Models\\Guest&documentable_id=${userId}`)
          .subscribe({
            next: (response) => {
              this.documents = response.data;
              this.loading = false;
            },
            error: (err) => {
              console.error('Error loading documents:', err);
              this.error = 'No se pudieron cargar los documentos';
              this.loading = false;
            }
          });
      },
      error: () => {
        this.error = 'No se pudo obtener la información del usuario';
        this.loading = false;
      }
    });
  }

  downloadDocument(document: Document): void {
    const token = this.authService.getTokenValue();
    const url = `${environment.apiUrl}/documents/${document.id}/download?token=${token}`;
    window.open(url, '_blank');
  }
}