import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

export type SkeletonType =
  | 'card'
  | 'stat'
  | 'list-item'
  | 'button'
  | 'default';

@Component({
  selector: 'app-skeleton',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './skeleton.component.html',
})
export class SkeletonComponent {

  @Input() type: SkeletonType = 'card';

  // solo para default
  @Input() width = '100%';
  @Input() height = '20px';
  @Input() className = '';
}