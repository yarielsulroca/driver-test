import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

interface Examen {
  examen_id: number;
  nombre: string;
  descripcion?: string;
  fecha_inicio: string;
  fecha_fin: string;
  duracion_minutos: number;
  puntaje_minimo: number;
  numero_preguntas: number;
  categorias?: Categoria[];
}

interface Categoria {
  categoria_id: number;
  nombre: string;
  codigo: string;
  descripcion?: string;
  estado: 'activo' | 'inactivo';
}

interface Conductor {
  conductor_id: number;
  nombre: string;
  apellido: string;
  dni: string;
  email?: string;
  telefono?: string;
  estado_registro: string;
}

@Component({
  selector: 'app-exam-list',
  templateUrl: './exam-list.html',
  styleUrls: ['./exam-list.scss'],
  imports: [CommonModule, FormsModule],
  standalone: true
})
export class ExamList implements OnInit {
  conductor: Conductor | null = null;
  examenes: Examen[] = [];
  filteredExams: Examen[] = [];
  categorias: Categoria[] = [];
  
  // Estados
  loading = false;
  error = '';
  
  // Filtros
  searchTerm = '';
  selectedCategory = '';
  
  // Modal
  showDetailsModal = false;
  selectedExam: Examen | null = null;

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    // Obtener datos del conductor desde la navegación
    const nav = this.router.getCurrentNavigation();
    if (nav && nav.extras && nav.extras.state) {
      this.conductor = nav.extras.state['conductor'] || null;
    }
  }

  ngOnInit() {
    this.loadExams();
    this.loadCategories();
  }

  async loadExams() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    try {
      console.log('🔄 Cargando exámenes...');
      
      // Intentar primero con proxy (URL relativa)
      try {
        const response = await this.http.get<{status: string, data: Examen[]}>('/api/examenes/activos').toPromise();
        console.log('✅ Exámenes cargados con proxy:', response);
        this.examenes = response?.data || [];
        this.filterExams();
        this.loading = false;
        this.cdr.detectChanges();
        return;
      } catch (proxyError) {
        console.warn('⚠️ Proxy falló, intentando con URL absoluta:', proxyError);
      }
      
      // Fallback: usar URL absoluta
      const response = await this.http.get<{status: string, data: Examen[]}>('http://examen.test/api/examenes/activos').toPromise();
      console.log('✅ Exámenes cargados con URL absoluta:', response);
      this.examenes = response?.data || [];
      this.filterExams();
      
    } catch (error: any) {
      console.error('❌ Error al cargar exámenes:', error);
      this.error = 'Error al cargar los exámenes: ' + (error.message || 'Error desconocido');
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  async loadCategories() {
    try {
      console.log('🔄 Cargando categorías...');
      
      // Intentar primero con proxy
      try {
        const response = await this.http.get<{status: string, data: Categoria[]}>('/api/categorias').toPromise();
        console.log('✅ Categorías cargadas con proxy:', response);
        this.categorias = response?.data || [];
        this.cdr.detectChanges();
        return;
      } catch (proxyError) {
        console.warn('⚠️ Proxy falló, intentando con URL absoluta:', proxyError);
      }
      
      // Fallback: usar URL absoluta
      const response = await this.http.get<{status: string, data: Categoria[]}>('http://examen.test/api/categorias').toPromise();
      console.log('✅ Categorías cargadas con URL absoluta:', response);
      this.categorias = response?.data || [];
      
    } catch (error: any) {
      console.error('❌ Error al cargar categorías:', error);
      // No mostrar error para categorías ya que no es crítico
    } finally {
      this.cdr.detectChanges();
    }
  }

  filterExams() {
    console.log('🔍 Aplicando filtros...');
    console.log('📊 Exámenes totales:', this.examenes.length);
    console.log('🔍 Término de búsqueda:', this.searchTerm);
    console.log('🔍 Categoría seleccionada:', this.selectedCategory);
    
    this.filteredExams = this.examenes.filter(examen => {
      // Filtro por búsqueda
      const cumpleBusqueda = !this.searchTerm || 
        examen.nombre.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        examen.descripcion?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        examen.categorias?.some(cat => 
          cat.nombre.toLowerCase().includes(this.searchTerm.toLowerCase())
        );
      
      // Filtro por categoría
      const cumpleCategoria = !this.selectedCategory || 
        examen.categorias?.some(cat => cat.categoria_id.toString() === this.selectedCategory);
      
      return cumpleBusqueda && cumpleCategoria;
    });
    
    console.log('✅ Exámenes filtrados:', this.filteredExams.length);
    this.cdr.detectChanges();
  }

  getExamStatus(examen: Examen): string {
    const now = new Date();
    const inicio = new Date(examen.fecha_inicio);
    const fin = new Date(examen.fecha_fin);
    
    if (now < inicio) {
      return 'pending';
    } else if (now >= inicio && now <= fin) {
      return 'active';
    } else {
      return 'expired';
    }
  }

  getExamStatusText(examen: Examen): string {
    const status = this.getExamStatus(examen);
    switch (status) {
      case 'pending': return 'Próximo';
      case 'active': return 'Activo';
      case 'expired': return 'Expirado';
      default: return 'Desconocido';
    }
  }

  canTakeExam(examen: Examen): boolean {
    const status = this.getExamStatus(examen);
    return status === 'active';
  }

  getActionText(examen: Examen): string {
    const status = this.getExamStatus(examen);
    switch (status) {
      case 'pending': return 'Próximamente';
      case 'active': return 'Comenzar Examen';
      case 'expired': return 'Expirado';
      default: return 'No Disponible';
    }
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  comenzarExamen(examen: Examen) {
    if (!this.canTakeExam(examen)) {
      alert('Este examen no está disponible en este momento.');
      return;
    }

    if (!this.conductor) {
      alert('Debes iniciar sesión para tomar un examen.');
      return;
    }

    console.log('🚀 Iniciando examen:', examen);
    
    // Navegar al exam-taker con el ID del examen
    this.router.navigate(['/examen', examen.examen_id]);
  }

  verDetalles(examen: Examen) {
    this.selectedExam = examen;
    this.showDetailsModal = true;
  }

  closeDetailsModal() {
    this.showDetailsModal = false;
    this.selectedExam = null;
  }
}
