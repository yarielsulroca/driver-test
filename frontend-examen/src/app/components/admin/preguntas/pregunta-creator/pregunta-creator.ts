import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PreguntaEditor } from '../pregunta-editor/pregunta-editor';

@Component({
  selector: 'app-pregunta-creator',
  standalone: true,
  imports: [CommonModule, PreguntaEditor],
  template: `
    <div class="pregunta-creator-container">
      <div class="header">
        <h1>Crear Nueva Pregunta</h1>
        <p>Utiliza el editor avanzado para crear preguntas con diferentes tipos y soporte para imágenes</p>
      </div>
      
      <app-pregunta-editor></app-pregunta-editor>
    </div>
  `,
  styles: [`
    .pregunta-creator-container {
      padding: 1rem;
    }

    .header {
      text-align: center;
      margin-bottom: 2rem;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 8px;
    }

    .header h1 {
      color: #2c3e50;
      margin-bottom: 0.5rem;
    }

    .header p {
      color: #7f8c8d;
      margin: 0;
    }
  `]
})
export class PreguntaCreator {
  // Este componente simplemente envuelve PreguntaEditor para crear nuevas preguntas
} 