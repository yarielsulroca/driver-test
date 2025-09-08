import { Component, OnInit } from '@angular/core';
import axios from 'axios';

@Component({
  selector: 'app-exam-results-table',
  templateUrl: './exam-results-table.html',
  styleUrls: ['./exam-results-table.scss']
})
export class ExamResultsTableComponent implements OnInit {
  resultados: any[] = [];
  search: string = '';
  pagina: number = 1;
  totalPaginas: number = 1;
  total: number = 0;
  inicio: number = 0;
  fin: number = 0;
  paginas: number[] = [];

  async ngOnInit() {
    await this.cargarResultados();
  }

  async cargarResultados(page: number = 1) {
    try {
      const response = await axios.get('http://examen.test/api/resultados', {
        params: {
          page,
          per_page: 10,
          search: this.search
        }
      });
      const data = response.data.data;
      this.resultados = data.resultados;
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
      for (let i = start; i <= end; i++) this.paginas.push(i);
    } catch (error) {
      console.error('Error al obtener resultados', error);
    }
  }

  async buscar() {
    await this.cargarResultados(1);
  }

  async cambiarPagina(p: number) {
    await this.cargarResultados(p);
  }
} 