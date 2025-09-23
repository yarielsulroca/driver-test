import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { SesionService } from '../../services/sesion.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  dni: string = '';
  token: string = '';

  constructor(private sesionService: SesionService) {}

  async handleLogin(): Promise<void> {
    this.sesionService.iniciarSesion(this.dni, this.token)
      .subscribe({
        next: (success) => {
          if (success) {
            // Manejar login exitoso
          } else {
            alert('Error al iniciar sesión');
          }
        },
        error: () => alert('Error al iniciar sesión')
      });
  }
}
