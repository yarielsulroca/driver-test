import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../../services/api.service';
import { NotificationService } from '../../../services/notification.service';
import { LoadingService } from '../../../services/loading.service';

interface Usuario {
  usuario_id: number;
  nombre: string;
  apellido: string;
  email: string;
  dni: string;
  estado: string;
}

interface ConductorForm {
  usuario_id: number | null;
  estado: 'p' | 'b';
  documentos_presentados: string;
}

@Component({
  selector: 'app-conductor-crear',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './conductor-crear.html',
  styleUrls: ['./conductor-crear.scss']
})
export class ConductorCrearComponent implements OnInit {
  
  formData: ConductorForm = {
    usuario_id: null,
    estado: 'p',
    documentos_presentados: ''
  };

  usuarios: Usuario[] = [];
  usuariosFiltrados: Usuario[] = [];
  searchTerm: string = '';
  selectedUsuario: Usuario | null = null;
  showDropdown: boolean = false;
  loading: boolean = false;
  error: string = '';

  constructor(
    private apiService: ApiService,
    private notificationService: NotificationService,
    private loadingService: LoadingService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.cargarUsuarios();
  }

  async cargarUsuarios() {
    this.loading = true;
    this.error = '';
    console.log('🔄 Iniciando carga de usuarios...');
    
    try {
      console.log('📡 Haciendo petición a /usuarios...');
      const response = await this.apiService.get('/usuarios').toPromise();
      console.log('📥 Respuesta recibida:', response);
      
      if (response?.status === 'success' && response?.data) {
        this.usuarios = response.data as Usuario[];
        this.usuariosFiltrados = [...this.usuarios];
        console.log('✅ Usuarios cargados:', this.usuarios);
        this.notificationService.info(`✅ Se cargaron ${this.usuarios.length} usuarios disponibles`);
      } else {
        console.log('⚠️ No hay usuarios en la respuesta:', response);
        this.usuarios = [];
        this.notificationService.warning('⚠️ No hay usuarios disponibles para asignar como conductores');
      }
    } catch (error: any) {
      console.error('❌ Error al cargar usuarios:', error);
      this.error = error.message || 'Error al cargar usuarios';
      this.notificationService.error('❌ Error al cargar usuarios');
    } finally {
      this.loading = false;
      console.log('🏁 Carga de usuarios finalizada. Loading:', this.loading);
      this.cdr.detectChanges();
    }
  }

  async crearConductor() {
    if (!this.formData.usuario_id || !Number.isInteger(this.formData.usuario_id)) {
      this.notificationService.warning('⚠️ Por favor selecciona un usuario válido');
      return;
    }

    if (!this.formData.documentos_presentados.trim()) {
      this.notificationService.warning('⚠️ Por favor describe los documentos presentados');
      return;
    }

    try {
      this.loadingService.showGlobalLoading('Creando conductor...');
      
      // Asegurar que los datos estén en el formato correcto
      const datosEnvio = {
        usuario_id: Number(this.formData.usuario_id),
        estado: this.formData.estado,
        documentos_presentados: this.formData.documentos_presentados.trim()
      };
      
      const response = await this.apiService.post('/conductores', datosEnvio).toPromise();
      
      if (response?.status === 'success') {
        this.notificationService.success('✅ Conductor creado exitosamente');
        this.router.navigate(['/conductores']);
      } else {
        throw new Error(response?.message || 'Error al crear conductor');
      }
    } catch (error: any) {
      console.error('Error al crear conductor:', error);
      console.error('Error completo:', JSON.stringify(error, null, 2));
      
      let errorMessage = '❌ Error al crear conductor';
      if (error.error && error.error.message) {
        errorMessage = `❌ ${error.error.message}`;
      } else if (error.message) {
        errorMessage = `❌ ${error.message}`;
      }
      
      this.notificationService.error(errorMessage);
    } finally {
      this.loadingService.hideGlobalLoading();
    }
  }

  cancelar() {
    this.router.navigate(['/conductores']);
  }

  getNombreCompleto(usuario: Usuario): string {
    return `${usuario.nombre} ${usuario.apellido}`;
  }

  trackByUsuario(index: number, usuario: Usuario): number {
    return usuario.usuario_id;
  }

  // Métodos para la búsqueda de usuarios
  onSearchChange(searchTerm: string) {
    this.searchTerm = searchTerm;
    this.showDropdown = true;
    
    if (!searchTerm.trim()) {
      this.usuariosFiltrados = [...this.usuarios];
      return;
    }

    const term = searchTerm.toLowerCase();
    this.usuariosFiltrados = this.usuarios.filter(usuario =>
      usuario.nombre.toLowerCase().includes(term) ||
      usuario.apellido.toLowerCase().includes(term) ||
      usuario.dni.toLowerCase().includes(term) ||
      usuario.email.toLowerCase().includes(term)
    );
  }

  selectUsuario(usuario: Usuario) {
    this.selectedUsuario = usuario;
    this.formData.usuario_id = Number(usuario.usuario_id);
    this.searchTerm = this.getNombreCompleto(usuario);
    this.showDropdown = false;
  }

  clearSelection() {
    this.selectedUsuario = null;
    this.formData.usuario_id = null;
    this.searchTerm = '';
    this.usuariosFiltrados = [...this.usuarios];
    this.showDropdown = false;
  }

  onInputFocus() {
    this.showDropdown = true;
    if (!this.searchTerm) {
      this.usuariosFiltrados = [...this.usuarios];
    }
  }

  onInputBlur() {
    // Delay para permitir que el click en el dropdown funcione
    setTimeout(() => {
      this.showDropdown = false;
    }, 200);
  }

  clearSearch() {
    this.searchTerm = '';
    this.usuariosFiltrados = [...this.usuarios];
  }
}
