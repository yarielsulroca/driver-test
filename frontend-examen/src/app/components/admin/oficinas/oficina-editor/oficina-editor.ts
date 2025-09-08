import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-oficina-editor',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './oficina-editor.html',
  styleUrl: './oficina-editor.scss',
  standalone: true
})
export class OficinaEditor implements OnInit {
  loading = false;
  saving = false;
  error = '';
  success = '';
  oficinaId: number = 0;
  
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
    private route: ActivatedRoute,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    console.log('🔧 Constructor de OficinaEditor llamado');
  }

  ngOnInit() {
    console.log('🚀 Componente OficinaEditor inicializado');
    console.log('🔍 URL actual:', this.router.url);
    console.log('🔍 Parámetros de ruta en ngOnInit:', this.route.snapshot.paramMap);
    this.cargarOficina();
  }

  async cargarOficina() {
    console.log('🔍 Iniciando carga de oficina...');
    console.log('🔍 Parámetros de ruta:', this.route.snapshot.paramMap);
    
    this.oficinaId = Number(this.route.snapshot.paramMap.get('id'));
    console.log('🔍 ID de oficina extraído:', this.oficinaId);
    
    if (!this.oficinaId) {
      this.error = 'ID de oficina no válido';
      console.error('❌ ID de oficina no válido');
      return;
    }

    this.loading = true;
    this.error = '';

    console.log('🔄 Cargando oficina ID:', this.oficinaId);
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any}>(`${environment.apiUrl}/escuelas/${this.oficinaId}`)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success' && response.data) {
        this.oficina = {
          nombre: response.data.nombre || '',
          direccion: response.data.direccion || '',
          ciudad: response.data.ciudad || '',
          telefono: response.data.telefono || '',
          email: response.data.email || '',
          estado: response.data.estado || 'activo'
        };
        console.log('✅ Oficina cargada:', this.oficina);
        console.log('🔄 Forzando detección de cambios...');
        this.cdr.detectChanges();
      } else {
        this.error = 'No se pudo cargar la oficina';
      }
    } catch (error: any) {
      console.error('❌ Error cargando oficina:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'La oficina no fue encontrada.';
      } else {
        this.error = `Error al cargar la oficina: ${error.message}`;
      }
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🔄 Estado final - loading:', this.loading, 'error:', this.error);
    }
  }

  async actualizarOficina() {
    if (!this.validarFormulario()) {
      return;
    }

    this.saving = true;
    this.error = '';
    this.success = '';

    console.log('🔄 Actualizando oficina ID:', this.oficinaId);
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
      
      console.log('📤 Datos enviados al backend:', datosLimpios);
      
      const response = await firstValueFrom(
        this.http.put<{status: string, message: string, data: any}>(`${environment.apiUrl}/escuelas/${this.oficinaId}`, datosLimpios)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success') {
        this.success = 'Oficina actualizada exitosamente';
        console.log('✅ Oficina actualizada:', response.data);
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.router.navigate(['/admin/oficinas']);
        }, 2000);
      } else {
        this.error = response.message || 'Error al actualizar la oficina';
      }
    } catch (error: any) {
      console.error('❌ Error actualizando oficina:', error);
      
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
      } else if (error.status === 404) {
        this.error = 'La oficina no fue encontrada.';
      } else if (error.status === 422) {
        this.error = 'Datos inválidos. Verifica la información ingresada.';
      } else {
        this.error = `Error al actualizar la oficina: ${error.message}`;
      }
    } finally {
      this.saving = false;
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