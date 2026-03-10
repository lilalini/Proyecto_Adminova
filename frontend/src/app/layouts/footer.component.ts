import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule],
  template: `
    <footer class="bg-gray-100 mt-auto">
      <div class="max-w-7xl mx-auto px-4 py-6">
        <p class="text-center text-gray-600">© 2026 ADMINOVA - Sistema de gestión de alojamientos</p>
      </div>
    </footer>
  `
})
export class FooterComponent {}