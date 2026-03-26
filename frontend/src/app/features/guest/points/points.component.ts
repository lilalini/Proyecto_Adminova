import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-points',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './points.component.html',
})
export class PointsComponent implements OnInit {
  pointsBalance = 0;
  pointsHistory: any[] = [];
  loading = true;

  ngOnInit() {
    this.loadPoints();
  }

  loadPoints() {
    // Aquí vendrá la llamada al backend
    setTimeout(() => {
      this.pointsBalance = 1250;
      this.pointsHistory = [
        {
          id: 1,
          date: '2026-03-20',
          description: 'Reserva confirmada - Apartamento Madrid',
          points: 250,
          type: 'earned'
        },
        {
          id: 2,
          date: '2026-03-15',
          description: 'Reserva completada - Loft Barcelona',
          points: 500,
          type: 'earned'
        },
        {
          id: 3,
          date: '2026-03-10',
          description: 'Canje por descuento de 20€',
          points: 200,
          type: 'redeemed'
        },
        {
          id: 4,
          date: '2026-03-01',
          description: 'Bono de bienvenida',
          points: 300,
          type: 'earned'
        }
      ];
      this.loading = false;
    }, 500);
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
}