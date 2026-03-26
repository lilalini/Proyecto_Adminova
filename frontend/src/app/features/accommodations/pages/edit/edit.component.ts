import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, Validators, FormControl, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule, Router } from '@angular/router';
import { AccommodationService } from '../../../../core/services/accommodation.service';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { UploadComponent } from '../../components/upload/upload.component';
import { Accommodation, CreateAccommodationData } from '../../../../core/models/accommodation.model'; // ← AÑADIR
import { Media } from '../../../../core/models/media.model'; // ← AÑADIR

@Component({
  selector: 'app-edit-accommodation',
  standalone: true,
  imports: [
    CommonModule, 
    ReactiveFormsModule, 
    RouterModule, 
    IconSvgComponent,
    UploadComponent
  ],
  templateUrl: './edit.component.html',
})
export class EditComponent implements OnInit {
  accommodationForm!: FormGroup;
  loading = true;
  submitting = false;
  errorMessage = '';
  accommodationId!: number;
  accommodation: Accommodation | null = null; // ← TIPADO
  uploadedImages: Media[] = []; // ← AÑADIDO

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
    private route: ActivatedRoute,
    public router: Router,
    private accommodationService: AccommodationService,
    private location: Location
  ) {}

  ngOnInit(): void {
    this.accommodationId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.accommodationId) {
      this.loadAccommodation();
    } else {
      this.router.navigate(['/accommodations']);
    }
  }

  goBack() {
    this.location.back();
  }

  goToList() {
    this.router.navigate(['/accommodations']);
  }

  loadAccommodation() {
    this.loading = true;
    this.accommodationService.getOne(this.accommodationId).subscribe({
      next: (response) => {
        this.accommodation = response.data;
        this.uploadedImages = this.accommodation.images || []; // ← CARGAR IMÁGENES
        this.initForm();
        this.loading = false;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar el alojamiento';
        this.loading = false;
      }
    });
  }

  initForm() {
    if (!this.accommodation) return;

    this.accommodationForm = this.fb.group({
      title: [this.accommodation.title, Validators.required],
      description: [this.accommodation.description, Validators.required],
      property_type: [this.accommodation.property_type, Validators.required],
      bedrooms: [this.accommodation.bedrooms, [Validators.required, Validators.min(1)]],
      bathrooms: [this.accommodation.bathrooms, [Validators.required, Validators.min(1)]],
      max_guests: [this.accommodation.max_guests, [Validators.required, Validators.min(1)]],
      size_m2: [this.accommodation.size_m2, [Validators.required, Validators.min(1)]],
      address: [this.accommodation.address, Validators.required],
      city: [this.accommodation.city, Validators.required],
      postal_code: [this.accommodation.postal_code, Validators.required],
      country: [this.accommodation.country, Validators.required],
      base_price: [this.accommodation.base_price, [Validators.required, Validators.min(0)]],
      cleaning_fee: [this.accommodation.cleaning_fee || 0, [Validators.min(0)]],
      security_deposit: [this.accommodation.security_deposit || 0, [Validators.min(0)]],
      minimum_stay: [this.accommodation.minimum_stay, [Validators.required, Validators.min(1)]],
      maximum_stay: [this.accommodation.maximum_stay || 30],
      check_in_time: [this.accommodation.check_in_time, Validators.required],
      check_out_time: [this.accommodation.check_out_time, Validators.required],
      cancellation_policy_id: [this.accommodation.cancellation_policy_id || 1, Validators.required],
      status: [this.accommodation.status || 'published'],
      amenities: this.fb.array([]),
      house_rules: this.fb.array([])
    });

    // Cargar amenities
    if (this.accommodation.amenities) {
      const amenitiesArray = typeof this.accommodation.amenities === 'string' 
        ? JSON.parse(this.accommodation.amenities) 
        : this.accommodation.amenities;
      
      amenitiesArray.forEach((a: string) => {
        (this.accommodationForm.get('amenities') as FormArray).push(new FormControl(a));
      });
    }

    // Cargar house rules
    if (this.accommodation.house_rules) {
      const rulesArray = typeof this.accommodation.house_rules === 'string' 
        ? JSON.parse(this.accommodation.house_rules) 
        : this.accommodation.house_rules;
      
      rulesArray.forEach((r: string) => {
        (this.accommodationForm.get('house_rules') as FormArray).push(new FormControl(r));
      });
    }
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
    if (this.accommodationForm.invalid) return;

    this.submitting = true;
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

    this.accommodationService.update(this.accommodationId, payload).subscribe({
      next: () => {
        this.router.navigate(['/accommodations']);
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Error al actualizar';
        this.submitting = false;
      }
    });
  }

  // ===== MÉTODOS PARA IMÁGENES =====
  onImageUploaded(image: Media) {
    this.uploadedImages.push(image);
  }

  removeImage(imageId: number) {
    this.uploadedImages = this.uploadedImages.filter(img => img.id !== imageId);
    
    this.accommodationService.deleteImage(this.accommodationId, imageId).subscribe({
      next: () => {
        console.log('Imagen eliminada');
      },
      error: (err) => {
        console.error('Error al eliminar imagen:', err);
      }
    });
  }
}