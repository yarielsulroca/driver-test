import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';

interface Exam {
  id: number;
  title: string;
  category: string;
  duration: number;
  questions: number;
  passingScore: number;
  attempts: number;
  status: 'active' | 'draft' | 'archived';
  createdAt: Date;
  updatedAt: Date;
}

@Component({
  selector: 'app-exam-list',
  templateUrl: './exam-list.html',
  styleUrls: ['./exam-list.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatPaginatorModule
  ]
})
export class ExamListComponent implements OnInit {
  exams: Exam[] = [];
  searchQuery: string = '';
  totalExams: number = 0;
  pageSize: number = 10;
  currentPage: number = 0;
  isLoading: boolean = false;

  constructor(
    private dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {
    // Datos de ejemplo
    this.exams = [
      {
        id: 1,
        title: 'Examen Teórico Básico',
        category: 'Licencia B',
        duration: 45,
        questions: 30,
        passingScore: 70,
        attempts: 3,
        status: 'active',
        createdAt: new Date('2024-01-01'),
        updatedAt: new Date('2024-01-15')
      },
      {
        id: 2,
        title: 'Examen Teórico Avanzado',
        category: 'Licencia A',
        duration: 60,
        questions: 40,
        passingScore: 80,
        attempts: 2,
        status: 'active',
        createdAt: new Date('2024-01-05'),
        updatedAt: new Date('2024-01-20')
      }
    ];
    this.totalExams = this.exams.length;
  }

  ngOnInit(): void {
    this.loadExams();
  }

  loadExams(): void {
    this.isLoading = true;
    // Aquí se implementaría la carga de datos desde el backend
    setTimeout(() => {
      this.isLoading = false;
    }, 500);
  }

  onSearch(): void {
    // Implementar lógica de búsqueda
    console.log('Búsqueda:', this.searchQuery);
  }

  onPageChange(event: PageEvent): void {
    this.currentPage = event.pageIndex;
    this.pageSize = event.pageSize;
    this.loadExams();
  }

  onNewExam(): void {
    // Implementar lógica para crear nuevo examen
    console.log('Crear nuevo examen');
  }

  onEditExam(exam: Exam): void {
    // Implementar lógica para editar examen
    console.log('Editar examen:', exam);
  }

  onDeleteExam(exam: Exam): void {
    // Implementar lógica para eliminar examen
    console.log('Eliminar examen:', exam);
  }

  onPreviewExam(exam: Exam): void {
    // Implementar lógica para vista previa
    console.log('Vista previa examen:', exam);
  }

  showMessage(message: string, action: string = 'Cerrar'): void {
    this.snackBar.open(message, action, {
      duration: 3000,
      horizontalPosition: 'end',
      verticalPosition: 'top'
    });
  }
} 