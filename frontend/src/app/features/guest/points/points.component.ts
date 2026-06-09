import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { LoyaltyPointService } from '../../../core/services/loyalty-point.service';
import { GuestService } from '../../../core/services/guest.service';
import { AuthService } from '../../../core/services/auth.service';
import { first } from 'rxjs/operators';
import { BackButtonComponent } from '../../../shared/components/back-button/back-button.component';

interface Guest {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
}

@Component({
  selector: 'app-points',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, BackButtonComponent],
  templateUrl: './points.component.html',
})
export class PointsComponent implements OnInit {
  pointsBalance = 0;
  pointsHistory: any[] = [];
  loading = true;

  constructor(
    private loyaltyPointService: LoyaltyPointService,
    private guestService: GuestService,
    private auth: AuthService
  ) {}

  ngOnInit() {
    this.loadPoints();
  }




  loadPoints() {
  this.auth.currentUser$.pipe(first()).subscribe(user => {
    if (!user) {
      this.loading = false;
      return;
    }

    this.guestService.getByUserId(user.id).subscribe({
      next: (response: any) => {
        const guest = response.data;
        if (guest) {
          // Ejecutar ambas peticiones en paralelo
          Promise.all([
            this.loyaltyPointService.getBalance(guest.id).toPromise(),
            this.loyaltyPointService.getHistory(guest.id).toPromise()
          ])
            .then(([balanceResponse, historyResponse]: any) => {
              this.pointsBalance = balanceResponse.balance;
              this.pointsHistory = historyResponse.data.map((point: any) => ({
                id: point.id,
                date: point.created_at,
                description: point.description,
                points: Math.abs(point.points),
                type: point.type === 'earned' ? 'earned' : 
                      (point.type === 'redeemed' ? 'redeemed' : 'other')
              }));
              this.loading = false;
            })
            .catch(err => {
              console.error('Error cargando puntos:', err);
              this.loading = false;
            });
        } else {
          this.loading = false;
        }
      },
      error: (err: any) => {
        console.error('Error buscando guest:', err);
        this.loading = false;
      }
    });
  });
}

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
}