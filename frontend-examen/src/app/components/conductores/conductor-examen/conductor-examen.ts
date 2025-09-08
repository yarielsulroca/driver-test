import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { ActivatedRoute, Router } from '@angular/router';
import { environment } from '../../../../environments/environment';

interface Usuario {
  usuario_id: number;
  nombre: string;
  apellido: string;
  dni: string;
  email: string;
  estado: string;
}

interface Perfil {
  perfil_id: number;
  usuario_id: number;
  telefono: string;
  direccion: string;
  fecha_nacimiento: string;
  genero: string;
  foto: string;
}

interface Conductor {
  conductor_id: number;
  usuario_id: number;
  licencia: string;
  fecha_vencimiento: string;
  estado: string;
  usuario?: Usuario;
  perfil?: Perfil;
}

interface Categoria {
  categoria_id: number;
  codigo: string;
  nombre: string;
  descripcion: string;
  requisitos: string;
  estado: string;
}

interface Examen {
  examen_id: number;
  titulo: string;
  nombre: string;
  descripcion: string;
  duracion_minutos: number;
  puntaje_minimo: number;
  estado: string;
}

interface CategoriaAprobada {
  categoria_aprobada_id: number;
  conductor_id: number;
  categoria_id: number;
  examen_id?: number;
  estado: 'pendiente' | 'aprobado' | 'rechazado';
  puntaje_obtenido?: number;
  fecha_aprobacion?: string;
  fecha_vencimiento?: string;
  observaciones?: string;
  conductor?: Conductor;
  categoria?: Categoria;
  examen?: Examen;
}

@Component({
  selector: 'app-conductor-examen',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './conductor-examen.html',
  styleUrls: ['./conductor-examen.scss']
})
export class ConductorExamenComponent implements OnInit {
  // Datos principales
  conductores: Conductor[] = [];
  categorias: Categoria[] = [];
  examenes: Examen[] = [];
  categoriasAprobadas: CategoriaAprobada[] = [];
  
  // Estados
  loading = false;
  error = '';
  success = '';
  
  // Filtros
  selectedConductor = '';
  selectedCategoria = '';
  selectedEstado = '';
  
  // Modal para crear/editar
  showModal = false;
  modalTitle = '';
  editingCategoriaAprobada: CategoriaAprobada | null = null;
  
  // Form data
  formData = {
    conductor_id: 0,
    categoria_id: 0,
    examen_id: 0,
    estado: 'pendiente' as 'pendiente' | 'aprobado' | 'rechazado',
    puntaje_obtenido: 0,
    fecha_aprobacion: '',
    fecha_vencimiento: '',
    observaciones: ''
  };

  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.cargarDatos();
  }

  async cargarDatos() {
    this.loading = true;
    this.error = '';

    try {
      // Cargar conductores con información completa de usuario y perfil
      const conductoresResponse = await this.http.get<any>(`${environment.apiUrl}/conductores`).toPromise();
      this.conductores = conductoresResponse.data || [];

      // Cargar categorías
      const categoriasResponse = await this.http.get<any>(`${environment.apiUrl}/categorias`).toPromise();
      this.categorias = categoriasResponse.data || [];

      // Cargar exámenes
      const examenesResponse = await this.http.get<any>(`${environment.apiUrl}/examenes`).toPromise();
      this.examenes = examenesResponse.data || [];

      // Cargar categorías aprobadas
      await this.cargarCategoriasAprobadas();

    } catch (error: any) {
      console.error('Error al cargar datos:', error);
      this.error = 'Error al cargar los datos. Por favor, inténtalo de nuevo.';
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  async cargarCategoriasAprobadas() {
    try {
      const response = await this.http.get<any>(`${environment.apiUrl}/categorias-aprobadas`).toPromise();
      this.categoriasAprobadas = response.data || [];
    } catch (error: any) {
      console.error('Error al cargar categorías aprobadas:', error);
      this.error = 'Error al cargar las categorías aprobadas.';
    }
  }

  aplicarFiltros() {
    // Los filtros se aplican automáticamente en la vista
  }

  limpiarFiltros() {
    this.selectedConductor = '';
    this.selectedCategoria = '';
    this.selectedEstado = '';
  }

  abrirModal(editing: CategoriaAprobada | null = null) {
    if (editing) {
      this.modalTitle = 'Editar Categoría Aprobada';
      this.editingCategoriaAprobada = editing;
      this.formData = {
        conductor_id: editing.conductor_id,
        categoria_id: editing.categoria_id,
        examen_id: editing.examen_id || 0,
        estado: editing.estado,
        puntaje_obtenido: editing.puntaje_obtenido || 0,
        fecha_aprobacion: editing.fecha_aprobacion || '',
        fecha_vencimiento: editing.fecha_vencimiento || '',
        observaciones: editing.observaciones || ''
      };
    } else {
      this.modalTitle = 'Nueva Categoría Aprobada';
      this.editingCategoriaAprobada = null;
      this.formData = {
        conductor_id: 0,
        categoria_id: 0,
        examen_id: 0,
        estado: 'pendiente',
        puntaje_obtenido: 0,
        fecha_aprobacion: '',
        fecha_vencimiento: '',
        observaciones: ''
      };
    }
    this.showModal = true;
  }

  cerrarModal() {
    this.showModal = false;
    this.editingCategoriaAprobada = null;
    this.error = '';
  }

  async guardarCategoriaAprobada() {
    if (!this.formData.conductor_id || !this.formData.categoria_id) {
      this.error = 'Por favor, selecciona un conductor y una categoría.';
      return;
    }

    try {
      if (this.editingCategoriaAprobada) {
        // Actualizar
        await this.http.put<any>(
          `${environment.apiUrl}/categorias-aprobadas/${this.editingCategoriaAprobada.categoria_aprobada_id}`,
          this.formData
        ).toPromise();
        this.success = 'Categoría aprobada actualizada exitosamente.';
      } else {
        // Crear
        await this.http.post<any>(
          `${environment.apiUrl}/categorias-aprobadas`,
          this.formData
        ).toPromise();
        this.success = 'Categoría aprobada creada exitosamente.';
      }

      this.cerrarModal();
      await this.cargarCategoriasAprobadas();
      this.cdr.detectChanges();

    } catch (error: any) {
      console.error('Error al guardar:', error);
      this.error = 'Error al guardar. Por favor, inténtalo de nuevo.';
    }
  }

  async eliminarCategoriaAprobada(id: number) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta categoría aprobada?')) {
      return;
    }

    try {
      await this.http.delete(`${environment.apiUrl}/categorias-aprobadas/${id}`).toPromise();
      this.success = 'Categoría aprobada eliminada exitosamente.';
      await this.cargarCategoriasAprobadas();
      this.cdr.detectChanges();
    } catch (error: any) {
      console.error('Error al eliminar:', error);
      this.error = 'Error al eliminar. Por favor, inténtalo de nuevo.';
    }
  }

  getConductorNombre(conductorId: number | undefined): string {
    if (!conductorId) return 'ID no válido';
    const conductor = this.conductores.find(c => c.conductor_id === conductorId);
    if (conductor?.usuario) {
      return `${conductor.usuario.nombre} ${conductor.usuario.apellido}`;
    }
    return 'Conductor no encontrado';
  }

  getCategoriaNombre(categoriaId: number | undefined): string {
    if (!categoriaId) return 'ID no válido';
    const categoria = this.categorias.find(c => c.categoria_id === categoriaId);
    return categoria ? categoria.nombre : 'Categoría no encontrada';
  }

  getExamenNombre(examenId: number | undefined): string {
    if (!examenId) return 'ID no válido';
    const examen = this.examenes.find(e => e.examen_id === examenId);
    return examen ? examen.titulo : 'Examen no encontrado';
  }

  getEstadoClass(estado: string): string {
    switch (estado) {
      case 'aprobado': return 'badge-success';
      case 'pendiente': return 'badge-warning';
      case 'rechazado': return 'badge-danger';
      default: return 'badge-secondary';
    }
  }

  getEstadoText(estado: string): string {
    switch (estado) {
      case 'aprobado': return 'Aprobado';
      case 'pendiente': return 'Pendiente';
      case 'rechazado': return 'Rechazado';
      default: return 'Desconocido';
    }
  }

  formatearFecha(fecha: string | undefined): string {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-ES');
  }

  formatearPuntaje(puntaje: number | undefined): string {
    if (!puntaje) return 'N/A';
    return puntaje.toFixed(2);
  }

  // Método para obtener conductores filtrados
  get conductoresFiltrados(): Conductor[] {
    if (!this.selectedConductor) return this.conductores;
    return this.conductores.filter(c => 
      c.usuario && 
      `${c.usuario.nombre} ${c.usuario.apellido}`.toLowerCase().includes(this.selectedConductor.toLowerCase())
    );
  }

  // Método para obtener categorías aprobadas filtradas
  get categoriasAprobadasFiltradas(): CategoriaAprobada[] {
    let filtradas = this.categoriasAprobadas;

    if (this.selectedConductor) {
      filtradas = filtradas.filter(ca => 
        this.getConductorNombre(ca.conductor_id).toLowerCase().includes(this.selectedConductor.toLowerCase())
      );
    }

    if (this.selectedCategoria) {
      filtradas = filtradas.filter(ca => 
        this.getCategoriaNombre(ca.categoria_id).toLowerCase().includes(this.selectedCategoria.toLowerCase())
      );
    }

    if (this.selectedEstado) {
      filtradas = filtradas.filter(ca => ca.estado === this.selectedEstado);
    }

    return filtradas;
  }
}
