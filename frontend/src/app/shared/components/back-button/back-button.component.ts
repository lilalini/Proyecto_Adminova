import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../icon-svg/icon-svg.component';

@Component({
  selector: 'app-back-button',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  template: `
<a [routerLink]="backUrl" 
   class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 transition-colors duration-200 translate-y-px">
  <app-icon-svg name="arrow-left" class="w-5 h-5"></app-icon-svg>
  <span class="text-base font-bold">{{ text }}</span>
</a>
  `
})
export class BackButtonComponent {
  @Input() backUrl: string = '/';
  @Input() text: string = 'Volver';
}