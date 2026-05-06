import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { GuestService } from '../../../../core/services/guest.service';
import { Guest } from '../../../../core/models/guest.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-guest-list',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-list.component.html',
})
export class GuestListComponent implements OnInit {
  guests: Guest[] = [];
  loading = true;
  errorMessage = '';
  pagination: any = null;
  currentPage = 1;

  constructor(private guestService: GuestService) {}

  ngOnInit() {
    this.loadGuests(this.currentPage);
  }

  loadGuests(page: number = 1) {
    this.loading = true;
    this.guestService.getAll(page).subscribe({
      next: (response) => {
        this.guests = response.data;
        this.pagination = response.meta;
        this.loading = false;
      },
      error: () => {
        this.errorMessage = 'Error al cargar los huéspedes';
        this.loading = false;
      }
    });
  }

  loadPage(page: number) {
    if (page >= 1 && page <= this.pagination?.last_page) {
      this.currentPage = page;
      this.loadGuests(page);
    }
  }
}