import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-oficina-creator',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './oficina-creator.html',
  styleUrl: './oficina-creator.scss',
  standalone: true
})
export class OficinaCreator implements OnInit {
  loading = false;
  error = '';
  success = '';
  
  oficina = {
    nombre: '',
    direccion: '',
    ciudad: '',
    telefono: '',
    email: '',
    estado: 'activo'
  };

  constructor(
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit() {
    console.log('🚀 Componente OficinaCreator inicializado');
  }

  async crearOficina() {
    if (!this.validarFormulario()) {
      return;
    }

    this.loading = true;
    this.error = '';
    this.success = '';

    console.log('🔄 Creando oficina...');
    console.log('📍 API URL:', environment.apiUrl);
    console.log('📊 Datos:', this.oficina);

    try {
      // Limpiar espacios en blanco de todos los campos
      const datosLimpios = {
        nombre: this.oficina.nombre.trim(),
        direccion: this.oficina.direccion.trim(),
        ciudad: this.oficina.ciudad.trim(),
        telefono: this.oficina.telefono.trim(),
        email: this.oficina.email.trim(),
        estado: this.oficina.estado
      };
      
      console.log('🔍 Headers de la petición:', {
        'Content-Type': 'application/json',
        'Authorization': localStorage.getItem('jwt_token') ? 'Bearer ' + localStorage.getItem('jwt_token') : 'No token'
      });
      
      console.log('📤 Datos enviados al backend:', datosLimpios);
      
      const response = await firstValueFrom(
        this.http.post<{status: string, message: string, data: any}>(`${environment.apiUrl}/escuelas`, datosLimpios)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success') {
        this.success = 'Oficina creada exitosamente';
        console.log('✅ Oficina creada:', response.data);
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.router.navigate(['/admin/oficinas']);
        }, 2000);
      } else {
        this.error = response.message || 'Error al crear la oficina';
      }
    } catch (error: any) {
      console.error('❌ Error creando oficina:', error);
      console.error('🔍 Detalles del error:', {
        status: error.status,
        statusText: error.statusText,
        url: error.url,
        error: error.error,
        message: error.message
      });
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 400) {
        let errorMessage = 'Datos inválidos';
        if (error.error?.message) {
          errorMessage = error.error.message;
        } else if (error.error?.errors) {
          // Si hay errores de validación específicos
          const validationErrors = Object.values(error.error.errors).flat();
          errorMessage = validationErrors.join(', ');
        } else if (error.message) {
          errorMessage = error.message;
        }
        this.error = `Error 400: ${errorMessage}`;
      } else if (error.status === 422) {
        this.error = 'Datos inválidos. Verifica la información ingresada.';
      } else {
        this.error = `Error al crear la oficina: ${error.message}`;
      }
    } finally {
      this.loading = false;
    }
  }

  validarFormulario(): boolean {
    if (!this.oficina.nombre.trim()) {
      this.error = 'El nombre es obligatorio';
      return false;
    }
    
    if (!this.oficina.direccion.trim()) {
      this.error = 'La dirección es obligatoria';
      return false;
    }
    
    if (!this.oficina.ciudad.trim()) {
      this.error = 'La ciudad es obligatoria';
      return false;
    }
    
    if (!this.oficina.telefono.trim()) {
      this.error = 'El teléfono es obligatorio';
      return false;
    }
    
    if (!this.oficina.email.trim()) {
      this.error = 'El email es obligatorio';
      return false;
    }
    
    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(this.oficina.email)) {
      this.error = 'El formato del email no es válido';
      return false;
    }
    
    this.error = '';
    return true;
  }

  cancelar() {
    this.router.navigate(['/admin/oficinas']);
  }
}