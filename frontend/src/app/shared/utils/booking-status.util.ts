import { Booking } from '../../core/models/booking.model';

export function getBookingStatus(booking: Booking): string {
  const today = new Date();
  const checkIn = new Date(booking.check_in);
  const checkOut = new Date(booking.check_out);

  today.setHours(0,0,0,0);
  checkIn.setHours(0,0,0,0);
  checkOut.setHours(0,0,0,0);

  if (booking.status === 'cancelled') return 'cancelled';

  if (today > checkOut) return 'checked_out';

  if (today >= checkIn && today <= checkOut) return 'checked_in';

  return booking.status;
}

export function getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    checked_in: 'bg-purple-100 text-purple-800',
    checked_out: 'bg-gray-100 text-gray-800'
  };

  return classes[status] || 'bg-gray-100 text-gray-800';
}

export function getStatusText(status: string): string {
  const texts: Record<string, string> = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    cancelled: 'Cancelada',
    checked_in: 'Check-in',
    checked_out: 'Check-out'
  };

  return texts[status] || status;
}


// Función para formatear fechas relativas (para notificaciones)
export function getRelativeDate(date: string): string {
  if (!date) return '';
  
  const d = new Date(date);
  if (isNaN(d.getTime())) return '';
  
  const now = new Date();
  const diff = now.getTime() - d.getTime();
  const hours = Math.floor(diff / (1000 * 60 * 60));
  
  if (hours < 24) {
    if (hours < 1) return 'Hace unos minutos';
    return `Hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
  }
  
  return d.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}