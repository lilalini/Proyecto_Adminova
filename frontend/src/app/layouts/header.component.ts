import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AuthService } from '../core/services/auth.service';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [RouterLink],
  template: `
    <header class="bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-700 text-white shadow-xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo - SIEMPRE visible y lleva a home -->
          <a routerLink="/" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-2xl font-bold bg-gradient-to-r from-indigo-700 to-pink-700 bg-clip-text text-transparent">A</span>
            </div>
            <div>
              <h1 class="text-2xl font-bold tracking-tight">ADMINOVA</h1>
              <p class="text-xs text-indigo-200">Gestión profesional</p>
            </div>
          </a>

          <!-- Navegación - SOLO visible si está logueado -->
          @if (isLoggedIn) {
            <nav class="hidden md:flex items-center space-x-8">
              <a routerLink="/dashboard" class="text-white hover:text-pink-200 transition font-medium">Dashboard</a>
              <a routerLink="/admin/accommodations" class="text-white hover:text-pink-200 transition font-medium">Alojamientos</a>
              <a routerLink="/bookings" class="text-white hover:text-pink-200 transition font-medium">Reservas</a>
              <a routerLink="/guests" class="text-white hover:text-pink-200 transition font-medium">Huéspedes</a>
            </nav>
          }

          <!-- Perfil o botones de login -->
          <div class="flex items-center space-x-4">
            @if (isLoggedIn) {
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                  <span class="text-white font-semibold">{{ iniciales }}</span>
                </div>
                <button (click)="logout()" class="text-white/80 hover:text-white text-sm">
                  Salir
                </button>
              </div>
            } @else {
              <div class="flex items-center space-x-4">
                <a routerLink="/login" class="text-white hover:text-pink-200">Iniciar sesión</a>
                <a routerLink="/register" class="bg-white text-indigo-700 px-4 py-2 rounded-lg hover:bg-indigo-50 transition">
                  Registrarse
                </a>
              </div>
            }
          </div>
        </div>
      </div>
    </header>
  `
})
export class HeaderComponent implements OnInit {
  isLoggedIn = false;
  iniciales: string = 'AD';

  constructor(private auth: AuthService) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.isLoggedIn = !!user;
      if (user) {
        this.iniciales = (user.first_name[0] + user.last_name[0]).toUpperCase();
      }
    });
  }

  logout() {
    this.auth.logout().subscribe({
      next: () => {
        window.location.href = '/';
      }
    });
  }
}