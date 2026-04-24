import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { GuestService } from '../../../core/services/guest.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './profile.component.html',
})
export class ProfileComponent implements OnInit {
  returnUrl = '/guest/dashboard';
  user: any = null;
  loading = true;
  saving = false;             
  successMessage = '';
  errorMessage = '';

  profile = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    document_type: '',
    document_number: '',
    nationality: 'ES',
    birth_date: '',
    gender: '',
    address: '',
    city: '',
    postal_code: '',
    country: '',
    accepts_newsletter: false
  };

  documentTypes = [
    { value: 'DNI', label: 'DNI (España)' },
    { value: 'NIE', label: 'NIE (Extranjero)' },
    { value: 'Passport', label: 'Pasaporte' }
  ];

  genders = [
    { value: 'male', label: 'Hombre' },
    { value: 'female', label: 'Mujer' },
    { value: 'other', label: 'Otro / Prefiero no decir' }
  ];

  countries = [
    { code: 'ES', name: 'España' },
    {code: 'RU', name: 'Rusia'},
    { code: 'FR', name: 'Francia' },
    { code: 'IT', name: 'Italia' },
    { code: 'PT', name: 'Portugal' },
    { code: 'DE', name: 'Alemania' },
    { code: 'GB', name: 'Reino Unido' },
    { code: 'US', name: 'Estados Unidos' },
    { code: 'NL', name: 'Países Bajos' },
    { code: 'BE', name: 'Bélgica' },
    { code: 'CH', name: 'Suiza' },
    { code: 'AT', name: 'Austria' },
    { code: 'SE', name: 'Suecia' },
    { code: 'NO', name: 'Noruega' },
    { code: 'DK', name: 'Dinamarca' },
    { code: 'FI', name: 'Finlandia' },
    { code: 'IE', name: 'Irlanda' },
    { code: 'LU', name: 'Luxemburgo' },
    { code: 'PL', name: 'Polonia' },
    { code: 'CZ', name: 'República Checa' },
    { code: 'HU', name: 'Hungría' },
    { code: 'GR', name: 'Grecia' },
    { code: 'HR', name: 'Croacia' },
    { code: 'AR', name: 'Argentina' },
    { code: 'MX', name: 'México' },
    { code: 'CO', name: 'Colombia' },
    { code: 'CL', name: 'Chile' },
    { code: 'PE', name: 'Perú' },
    { code: 'UY', name: 'Uruguay' },
    { code: 'BR', name: 'Brasil' }
  ];

  constructor(
    private auth: AuthService,
    private guestService: GuestService,
    private route: ActivatedRoute,
    private router: Router,
  ) { const navigation = this.router.getCurrentNavigation();
    this.returnUrl = localStorage.getItem('returnUrl') || '/guest/dashboard';
}

  ngOnInit() {
    // Capturar mensaje de perfil incompleto
    this.route.queryParams.subscribe(params => {
      if (params['incomplete'] === 'true') {
        this.errorMessage = 'Debes completar todos los campos obligatorios (*) antes de realizar una reserva.';
      }
    });

    this.auth.currentUser$.subscribe(user => {
      if (user) {
        this.user = user;
        this.loadGuestProfile();
      }
    });
  }

  loadGuestProfile() {
      this.guestService.getByUserId(this.user.id).subscribe({
        next: (response: any) => {
          const guest = response.data;
          if (guest) {
            this.profile.first_name = guest.first_name || this.user.first_name;
            this.profile.last_name = guest.last_name || this.user.last_name;
            this.profile.email = guest.email || this.user.email;
            this.profile.phone = guest.phone || this.user.phone || '';
            this.profile.document_type = guest.document_type || '';
            this.profile.document_number = guest.document_number || '';
            this.profile.nationality = guest.nationality || 'ES';
            this.profile.birth_date = guest.birth_date ? guest.birth_date.split('T')[0] : '';
            this.profile.gender = guest.gender || '';
            this.profile.address = guest.address || '';
            this.profile.city = guest.city || '';
            this.profile.postal_code = guest.postal_code || '';
            this.profile.country = guest.country || '';
            this.profile.accepts_newsletter = guest.accepts_newsletter || false;
          }
          this.loading = false;
        },
        error: (err) => {
          console.error('Error cargando perfil:', err);
          // Si no hay guest, usar datos del usuario
        this.profile.first_name = this.user.first_name;
        this.profile.last_name = this.user.last_name;
        this.profile.email = this.user.email;
        this.profile.phone = this.user.phone || '';
        this.loading = false;
      }
    });
  }

  onSubmit() {
    this.saving = true;
    this.successMessage = '';
    this.errorMessage = '';

    const userId = this.user?.id;
    
    if (!userId) {
      this.errorMessage = 'Usuario no identificado';
      this.saving = false;
      return;
    }

    this.guestService.updateByUserId(userId, {
      first_name: this.profile.first_name,
      last_name: this.profile.last_name,
      email: this.profile.email,
      phone: this.profile.phone,
      document_type: this.profile.document_type,
      document_number: this.profile.document_number,
      nationality: this.profile.nationality,
      birth_date: this.profile.birth_date,
      gender: this.profile.gender,
      address: this.profile.address,
      city: this.profile.city,
      postal_code: this.profile.postal_code,
      country: this.profile.country,
      accepts_newsletter: this.profile.accepts_newsletter
    }).subscribe({
      next: () => {
        this.saving = false;
        this.successMessage = 'Perfil actualizado correctamente';
        setTimeout(() => this.successMessage = '', 3000);
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al actualizar perfil';
        this.saving = false;
      }
    });
  }

    clearReturnUrl() {
    localStorage.removeItem('returnUrl');
  }

  isProfileComplete(): boolean {
    return !!(
      this.profile.first_name &&
      this.profile.last_name &&
      this.profile.email &&
      this.profile.phone &&
      this.profile.document_type &&
      this.profile.document_number &&
      this.profile.nationality &&
      this.profile.address &&
      this.profile.city &&
      this.profile.country
    );
  }
}