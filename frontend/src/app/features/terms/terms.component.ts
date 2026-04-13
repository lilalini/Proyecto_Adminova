import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-terms',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './terms.component.html',
})
export class TermsComponent {
  today = new Date();
}