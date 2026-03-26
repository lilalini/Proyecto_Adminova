import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-upload',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
      <input 
        type="file" 
        (change)="onFileSelected($event)"
        accept="image/*"
        class="hidden"
        #fileInput>
      
      <button 
        (click)="fileInput.click()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        Seleccionar imagen
      </button>

      @if (uploading) {
        <p class="mt-2 text-gray-600">Subiendo...</p>
      }

      @if (uploadedUrl) {
        <div class="mt-4">
          <img [src]="uploadedUrl" class="max-h-40 mx-auto rounded-lg">
          <p class="text-sm text-green-600 mt-2">¡Imagen subida!</p>
        </div>
      }
    </div>
  `
})
export class UploadComponent {
  @Input() accommodationId!: number;
  @Output() imageUploaded = new EventEmitter<any>();
  uploading = false;
  uploadedUrl = '';

  constructor(private http: HttpClient) {}

onFileSelected(event: Event) {
  const input = event.target as HTMLInputElement;
  if (!input.files?.length) return;

  const file = input.files[0];

  const formData = new FormData();
  formData.append('image', file);

  this.uploading = true;

  // PRUEBA: enviamos la imagen a Laravel
  this.http.post(
    `${environment.apiUrl}/accommodations/${this.accommodationId}/media`,
    formData
  ).subscribe({
    next: (response: any) => {
      console.log('Subida exitosa:', response); // <-- aquí vemos que llega al backend
      this.uploadedUrl = response.url || response.data?.url;
      this.imageUploaded.emit(response.data || response);
      this.uploading = false;
      input.value = '';
    },
    error: (error) => {
      console.error('Error backend:', error.error); // <-- aquí vemos si da algún 422
      this.uploading = false;
    }
  });
}
}