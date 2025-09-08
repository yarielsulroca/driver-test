import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';

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

@Component({
  selector: 'app-conductores',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './conductores.html',
  styleUrls: ['./conductores.scss']
})
export class Conductores implements OnInit {
  conductores: Conductor[] = [];
  conductoresFiltrados: Conductor[] = [];
  searchTerm: string = '';
  selectedEstado: string = 'todos';
  loading: boolean = false;
  error: string = '';
  
  // Modal
  showModal: boolean = false;
  editingConductor: Conductor | null = null;
  modalTitle: string = '';
  
  // Form data
  formData = {
    usuario_id: 0,
    licencia: '',
    fecha_vencimiento: '',
    estado: 'activo'
  };

  constructor(
    private cdr: ChangeDetectorRef,
    private apiService: ApiService
  ) {}

  ngOnInit() {
    this.cargarConductores();
  }

  async cargarConductores() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();
    
    try {
      const response = await this.apiService.get('/conductores').toPromise();
      this.conductores = (response?.data as Conductor[]) || [];
      this.filtrarConductores();
      this.cdr.detectChanges();
    } catch (error: any) {
      console.error('Error al cargar conductores:', error);
      this.error = error.message || 'Error al cargar conductores';
      this.cdr.detectChanges();
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  filtrarConductores() {
    let filtrados = this.conductores;

    // Filtro por búsqueda (nombre, apellido, DNI)
    if (this.searchTerm.trim()) {
      const term = this.searchTerm.toLowerCase();
      filtrados = filtrados.filter(conductor =>
        conductor.usuario?.nombre.toLowerCase().includes(term) ||
        conductor.usuario?.apellido.toLowerCase().includes(term) ||
        conductor.usuario?.dni.toLowerCase().includes(term)
      );
    }

    // Filtro por estado
    if (this.selectedEstado !== 'todos') {
      filtrados = filtrados.filter(conductor =>
        conductor.estado === this.selectedEstado
      );
    }

    this.conductoresFiltrados = filtrados;
    this.cdr.detectChanges();
  }

  get searchTermValue(): string {
    return this.searchTerm;
  }

  set searchTermValue(value: string) {
    this.searchTerm = value;
    this.filtrarConductores();
  }

  onEstadoChange() {
    this.filtrarConductores();
  }

  abrirModal(conductor?: Conductor) {
    if (conductor) {
      this.modalTitle = 'Editar Conductor';
      this.editingConductor = conductor;
      this.formData = {
        usuario_id: conductor.usuario_id,
        licencia: conductor.licencia,
        fecha_vencimiento: conductor.fecha_vencimiento,
        estado: conductor.estado
      };
    } else {
      this.modalTitle = 'Nuevo Conductor';
      this.editingConductor = null;
      this.formData = {
        usuario_id: 0,
        licencia: '',
        fecha_vencimiento: '',
        estado: 'activo'
      };
    }
    this.showModal = true;
  }

  cerrarModal() {
    this.showModal = false;
    this.editingConductor = null;
    this.error = '';
  }

  async guardarConductor() {
    if (!this.formData.usuario_id) {
      this.error = 'Debe seleccionar un usuario';
      return;
    }

    try {
      if (this.editingConductor) {
        // Actualizar
        await this.apiService.put(`/conductores/${this.editingConductor.conductor_id}`, this.formData).toPromise();
      } else {
        // Crear
        await this.apiService.post('/conductores', this.formData).toPromise();
      }

      this.cerrarModal();
      await this.cargarConductores();
    } catch (error: any) {
      console.error('Error al guardar conductor:', error);
      this.error = error.message || 'Error al guardar conductor';
    }
  }

  async eliminarConductor(conductor: Conductor) {
    if (!confirm('¿Está seguro de que desea eliminar este conductor?')) {
      return;
    }

    try {
      await this.apiService.delete(`/conductores/${conductor.conductor_id}`).toPromise();
      await this.cargarConductores();
    } catch (error: any) {
      console.error('Error al eliminar conductor:', error);
      this.error = error.message || 'Error al eliminar conductor';
    }
  }

  // Métodos helper para mostrar información
  getConductorNombre(conductor: Conductor): string {
    if (conductor.usuario) {
      return `${conductor.usuario.nombre} ${conductor.usuario.apellido}`;
    }
    return 'Usuario no encontrado';
  }

  getConductorDNI(conductor: Conductor): string {
    return conductor.usuario?.dni || 'N/A';
  }

  getConductorEmail(conductor: Conductor): string {
    return conductor.usuario?.email || 'N/A';
  }

  getConductorTelefono(conductor: Conductor): string {
    return conductor.perfil?.telefono || 'N/A';
  }

  getConductorDireccion(conductor: Conductor): string {
    return conductor.perfil?.direccion || 'N/A';
  }

  getConductorFechaNacimiento(conductor: Conductor): string {
    if (conductor.perfil?.fecha_nacimiento) {
      return new Date(conductor.perfil.fecha_nacimiento).toLocaleDateString('es-ES');
    }
    return 'N/A';
  }

  getEstadoClass(estado: string): string {
    switch (estado) {
      case 'activo': return 'badge-success';
      case 'inactivo': return 'badge-danger';
      default: return 'badge-secondary';
    }
  }

  getEstadoText(estado: string): string {
    switch (estado) {
      case 'activo': return 'Activo';
      case 'inactivo': return 'Inactivo';
      default: return 'Desconocido';
    }
  }
}