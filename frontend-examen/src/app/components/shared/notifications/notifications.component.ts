import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatChipsModule } from '@angular/material/chips';
import { Subscription } from 'rxjs';
import { NotificationService, Notification } from '../../../services/notification.service';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [
    CommonModule,
    MatSnackBarModule,
    MatIconModule,
    MatButtonModule,
    MatChipsModule
  ],
  template: `
    <div class="notifications-container">
      <div 
        *ngFor="let notification of notifications; trackBy: trackByNotification" 
        class="notification-item"
        [ngClass]="'notification-' + notification.type">
        <div class="notification-content">
          <mat-icon class="notification-icon">
            {{ getIconForType(notification.type) }}
          </mat-icon>
          <span class="notification-message">{{ notification.message }}</span>
        </div>
        <button 
          mat-icon-button 
          class="notification-close"
          (click)="removeNotification(notification)">
          <mat-icon>close</mat-icon>
        </button>
      </div>
    </div>
  `,
  styles: [`
    .notifications-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      max-width: 400px;
    }

    .notification-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
      padding: 12px 16px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      animation: slideIn 0.3s ease-out;
      min-width: 300px;
    }

    .notification-content {
      display: flex;
      align-items: center;
      flex: 1;
    }

    .notification-icon {
      margin-right: 12px;
    }

    .notification-message {
      flex: 1;
      font-size: 14px;
      line-height: 1.4;
    }

    .notification-close {
      margin-left: 8px;
    }

    .notification-success {
      background-color: #4caf50;
      color: white;
    }

    .notification-error {
      background-color: #f44336;
      color: white;
    }

    .notification-warning {
      background-color: #ff9800;
      color: white;
    }

    .notification-info {
      background-color: #2196f3;
      color: white;
    }

    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }

    .notification-item.removing {
      animation: slideOut 0.3s ease-in;
    }
  `]
})
export class NotificationsComponent implements OnInit, OnDestroy {
  notifications: Notification[] = [];
  private subscription = new Subscription();

  constructor(private notificationService: NotificationService) {}

  ngOnInit(): void {
    this.subscription.add(
      this.notificationService.notifications$.subscribe(notifications => {
        this.notifications = notifications;
      })
    );
  }

  ngOnDestroy(): void {
    this.subscription.unsubscribe();
  }

  getIconForType(type: string): string {
    switch (type) {
      case 'success':
        return 'check_circle';
      case 'error':
        return 'error';
      case 'warning':
        return 'warning';
      case 'info':
        return 'info';
      default:
        return 'notifications';
    }
  }

  removeNotification(notification: Notification): void {
    const index = this.notifications.indexOf(notification);
    if (index > -1) {
      this.notificationService.removeNotification(index);
    }
  }

  trackByNotification(index: number, notification: Notification): string {
    return `${notification.type}-${notification.message}-${index}`;
  }
} 