import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface LoadingState {
  isLoading: boolean;
  message?: string;
  progress?: number;
}

@Injectable({
  providedIn: 'root'
})
export class LoadingService {
  private globalLoadingSubject = new BehaviorSubject<LoadingState>({ isLoading: false });
  public globalLoading$ = this.globalLoadingSubject.asObservable();

  private specificLoadingSubject = new BehaviorSubject<Map<string, LoadingState>>(new Map());
  public specificLoading$ = this.specificLoadingSubject.asObservable();

  constructor() {}

  // Métodos para carga global
  showGlobalLoading(message?: string): void {
    this.globalLoadingSubject.next({
      isLoading: true,
      message: message || 'Cargando...'
    });
  }

  hideGlobalLoading(): void {
    this.globalLoadingSubject.next({ isLoading: false });
  }

  updateGlobalProgress(progress: number): void {
    const currentState = this.globalLoadingSubject.value;
    this.globalLoadingSubject.next({
      ...currentState,
      progress
    });
  }

  // Métodos para carga específica
  showLoading(key: string, message?: string): void {
    const currentMap = this.specificLoadingSubject.value;
    currentMap.set(key, {
      isLoading: true,
      message: message || 'Cargando...'
    });
    this.specificLoadingSubject.next(new Map(currentMap));
  }

  hideLoading(key: string): void {
    const currentMap = this.specificLoadingSubject.value;
    currentMap.delete(key);
    this.specificLoadingSubject.next(new Map(currentMap));
  }

  updateProgress(key: string, progress: number): void {
    const currentMap = this.specificLoadingSubject.value;
    const currentState = currentMap.get(key);
    if (currentState) {
      currentState.progress = progress;
      this.specificLoadingSubject.next(new Map(currentMap));
    }
  }

  // Métodos de utilidad
  isLoading(key?: string): boolean {
    if (key) {
      const currentMap = this.specificLoadingSubject.value;
      return currentMap.get(key)?.isLoading || false;
    }
    return this.globalLoadingSubject.value.isLoading;
  }

  getLoadingMessage(key?: string): string | undefined {
    if (key) {
      const currentMap = this.specificLoadingSubject.value;
      return currentMap.get(key)?.message;
    }
    return this.globalLoadingSubject.value.message;
  }

  getProgress(key?: string): number | undefined {
    if (key) {
      const currentMap = this.specificLoadingSubject.value;
      return currentMap.get(key)?.progress;
    }
    return this.globalLoadingSubject.value.progress;
  }

  // Métodos para operaciones comunes
  withLoading<T>(
    operation: Promise<T> | Observable<T>,
    key?: string,
    message?: string
  ): Promise<T> | Observable<T> {
    const loadingKey = key || 'default';
    this.showLoading(loadingKey, message);

    if (operation instanceof Promise) {
      return operation.finally(() => {
        this.hideLoading(loadingKey);
      });
    } else {
      return operation.pipe(
        finalize(() => {
          this.hideLoading(loadingKey);
        })
      );
    }
  }

  withGlobalLoading<T>(
    operation: Promise<T> | Observable<T>,
    message?: string
  ): Promise<T> | Observable<T> {
    this.showGlobalLoading(message);

    if (operation instanceof Promise) {
      return operation.finally(() => {
        this.hideGlobalLoading();
      });
    } else {
      return operation.pipe(
        finalize(() => {
          this.hideGlobalLoading();
        })
      );
    }
  }

  // Métodos para operaciones de archivo
  showFileUploadProgress(key: string, fileName: string): void {
    this.showLoading(key, `Subiendo ${fileName}...`);
  }

  updateFileUploadProgress(key: string, progress: number): void {
    this.updateProgress(key, progress);
  }

  hideFileUploadProgress(key: string): void {
    this.hideLoading(key);
  }

  // Métodos para operaciones de descarga
  showDownloadProgress(key: string, fileName: string): void {
    this.showLoading(key, `Descargando ${fileName}...`);
  }

  updateDownloadProgress(key: string, progress: number): void {
    this.updateProgress(key, progress);
  }

  hideDownloadProgress(key: string): void {
    this.hideLoading(key);
  }

  // Métodos para operaciones de guardado
  showSavingProgress(key: string, message?: string): void {
    this.showLoading(key, message || 'Guardando...');
  }

  hideSavingProgress(key: string): void {
    this.hideLoading(key);
  }

  // Métodos para operaciones de eliminación
  showDeletingProgress(key: string, message?: string): void {
    this.showLoading(key, message || 'Eliminando...');
  }

  hideDeletingProgress(key: string): void {
    this.hideLoading(key);
  }

  // Métodos para operaciones de búsqueda
  showSearchingProgress(key: string, message?: string): void {
    this.showLoading(key, message || 'Buscando...');
  }

  hideSearchingProgress(key: string): void {
    this.hideLoading(key);
  }

  // Método para limpiar todo
  clearAll(): void {
    this.hideGlobalLoading();
    this.specificLoadingSubject.next(new Map());
  }
}

// Importación necesaria para el método withLoading
import { Observable } from 'rxjs';
import { finalize } from 'rxjs/operators'; 