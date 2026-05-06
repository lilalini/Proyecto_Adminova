import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-terms-modal',
  standalone: true,
  imports: [CommonModule, IconSvgComponent],
  templateUrl: './terms-modal.component.html'
})
export class TermsModalComponent {
  @Input() cancellationPolicy: any = null;
  @Input() securityDeposit: number = 0;
  @Output() closed = new EventEmitter<void>();

  close() {
    this.closed.emit();
  }
}