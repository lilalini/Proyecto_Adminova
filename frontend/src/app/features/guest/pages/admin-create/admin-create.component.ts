import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { GuestService } from '../../../../core/services/guest.service';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { DashboardUpdateService } from '../../../../core/services/dashboard-update.service';

@Component({
  selector: 'app-admin-create-guest',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-create.component.html',
})
export class AdminCreateGuestComponent {
  guest = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    document_type: '',
    document_number: '',
    nationality: '',
    source: 'admin' as const
  };
  saving = false;
  errorMessage = '';
  successMessage = '';

  constructor(
    private guestService: GuestService,
    private router: Router,
    private dashboardUpdate: DashboardUpdateService
  ) {}

  onSubmit() {
    this.saving = true;
    this.errorMessage = '';

    this.guestService.create(this.guest).subscribe({
      next: () => {
        this.saving = false;
        this.successMessage = 'Huésped creado correctamente';
        this.dashboardUpdate.notifyRefresh();
        setTimeout(() => this.router.navigate(['/admin/dashboard']), 1500);
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al crear huésped';
        this.saving = false;
      }
    });
  }
}