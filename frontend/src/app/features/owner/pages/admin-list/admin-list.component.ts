import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { OwnerService } from '../../../../core/services/owner.service';
import { Owner } from '../../../../core/models/owner.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-admin-owner-list',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-list.component.html',
})
export class AdminOwnerListComponent implements OnInit {
  owners: Owner[] = [];
  loading = true;
  errorMessage = '';
  pagination: any = null;
  currentPage = 1;

  showDeleteModal = false;
  deleteModalMessage = '';
  ownerToDelete: number | null = null;

  constructor(private ownerService: OwnerService) {}

  ngOnInit() {
    this.loadOwners(this.currentPage);
  }

  loadOwners(page: number = 1) {
    this.loading = true;
    this.ownerService.getAll(page).subscribe({
      next: (response) => {
        this.owners = response.data;
        this.pagination = response.meta;
        this.loading = false;
      },
      error: () => {
        this.errorMessage = 'Error al cargar los propietarios';
        this.loading = false;
      }
    });
  }

  loadPage(page: number) {
    if (page >= 1 && page <= this.pagination?.last_page) {
      this.currentPage = page;
      this.loadOwners(page);
    }
  }

  confirmDelete(id: number, name: string) {
    this.ownerToDelete = id;
    this.deleteModalMessage = `¿Estás seguro de que quieres eliminar al propietario "${name}"?`;
    this.showDeleteModal = true;
  }

  cancelDelete() {
    this.showDeleteModal = false;
    this.ownerToDelete = null;
  }

  deleteOwner() {
    if (!this.ownerToDelete) return;
    const currentPage = this.pagination?.current_page || 1;

    this.ownerService.delete(this.ownerToDelete).subscribe({
      next: () => {
        this.showDeleteModal = false;
        this.ownerToDelete = null;
        this.loadOwners(currentPage);
      },
      error: () => {
        this.showDeleteModal = false;
      }
    });
  }
}