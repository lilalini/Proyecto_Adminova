import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, Validators, FormControl, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AccommodationService } from '../../../../core/services/accommodation.service';
import { RouterModule, Router } from '@angular/router';
import { UploadComponent } from '../../components/upload/upload.component';
import { CreateAccommodationData } from '../../../../core/models/accommodation.model';
import { Media } from '../../../../core/models/media.model'; 

@Component({
  selector: 'app-create-accommodation',
  templateUrl: './create.component.html',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, UploadComponent],
})
export class CreateAccommodationComponent implements OnInit {
  accommodationForm!: FormGroup;
  loading = false;
  errorMessage = '';
  accommodationCreated = false;
  newAccommodationId: number | null = null;
  uploadedImages: Media[] = []; 

  propertyTypes = [
    { value: 'apartment', label: 'Apartamento' },
    { value: 'house', label: 'Casa' },
    { value: 'studio', label: 'Estudio' },
    { value: 'room', label: 'Habitación' }
  ];
  
  cancellationPolicies = [
    { id: 1, name: 'Flexible' },
    { id: 2, name: 'Moderada' },
    { id: 3, name: 'Estricta' },
    { id: 4, name: 'No reembolsable' }
  ];

  constructor(
    private fb: FormBuilder,
    private accommodationService: AccommodationService,
    public router: Router
  ) {}

  ngOnInit(): void {
    this.accommodationForm = this.fb.group({
      title: ['', Validators.required],
      description: ['', Validators.required],
      property_type: ['', Validators.required],
      bedrooms: [1, [Validators.required, Validators.min(1)]],
      bathrooms: [1, [Validators.required, Validators.min(1)]],
      max_guests: [2, [Validators.required, Validators.min(1)]],
      size_m2: [50, [Validators.required, Validators.min(1)]],
      address: ['', Validators.required],
      city: ['', Validators.required],
      postal_code: ['', Validators.required],
      country: ['', Validators.required],
      base_price: [0, [Validators.required, Validators.min(0)]],
      cleaning_fee: [0, [Validators.required, Validators.min(0)]],
      security_deposit: [0, [Validators.required, Validators.min(0)]],
      minimum_stay: [1, [Validators.required, Validators.min(1)]],
      maximum_stay: [30],
      check_in_time: ['15:00', Validators.required],
      check_out_time: ['11:00', Validators.required],
      cancellation_policy_id: [1, Validators.required],
      status: ['published'],
      amenities: this.fb.array([]),
      house_rules: this.fb.array([]),
      latitude: [''],
      longitude: ['']
    });
  }

  get amenitiesControls(): FormControl[] {
    return (this.accommodationForm.get('amenities') as FormArray).controls as FormControl[];
  }

  get rulesControls(): FormControl[] {
    return (this.accommodationForm.get('house_rules') as FormArray).controls as FormControl[];
  }

  addAmenity() {
    (this.accommodationForm.get('amenities') as FormArray).push(new FormControl(''));
  }

  removeAmenity(index: number) {
    (this.accommodationForm.get('amenities') as FormArray).removeAt(index);
  }

  addRule() {
    (this.accommodationForm.get('house_rules') as FormArray).push(new FormControl(''));
  }

  removeRule(index: number) {
    (this.accommodationForm.get('house_rules') as FormArray).removeAt(index);
  }

  onSubmit() {
    if (this.accommodationForm.invalid) {
      return;
    }

    const formValue = this.accommodationForm.value;
    
    const payload: CreateAccommodationData = { // ← TIPADO
      title: formValue.title,
      description: formValue.description,
      property_type: formValue.property_type,
      bedrooms: formValue.bedrooms,
      bathrooms: formValue.bathrooms,
      max_guests: formValue.max_guests,
      size_m2: formValue.size_m2,
      address: formValue.address,
      city: formValue.city,
      postal_code: formValue.postal_code,
      country: formValue.country,
      base_price: formValue.base_price,
      cleaning_fee: formValue.cleaning_fee,
      security_deposit: formValue.security_deposit,
      minimum_stay: formValue.minimum_stay,
      maximum_stay: formValue.maximum_stay,
      check_in_time: formValue.check_in_time,
      check_out_time: formValue.check_out_time,
      status: formValue.status,
      amenities: formValue.amenities.filter((a: string) => a.trim() !== ''),
      house_rules: formValue.house_rules.filter((r: string) => r.trim() !== ''),
      cancellation_policy_id: formValue.cancellation_policy_id
    };

    this.loading = true;
    this.errorMessage = '';

    this.accommodationService.create(payload).subscribe({
      next: (response) => { // ← TIPADO
        this.newAccommodationId = response.data.id;
        this.accommodationCreated = true;
        this.loading = false;
        
        setTimeout(() => {
          document.getElementById('upload-section')?.scrollIntoView({ 
            behavior: 'smooth' 
          });
        }, 100);
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Error al guardar alojamiento';
        this.loading = false;
      }
    });
  }

  onImageUploaded(image: Media) {
  console.log('Imagen recibida:', image);
  
  if (image && image.url) {
    this.uploadedImages.push(image);
    console.log('uploadedImages ahora:', this.uploadedImages.length);
  } else {
    console.warn('Imagen sin URL:', image);
  }
}

  removeImage(imageId: number) {
    // Eliminar del frontend
    this.uploadedImages = this.uploadedImages.filter(img => img.id !== imageId);
    
    // Eliminar del backend
    if (this.newAccommodationId) {
      this.accommodationService.deleteImage(this.newAccommodationId, imageId).subscribe({
        next: () => {
          console.log('Imagen eliminada correctamente');
        },
        error: (err) => {
          console.error('Error al eliminar imagen:', err);
        }
      });
    }
  }

  handleImageError(image: Media) {
    console.warn('Error al cargar imagen:', image.url);
  }
}