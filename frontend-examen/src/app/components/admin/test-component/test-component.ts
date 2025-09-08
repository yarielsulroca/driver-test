import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-test-component',
  imports: [CommonModule],
  template: `
    <div style="padding: 20px;">
      <h1>Componente de Prueba</h1>
      <p>Este es un componente de prueba para verificar que la navegación funciona.</p>
      <p>ID recibido: {{ testId }}</p>
      <button (click)="goBack()">Volver</button>
    </div>
  `,
  standalone: true
})
export class TestComponent implements OnInit {
  testId: string = '';

  constructor(
    private router: Router,
    private route: ActivatedRoute
  ) {
    console.log('🔧 Constructor de TestComponent llamado');
  }

  ngOnInit() {
    console.log('🚀 Componente TestComponent inicializado');
    this.testId = this.route.snapshot.paramMap.get('id') || 'No ID';
    console.log('🔍 ID de prueba:', this.testId);
  }

  goBack() {
    this.router.navigate(['/admin']);
  }
} 