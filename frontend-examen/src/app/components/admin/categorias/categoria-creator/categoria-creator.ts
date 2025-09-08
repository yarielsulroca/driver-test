import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-categoria-creator',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './categoria-creator.html',
  styleUrl: './categoria-creator.scss',
  standalone: true
})
export class CategoriaCreator implements OnInit {
  loading = false;
  error = '';
  success = '';
  
  categoria = {
    nombre: '',
    descripcion: '',
    estado: 'activo'
  };

  constructor(
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit() {
    console.log('🚀 Componente CategoriaCreator inicializado');
  }

  async crearCategoria() {
    if (!this.validarFormulario()) {
      return;
    }

    this.loading = true;
    this.error = '';
    this.success = '';

    console.log('🔄 Creando categoría...');
    console.log('📍 API URL:', environment.apiUrl);
    console.log('📊 Datos:', this.categoria);

    try {
      // Limpiar espacios en blanco de todos los campos
      const datosLimpios = {
        nombre: this.categoria.nombre.trim(),
        descripcion: this.categoria.descripcion.trim(),
        estado: this.categoria.estado
      };
      
      console.log('🔍 Headers de la petición:', {
        'Content-Type': 'application/json',
        'Authorization': localStorage.getItem('jwt_token') ? 'Bearer ' + localStorage.getItem('jwt_token') : 'No token'
      });
      
      console.log('📤 Datos enviados al backend:', datosLimpios);
      
      const response = await firstValueFrom(
        this.http.post<{status: string, message: string, data: any}>(`${environment.apiUrl}/categorias`, datosLimpios)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success') {
        this.success = 'Categoría creada exitosamente';
        console.log('✅ Categoría creada:', response.data);
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.router.navigate(['/admin/categorias']);
        }, 2000);
      } else {
        this.error = response.message || 'Error al crear la categoría';
      }
    } catch (error: any) {
      console.error('❌ Error creando categoría:', error);
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
        this.error = `Error al crear la categoría: ${error.message}`;
      }
    } finally {
      this.loading = false;
    }
  }

  validarFormulario(): boolean {
    if (!this.categoria.nombre.trim()) {
      this.error = 'El nombre es obligatorio';
      return false;
    }
    
    if (!this.categoria.descripcion.trim()) {
      this.error = 'La descripción es obligatoria';
      return false;
    }
    
    this.error = '';
    return true;
  }

  cancelar() {
    this.router.navigate(['/admin/categorias']);
  }
} 