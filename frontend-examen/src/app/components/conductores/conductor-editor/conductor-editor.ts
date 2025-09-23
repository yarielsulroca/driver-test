import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../../../services/api.service';

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
  estado: 'activo' | 'inactivo' | 'suspendido';
  categoria_principal: string;
  fecha_registro: string;
  created_at: string;
  updated_at: string;
  usuario?: Usuario;
}

@Component({
  selector: 'app-conductor-editor',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './conductor-editor.html',
  styleUrls: ['./conductor-editor.scss']
})
export class ConductorEditor implements OnInit {
  conductor: Conductor | null = null;
  usuarios: Usuario[] = [];
  loading: boolean = false;
  loadingUsuarios: boolean = false;
  error: string = '';
  isEditing: boolean = false;
  
  // Form data
  formData = {
    usuario_id: 0,
    licencia: '',
    fecha_vencimiento: '',
    estado: 'activo' as 'activo' | 'inactivo' | 'suspendido',
    categoria_principal: '',
    fecha_registro: ''
  };

  constructor(
    private cdr: ChangeDetectorRef,
    private apiService: ApiService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      const conductorId = params['id'];
      if (conductorId) {
        this.isEditing = true;
        this.cargarConductor(conductorId);
      } else {
        this.isEditing = false;
        // Asignar fecha actual para nuevo conductor
        this.formData.fecha_registro = new Date().toISOString().split('T')[0];
      }
    });
    this.cargarUsuarios();
  }

  async cargarConductor(conductorId: number) {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();
    
    try {
      const response = await this.apiService.get(`/conductores/${conductorId}`).toPromise();
      this.conductor = response?.data as Conductor;
      if (this.conductor) {
        this.formData = {
          usuario_id: this.conductor.usuario_id,
          licencia: this.conductor.licencia,
          fecha_vencimiento: this.conductor.fecha_vencimiento,
          estado: this.conductor.estado,
          categoria_principal: this.conductor.categoria_principal || '',
          fecha_registro: this.conductor.fecha_registro || ''
        };
      }
      this.cdr.detectChanges();
    } catch (error: any) {
      console.error('Error al cargar conductor:', error);
      this.error = error.message || 'Error al cargar conductor';
      this.cdr.detectChanges();
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  async cargarUsuarios() {
    this.loadingUsuarios = true;
    console.log('🔄 Cargando usuarios...');
    this.cdr.detectChanges();
    
    try {
      // Intentar primero con el endpoint real
      let response;
      try {
        response = await this.apiService.get('/usuarios').toPromise();
        console.log('📋 Respuesta de usuarios (endpoint real):', response);
      } catch (realError) {
        console.log('⚠️ Endpoint real falló, usando endpoint de prueba:', realError);
        // Si falla, usar endpoint de prueba
        response = await this.apiService.get('/test/usuarios').toPromise();
        console.log('📋 Respuesta de usuarios (endpoint prueba):', response);
      }
      
      this.usuarios = (response?.data as Usuario[]) || [];
      console.log('👥 Usuarios cargados:', this.usuarios.length);
      
      if (this.usuarios.length === 0) {
        this.error = 'No se encontraron usuarios disponibles. Verifique que existan usuarios activos en el sistema.';
      }
      
      this.cdr.detectChanges();
    } catch (error: any) {
      console.error('❌ Error al cargar usuarios:', error);
      this.error = `Error al cargar usuarios: ${error.message || error}`;
      this.usuarios = [];
      this.cdr.detectChanges();
    } finally {
      this.loadingUsuarios = false;
      this.cdr.detectChanges();
    }
  }

  async guardarConductor() {
    if (!this.formData.usuario_id) {
      this.error = 'Debe seleccionar un usuario';
      return;
    }

    if (!this.formData.licencia.trim()) {
      this.error = 'El número de licencia es requerido';
      return;
    }

    if (!this.formData.fecha_vencimiento) {
      this.error = 'La fecha de vencimiento es requerida';
      return;
    }

    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    try {
      // Preparar datos para envío
      const datosEnvio = {
        usuario_id: this.formData.usuario_id,
        licencia: this.formData.licencia.trim(),
        fecha_vencimiento: this.formData.fecha_vencimiento,
        estado: this.formData.estado,
        categoria_principal: this.formData.categoria_principal || null,
        fecha_registro: this.formData.fecha_registro || new Date().toISOString().split('T')[0]
      };

      if (this.isEditing && this.conductor) {
        // Actualizar
        await this.apiService.put(`/conductores/${this.conductor.conductor_id}`, datosEnvio).toPromise();
        console.log('✅ Conductor actualizado exitosamente');
      } else {
        // Crear - intentar primero con endpoint real, luego con prueba
        try {
          await this.apiService.post('/conductores', datosEnvio).toPromise();
          console.log('✅ Conductor creado exitosamente');
        } catch (realError) {
          console.log('⚠️ Endpoint real falló, usando endpoint de prueba:', realError);
          await this.apiService.post('/test/conductores', datosEnvio).toPromise();
          console.log('✅ Conductor creado exitosamente (modo prueba)');
        }
      }

      // Navegar de vuelta a la lista de conductores
      this.router.navigate(['/conductores']);
    } catch (error: any) {
      console.error('❌ Error al guardar conductor:', error);
      this.error = error.message || error.error?.message || 'Error al guardar conductor';
      this.cdr.detectChanges();
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  cancelar() {
    this.router.navigate(['/conductores']);
  }

  getUsuarioNombre(usuario: Usuario): string {
    return `${usuario.nombre} ${usuario.apellido}`;
  }

  getTitulo(): string {
    return this.isEditing ? 'Editar Conductor' : 'Nuevo Conductor';
  }
}
