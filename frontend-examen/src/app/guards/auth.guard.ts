import { Injectable } from '@angular/core';
import { CanActivate, CanActivateChild, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { map, catchError, tap } from 'rxjs/operators';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate, CanActivateChild {
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    return this.checkAuth(route, state);
  }

  canActivateChild(
    childRoute: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    return this.checkAuth(childRoute, state);
  }

  private checkAuth(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    // Verificar si el usuario está autenticado
    if (!this.authService.isLoggedIn()) {
      this.router.navigate(['/auth/login'], {
        queryParams: { returnUrl: state.url }
      });
      return of(false);
    }

    // Verificar roles requeridos
    const requiredRoles = route.data['roles'] as string[];
    if (requiredRoles && requiredRoles.length > 0) {
      if (!this.authService.hasAnyRole(requiredRoles)) {
        this.router.navigate(['/unauthorized']);
        return of(false);
      }
    }

    // Verificar permisos requeridos
    const requiredPermissions = route.data['permissions'] as string[];
    if (requiredPermissions && requiredPermissions.length > 0) {
      if (!this.authService.hasAnyPermission(requiredPermissions)) {
        this.router.navigate(['/unauthorized']);
        return of(false);
      }
    }

    // Verificar si el token está próximo a expirar y renovarlo si es necesario
    return this.authService.autoRefreshToken().pipe(
      map(success => {
        if (!success) {
          this.router.navigate(['/auth/login'], {
            queryParams: { returnUrl: state.url }
          });
          return false;
        }
        return true;
      }),
      catchError(() => {
        this.router.navigate(['/auth/login'], {
          queryParams: { returnUrl: state.url }
        });
        return of(false);
      })
    );
  }
} 