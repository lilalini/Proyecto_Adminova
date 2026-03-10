// create.component.ts
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, Validators, FormControl, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AccommodationService } from '../../../../core/services/accommodation.service';
import { RouterModule, Router } from '@angular/router';

@Component({
  selector: 'app-create-accommodation',
  templateUrl: './create.component.html',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
})
export class CreateAccommodationComponent implements OnInit {
  accommodationForm!: FormGroup;
  loading = false;
  errorMessage = '';

  // Tipos de propiedad disponibles
  propertyTypes = ['Apartment', 'House', 'Studio', 'Room'];

  constructor(
    private fb: FormBuilder,
    private accommodationService: AccommodationService,
    public router: Router
  ) {}

  ngOnInit(): void {
    this.accommodationForm = this.fb.group({
      title: ['', Validators.required],
      description: ['', Validators.required],
      address: ['', Validators.required],
      city: ['', Validators.required],
      country: ['', Validators.required],
      base_price: [0, [Validators.required, Validators.min(0)]],
      max_guests: [1, [Validators.required, Validators.min(1)]],
      cleaning_fee: [0, [Validators.required, Validators.min(0)]],
      security_deposit: [0, [Validators.required, Validators.min(0)]],
      property_type: this.fb.group({
        Apartment: [false],
        House: [false],
        Studio: [false],
        Room: [false],
      }),
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
    if (this.accommodationForm.invalid) return;

    // Convertimos property_type a array de seleccionadas
    const selectedTypes = Object.entries(this.accommodationForm.value.property_type)
      .filter(([key, value]) => value)
      .map(([key]) => key);

    const payload = {
      ...this.accommodationForm.value,
      property_type: selectedTypes
    };

    this.loading = true;
    this.errorMessage = '';

    this.accommodationService.create(payload).subscribe({
      next: () => this.router.navigate(['/dashboard']),
      error: (err) => {
        this.errorMessage = err.error?.message || 'Error al guardar alojamiento';
        this.loading = false;
      }
    });
  }
}