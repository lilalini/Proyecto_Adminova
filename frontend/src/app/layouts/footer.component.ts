import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule, RouterModule],
template: `
  <footer class="bg-gray-100 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-gray-600 text-sm text-center sm:text-left">
          © 2026 ADMINOVA - Sistema de gestión de alojamientos
        </p>
        <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
          <a routerLink="/contact" class="text-gray-600 text-sm hover:text-indigo-600 transition">Contacto</a>
          <a routerLink="/terms" class="text-gray-600 text-sm hover:text-indigo-600 transition">Términos</a>
          <a routerLink="/privacy" class="text-gray-600 text-sm hover:text-indigo-600 transition">Privacidad</a>
          <a routerLink="/cookies" class="text-gray-600 text-sm hover:text-indigo-600 transition">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
`
})
export class FooterComponent {}