import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';

interface ResultadoExamen {
  resultado_id: number;
  conductor_id: number;
  examen_id: number;
  nombre: string;
  apellido: string;
  dni: string;
  examen_nombre?: string;
  categoria?: string;
  fecha_realizacion: string;
  puntaje_total: number;
  estado: string;
}

interface PaginationInfo {
  current_page: number;
  total_pages: number;
  total_items: number;
  per_page: number;
}

@Component({
  selector: 'app-exam-results-table',
  imports: [FormsModule, CommonModule],
  templateUrl: './exam-results-table.html',
  styleUrls: ['./exam-results-table.scss'],
  standalone: true
})
export class ExamResultsTable implements OnInit {
  resultados: ResultadoExamen[] = [];
  searchTerm: string = '';
  selectedStatus: string = '';
  pagina: number = 1;
  totalPaginas: number = 1;
  total: number = 0;
  inicio: number = 0;
  fin: number = 0;
  paginas: number[] = [];
  loading: boolean = false;
  error: string = '';
  
  // Modal
  showDetailsModal = false;
  selectedResult: ResultadoExamen | null = null;

  constructor(
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  async ngOnInit() {
    await this.cargarResultados();
  }

  async cargarResultados(page: number = 1) {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();

    try {
      console.log('🔄 Cargando resultados...');
      
      const params: any = {
        page,
        per_page: 10
      };
      
      if (this.searchTerm) {
        params.search = this.searchTerm;
      }
      
      if (this.selectedStatus) {
        params.status = this.selectedStatus;
      }
      
      // Intentar primero con proxy (URL relativa)
      try {
        const response = await this.http.get<{status: string, data: {resultados: ResultadoExamen[], pagination: PaginationInfo}}>('/api/resultados', { params }).toPromise();
        console.log('✅ Resultados cargados con proxy:', response);
        this.procesarResultados(response?.data);
        this.loading = false;
        this.cdr.detectChanges();
        return;
      } catch (proxyError) {
        console.warn('⚠️ Proxy falló, intentando con URL absoluta:', proxyError);
      }
      
      // Fallback: usar URL absoluta
      const response = await this.http.get<{status: string, data: {resultados: ResultadoExamen[], pagination: PaginationInfo}}>('http://examen.test/api/resultados', { params }).toPromise();
      console.log('✅ Resultados cargados con URL absoluta:', response);
      this.procesarResultados(response?.data);
      
    } catch (error: any) {
      console.error('❌ Error al cargar resultados:', error);
      this.error = 'Error al cargar los resultados: ' + (error.message || 'Error desconocido');
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  private procesarResultados(data: any) {
    if (!data) return;
    
    this.resultados = data.resultados || [];
    this.pagina = data.pagination.current_page;
    this.totalPaginas = data.pagination.total_pages;
    this.total = data.pagination.total_items;
    this.inicio = (this.pagina - 1) * 10 + 1;
    this.fin = Math.min(this.inicio + 9, this.total);
    
    // Mostrar máximo 5 páginas en la paginación
    let start = Math.max(1, this.pagina - 2);
    let end = Math.min(this.totalPaginas, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);
    
    this.paginas = [];
    for (let i = start; i <= end; i++) {
      this.paginas.push(i);
    }
  }

  async buscar() {
    console.log('🔍 Buscando con término:', this.searchTerm);
    await this.cargarResultados(1);
  }

  async aplicarFiltros() {
    console.log('🔍 Aplicando filtros - Estado:', this.selectedStatus);
    await this.cargarResultados(1);
  }

  async cambiarPagina(p: number) {
    console.log('📄 Cambiando a página:', p);
    await this.cargarResultados(p);
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }

  formatTime(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  getStatusText(estado: string): string {
    switch (estado) {
      case 'aprobado': return 'Aprobado';
      case 'desaprobado': return 'Desaprobado';
      case 'en_progreso': return 'En Progreso';
      default: return estado;
    }
  }

  getScoreClass(puntaje: number): string {
    if (puntaje >= 90) return 'excellent';
    if (puntaje >= 80) return 'good';
    if (puntaje >= 70) return 'average';
    return 'poor';
  }

  verDetalles(resultado: ResultadoExamen) {
    console.log('👁️ Ver detalles del resultado:', resultado);
    this.selectedResult = resultado;
    this.showDetailsModal = true;
  }

  closeDetailsModal() {
    this.showDetailsModal = false;
    this.selectedResult = null;
  }

  async editarResultado(resultado: ResultadoExamen) {
    console.log('✏️ Editando resultado:', resultado);
    
    // Aquí podrías abrir un modal de edición o navegar a una página de edición
    alert(`Función de edición para el resultado ${resultado.resultado_id} - En desarrollo`);
    
    // Ejemplo de implementación futura:
    // this.router.navigate(['/resultados/editar', resultado.resultado_id]);
  }

  async eliminarResultado(resultado: ResultadoExamen) {
    console.log('🗑️ Eliminando resultado:', resultado);
    
    if (!confirm(`¿Estás seguro de que quieres eliminar el resultado de ${resultado.nombre} ${resultado.apellido}?`)) {
      return;
    }
    
    try {
      // Intentar primero con proxy
      try {
        await this.http.delete(`/api/resultados/${resultado.resultado_id}`).toPromise();
        console.log('✅ Resultado eliminado con proxy');
      } catch (proxyError) {
        console.warn('⚠️ Proxy falló, intentando con URL absoluta:', proxyError);
        await this.http.delete(`http://examen.test/api/resultados/${resultado.resultado_id}`).toPromise();
        console.log('✅ Resultado eliminado con URL absoluta');
      }
      
      // Recargar la lista
      await this.cargarResultados(this.pagina);
      
    } catch (error: any) {
      console.error('❌ Error al eliminar resultado:', error);
      alert('Error al eliminar el resultado: ' + (error.message || 'Error desconocido'));
    }
  }

  async exportarResultados() {
    console.log('📊 Exportando resultados...');
    
    try {
      // Crear CSV con los resultados actuales
      const csvContent = this.generarCSV();
      
      // Crear y descargar el archivo
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      
      link.setAttribute('href', url);
      link.setAttribute('download', `resultados_examenes_${new Date().toISOString().split('T')[0]}.csv`);
      link.style.visibility = 'hidden';
      
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      console.log('✅ Resultados exportados exitosamente');
      
    } catch (error: any) {
      console.error('❌ Error al exportar resultados:', error);
      alert('Error al exportar los resultados: ' + (error.message || 'Error desconocido'));
    }
  }

  private generarCSV(): string {
    const headers = [
      'ID',
      'Nombre',
      'Apellido',
      'DNI',
      'Examen',
      'Categoría',
      'Fecha',
      'Puntaje',
      'Estado'
    ];
    
    const rows = this.resultados.map(resultado => [
      resultado.resultado_id,
      resultado.nombre,
      resultado.apellido,
      resultado.dni,
      resultado.examen_nombre || 'N/A',
      resultado.categoria || 'N/A',
      this.formatDate(resultado.fecha_realizacion),
      resultado.puntaje_total + '%',
      this.getStatusText(resultado.estado)
    ]);
    
    const csvContent = [
      headers.join(','),
      ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
    ].join('\n');
    
    return csvContent;
  }
}
