import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';

// Importaciones de componentes
import { Dashboard } from './dashboard/dashboard';
import { Oficinas } from './oficinas/oficinas';
import { Categorias } from './categorias/categorias';
import { Examenes } from './examenes/examenes';
import { PreguntaList } from './preguntas/pregunta-list/pregunta-list';

@Component({
  selector: 'app-admin',
  imports: [
    CommonModule,
    Dashboard,
    Oficinas,
    Categorias,
    Examenes,
    PreguntaList
  ],
  templateUrl: './admin.html',
  styleUrl: './admin.scss',
  standalone: true
})
export class Admin implements OnInit {
  activeSection: string = 'dashboard';

  constructor(
    public router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    console.log('🚀 Componente Admin inicializado');
    console.log('🔍 URL actual en Admin:', this.router.url);
    this.detectActiveSection();
    console.log('📍 Sección activa detectada:', this.activeSection);
  }

  detectActiveSection() {
    const url = this.router.url;

    if (url.includes('/admin/preguntas')) {
      this.activeSection = 'preguntas';
    } else if (url.includes('/admin/categorias')) {
      this.activeSection = 'categorias';
    } else if (url.includes('/admin/oficinas')) {
      this.activeSection = 'oficinas';
    } else if (url.includes('/admin/examenes')) {
      this.activeSection = 'examenes';
    } else {
      this.activeSection = 'dashboard';
    }
  }

  setActiveSection(section: string) {
    console.log('🔄 Cambiando sección activa de', this.activeSection, 'a', section);
    this.activeSection = section;
  }

  goBack() {
    this.router.navigate(['/']);
  }

  getSectionTitle(): string {
    const titles: { [key: string]: string } = {
      'dashboard': 'Dashboard',
      'oficinas': 'Gestión de Oficinas de Tránsito',
      'categorias': 'Gestión de Categorías',
      'examenes': 'Gestión de Exámenes',
      'preguntas': 'Gestión de Preguntas'
    };
    return titles[this.activeSection] || 'Dashboard';
  }
} 