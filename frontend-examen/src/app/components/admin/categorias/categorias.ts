import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-categorias',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './categorias.html',
  styleUrl: './categorias.scss',
  standalone: true
})
export class Categorias implements OnInit {
  loading = false;
  error = '';
  categorias: any[] = [];
  categoriasFiltradas: any[] = [];
  searchTerm = '';
  selectedEstado = '';

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    console.log('🚀 Componente Categorias inicializado');
    this.cargarCategorias();
  }

  async cargarCategorias() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    console.log('🔄 Cargando categorías...');
    console.log('📍 API URL:', environment.apiUrl);

    try {
      const response = await firstValueFrom(
        this.http.get<{status: string, data: any[]}>(`${environment.apiUrl}/categorias`)
      );

      console.log('📊 Respuesta de categorías:', response);

      if (response.status === 'success' && response.data) {
        this.categorias = response.data;
        this.categoriasFiltradas = [...this.categorias];
        console.log('✅ Categorías cargadas:', this.categorias.length);
      } else {
        this.categorias = [];
        this.categoriasFiltradas = [];
        console.log('⚠️ No se encontraron categorías');
      }
    } catch (error: any) {
      console.error('❌ Error cargando categorías:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo.';
      } else if (error.status === 404) {
        this.error = 'La ruta de categorías no existe en la API.';
      } else {
        this.error = `Error al cargar las categorías: ${error.message}`;
      }
      
      this.categorias = [];
      this.categoriasFiltradas = [];
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🏁 Carga de categorías finalizada');
    }
  }

  aplicarFiltros() {
    console.log('🔍 Aplicando filtros...');
    
    this.categoriasFiltradas = this.categorias.filter(categoria => {
      const coincideBusqueda = !this.searchTerm || 
        categoria.nombre?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        categoria.descripcion?.toLowerCase().includes(this.searchTerm.toLowerCase());
      
      const coincideEstado = !this.selectedEstado || 
        categoria.estado === this.selectedEstado;
      
      return coincideBusqueda && coincideEstado;
    });

    console.log('📊 Categorías filtradas:', this.categoriasFiltradas.length);
  }

  limpiarFiltros() {
    this.searchTerm = '';
    this.selectedEstado = '';
    this.categoriasFiltradas = [...this.categorias];
    console.log('🧹 Filtros limpiados');
  }

  crearNuevaCategoria() {
    console.log('🔄 Navegando a crear categoría...');
    this.router.navigate(['/admin/categorias/crear']);
  }

  editarCategoria(id: number) {
    console.log('🔄 Editando categoría:', id);
    this.router.navigate(['/admin/categorias/editar', id]);
  }

  async eliminarCategoria(id: number) {
    console.log('🗑️ Eliminando categoría:', id);
    
    if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
      try {
        const response = await firstValueFrom(
          this.http.delete<{status: string, message: string}>(`${environment.apiUrl}/categorias/${id}`)
        );

        if (response.status === 'success') {
          console.log('✅ Categoría eliminada exitosamente');
          this.cargarCategorias(); // Recargar la lista
        } else {
          console.error('❌ Error al eliminar categoría:', response.message);
          alert('Error al eliminar la categoría: ' + response.message);
        }
      } catch (error: any) {
        console.error('❌ Error eliminando categoría:', error);
        alert('Error al eliminar la categoría: ' + error.message);
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