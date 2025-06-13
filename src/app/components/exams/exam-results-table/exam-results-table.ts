import { Component, OnInit } from '@angular/core';
import axios from 'axios';

@Component({
  selector: 'app-exam-results-table',
  templateUrl: './exam-results-table.html',
  styleUrls: ['./exam-results-table.scss']
})
export class ExamResultsTableComponent implements OnInit {
  resultados: any[] = [];

  async ngOnInit() {
    try {
      const response = await axios.get('http://examen.test/api/resultados');
      this.resultados = response.data.data.resultados;
    } catch (error) {
      console.error('Error al obtener resultados', error);
    }
  }
} 