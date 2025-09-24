import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { LoadingComponent } from './components/shared/loading/loading.component';
import { NotificationsComponent } from './components/shared/notifications/notifications.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, CommonModule, LoadingComponent, NotificationsComponent],
  templateUrl: './app.html',
  styleUrl: './app.scss',
  standalone: true,
  host: {
    'class': 'municipio-theme'
  }
})
export class App {
  protected title = 'frontend-examen';
}
