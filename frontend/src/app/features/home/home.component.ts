import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { PublicService } from '../../core/services/public.service';
import { IconSvgComponent } from '../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, IconSvgComponent],
  templateUrl: './home.component.html',
})
export class HomeComponent implements OnInit {
  @ViewChild('cityInput') cityInput!: ElementRef<HTMLInputElement>;

  accommodations: any[] = [];
  loading = true;
  currentPage = 1;
  lastPage = 1;
  total = 0;

  searchCity: string = '';
  searchCheckIn: string = '';
  searchCheckOut: string = '';
  searchGuests: number = 1;
  currentSort: string = 'newest';

  constructor(private publicService: PublicService) {}

  ngOnInit() {
    this.search(1);
  }

  // Método único que maneja todo — filtros, ordenación y paginación
  private search(page: number = 1) {
    this.loading = true;
    this.currentPage = page;

    const params: any = {
      page,
      sort: this.currentSort
    };

    if (this.searchCity.trim()) {
      params.city = this.searchCity.trim();
    }

    if (this.searchGuests > 1) {
      params.guests = this.searchGuests;
    }

    if (this.searchCheckIn && this.searchCheckOut) {
      params.check_in = this.searchCheckIn;
      params.check_out = this.searchCheckOut;
    }

    this.publicService.searchAccommodations(params).subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.lastPage = response.meta?.last_page || 1;
        this.total = response.meta?.total || 0;
        this.currentPage = response.meta?.current_page || 1;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error en búsqueda:', error);
        this.loading = false;
      }
    });
  }

  performSearch() {
    this.search(1);
  }

  onSortChange(event: Event) {
    this.currentSort = (event.target as HTMLSelectElement).value;
    this.search(1); // vuelve a página 1 con nuevo orden
  }

  onSearchEnter(event: Event) {
    event.preventDefault();
    this.cityInput.nativeElement.blur();
    this.performSearch();
  }

  goToPage(page: number) {
    if (page < 1 || page > this.lastPage || page === this.currentPage) return;
    this.search(page);
  }

  hasActiveFilters(): boolean {
    return !!(this.searchCity.trim() || this.searchCheckIn || this.searchCheckOut || this.searchGuests > 1);
  }

  clearFilter(filter: string) {
    switch (filter) {
      case 'city': this.searchCity = ''; break;
      case 'dates': this.searchCheckIn = ''; this.searchCheckOut = ''; break;
      case 'guests': this.searchGuests = 1; break;
    }
    this.search(1);
  }

  clearAllFilters() {
    this.searchCity = '';
    this.searchCheckIn = '';
    this.searchCheckOut = '';
    this.searchGuests = 1;
    this.search(1);
  }
}