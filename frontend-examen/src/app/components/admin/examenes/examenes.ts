import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-examenes',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './examenes.html',
  styleUrl: './examenes.scss',
  standalone: true
})
export class Examenes implements OnInit {
  loading = false;
  error = '';
  examenes: any[] = [];
  examenesFiltrados: any[] = [];
  searchTerm = '';
  selectedCategoria = '';
  categorias: any[] = [];

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    console.log('🚀 Componente Examenes inicializado');
    // Cargar categorías primero, luego exámenes
    this.cargarCategorias().then(() => {
      this.cargarExamenes();
    });
  }

  async cargarExamenes() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    console.log('🔄 Cargando exámenes...');
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any[]}>(`${environment.apiUrl}/examenes`)
      );

      console.log('📊 Respuesta de exámenes:', response);

      if (response.status === 'success' && response.data) {
        // Los exámenes ya vienen con la información básica
        this.examenes = response.data.map((examen) => {
          console.log(`🔍 Procesando examen ${examen.examen_id}:`, examen);
          
          // Usar los campos categoria_codigo y categoria_nombre que vienen del backend
          if (examen.categoria_codigo && examen.categoria_codigo !== 'Sin categoría') {
            examen.categoria_nombres = `${examen.categoria_codigo} - ${examen.categoria_nombre}`;
            console.log(`✅ Examen ${examen.examen_id} tiene categorías:`, examen.categoria_nombres);
          } else {
            examen.categoria_nombres = 'Sin categorías asignadas';
            console.log(`⚠️ Examen ${examen.examen_id} sin categorías`);
          }
          return examen;
        });
        
        this.examenesFiltrados = [...this.examenes];
        console.log('✅ Exámenes cargados:', this.examenes.length);
      } else {
        this.examenes = [];
        this.examenesFiltrados = [];
        console.log('⚠️ No se encontraron exámenes');
      }
    } catch (error: any) {
      console.error('❌ Error cargando exámenes:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'La ruta de exámenes no existe en la API.';
      } else {
        this.error = `Error al cargar los exámenes: ${error.message}`;
      }
      
      this.examenes = [];
      this.examenesFiltrados = [];
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🏁 Carga de exámenes finalizada');
    }
  }

  async cargarCategorias() {
    console.log('🔄 Cargando categorías para filtros...');
    
    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any[]}>(`${environment.apiUrl}/categorias`)
      );

      if (response.status === 'success' && response.data) {
        this.categorias = response.data;
        console.log('✅ Categorías cargadas para filtros:', this.categorias.length);
      }
    } catch (error: any) {
      console.error('❌ Error cargando categorías:', error);
      this.categorias = [];
    }
  }

  aplicarFiltros() {
    console.log('🔍 Aplicando filtros...');
    
    this.examenesFiltrados = this.examenes.filter(examen => {
      const coincideBusqueda = !this.searchTerm || 
        examen.titulo?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        examen.descripcion?.toLowerCase().includes(this.searchTerm.toLowerCase());
      
      const coincideCategoria = !this.selectedCategoria || 
        examen.categoria_id == this.selectedCategoria;
      
      return coincideBusqueda && coincideCategoria;
    });

    console.log('📊 Exámenes filtrados:', this.examenesFiltrados.length);
  }

  limpiarFiltros() {
    this.searchTerm = '';
    this.selectedCategoria = '';
    this.examenesFiltrados = [...this.examenes];
    console.log('🧹 Filtros limpiados');
  }

  crearNuevoExamen() {
    console.log('🔄 Navegando a crear examen...');
    this.router.navigate(['/admin/examenes/crear']);
  }

  editarExamen(id: number) {
    console.log('🔄 Editando examen:', id);
    this.router.navigate(['/admin/examenes/editar', id]);
  }

  async eliminarExamen(id: number) {
    console.log('🗑️ Eliminando examen:', id);
    
    if (confirm('¿Estás seguro de que quieres eliminar este examen?')) {
      try {
        const response = await firstValueFrom(
          this.http.delete<{status: string, message: string}>(`${environment.apiUrl}/examenes/${id}`)
        );

        if (response.status === 'success') {
          console.log('✅ Examen eliminado exitosamente');
          this.cargarExamenes(); // Recargar la lista
        } else {
          console.error('❌ Error al eliminar examen:', response.message);
          alert('Error al eliminar el examen: ' + response.message);
        }
      } catch (error: any) {
        console.error('❌ Error eliminando examen:', error);
        alert('Error al eliminar el examen: ' + error.message);
      }
    }
  }

  getCategoriaNombre(examen: any): string {
    if (examen.categoria_nombres) {
      return examen.categoria_nombres;
    }
    if (examen.categoria_codigo && examen.categoria_codigo !== 'Sin categoría') {
      return `${examen.categoria_codigo} - ${examen.categoria_nombre}`;
    }
    return 'Sin categorías asignadas';
  }

  getEstadoText(estado: string): string {
    const estados: { [key: string]: string } = {
      'activo': 'Activo',
      'inactivo': 'Inactivo',
      'borrador': 'Borrador'
    };
    return estados[estado] || estado;
  }

  getEstadoClass(estado: string): string {
    const clases: { [key: string]: string } = {
      'activo': 'estado-activo',
      'inactivo': 'estado-inactivo',
      'borrador': 'estado-borrador'
    };
    return clases[estado] || 'estado-default';
  }
} 