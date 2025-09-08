import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-oficinas',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './oficinas.html',
  styleUrl: './oficinas.scss',
  standalone: true
})
export class Oficinas implements OnInit {
  loading = false;
  error = '';
  oficinas: any[] = [];
  oficinasFiltradas: any[] = [];
  searchTerm = '';
  selectedEstado = '';

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    console.log('🚀 Componente Oficinas inicializado');
    this.cargarOficinas();
  }

  async cargarOficinas() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    console.log('🔄 Cargando oficinas...');
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any[]}>(`${environment.apiUrl}/escuelas`)
      );

      console.log('📊 Respuesta de oficinas:', response);

      if (response.status === 'success' && response.data) {
        this.oficinas = response.data;
        this.oficinasFiltradas = [...this.oficinas];
        console.log('✅ Oficinas cargadas:', this.oficinas.length);
      } else {
        this.oficinas = [];
        this.oficinasFiltradas = [];
        console.log('⚠️ No se encontraron oficinas');
      }
    } catch (error: any) {
      console.error('❌ Error cargando oficinas:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'La ruta de oficinas no existe en la API.';
      } else {
        this.error = `Error al cargar las oficinas: ${error.message}`;
      }
      
      this.oficinas = [];
      this.oficinasFiltradas = [];
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🏁 Carga de oficinas finalizada');
    }
  }

  aplicarFiltros() {
    console.log('🔍 Aplicando filtros...');
    
    this.oficinasFiltradas = this.oficinas.filter(oficina => {
      const coincideBusqueda = !this.searchTerm || 
        oficina.nombre?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        oficina.direccion?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        oficina.ciudad?.toLowerCase().includes(this.searchTerm.toLowerCase());
      
      const coincideEstado = !this.selectedEstado || 
        oficina.estado === this.selectedEstado;
      
      return coincideBusqueda && coincideEstado;
    });

    console.log('📊 Oficinas filtradas:', this.oficinasFiltradas.length);
  }

  limpiarFiltros() {
    this.searchTerm = '';
    this.selectedEstado = '';
    this.oficinasFiltradas = [...this.oficinas];
    console.log('🧹 Filtros limpiados');
  }

  crearNuevaOficina() {
    console.log('🔄 Navegando a crear oficina...');
    this.router.navigate(['/admin/oficinas/crear']);
  }

  editarOficina(id: number) {
    console.log('🔄 Editando oficina:', id);
    this.router.navigate(['/admin/oficinas/editar', id]);
  }

  async eliminarOficina(id: number) {
    console.log('🗑️ Eliminando oficina:', id);
    
    if (confirm('¿Estás seguro de que quieres eliminar esta oficina?')) {
      try {
        const response = await firstValueFrom(
          this.http.delete<{status: string, message: string}>(`${environment.apiUrl}/escuelas/${id}`)
        );

        if (response.status === 'success') {
          console.log('✅ Oficina eliminada exitosamente');
          this.cargarOficinas(); // Recargar la lista
        } else {
          console.error('❌ Error al eliminar oficina:', response.message);
          alert('Error al eliminar la oficina: ' + response.message);
        }
      } catch (error: any) {
        console.error('❌ Error eliminando oficina:', error);
        alert('Error al eliminar la oficina: ' + error.message);
      }
    }
  }

  getEstadoText(estado: string): string {
    const estados: { [key: string]: string } = {
      'activo': 'Activo',
      'inactivo': 'Inactivo'
    };
    return estados[estado] || estado;
  }

  getEstadoClass(estado: string): string {
    const clases: { [key: string]: string } = {
      'activo': 'estado-activo',
      'inactivo': 'estado-inactivo'
    };
    return clases[estado] || 'estado-default';
  }
} 