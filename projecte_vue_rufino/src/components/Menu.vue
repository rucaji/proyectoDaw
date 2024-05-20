<template>
  <nav ref="navbar" v-show="showNavbar">
    <div v-if="logueado" class="menu-container">
      <div class="button-group">
        <router-link to="/" class="menu"
          ><button
            class="btn btn-outline-success"
            title="Va a la página inicial"
          >
            Inicio
          </button></router-link
        >
        |
        <router-link to="/ejercicios" class="menu"
          ><button
            class="btn btn-outline-success"
            title="Muestra la lista de ejercicios y da la oportunidad de resolverlos"
          >
            Mostrar ejercicios
          </button></router-link
        >
        |
        <router-link to="/partidas" class="menu"
          ><button
            class="btn btn-outline-success"
            title="Va a la pagina para ver, importar partidas, etc"
          >
            Ver Partidas
          </button></router-link
        >
      </div>
      <span class="welcome-text">Bienvenid@ {{ usuarioActual }}</span>
      <button
      
        id="botonLogout"
        class="btn btn-outline-success"
        title="salir"
        @click="logOut"
      >
        LogOut
      </button>
    </div>

    <span class="spacerBig" v-if="!logueado"></span>
    <button class="btn btn-success" v-if="!logueado" @click="login">
      Login
    </button>
    <span class="spacer" v-if="!logueado"></span>

    <button class="btn btn-success" v-if="!logueado" @click="registrar">
      Registro
    </button>
    <login v-if="logueando" @login-ok="finLogin" /><registro
      v-if="registrando"
      @registro-ok="finRegistro"
    />
    <div v-if="showMessage" class="alert">
      {{ message }}
    </div>
  </nav>
</template>
  
  <script>
import login from "./login.vue";
import Registro from "./registro.vue";

export default {
  name: "_Menu",
  components: { login, Registro },
  data() {
    return {
      registrando: false,
      logueando: false,
      logueado: false,
      showNavbar: false,
      message: "",
      showMessage: false,
      usuarioActual: "",
      
    };
  },
  
  mounted() {
    document.addEventListener("mousemove", this.handleMouseMove); // Agregar evento de escucha
    this.usuarioActual = localStorage.getItem("savedUser");
    this.logueado = (localStorage.getItem("logueado" )=== "si");
  },
  beforeUnmount() {
   
    document.removeEventListener("mousemove", this.handleMouseMove); // para la barra que se oculta
  },
  methods: {
    handleMouseMove(e) {// para la barra que se oculta
      if (e.clientY < 60 || this.$route.path == "/") {
        
        this.showNavbar = true;
      } else {
        this.showNavbar = false;
      }
    },
    login() {
      this.logueando = true;
      this.registrando = false;
    },
    logOut() {// te manda al inicio si no estas logueado
      this.logueado = false;
      localStorage.setItem("logueado","no");
      window.location = `http://localhost:8080/`;
      setTimeout(() => {
        window.location.reload(true);
      }, 500);
    },
    registrar() {
      this.registrando = true;
      this.logueando = false;
    },
    finRegistro(mensaje) {
      this.message = mensaje;
      this.registrando = false;
      this.showMessageMetod();
    },
    finLogin(mensaje) {
      let inicioMenjaje = mensaje.split(" ")[0];// para no poner mensaje en el caso de login exitoso, sini que le damos la bienvenida
      if (inicioMenjaje !== "login") {
        this.message = mensaje;
        this.showMessageMetod();
      } else {
        let extraerUserCadena = mensaje.match(/de (.*)/);//para sacar el nombre usuario de la cadena enviada por el login
        this.usuarioActual = extraerUserCadena[1];
        this.logueando = false;
        this.logueado = true;
      }
    },
    showMessageMetod() {
      this.showMessage = true;
      setTimeout(() => {
        this.showMessage = false;
      }, 1500);
    },
  },
};
</script>
  
  <style>
nav {
  position: fixed;
  top: 0;
  width: 100%;
  background-color: rgb(126, 171, 151);
  font-size: large;
  font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
  z-index: 11;
}

.spacer {
  margin-left: 15px; /* Ajusta el tamaño del espacio según sea necesario */
}
.button-group {
  display: flex;
  justify-content: center;
  flex-grow: 1; /* Permite que este grupo ocupe todo el espacio disponible */
}
.menu-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.spacerBig {
  margin-left: 50vw; /* para la separacion de botones de login y registrar Ajusta el tamaño del espacio según sea necesario */
}
.welcome-text {
  flex-shrink: 0; /* Evita que el texto se encoja, asegurando que siempre se muestre completamente */
  white-space: nowrap; /* Evita que el texto se divida en varias líneas */
}
#botonLogout {
  margin-left: 5px;
}
</style>
  