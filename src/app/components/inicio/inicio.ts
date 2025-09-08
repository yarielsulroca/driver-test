import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-inicio',
  templateUrl: './inicio.html',
  styleUrls: ['./inicio.scss'],
  standalone: true,
  imports: [ReactiveFormsModule]
})
export class InicioComponent {
  dniForm: FormGroup;

  constructor(private fb: FormBuilder) {
    this.dniForm = this.fb.group({
      dni: ['', [Validators.required, Validators.pattern(/^[0-9]{7,8}$/)]]
    });
  }

  onSubmit() {
    if (this.dniForm.valid) {
      console.log('DNI:', this.dniForm.value.dni);
      // Aquí puedes redirigir o hacer la lógica de autenticación
    }
  }
} 