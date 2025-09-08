import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-preguntas',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="preguntas-container">
      <div class="header">
        <h2>Gestión de Preguntas</h2>
        <p>Administra las preguntas de los exámenes con diferentes tipos y soporte para imágenes</p>
      </div>
      
      <div class="actions">
        <button class="btn-primary" (click)="crearPregunta()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Crear Nueva Pregunta
        </button>
      </div>

      <div class="info-cards">
        <div class="info-card">
          <h3>Tipos de Pregunta Soportados</h3>
          <ul>
            <li><strong>Opción Múltiple:</strong> Seleccionar una o más respuestas correctas</li>
            <li><strong>Opción Única:</strong> Seleccionar una sola respuesta correcta</li>
            <li><strong>Verdadero/Falso:</strong> Respuesta de verdadero o falso</li>
            <li><strong>Completar Espacios:</strong> Completar espacios en blanco</li>
            <li><strong>Ordenar:</strong> Ordenar elementos en secuencia correcta</li>
            <li><strong>Emparejar:</strong> Emparejar elementos de dos columnas</li>
          </ul>
        </div>

        <div class="info-card">
          <h3>Características Avanzadas</h3>
          <ul>
            <li><strong>Imágenes en Respuestas:</strong> Cada respuesta puede incluir una imagen</li>
            <li><strong>Explicaciones:</strong> Agregar explicaciones a las respuestas</li>
            <li><strong>Ordenamiento:</strong> Definir el orden correcto de elementos</li>
            <li><strong>Dificultad:</strong> Configurar nivel de dificultad (Fácil, Medio, Difícil)</li>
            <li><strong>Preguntas Críticas:</strong> Marcar preguntas obligatorias para aprobar</li>
          </ul>
        </div>
      </div>

      <div class="navigation-hint">
        <p>💡 <strong>Consejo:</strong> Haz clic en "Crear Nueva Pregunta" para acceder al editor avanzado con todas las funcionalidades.</p>
      </div>
    </div>
  `,
  styles: [`
    .preguntas-container {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .header h2 {
      color: #2c3e50;
      margin-bottom: 0.5rem;
    }

    .header p {
      color: #7f8c8d;
      font-size: 1.1rem;
    }

    .actions {
      text-align: center;
      margin-bottom: 3rem;
    }

    .btn-primary {
      background: #3498db;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 1.1rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background 0.3s;
    }

    .btn-primary:hover {
      background: #2980b9;
    }

    .btn-primary svg {
      width: 20px;
      height: 20px;
    }

    .info-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .info-card {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .info-card h3 {
      color: #2c3e50;
      margin-bottom: 1rem;
      border-bottom: 2px solid #3498db;
      padding-bottom: 0.5rem;
    }

    .info-card ul {
      list-style: none;
      padding: 0;
    }

    .info-card li {
      padding: 0.5rem 0;
      border-bottom: 1px solid #e9ecef;
      color: #495057;
    }

    .info-card li:last-child {
      border-bottom: none;
    }

    .info-card strong {
      color: #2c3e50;
    }

    .navigation-hint {
      background: #e8f4fd;
      border: 1px solid #bee5eb;
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      color: #0c5460;
    }

    .navigation-hint p {
      margin: 0;
      font-size: 1rem;
    }

    @media (max-width: 768px) {
      .preguntas-container {
        padding: 1rem;
      }
      
      .info-cards {
        grid-template-columns: 1fr;
      }
    }
  `]
})
export class Preguntas implements OnInit {

  constructor(private router: Router) {}

  ngOnInit() {
    console.log('🚀 Componente Preguntas Avanzado inicializado');
  }

  crearPregunta() {
    this.router.navigate(['/admin/preguntas/crear']);
  }
} 