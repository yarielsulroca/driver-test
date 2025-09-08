import { Injectable, NgZone } from '@angular/core';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { BehaviorSubject } from 'rxjs';

export interface Notification {
  message: string;
  type: 'success' | 'error' | 'warning' | 'info';
  duration?: number;
  action?: string;
}

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  private notificationsSubject = new BehaviorSubject<Notification[]>([]);
  public notifications$ = this.notificationsSubject.asObservable();
  
  // Flag para controlar cuándo actualizar las notificaciones
  private isInitializing = true;

  private defaultConfig: MatSnackBarConfig = {
    duration: 4000,
    horizontalPosition: 'end',
    verticalPosition: 'top',
    panelClass: []
  };

  constructor(private snackBar: MatSnackBar, private ngZone: NgZone) {
    // Permitir actualizaciones después de un breve delay
    setTimeout(() => {
      this.isInitializing = false;
    }, 100);
  }

  success(message: string, duration?: number, action?: string): void {
    this.showNotification(message, 'success', duration, action);
  }

  error(message: string, duration?: number, action?: string): void {
    this.showNotification(message, 'error', duration, action);
  }

  warning(message: string, duration?: number, action?: string): void {
    this.showNotification(message, 'warning', duration, action);
  }

  info(message: string, duration?: number, action?: string): void {
    this.showNotification(message, 'info', duration, action);
  }

  private showNotification(
    message: string,
    type: 'success' | 'error' | 'warning' | 'info',
    duration?: number,
    action?: string
  ): void {
    const config: MatSnackBarConfig = {
      ...this.defaultConfig,
      duration: duration || this.getDefaultDuration(type),
      panelClass: [`snackbar-${type}`]
    };

    this.snackBar.open(message, action || 'Cerrar', config);

    // Solo actualizar el estado si no estamos inicializando
    if (!this.isInitializing) {
      this.ngZone.runOutsideAngular(() => {
        setTimeout(() => {
          this.ngZone.run(() => {
            const notification: Notification = {
              message,
              type,
              duration: config.duration,
              action: action || 'Cerrar'
            };

            const currentNotifications = this.notificationsSubject.value;
            this.notificationsSubject.next([...currentNotifications, notification]);
          });
        }, 0);
      });
    }
  }

  private getDefaultDuration(type: string): number {
    switch (type) {
      case 'error':
        return 6000; // Errores se muestran más tiempo
      case 'warning':
        return 5000; // Advertencias un poco más
      case 'success':
      case 'info':
      default:
        return 4000; // Éxito e info estándar
    }
  }

  clearNotifications(): void {
    if (!this.isInitializing) {
      this.notificationsSubject.next([]);
    }
  }

  removeNotification(index: number): void {
    if (!this.isInitializing) {
      this.ngZone.runOutsideAngular(() => {
        setTimeout(() => {
          this.ngZone.run(() => {
            const currentNotifications = this.notificationsSubject.value;
            currentNotifications.splice(index, 1);
            this.notificationsSubject.next([...currentNotifications]);
          });
        }, 0);
      });
    }
  }

  // Métodos específicos para errores comunes
  showApiError(error: any): void {
    let message = 'Ha ocurrido un error inesperado';
    
    if (error.error?.message) {
      message = error.error.message;
    } else if (error.message) {
      message = error.message;
    } else if (typeof error === 'string') {
      message = error;
    }

    this.error(message);
  }

  showValidationError(errors: any): void {
    if (typeof errors === 'string') {
      this.error(errors);
      return;
    }

    if (errors && typeof errors === 'object') {
      const errorMessages = Object.values(errors).flat();
      if (errorMessages.length > 0) {
        this.error(errorMessages[0] as string);
      } else {
        this.error('Datos de validación incorrectos');
      }
    } else {
      this.error('Datos de validación incorrectos');
    }
  }

  showNetworkError(): void {
    this.error('Error de conexión. Verifique su conexión a internet.');
  }

  showUnauthorizedError(): void {
    this.error('No tiene permisos para realizar esta acción.');
  }

  showNotFoundError(): void {
    this.error('El recurso solicitado no fue encontrado.');
  }

  showServerError(): void {
    this.error('Error interno del servidor. Intente nuevamente más tarde.');
  }
} 