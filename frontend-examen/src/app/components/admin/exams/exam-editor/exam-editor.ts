import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-exam-editor',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './exam-editor.html',
  styleUrl: './exam-editor.scss',
  standalone: true
})
export class ExamEditor implements OnInit {
  loading = false;
  saving = false;
  error = '';
  success = '';
  examId: number = 0;
  categorias: any[] = [];
  preguntas: any[] = [];
  
  examen = {
    titulo: '',
    nombre: '',
    descripcion: '',
    categorias: [] as number[],
    tiempo_limite: 60,
    duracion_minutos: 60,
    numero_preguntas: 10,
    puntaje_minimo: 70,
    estado: 'activo',
    dificultad: 'medio'
  };

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    console.log('🔧 Constructor de ExamEditor llamado');
  }

  ngOnInit() {
    console.log('🚀 Componente ExamEditor inicializado');
    this.cargarCategorias();
    this.cargarExamen();
  }

  async cargarCategorias() {
    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any[]}>(`${environment.apiUrl}/categorias`)
      );
      
      if (response.status === 'success') {
        this.categorias = response.data;
        console.log('✅ Categorías cargadas:', this.categorias.length);
      }
    } catch (error) {
      console.error('❌ Error cargando categorías:', error);
    }
  }

  async cargarExamen() {
    console.log('🔍 Iniciando carga de examen...');
    console.log('🔍 Parámetros de ruta:', this.route.snapshot.paramMap);
    
    this.examId = Number(this.route.snapshot.paramMap.get('id'));
    console.log('🔍 ID de examen extraído:', this.examId);
    
    if (!this.examId) {
      this.error = 'ID de examen no válido';
      console.error('❌ ID de examen no válido');
      return;
    }

    this.loading = true;
    this.error = '';

    console.log('🔄 Cargando examen ID:', this.examId);
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any}>(`${environment.apiUrl}/examenes/${this.examId}`)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success' && response.data) {
        this.examen = {
          titulo: response.data.titulo || '',
          nombre: response.data.nombre || response.data.titulo || '',
          descripcion: response.data.descripcion || '',
          categorias: response.data.categorias || [],
          tiempo_limite: response.data.tiempo_limite || 60,
          duracion_minutos: response.data.duracion_minutos || response.data.tiempo_limite || 60,
          numero_preguntas: response.data.numero_preguntas || 10,
          puntaje_minimo: response.data.puntaje_minimo || 70,
          estado: response.data.estado || 'activo',
          dificultad: response.data.dificultad || 'medio'
        };
        console.log('✅ Examen cargado:', this.examen);
        console.log('🔄 Forzando detección de cambios...');
        this.cdr.detectChanges();
      } else {
        this.error = 'No se pudo cargar el examen';
      }
    } catch (error: any) {
      console.error('❌ Error cargando examen:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'El examen no fue encontrado.';
      } else {
        this.error = `Error al cargar el examen: ${error.message}`;
      }
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🔄 Estado final - loading:', this.loading, 'error:', this.error);
    }
  }

  async actualizarExamen() {
    if (!this.validarFormulario()) {
      return;
    }

    this.saving = true;
    this.error = '';
    this.success = '';

    console.log('🔄 Actualizando examen ID:', this.examId);
    console.log('📊 Datos:', this.examen);

    try {
      // Limpiar espacios en blanco de todos los campos
      const datosLimpios = {
        titulo: this.examen.titulo.trim(),
        nombre: this.examen.nombre.trim(),
        descripcion: this.examen.descripcion.trim(),
        categorias: this.examen.categorias,
        tiempo_limite: this.examen.tiempo_limite,
        duracion_minutos: this.examen.duracion_minutos,
        numero_preguntas: this.examen.numero_preguntas,
        puntaje_minimo: this.examen.puntaje_minimo,
        estado: this.examen.estado,
        dificultad: this.examen.dificultad
      };
      
      console.log('📤 Datos enviados al backend:', datosLimpios);
      
      const response = await firstValueFrom(
        this.http.put<{status: string, message: string, data: any}>(`${environment.apiUrl}/examenes/${this.examId}`, datosLimpios)
      );

      console.log('📊 Respuesta:', response);

      if (response.status === 'success') {
        this.success = 'Examen actualizado exitosamente';
        console.log('✅ Examen actualizado:', response.data);
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.router.navigate(['/admin/examenes']);
        }, 2000);
      } else {
        this.error = response.message || 'Error al actualizar el examen';
      }
    } catch (error: any) {
      console.error('❌ Error actualizando examen:', error);
      
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
        this.error = 'El examen no fue encontrado.';
      } else if (error.status === 422) {
        this.error = 'Datos inválidos. Verifica la información ingresada.';
      } else {
        this.error = `Error al actualizar el examen: ${error.message}`;
      }
    } finally {
      this.saving = false;
    }
  }

  validarFormulario(): boolean {
    if (!this.examen.titulo.trim()) {
      this.error = 'El título es obligatorio';
      return false;
    }
    
    if (!this.examen.descripcion.trim()) {
      this.error = 'La descripción es obligatoria';
      return false;
    }
    
    if (!this.examen.categorias || this.examen.categorias.length === 0) {
      this.error = 'Debe seleccionar al menos una categoría';
      return false;
    }
    
    if (this.examen.tiempo_limite <= 0) {
      this.error = 'El tiempo límite debe ser mayor a 0';
      return false;
    }
    
    if (this.examen.numero_preguntas <= 0) {
      this.error = 'El número de preguntas debe ser mayor a 0';
      return false;
    }
    
    if (this.examen.puntaje_minimo < 0 || this.examen.puntaje_minimo > 100) {
      this.error = 'El puntaje mínimo debe estar entre 0 y 100';
      return false;
    }
    
    this.error = '';
    return true;
  }

  toggleCategoria(categoriaId: number) {
    const index = this.examen.categorias.indexOf(categoriaId);
    if (index > -1) {
      this.examen.categorias.splice(index, 1);
    } else {
      this.examen.categorias.push(categoriaId);
    }
    console.log('🔄 Categorías actualizadas:', this.examen.categorias);
  }

  cancelar() {
    this.router.navigate(['/admin/examenes']);
  }
} 