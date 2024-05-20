<template>
  <div class="login">
    <form @submit.prevent="loginUser" class="form">
      <label class="form-label" for="username">Nombre de usuario:</label>
      <input
        class="form-input"
        id="username"
        required
        placeholder="nombre de usuario"
        v-model="username"
        @input="saneaUser"
        title="Caracteres prohibidos <>&/\'etc "
      />
      <label class="form-label" for="password">Contraseña:</label>
      <input
        class="form-input"
        type="password"
        id="password"
        placeholder="Contraseña"
        v-model="password"
        @input="saneaPassword"
        title="Caracteres prohibidos <>&/\'etc "
      />
      <label>
        <input type="checkbox" id="remember-me" v-model="recordarBool" />
        Recordar en este navegador
      </label>
      <br />
      <input class="form-submit" type="submit" value="Entrar" />
    </form>
  </div>
</template>

<script>

import * as jwtDecode from 'jwt-decode';
export default {
  name: "_login",
  data() {
    return {
      username: "",
      password: "",
      recordarBool: false,
    };
  },
  mounted() {
    this.username = localStorage.getItem("savedUser") || "";
    this.password = localStorage.getItem("savedPassword") || "";
    this.recordarBool = !!this.username;
  },
  methods: {
    
    saneaUser() {
      this.username = this.sanitizarDatos(this.username);
    },
    saneaPassword() {
      this.password = this.sanitizarDatos(this.password);
    },
    sanitizarDatos(datos) {
      return datos.replace(/[<>&/"']/g, "");
    },
    async loginUser() {
      let user = { username: this.username, password: this.password };
      try {
        const response = await fetch("http://localhost:8181/auth", {
          method: "POST",
          body: JSON.stringify(user),
          headers: { "Content-type": "application/ld+json; charset=UTF-8" },
        });

        const data = await response.json();
        if (response.ok) {
          document.cookie = `authToken=${data.token}; path=/; max-age=3600`; // Expira en 1 hora
          this.$emit("login-ok", `login exitoso de ${this.username}`);
          localStorage.setItem("logueado", "si");
          if (this.recordarBool) {// guardar datos si asi lo indica el usuario con el checkbox
            localStorage.setItem("savedUser", this.username);
            localStorage.setItem("savedPassword", this.password);
            const token = data.token; //obtenemos el rol del token y lo guardamos
            const rolDecodedToken = jwtDecode.jwtDecode(token)['roles'][0];
            localStorage.setItem("savedRol", rolDecodedToken);
          }
        } else {
          this.userErrorServidor = true;
          this.$emit(
            "login-error",
            "No se ha podido entrar intentelo de nuevo más tarde"
          );
        }
      } catch (error) {
        console.error(error);
        this.loginErrorServidor = true;
        this.$emit("login-error", "Error en la conexión al servidor");
      }
    },
  },
};
</script>

  
  <style scoped>
.title {
  text-align: center;
}
.form {
  margin: 1rem auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: 10%;
  background: rgba(101, 138, 106, 0.9);
  border-radius: 10px;
  padding: 10px;
  box-shadow: 0 4px 10px 4px rgba(0, 0, 0, 0.3);
  overflow: auto;
}
label {
  color: rgb(161, 182, 161);
}
.form-label {
  color: rgb(207, 229, 207);
  margin-bottom: 0.5rem;
}
.form-input {
  padding: 5px;
  background: none;
  background-image: none;
  border: 1px solid white;
  border-radius: 5px;
  color: rgb(169, 174, 171);
}
.form-submit {
  background: #4bae93;
  border-radius: 5px;
  border: 1px solid rgb(197, 240, 208);
  color: rgb(19, 111, 51);
  cursor: pointer;
  margin-left: auto;
  margin-right: 10px;
  margin-top: 5px;
  padding: 2px;
}
</style>