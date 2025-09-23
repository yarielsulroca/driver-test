
<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label>DNI:</label>
        <input type="text" v-model="dni" required>
      </div>
      <div class="form-group">
        <label>Token:</label>
        <input type="password" v-model="token" required>
      </div>
      <button type="submit">Iniciar Sesión</button>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { SesionService } from '../services/sesionService';

export default defineComponent({
  name: 'Login',
  data() {
    return {
      dni: '',
      token: '',
      sesionService: new SesionService()
    }
  },
  methods: {
    async handleLogin() {
      const loggedIn = await this.sesionService.iniciarSesion(this.dni, this.token);
      if (loggedIn) {
        this.$emit('login-success');
      } else {
        alert('Error al iniciar sesión');
      }
    }
  }
});
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
}
</style>
