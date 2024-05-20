<template>
  <div class="login">
    <form @submit.prevent="registerUser" class="form">
      <label class="form-label">REGISTRO</label>
      <label class="form-label" for="#username">Nombre de usuario:</label>
      <input
        v-model="username"
        class="form-input"
        id="user"
        required
        placeholder="Nombre de usuario"
        @input="saneaUser"
        title="Caracteres prohibidos <>&/\'etc "
      />
      <label class="form-label" for="#password">Password:</label>
      <input
        v-model="password"
        class="form-input"
        type="password"
        id="password"
        required
        placeholder="Password"
        @input="saneaPassword"
        title="Caracteres prohibidos <>&/\'etc "
      />
      <div class="radio-container">
        Registrarse como:
        <label class="form-label" for="usuario">Usuario</label>
        <input type="radio" id="usuario" name="role" value="ROLE_USER" v-model="selectedRole" checked />
        <label class="form-label" for="entrenador">Entrenador</label>
        <input type="radio" id="entrenador" name="role" value="ROLE_ADMIN" v-model="selectedRole"/>
        
      </div>
      <input
        class="form-submit"
        type="submit"
        value="Registrarse"
      />
    </form>
    <div v-if="userCreadoBool">Usuario registrado correctamente</div>
    <div v-if="userErrorServidor">
      No se ha podido registrar al usuario intentelo de nuevo más tarde
    </div>
  </div>
</template>
  
  <script>
export default {
  name: "_registro",
  data() {
    return {
      username: "",
      password: "",
      userCreadoBool: false,
      registroErrorServidor: false,
      selectedRole: "ROLE_USER" // Valor predeterminado para el radio button
    };
  },
  methods: {
    saneaUser() {
      this.username = this.sanitizarDatos(this.username);
    },
    saneaPassword() {
      this.password = this.sanitizarDatos(this.password);
    },
    sanitizarDatos(datos) {
      return datos.replace(/[<>&/"']/g, "");//eliminar caracteres peligrosos
    },
    async registerUser() {
      let rol = [] ;
      rol.push(this.selectedRole)// añadimos el rol seleccionado a la peticion de la api
      let newUser = { username: this.username, plainPassword: this.password ,"roles": rol};
      try {
        const response = await fetch("http://localhost:8181/api/users", {
          method: "POST",
          body: JSON.stringify(newUser),
          headers: { "Content-type": "application/ld+json; charset=UTF-8" },
        });

        await response.json();
        if (response.ok) {
          // Esto verifica que la respuesta esté en el rango 200-299
          this.userCreadoBool = true;
          this.$emit("registro-ok", "Usuario registrado correctamente");
        } else {
          this.userErrorServidor = true;
          this.$emit(
            "registro-ok",
            "No se ha podido registrar al usuario intentelo de nuevo más tarde"
          );
        }
      } catch (error) {
        console.error(error);
        this.registroErrorServidor = true;
      }
    },
  },
};
</script>
  
  <style scoped>
.title {
  text-align: center;
}
.titulo {
  margin-top: 10px;
  margin-bottom: 0px !important;
}
.form {
  margin: 1rem auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: 13%;
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
.radio-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
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
  margin-top: 10px;
  padding: 2px;
}
</style>
  