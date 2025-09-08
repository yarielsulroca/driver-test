import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ApiService } from '../../../services/api.service';

@Component({
  selector: 'app-login',
  imports: [FormsModule, CommonModule],
  templateUrl: './login.html',
  styleUrl: './login.scss',
  standalone: true
})
export class Login {
  dni = '';
  loading = false;
  error = '';

  constructor(
    private router: Router,
    private apiService: ApiService
  ) {}

  async onSubmit() {
    if (!this.dni) {
      this.error = 'Por favor, ingresá tu DNI';
      return;
    }
    this.loading = true;
    this.error = '';
    
    try {
      const response = await this.apiService.post('/auth/login', {
        dni: this.dni
      }).toPromise();
      
      if (response && response.success) {
        const data = response.data as any;
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.conductor));
        this.router.navigate(['/examenes'], {
          state: {
            conductor: data.conductor,
            examenes: data.examenes?.detalle || []
          }
        });
      } else {
        this.error = response?.message || 'Error al ingresar';
      }
    } catch (error: any) {
      this.error = error.message || 'Error al ingresar';
    } finally {
      this.loading = false;
    }
  }
}
