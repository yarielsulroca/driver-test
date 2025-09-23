import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

export class SessionService {
    async iniciarSesion(dni: string, token: string): Promise<boolean> {
        try {
            const response = await axios.post(`${API_URL}/session/registrar`, { dni, token });
            return response.data.success;
        } catch (error) {
            console.error('Error al iniciar sesión:', error);
            return false;
        }
    }

    async verificarSesion(dni: string, token: string): Promise<boolean> {
        try {
            const response = await axios.post(`${API_URL}/session/verificar`, { dni, token });
            return response.data.valid;
        } catch (error) {
            return false;
        }
    }

    async cerrarSesion(dni: string): Promise<boolean> {
        try {
            const response = await axios.post(`${API_URL}/session/cerrar`, { dni });
            return response.data.success;
        } catch (error) {
            return false;
        }
    }
}
