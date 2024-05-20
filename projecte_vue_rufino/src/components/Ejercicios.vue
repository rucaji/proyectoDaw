<template>
  <div class="containerBig">
    <div class="cabecera">
      <h1>{{ msg }}</h1>

      <button
        type="button"
        class="btn btn-outline-success"
        @click="toggleFiltro"
        title="Puede seleccionar uno o varios filtros de búsqueda"
      >
        Filtrar
      </button>
      <button
        type="button"
        class="btn btn-outline-success"
        @click="this.afegirEjercicio()"
        title="Puede añadir un nuevo ejercio si es un entrenador registrado"
        v-if="rolAdmin"
      >
        Añadir ejercicio
      </button>
    </div>

    <div v-if="mostrarFiltro" class="filtro-container">
      <!-- Filtro de autor -->
      <div class="form-group">
        <input
          type="checkbox"
          id="filtroAutor"
          v-model="filtroAutor"
          @change="cambioCheckbox"
        />
        <label for="filtroAutor" title="Puede filtrar por autor">Autor</label>
        <input
          v-if="filtroAutor"
          v-model="valorAutor"
          type="text"
          class="form-control"
          placeholder="Nombre del autor"
        />
      </div>

      <!-- Filtro de dificultad -->
      <div class="form-group radio-group">
        <label class="titulo" title="Puede filtrar por dificultad"
          >Dificultad:</label
        >
        <div class="radio-options">
          <div>
            <input
              type="radio"
              id="filtroBaja"
              value="baja"
              v-model="filtroDificultad"
            />
            <label for="filtroBaja">Baja</label>
          </div>
          <div>
            <input
              type="radio"
              id="filtroIntermedia"
              value="intermedia"
              v-model="filtroDificultad"
            />
            <label for="filtroIntermedia">Intermedia</label>
          </div>
          <div>
            <input
              type="radio"
              id="filtroAlta"
              value="alta"
              v-model="filtroDificultad"
            />
            <label for="filtroAlta">Alta</label>
          </div>
        </div>
      </div>

      <!-- Filtro de tema -->
      <div class="form-group">
        <label
          for="filtroTema"
          class="titulo"
          title="Puede filtrar por tipo de ejercicio"
          >Tema del ejercicio:</label
        >
        <select v-model="filtroTema" id="filtroTema" class="form-control">
          <option disabled value="">Seleccione un tema</option>
          <option value="Clavada">Clavada</option>
          <option value="Descubierta">Descubierta</option>
          <option value="Enfilada">Enfilada</option>
          <option value="Rayos-x">Rayos-x</option>
          <option value="Jugada intermedia">Jugada intermedia</option>
          <option value="Ataque doble">Ataque doble</option>
          <option value="Ataque de mate">Ataque de mate</option>
          <option value="Coronación">Coronación</option>
          <option value="Finales">Finales</option>
          <option value="Acorralamiento">Acorralamiento</option>
          <option value="Desvio">Desvio</option>
          <option value="Encaminamiento">Encaminamiento</option>
          <option value="Intercepcion">Intercepcion</option>
          <option value="Eliminacion de la defensa">
            Eliminacion de la defensa
          </option>
          <option value="Sobrecarga">Sobrecarga</option>
          <option value="Combinación de elementos">
            Combinación de elementos
          </option>
        </select>
      </div>
      <button class="btn btn-success" @click="guardarFiltro">OK</button>
      <button class="btn btn-secondary" @click="cancelarFiltro">
        Cancelar
      </button>
    </div>
    <br /><br />
    <div id="tabla">
      <table class="table table-hover">
        <thead class="table-fija">
          <tr>
            <th title="número del ejercicio en la base de datos">Número</th>
            <th title="quien registró el ejercicio">Autor</th>
            <th title="dificultad del ejercicio según su autor">Dificultad</th>
            <th title="tema del ejercicio según su autor">Tema</th>
            <th title="enunciado del ejercicio">Enunciado</th>
            <th title="posición del ejercio, pinche para intentar resolverlo">
              Posicion
            </th>
            <th title="">Opciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ejercicio in ejercicios" :key="ejercicio.id">
            <td>{{ ejercicio.id }}</td>
            <td>{{ ejercicio.autor }}</td>
            <td>{{ ejercicio.dificultad }}</td>
            <td>{{ ejercicio.tema }}</td>
            <td>{{ ejercicio.enunciado }}</td>
            <td>
              <TheChessboard
                :board-config="getCustomBoardConfig(ejercicio)"
                style="width: 100px; height: 100px"
                @click="VisualizaEjercicio(ejercicio.id)"
              />
            </td>

            <td>
              <botones
                :rowId="ejercicio.id"
                @visualiza="VisualizaEjercicio"
                @edita="editaEjercicio"
                @delete="deleteEjercicio"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showAlert" class="alert">
      {{ alertMessage }}
    </div>
  </div>
</template>
  
<script>
import TheChessboard from "./TheChessboard.vue";
import Botones from "./botones.vue";
export default {
  name: "_Ejercicios",
  data() {
    return {
      msg: "Lista de ejercicios",
      ejercicios: Array,
      rowId: "",
      mostrarFiltro: false,//booleano para la visibilidad del filtro de ejercicios
      filtroAutor: false,//booleano para la visibilidad del input de autor en elfiltro de ejercicios
      valorAutor: "",
      filtroDificultad: "",
      filtroTema: "",
      showAlert: false,//textos temporales de alerta al usuario
      alertMessage: "",
      token: "",
      rolAdmin: false,// para difernciar las visibilidades segun roles
      usuarioActual: "",
    };
  },
  components: {
    TheChessboard,
    Botones,
  },
  methods: {
    cambioCheckbox() {
      if (!this.filtroAutor) {
        this.valorAutor = ""; // Vacía valorAutor cuando el checkbox es desmarcado
      }
    },
    getCustomBoardConfig(ejercicio) {//inicializar la posicion del ejercicio en el tablero
      return {
        fen: ejercicio.fen,
      };
    },
    toggleFiltro() {// cambia la visibilidad
      this.mostrarFiltro = !this.mostrarFiltro;
    },
    async guardarFiltro() {
      // Guardar los valores de los filtros y hacer la petición a la api
      this.mostrarFiltro = false; // Ocultar el div después de guardar
      let filtro = {
        autor: this.valorAutor,
        dificultad: this.filtroDificultad,
        tema: this.filtroTema,
      };
      try {
        const response = await fetch(
          "http://localhost:8181/api/ejercicios/filtro",
          {
            method: "POST",
            body: JSON.stringify(filtro),
            headers: {
              "Content-type": "application/ld+json; charset=UTF-8",
              Authorization: `Bearer ${this.token}`,
            },
          }
        );
        let llegada = await response.json();
        this.ejercicios = llegada;

        const ids = this.ejercicios.map((ejercicio) => ejercicio.id);

        // Convertir el array de IDs a JSON y guardarlo como cookie de sesión para manejar el siguiente ejercicio
        document.cookie = `idsEjercicios=${JSON.stringify(ids)}; path=/`;
      } catch (error) {
        console.error("ERROR: " + error);
      }
    },
    cancelarFiltro() {
      // Restablecer valores y ocultar el filtro
      this.filtroAutor = false;
      this.valorAutor = "";
      this.filtroDificultad = "";
      this.filtroTema = "";
      this.mostrarFiltro = false;
    },
    async getEjercicios() {
      try {
        const response = await fetch("http://localhost:8181/api/ejercicios", {
          headers: {
            Authorization: `Bearer ${this.token}`,
            "Content-Type": "application/json",
          },
        });
        let llegada = await response.json();
        this.ejercicios = llegada["hydra:member"];

        const ids = this.ejercicios.map((ejercicio) => ejercicio.id);

        // Convertir el array de IDs a JSON y guardarlo como cookie de sesión para manejar el ejercicio siguiente
        document.cookie = `idsEjercicios=${JSON.stringify(ids)}; path=/`;
      } catch (error) {
        console.error("ERROR: " + error);
      }
    },
    async deleteEjercicio(rowId) {
      let ejercicio = await this.getEjercicio(rowId);
      if (ejercicio["autor"] === localStorage.getItem("savedUser")) {// para no permitir borrar si no es del autor
        try {
          const response = await fetch(
            `http://localhost:8181/api/ejercicios/${rowId}`,
            {
              method: "DELETE",
              headers: {
                Authorization: `Bearer ${this.token}`,
                "Content-Type": "application/json",
              },
            }
          );
          await this.getEjercicios(); // Actualizar la lista de ejercicios

          if (response.ok) {
            this.alertMessage = "Ejercicio borrado exitosamente";
          } else {
            this.alertMessage = "No ha sido posible borrar el ejercicio";
          }
          this.showAlert = true; // Mostrar el cartel
          setTimeout(() => {
            this.showAlert = false;
          }, 3000);
        } catch (error) {
          console.error(error);
          this.alertMessage = "Error en la conexión o el servidor";
          this.showAlert = true; // Mostrar el cartel

          setTimeout(() => {
            this.showAlert = false;
          }, 3000);
        }
      } else {
        this.alertMessage = "Solo puede eliminar sus propios ejercicios";
        this.showAlert = true; // Mostrar el cartel
        setTimeout(() => {
          this.showAlert = false;
        }, 3000);
      }
    },
    getToken() {
      let name = "authToken";
      let matches = document.cookie.match(
        new RegExp(
          "(?:^|; )" +
            name.replace(/([.$?*|{}()[\]\\/+^])/g, "\\$1") +
            "=([^;]*)"
        )
      );
      return matches ? decodeURIComponent(matches[1]) : undefined;
    },
    afegirEjercicio() {
      this.$router.push("NuevoEjercicio");
    },
    VisualizaEjercicio(rowId) {
      this.$router.push({ name: "ejercicio", params: { id: rowId } });
    },
    async editaEjercicio(rowId) {
      let ejercicio = await this.getEjercicio(rowId);
      if (ejercicio["autor"] === localStorage.getItem("savedUser")) {
        this.$router.push({
          name: "EditaEjercicio",
          params: { id: rowId },
        });
      } else {
        this.alertMessage = "Solo puede editar sus propios ejercicios";
        this.showAlert = true; // Mostrar el 
        setTimeout(() => {
          this.showAlert = false;
        }, 3000);
      }
    },
    async getEjercicio(id) {
      try {
        const response = await fetch(
          `http://localhost:8181/api/ejercicios/${id}`,
          {
            headers: {
              Authorization: `Bearer ${this.token}`,
              "Content-Type": "application/json",
            },
          }
        );
        const ejercicio = await response.json();
        return ejercicio;
      } catch (error) {
        console.error(error);
      }
    },
  },
  async beforeMount() {// para actualizar el token de sesion
    let usernameActual = localStorage.getItem("savedUser") || "";
    let passwordActual = localStorage.getItem("savedPassword") || "";
    let user = { username: usernameActual, password: passwordActual };
    try {
      const response = await fetch("http://localhost:8181/auth", {
        method: "POST",
        body: JSON.stringify(user),
        headers: { "Content-type": "application/ld+json; charset=UTF-8" },
      });

      const data = await response.json();
      if (response.ok) {
        document.cookie = `authToken=${data.token}; path=/; max-age=3600`; // Expira en 1 hora
      }
    } catch (error) {
      console.error(error);
    }
  },
  mounted() {
    if (localStorage.getItem("logueado") !== "si") {// si no esta logueado te manda a la pantalla inicio
      window.location = `http://localhost:8080/`;
      setTimeout(() => {
        window.location.reload(true);
      }, 500);
    }
    (this.rolAdmin = localStorage.getItem("savedRol") === "ROLE_ADMIN"),
      (this.token = this.getToken());
    this.getEjercicios();
  },
};
</script>
 <style scoped >
.cabecera {
  display: inline-flex;
}
h1 {
  margin-right: 60px;
}
.containerBig {
  background-image: url("../assets/estudio_partidas_opacity_50.png");
  background-size: cover;
  color: rgb(87, 153, 87);
  text-shadow: 3px 3px 4px #6a4e4e;
  padding: 50px;
}
#tabla {
  max-height: 75vh; /* Altura máxima de la zona scrollable */
  max-width: 75vw;
  overflow-y: auto; /* Añade un scrollbar vertical cuando sea necesario */
  border-radius: 8px;
  border: 1px solid #366e36;
  margin-left: auto; 
  margin-right: auto; 
}
.table-fija {
  position: sticky;
  top: 0;
  z-index: 10;
}
th {
  background-color: #8fc196;
}
.filtro-container {
  padding: 20px;
  background-color: #dfe2e4;
  border: 1px solid #808387;
  margin-top: 20px;
  margin-left: auto; 
  margin-right: auto; 
  display: flex;
  justify-content: space-between; /* Alinea los elementos a los extremos del contenedor */
  align-items: center;
  width: 50vw;
  border-radius: 8px;
}
.btn-outline-success {
  padding: 5px;
  margin: 10px;
}
.radio-group {
  text-align: left;
}
.titulo {
  font-size: large;
  font-weight: bold;
  color: #366e36;
}
label {
  text-shadow: none;
}
.alert {
  color: #000;
  text-align: end;
  margin-right: 125px;
}
</style> 