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

  constructor(private publicService: PublicService) {}

  ngOnInit() {
    this.loadAccommodations();
  }

  private formatCity(city: string): string {
    return city.trim();
  }

  loadAccommodations(page: number = 1) {
    this.loading = true;
    this.currentPage = page;
    
    this.publicService.getAccommodations(page).subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.lastPage = response.meta?.last_page || 1;
        this.total = response.meta?.total || 0;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error cargando alojamientos:', error);
        this.loading = false;
      }
    });
  }

  onSortChange(event: Event) {
    const order = (event.target as HTMLSelectElement).value;
    const sorted = [...this.accommodations];
    
    switch (order) {
      case 'newest':
        sorted.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
        break;
      case 'price_desc':
        sorted.sort((a, b) => b.base_price - a.base_price);
        break;
      case 'price_asc':
        sorted.sort((a, b) => a.base_price - b.base_price);
        break;
      case 'bedrooms_desc':
        sorted.sort((a, b) => b.bedrooms - a.bedrooms);
        break;
      case 'bedrooms_asc':
        sorted.sort((a, b) => a.bedrooms - b.bedrooms);
        break;
    }
    
    this.accommodations = sorted;
  }

  onSearchEnter(event: Event) {
    event.preventDefault();
    this.cityInput.nativeElement.blur();
    this.performSearch();
  }

  performSearch() {
    this.loading = true;
    
    const params: any = { page: 1 };
    
    if (this.searchCity.trim()) {
      params.city = this.formatCity(this.searchCity);
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

  goToPage(page: number) {
    if (page < 1 || page > this.lastPage || page === this.currentPage) return;
    
    this.loading = true;
    
    if (this.hasActiveFilters()) {
      const params: any = { page };
      
      if (this.searchCity.trim()) {
        params.city = this.formatCity(this.searchCity);
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
          console.error('Error en paginación:', error);
          this.loading = false;
        }
      });
    } else {
      this.loadAccommodations(page);
    }
  }

  hasActiveFilters(): boolean {
    return !!(this.searchCity.trim() || this.searchCheckIn || this.searchCheckOut || this.searchGuests > 1);
  }

  clearFilter(filter: string) {
    switch(filter) {
      case 'city':
        this.searchCity = '';
        break;
      case 'dates':
        this.searchCheckIn = '';
        this.searchCheckOut = '';
        break;
      case 'guests':
        this.searchGuests = 1;
        break;
    }
    this.performSearch();
  }

  clearAllFilters() {
    this.searchCity = '';
    this.searchCheckIn = '';
    this.searchCheckOut = '';
    this.searchGuests = 1;
    this.loadAccommodations(1);
  }
}