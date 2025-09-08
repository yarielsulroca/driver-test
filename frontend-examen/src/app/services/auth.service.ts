import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { tap, catchError, map } from 'rxjs/operators';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';
import { ApiService } from './api.service';
import { ApiResponse } from '../shared/interfaces';
import { environment } from '../../environments/environment';

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  permissions?: string[];
}

export interface LoginCredentials {
  email: string;
  password: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

  private jwtHelper = new JwtHelperService();

  constructor(
    private apiService: ApiService,
    private router: Router
  ) {
    this.checkAuthStatus();
  }

  private checkAuthStatus(): void {
    const token = this.getToken();
    if (token && !this.jwtHelper.isTokenExpired(token)) {
      try {
        const decodedToken = this.jwtHelper.decodeToken(token);
        const user: User = {
          id: decodedToken.user_id,
          name: decodedToken.name,
          email: decodedToken.email,
          role: decodedToken.role,
          permissions: decodedToken.permissions || []
        };
        this.currentUserSubject.next(user);
        this.isAuthenticatedSubject.next(true);
      } catch (error) {
        this.logout();
      }
    } else {
      this.logout();
    }
  }

  login(credentials: LoginCredentials): Observable<ApiResponse> {
    return this.apiService.login(credentials).pipe(
      tap((response: ApiResponse) => {
        if (response.success && response.data?.token) {
          this.setToken(response.data.token);
          this.setUser(response.data.user);
          this.isAuthenticatedSubject.next(true);
        }
      }),
      catchError((error) => {
        this.logout();
        throw error;
      })
    );
  }

  logout(): Observable<ApiResponse> {
    return this.apiService.logout().pipe(
      tap(() => {
        this.clearAuth();
        this.router.navigate(['/auth/login']);
      }),
      catchError((error) => {
        this.clearAuth();
        this.router.navigate(['/auth/login']);
        return of({ status: 'error', success: false, message: 'Logout exitoso' });
      })
    );
  }

  refreshToken(): Observable<ApiResponse> {
    return this.apiService.refreshToken().pipe(
      tap((response: ApiResponse) => {
        if (response.status === 'success' && response.data?.token) {
          this.setToken(response.data.token);
        }
      }),
      catchError((error) => {
        this.logout();
        throw error;
      })
    );
  }

  public setToken(token: string): void {
    localStorage.setItem(environment.jwtKey, token);
  }

  private getToken(): string | null {
    return localStorage.getItem(environment.jwtKey);
  }

  private setUser(user: User): void {
    this.currentUserSubject.next(user);
  }

  private clearAuth(): void {
    localStorage.removeItem(environment.jwtKey);
    this.currentUserSubject.next(null);
    this.isAuthenticatedSubject.next(false);
  }

  getCurrentUser(): User | null {
    return this.currentUserSubject.value;
  }

  isLoggedIn(): boolean {
    return this.isAuthenticatedSubject.value;
  }

  hasRole(role: string): boolean {
    const user = this.getCurrentUser();
    return user ? user.role === role : false;
  }

  hasPermission(permission: string): boolean {
    const user = this.getCurrentUser();
    return user && user.permissions ? user.permissions.includes(permission) : false;
  }

  hasAnyRole(roles: string[]): boolean {
    const user = this.getCurrentUser();
    return user ? roles.includes(user.role) : false;
  }

  hasAnyPermission(permissions: string[]): boolean {
    const user = this.getCurrentUser();
    if (!user || !user.permissions) return false;
    return permissions.some(permission => user.permissions!.includes(permission));
  }

  // Método para verificar si el token está próximo a expirar
  isTokenExpiringSoon(minutes: number = 5): boolean {
    const token = this.getToken();
    if (!token) return true;

    const expirationDate = this.jwtHelper.getTokenExpirationDate(token);
    if (!expirationDate) return true;

    const now = new Date();
    const timeUntilExpiration = expirationDate.getTime() - now.getTime();
    const minutesUntilExpiration = timeUntilExpiration / (1000 * 60);

    return minutesUntilExpiration <= minutes;
  }

  // Método para renovar automáticamente el token si es necesario
  autoRefreshToken(): Observable<boolean> {
    if (this.isTokenExpiringSoon()) {
      return this.refreshToken().pipe(
        map(() => true),
        catchError(() => of(false))
      );
    }
    return of(true);
  }
} 