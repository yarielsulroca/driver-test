import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-inicio',
  imports: [CommonModule],
  templateUrl: './inicio.html',
  styleUrl: './inicio.scss',
  standalone: true
})
export class Inicio {
  constructor(private router: Router) {}

  goToLogin() {
    this.router.navigate(['/login']);
  }

  goToResults() {
    this.router.navigate(['/resultados']);
  }

  goToConductores() {
    this.router.navigate(['/conductores']);
  }

  goToAdmin() {
    this.router.navigate(['/admin']);
  }

  goToExams() {
    this.router.navigate(['/examenes']);
  }
}
