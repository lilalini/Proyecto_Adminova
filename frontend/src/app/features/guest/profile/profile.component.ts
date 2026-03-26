import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { GuestService } from '../../../core/services/guest.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './profile.component.html',
})
export class ProfileComponent implements OnInit {
  user: any = null;
  loading = true;
  saving = false;
  successMessage = '';
  errorMessage = '';

  profile = {
    first_name: '',
    last_name: '',
    email: '',
    phone: ''
  };

  constructor(
    private auth: AuthService,
    private guestService: GuestService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      if (user) {
        this.user = user;
        this.profile.first_name = user.first_name;
        this.profile.last_name = user.last_name;
        this.profile.email = user.email;
        this.profile.phone = user.phone || '';
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

  this.guestService.update(userId, {
    first_name: this.profile.first_name,
    last_name: this.profile.last_name,
    email: this.profile.email,
    phone: this.profile.phone
  }).subscribe({
    next: (response: any) => {
      const updatedUser = response.data || response;
      this.auth.updateCurrentUser(updatedUser);
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
}