import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatCardModule } from '@angular/material/card';
import { Subscription } from 'rxjs';
import { LoadingService } from '../../../services/loading.service';

@Component({
  selector: 'app-loading',
  standalone: true,
  imports: [
    CommonModule,
    MatProgressBarModule,
    MatProgressSpinnerModule,
    MatCardModule
  ],
  template: `
    <div *ngIf="isLoading" class="loading-overlay">
      <mat-card class="loading-card">
        <mat-card-content class="loading-content">
          <mat-spinner diameter="50"></mat-spinner>
          <p class="loading-message">{{ loadingMessage }}</p>
          <mat-progress-bar 
            *ngIf="progress !== undefined" 
            [value]="progress" 
            mode="determinate"
            class="loading-progress">
          </mat-progress-bar>
        </mat-card-content>
      </mat-card>
    </div>
  `,
  styles: [`
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .loading-card {
      min-width: 300px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .loading-content {
      padding: 2rem;
    }

    .loading-message {
      margin-top: 1rem;
      color: #666;
      font-size: 1rem;
    }

    .loading-progress {
      margin-top: 1rem;
    }

    mat-spinner {
      margin: 0 auto;
    }
  `]
})
export class LoadingComponent implements OnInit, OnDestroy {
  isLoading = false;
  loadingMessage = 'Cargando...';
  progress?: number;

  private subscription = new Subscription();

  constructor(private loadingService: LoadingService) {}

  ngOnInit(): void {
    this.subscription.add(
      this.loadingService.globalLoading$.subscribe(state => {
        this.isLoading = state.isLoading;
        this.loadingMessage = state.message || 'Cargando...';
        this.progress = state.progress;
      })
    );
  }

  ngOnDestroy(): void {
    this.subscription.unsubscribe();
  }
} 