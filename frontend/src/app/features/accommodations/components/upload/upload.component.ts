import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../../environments/environment';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

interface MediaItem {
  id: number;
  url: string;
  thumbnail_url: string;
  is_main: boolean;
  order: number;
}

@Component({
  selector: 'app-upload',
  standalone: true,
  imports: [CommonModule, IconSvgComponent],
  templateUrl: './upload.component.html',
})
export class UploadComponent implements OnInit {
  @Input() accommodationId!: number;
  @Output() imageUploaded = new EventEmitter<any>();
  @Output() imagesChanged = new EventEmitter<MediaItem[]>();

  images: MediaItem[] = [];
  uploading = false;
  isDragging = false;
  private dragIndex = -1;

  constructor(private http: HttpClient) {}

  ngOnInit() {
    //console.log('Token:', localStorage.getItem('auth_token'));
    //console.log('AccommodationId:', this.accommodationId);
    this.loadImages();
  }

  loadImages() {
    this.http.get<any>(`${environment.apiUrl}/media`, {
      params: {
        model_type: 'App\\Models\\Accommodation',
        model_id: this.accommodationId.toString(),
        collection_name: 'gallery'
      }
    }).subscribe({
      next: (res) => {
        this.images = (res.data || res).map((m: any) => ({
          id: m.id,
          url: m.url,
          thumbnail_url: m.thumbnail_url,
          is_main: m.is_main ?? false,
          order: m.order ?? 0
        }));
      },
      error: () => {}
    });
  }

  // ── Upload ──────────────────────────────────────────────────────────────

  onDragOver(event: DragEvent) {
    event.preventDefault();
    this.isDragging = true;
  }

  onDragLeave() {
    this.isDragging = false;
  }

  onDrop(event: DragEvent) {
    event.preventDefault();
    this.isDragging = false;
    const files = event.dataTransfer?.files;
    if (files?.length) this.uploadFiles(Array.from(files));
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files?.length) return;
    this.uploadFiles(Array.from(input.files));
    input.value = '';
  }

  uploadFiles(files: File[]) {
    files.forEach(file => this.uploadFile(file));
  }

  uploadFile(file: File) {
    const formData = new FormData();
    formData.append('image', file);
    this.uploading = true;

    this.http.post<any>(
      `${environment.apiUrl}/accommodations/${this.accommodationId}/media`,
      formData
    ).subscribe({
      next: (response) => {
        const data = response.data || response;
        this.images.push({
          id: data.id,
          url: data.url,
          thumbnail_url: data.thumbnail_url,
          is_main: data.is_main ?? false,
          order: data.order ?? this.images.length
        });
        this.imageUploaded.emit(data);
        this.imagesChanged.emit(this.images);
        this.uploading = false;
      },
      error: () => { this.uploading = false; }
    });
  }

  // ── Set main ────────────────────────────────────────────────────────────

  setMain(image: MediaItem) {
    this.http.post<any>(`${environment.apiUrl}/media/${image.id}/set-main`, {})
      .subscribe({
        next: () => {
          this.images.forEach(img => img.is_main = img.id === image.id);
          this.imagesChanged.emit(this.images);
        }
      });
  }

  // ── Delete ──────────────────────────────────────────────────────────────

  deleteImage(image: MediaItem) {
    if (!confirm('¿Eliminar esta imagen?')) return;

    this.http.delete(`${environment.apiUrl}/accommodations/${this.accommodationId}/media/${image.id}`)
      .subscribe({
        next: () => {
          this.images = this.images.filter(img => img.id !== image.id);
          if (image.is_main && this.images.length > 0) {
            this.setMain(this.images[0]);
          }
          this.imagesChanged.emit(this.images);
        }
      });
  }

  // ── Reorder ─────────────────────────────────────────────────────────────

  onDragStart(index: number) {
    this.dragIndex = index;
  }

  onDragOverItem(event: DragEvent) {
    event.preventDefault();
  }

  onDropItem(targetIndex: number) {
    if (this.dragIndex === -1 || this.dragIndex === targetIndex) return;
    const moved = this.images.splice(this.dragIndex, 1)[0];
    this.images.splice(targetIndex, 0, moved);
    this.dragIndex = -1;

    const ids = this.images.map(img => img.id);
    this.http.post(`${environment.apiUrl}/media/reorder`, { media_ids: ids }).subscribe();
    this.imagesChanged.emit(this.images);
  }

  onDragEnd() {
    this.dragIndex = -1;
  }
}