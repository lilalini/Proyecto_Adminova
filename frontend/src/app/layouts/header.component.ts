import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AuthService } from '../core/services/auth.service';
import { User } from '../core/models/user.model';
import { IconSvgComponent } from '../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [RouterLink, IconSvgComponent],
  templateUrl: './header.component.html', 
})
export class HeaderComponent implements OnInit {
  isLoggedIn = false;
  userRole: string | null = null;
  user: User | null = null;
  iniciales: string = 'INVITADO';
  isMobileMenuOpen = false;

  constructor(private auth: AuthService) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.isLoggedIn = !!user;
      this.user = user;
      this.userRole = user?.role || null;
      if (user) {
        this.iniciales = (user.first_name[0] + user.last_name[0]).toUpperCase();
      }
    });
  }

  toggleMobileMenu() {
    this.isMobileMenuOpen = !this.isMobileMenuOpen;
  }

  logout() {
    this.auth.logout().subscribe({
      next: () => {
        window.location.href = '/';
      }
    });
  }
}