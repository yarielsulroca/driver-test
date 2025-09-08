import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-categoria-editor',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './categoria-editor.html',
  styleUrl: './categoria-editor.scss',
  standalone: true
})
export class CategoriaEditor implements OnInit {
  loading = false;
  saving = false;
  error = '';
  success = '';
  categoriaId: number = 0;
  
  categoria = {
    nombre: '',
    descripcion: '',
    estado: 'activo'
  };

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    console.log('🔧 Constructor de CategoriaEditor llamado');
  }

  ngOnInit() {
    console.log('🚀 Componente CategoriaEditor inicializado');
    this.cargarCategoria();
  }

  async cargarCategoria() {
    console.log('🔍 Iniciando carga de categoría...');
    console.log('🔍 Parámetros de ruta:', this.route.snapshot.paramMap);
    
    this.categoriaId = Number(this.route.snapshot.paramMap.get('id'));
    console.log('🔍 ID de categoría extraído:', this.categoriaId);
    
    if (!this.categoriaId) {
      this.error = 'ID de categoría no válido';
      console.error('❌ ID de categoría no válido');
      return;
    }

    this.loading = true;
    this.error = '';

    console.log('🔄 Cargando categoría ID:', this.categoriaId);
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any}>(`${environment.apiUrl}/categorias/${this.categoriaId}`)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success' && response.data) {
        this.categoria = {
          nombre: response.data.nombre || '',
          descripcion: response.data.descripcion || '',
          estado: response.data.estado || 'activo'
        };
        console.log('✅ Categoría cargada:', this.categoria);
        console.log('🔄 Forzando detección de cambios...');
        this.cdr.detectChanges();
      } else {
        this.error = 'No se pudo cargar la categoría';
      }
    } catch (error: any) {
      console.error('❌ Error cargando categoría:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'La categoría no fue encontrada.';
      } else {
        this.error = `Error al cargar la categoría: ${error.message}`;
      }
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🔄 Estado final - loading:', this.loading, 'error:', this.error);
    }
  }

  async actualizarCategoria() {
    if (!this.validarFormulario()) {
      return;
    }

    this.saving = true;
    this.error = '';
    this.success = '';

    console.log('🔄 Actualizando categoría ID:', this.categoriaId);
    console.log('📊 Datos:', this.categoria);

    try {
      // Limpiar espacios en blanco de todos los campos
      const datosLimpios = {
        nombre: this.categoria.nombre.trim(),
        descripcion: this.categoria.descripcion.trim(),
        estado: this.categoria.estado
      };
      
      console.log('📤 Datos enviados al backend:', datosLimpios);
      
      const response = await firstValueFrom(
        this.http.put<{status: string, message: string, data: any}>(`${environment.apiUrl}/categorias/${this.categoriaId}`, datosLimpios)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success') {
        this.success = 'Categoría actualizada exitosamente';
        console.log('✅ Categoría actualizada:', response.data);
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.router.navigate(['/admin/categorias']);
        }, 2000);
      } else {
        this.error = response.message || 'Error al actualizar la categoría';
      }
    } catch (error: any) {
      console.error('❌ Error actualizando categoría:', error);
      
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
        this.error = 'La categoría no fue encontrada.';
      } else if (error.status === 422) {
        this.error = 'Datos inválidos. Verifica la información ingresada.';
      } else {
        this.error = `Error al actualizar la categoría: ${error.message}`;
      }
    } finally {
      this.saving = false;
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