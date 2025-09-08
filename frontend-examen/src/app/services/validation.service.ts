import { Injectable } from '@angular/core';
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export interface ValidationRule {
  name: string;
  validator: ValidatorFn;
  message: string;
}

export interface FieldValidation {
  field: string;
  rules: ValidationRule[];
}

@Injectable({
  providedIn: 'root'
})
export class ValidationService {
  constructor() {}

  // Validadores comunes
  required(message: string = 'Este campo es obligatorio'): ValidationRule {
    return {
      name: 'required',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (value === null || value === undefined || value === '') {
          return { required: true };
        }
        if (Array.isArray(value) && value.length === 0) {
          return { required: true };
        }
        return null;
      },
      message
    };
  }

  email(message: string = 'Ingrese un email válido'): ValidationRule {
    return {
      name: 'email',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(value) ? null : { email: true };
      },
      message
    };
  }

  minLength(min: number, message?: string): ValidationRule {
    return {
      name: 'minlength',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        return value.length >= min ? null : { minlength: { requiredLength: min, actualLength: value.length } };
      },
      message: message || `Mínimo ${min} caracteres`
    };
  }

  maxLength(max: number, message?: string): ValidationRule {
    return {
      name: 'maxlength',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        return value.length <= max ? null : { maxlength: { requiredLength: max, actualLength: value.length } };
      },
      message: message || `Máximo ${max} caracteres`
    };
  }

  pattern(regex: RegExp, message: string): ValidationRule {
    return {
      name: 'pattern',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        return regex.test(value) ? null : { pattern: true };
      },
      message
    };
  }

  numeric(message: string = 'Solo se permiten números'): ValidationRule {
    return {
      name: 'numeric',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        return /^\d+$/.test(value) ? null : { numeric: true };
      },
      message
    };
  }

  decimal(message: string = 'Ingrese un número decimal válido'): ValidationRule {
    return {
      name: 'decimal',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        return /^\d+(\.\d+)?$/.test(value) ? null : { decimal: true };
      },
      message
    };
  }

  phone(message: string = 'Ingrese un teléfono válido'): ValidationRule {
    return {
      name: 'phone',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(value.replace(/\s/g, '')) ? null : { phone: true };
      },
      message
    };
  }

  password(message: string = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número'): ValidationRule {
    return {
      name: 'password',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(value) ? null : { password: true };
      },
      message
    };
  }

  confirmPassword(passwordField: string, message: string = 'Las contraseñas no coinciden'): ValidationRule {
    return {
      name: 'confirmPassword',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        const passwordControl = control.parent?.get(passwordField);
        
        if (!value || !passwordControl) return null;
        
        return value === passwordControl.value ? null : { confirmPassword: true };
      },
      message
    };
  }

  dateRange(minDate?: Date, maxDate?: Date, message?: string): ValidationRule {
    return {
      name: 'dateRange',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const date = new Date(value);
        const now = new Date();
        
        if (minDate && date < minDate) {
          return { dateRange: { min: minDate, actual: date } };
        }
        
        if (maxDate && date > maxDate) {
          return { dateRange: { max: maxDate, actual: date } };
        }
        
        return null;
      },
      message: message || 'Fecha fuera del rango permitido'
    };
  }

  fileSize(maxSizeMB: number, message?: string): ValidationRule {
    return {
      name: 'fileSize',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const file = value instanceof File ? value : null;
        if (!file) return null;
        
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        return file.size <= maxSizeBytes ? null : { fileSize: { maxSize: maxSizeMB, actualSize: file.size } };
      },
      message: message || `El archivo no debe superar ${maxSizeMB}MB`
    };
  }

  fileType(allowedTypes: string[], message?: string): ValidationRule {
    return {
      name: 'fileType',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const file = value instanceof File ? value : null;
        if (!file) return null;
        
        const fileExtension = file.name.split('.').pop()?.toLowerCase();
        return fileExtension && allowedTypes.includes(fileExtension) ? null : { fileType: { allowedTypes, actualType: fileExtension } };
      },
      message: message || `Solo se permiten archivos: ${allowedTypes.join(', ')}`
    };
  }

  // Métodos de utilidad
  getErrorMessage(control: AbstractControl, rules: ValidationRule[]): string {
    if (!control.errors) return '';

    for (const rule of rules) {
      if (control.errors[rule.name]) {
        return rule.message;
      }
    }

    return 'Campo inválido';
  }

  hasError(control: AbstractControl, errorName: string): boolean {
    return control.errors && control.errors[errorName];
  }

  isFieldValid(control: AbstractControl): boolean {
    return control.valid && (control.dirty || control.touched);
  }

  isFieldInvalid(control: AbstractControl): boolean {
    return control.invalid && (control.dirty || control.touched);
  }

  // Validadores específicos para el sistema de exámenes
  examDuration(message: string = 'La duración debe estar entre 5 y 180 minutos'): ValidationRule {
    return {
      name: 'examDuration',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const duration = parseInt(value);
        return duration >= 5 && duration <= 180 ? null : { examDuration: true };
      },
      message
    };
  }

  examScore(message: string = 'La puntuación debe estar entre 0 y 100'): ValidationRule {
    return {
      name: 'examScore',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const score = parseFloat(value);
        return score >= 0 && score <= 100 ? null : { examScore: true };
      },
      message
    };
  }

  questionCount(message: string = 'Debe tener al menos 1 pregunta'): ValidationRule {
    return {
      name: 'questionCount',
      validator: (control: AbstractControl): ValidationErrors | null => {
        const value = control.value;
        if (!value) return null;
        
        const count = parseInt(value);
        return count >= 1 ? null : { questionCount: true };
      },
      message
    };
  }
} 