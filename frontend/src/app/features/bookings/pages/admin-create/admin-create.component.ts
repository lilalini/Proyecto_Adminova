import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { AccommodationService } from '../../../../core/services/accommodation.service';
import { GuestService } from '../../../../core/services/guest.service';
import { Accommodation } from '../../../../core/models/accommodation.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { Booking, BookingStatus } from '../../../../core/models/booking.model';
import { DashboardUpdateService } from '../../../../core/services/dashboard-update.service';

@Component({
  selector: 'app-admin-create',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-create.component.html',
})
export class AdminCreateComponent implements OnInit {
  accommodations: Accommodation[] = [];
  guests: any[] = [];
  loading = true;
  saving = false;
  errorMessage = '';
  successMessage = '';

  bookingData: {
    accommodation_id: number | null;
    guest_id: number | null;
    check_in: string;
    check_out: string;
    nights: number;
    adults: number;
    children: number;
    infants: number;
    pets: number;
    total_amount: number;
    guest_name: string;
    guest_email: string;
    guest_phone: string;
    status: BookingStatus;
  } = {
    accommodation_id: null,
    guest_id: null,
    check_in: '',
    check_out: '',
    nights: 0,
    adults: 1,
    children: 0,
    infants: 0,
    pets: 0,
    total_amount: 0,
    guest_name: '',
    guest_email: '',
    guest_phone: '',
    status: 'confirmed'
  };

  constructor(
    private bookingService: BookingService,
    private accommodationService: AccommodationService,
    private guestService: GuestService,
    private router: Router,
    private dashboardUpdate: DashboardUpdateService
  ) {}

  ngOnInit() {
    this.loadAccommodations();
    this.loadGuests();
  }

  loadAccommodations() {
    this.accommodationService.getAll().subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.checkLoadingComplete();
      },
      error: (error) => {
        console.error(error);
        this.checkLoadingComplete();
      }
    });
  }

  loadGuests() {
    this.guestService.getAll().subscribe({
      next: (response) => {
        this.guests = response.data;
        this.checkLoadingComplete();
      },
      error: (error) => {
        console.error(error);
        this.checkLoadingComplete();
      }
    });
  }

  checkLoadingComplete() {
    if (this.accommodations !== undefined && this.guests !== undefined) {
      this.loading = false;
    }
  }

  calculateNights() {
    if (this.bookingData.check_in && this.bookingData.check_out) {
      const start = new Date(this.bookingData.check_in);
      const end = new Date(this.bookingData.check_out);
      const diffTime = Math.abs(end.getTime() - start.getTime());
      this.bookingData.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
  }

  onAccommodationChange() {
    const selected = this.accommodations.find(a => a.id === this.bookingData.accommodation_id);
    if (selected) {
      this.bookingData.total_amount = selected.base_price * (this.bookingData.nights || 1);
    }
  }

  onSubmit() {
    this.saving = true;
    this.errorMessage = '';

    const selectedGuest = this.guests.find(g => g.id === this.bookingData.guest_id);
    
    const data: Partial<Booking> = {
      accommodation: { id: this.bookingData.accommodation_id! } as any,
      guest: { id: this.bookingData.guest_id! } as any,
      check_in: this.bookingData.check_in,
      check_out: this.bookingData.check_out,
      nights: this.bookingData.nights,
      adults: this.bookingData.adults,
      children: this.bookingData.children,
      infants: this.bookingData.infants,
      pets: this.bookingData.pets,
      total_amount: this.bookingData.total_amount,
      guest_name: selectedGuest ? `${selectedGuest.first_name} ${selectedGuest.last_name}` : this.bookingData.guest_name,
      guest_email: selectedGuest?.email || this.bookingData.guest_email,
      guest_phone: selectedGuest?.phone || this.bookingData.guest_phone,
      status: this.bookingData.status,
      source: 'admin'
    };

    this.bookingService.create(data).subscribe({
      next: () => {
        this.saving = false;
        this.successMessage = 'Reserva creada correctamente';
        
        // Notificar al dashboard que debe actualizarse
        this.dashboardUpdate.notifyRefresh();
        
        setTimeout(() => {
          this.router.navigate(['/admin/dashboard']);
        }, 1500);
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al crear la reserva';
        this.saving = false;
      }
    });
  }
}