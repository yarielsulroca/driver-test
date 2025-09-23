import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';

interface ConductorSimple {
  conductor_id: number;
  usuario_id: number;
  estado: string;
  documentos_presentados?: string;
  created_at?: string;
  updated_at?: string;
  nombre: string;
  apellido: string;
  email: string;
  dni: string;
  total_examenes_asignados: number;
  examenes_aprobados: number;
  examenes_pendientes: number;
}

@Component({
  selector: 'app-conductores-simple',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="container">
      <h2>Conductores - Vista Simple</h2>
      
      <div *ngIf="loading" class="loading">
        Cargando conductores...
      </div>
      
      <div *ngIf="error" class="error">
        {{ error }}
      </div>
      
      <div *ngIf="!loading && !error" class="table-container">
        <table class="conductores-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Email</th>
              <th>DNI</th>
              <th>Estado</th>
              <th>Exámenes Asignados</th>
              <th>Exámenes Aprobados</th>
              <th>Exámenes Pendientes</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let conductor of conductores">
              <td>{{ conductor.conductor_id }}</td>
              <td>{{ conductor.nombre }}</td>
              <td>{{ conductor.apellido }}</td>
              <td>{{ conductor.email }}</td>
              <td>{{ conductor.dni }}</td>
              <td>
                <span [class]="conductor.estado === 'b' ? 'estado-bueno' : 'estado-pendiente'">
                  {{ conductor.estado === 'b' ? 'Bueno' : 'Pendiente' }}
                </span>
              </td>
              <td>{{ conductor.total_examenes_asignados }}</td>
              <td>{{ conductor.examenes_aprobados }}</td>
              <td>{{ conductor.examenes_pendientes }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `,
  styles: [`
    .container {
      padding: 20px;
    }
    
    .loading {
      text-align: center;
      padding: 20px;
      font-size: 18px;
      color: #666;
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .table-container {
      overflow-x: auto;
    }
    
    .conductores-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    
    .conductores-table th,
    .conductores-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    .conductores-table th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    
    .conductores-table tr:hover {
      background-color: #f5f5f5;
    }
    
    .estado-bueno {
      color: #28a745;
      font-weight: bold;
    }
    
    .estado-pendiente {
      color: #ffc107;
      font-weight: bold;
    }
  `]
})
export class ConductoresSimpleComponent implements OnInit {
  conductores: ConductorSimple[] = [];
  loading = false;
  error = '';

  constructor(private apiService: ApiService) {}

  ngOnInit() {
    this.cargarConductores();
  }

  async cargarConductores() {
    this.loading = true;
    this.error = '';
    
    try {
      const response = await this.apiService.get('/conductores').toPromise();
      
      if (response?.status === 'success' && response?.data && Array.isArray(response.data)) {
        // Mapear solo los campos que necesitamos
        this.conductores = response.data.map((conductor: any) => ({
          conductor_id: conductor.conductor_id,
          usuario_id: conductor.usuario_id,
          estado: conductor.estado,
          documentos_presentados: conductor.documentos_presentados,
          created_at: conductor.created_at,
          updated_at: conductor.updated_at,
          nombre: conductor.nombre,
          apellido: conductor.apellido,
          email: conductor.email,
          dni: conductor.dni,
          total_examenes_asignados: conductor.total_examenes_asignados || 0,
          examenes_aprobados: conductor.examenes_aprobados || 0,
          examenes_pendientes: conductor.examenes_pendientes || 0
        }));
      } else {
        this.conductores = [];
      }
    } catch (error: any) {
      console.error('Error al cargar conductores:', error);
      this.error = error.message || 'Error al cargar conductores';
    } finally {
      this.loading = false;
    }
  }
}
