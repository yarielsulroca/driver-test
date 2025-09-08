import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

interface Examen {
  examen_id: number;
  titulo: string;
  descripcion: string;
  categoria_id: number;
  categoria_nombre: string;
  tiempo_limite: number;
  numero_preguntas: number;
  puntaje_minimo: number;
  estado: string;
  created_at: string;
  updated_at: string;
}

@Component({
  selector: 'app-exam-list',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './exam-list.html',
  styleUrl: './exam-list.scss',
  standalone: true
})
export class ExamList implements OnInit {
  loading = false;
  error = '';
  examenes: Examen[] = [];
  filteredExamenes: Examen[] = [];
  searchTerm = '';
  selectedEstado = '';

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    console.log('🔧 Constructor de ExamList llamado');
  }

  ngOnInit() {
    console.log('🚀 Componente ExamList inicializado');
    this.cargarExamenes();
  }

  async cargarExamenes() {
    this.loading = true;
    this.error = '';

    try {
      console.log('📡 Cargando exámenes...');
      console.log('📍 API URL:', environment.apiUrl);
      
      const response = await firstValueFrom(
        this.http.get(`${environment.apiUrl}/examenes`)
      );
      
      if (response && (response as any).status === 'success') {
        this.examenes = (response as any).data || [];
        this.filteredExamenes = [...this.examenes];
        console.log('✅ Exámenes cargados:', this.examenes.length);
      } else {
        console.error('❌ Error al cargar exámenes:', response);
        this.error = 'Error al cargar los exámenes';
      }
    } catch (error: any) {
      console.error('❌ Error al cargar exámenes:', error);
      if (error.status === 0) {
        this.error = 'Error de conexión. Verifica tu conexión a internet.';
      } else if (error.status === 401) {
        this.error = 'No autorizado. Inicia sesión nuevamente.';
      } else {
        this.error = 'Error al cargar los exámenes. Intenta nuevamente.';
      }
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  async eliminarExamen(examen: Examen) {
    if (!confirm(`¿Estás seguro de que quieres eliminar el examen "${examen.titulo}"?`)) {
      return;
    }

    try {
      console.log('🗑️ Eliminando examen:', examen.examen_id);
      
      const response = await firstValueFrom(
        this.http.delete(`${environment.apiUrl}/examenes/${examen.examen_id}`)
      );
      
      if (response && (response as any).status === 'success') {
        console.log('✅ Examen eliminado exitosamente');
        this.examenes = this.examenes.filter(e => e.examen_id !== examen.examen_id);
        this.aplicarFiltros();
      } else {
        console.error('❌ Error al eliminar examen:', response);
        alert('Error al eliminar el examen');
      }
    } catch (error: any) {
      console.error('❌ Error al eliminar examen:', error);
      alert('Error al eliminar el examen. Intenta nuevamente.');
    }
  }

  crearExamen() {
    this.router.navigate(['/admin/examenes/crear']);
  }

  editarExamen(examen: Examen) {
    this.router.navigate(['/admin/examenes/editar', examen.examen_id]);
  }

  aplicarFiltros() {
    this.filteredExamenes = this.examenes.filter(examen => {
      const matchesSearch = !this.searchTerm || 
        examen.titulo.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        examen.descripcion.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        examen.categoria_nombre.toLowerCase().includes(this.searchTerm.toLowerCase());
      
      const matchesEstado = !this.selectedEstado || examen.estado === this.selectedEstado;
      
      return matchesSearch && matchesEstado;
    });
  }

  onSearchChange() {
    this.aplicarFiltros();
  }

  onEstadoChange() {
    this.aplicarFiltros();
  }

  limpiarFiltros() {
    this.searchTerm = '';
    this.selectedEstado = '';
    this.aplicarFiltros();
  }

  getEstadoClass(estado: string): string {
    return estado === 'activo' ? 'status-active' : 'status-inactive';
  }

  getEstadoText(estado: string): string {
    return estado === 'activo' ? 'Activo' : 'Inactivo';
  }
} 