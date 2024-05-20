<template>
  <div class="containerBig">
    <div class="containerInterior">
      <div class="left-panel">
        <br v-if="!mostrarFiltro" />
        <br v-if="!mostrarFiltro" />
        <br />
        <div class="cabecera">
          <h4>Lista de partidas</h4>
          <button
            v-if="!mostrarFiltro"
            type="button"
            class="btn btn-outline-success"
            @click="toggleFiltro"
            title="Puede filtrar por autor"
          >
            Filtrar por autor
          </button>
          <div v-if="mostrarFiltro" class="filtro-container">
            <!-- Filtro de autor -->
            <div class="form-group">
              <label for="filtroAutor" title="Puede filtrar por autor"
                >Autor</label
              >
              <input
                v-if="mostrarFiltro"
                v-model="valorAutor"
                type="text"
                class="form-control"
                placeholder="Nombre del autor"
              />
            </div>
            <button class="btn btn-secondary" @click="cancelarFiltro">
              Cancelar
            </button>
            <button class="btn btn-success" @click="guardarFiltro">OK</button>
          </div>
        </div>
        <br />

        <div id="tabla">
          <table class="table table-hover">
            <thead class="table-fija">
              <tr>
                <th>Partida</th>
                <th>Autor</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="partida in lista_partidas"
                :key="partida.id"
                @click="mostrarPartida(partida)"
              >
                <td class="tdFlex">
                  {{ partida.cabecera }}
                  <button
                    class="btn btn-outline-danger"
                    @click.stop="delete_id(partida.id)"
                    title="Borrar la partida de la base de datos si es su autor"
                    v-if="rolAdmin"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      fill="currentColor"
                      class="bi bi-trash3-fill"
                      viewBox="0 0 16 16"
                    >
                      <path
                        d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"
                      />
                    </svg>
                  </button>
                </td>
                <td>{{ partida.autor }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <input
          type="file"
          ref="fileInput"
          style="display: none"
          accept=".pgn"
          @change="cargarArchivo"
        />
        <br />
        <button
          class="btn btn-outline-success"
          @click="importarPartidaPGN"
          title="Incorporar partidas a la base en formato pgn desde tu dispositivo si es entrenador"
          v-if="rolAdmin"
        >
          Importar PGN
        </button>
        <div v-if="showAlert" class="alert">
          {{ alertMessage }}
        </div>
      </div>
      <div class="right-panel">
        <br />
        <br />
        <br />
        <div class="button-container">
          <button
            @click="girarTablero"
            class="btn btn-outline-success"
            title="Cambiar la vista del tablero blancas/negras arriba/abajo"
          >
            Girar tablero
          </button>
          <button
            @click="analizarPartida"
            class="btn btn-outline-success"
            title="Analizar la actual situación con stockfish en una ventana nueva"
          >
            Analizar posicion
          </button>
          <button
            @click="practicarStockfish"
            class="btn btn-outline-success"
            title="Jugar la posicion contra stockfish en una ventana nueva"
          >
            Practicar contra Stockfish
          </button>
        </div>
        <br />
      </div>
    </div>
  </div>
</template>

<script>
import LichessPgnViewer from "lichess-pgn-viewer";
import "../output.css";

export default {
  name: "_verPartidas",
  data() {
    return {
      lpv: null,
      lista_partidas: [],
      rowId: "",
      showAlert: false,
      alertMessage: "",
      token: "",
      autor: "",
      rolAdmin: false,
      mostrarFiltro: false,
      filtroAutor: false,
      valorAutor: "",
    };
  },
  methods: {
    async guardarFiltro() {
      // Guardar los valores de los filtros
      this.mostrarFiltro = false; // Ocultar el div después de guardar
      let filtro = {
        autor: this.valorAutor,
      };
      try {
        const response = await fetch(
          "http://localhost:8181/api/partidas/filtro",
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
        this.lista_partidas = llegada;
      } catch (error) {
        console.error("ERROR: " + error);
      }
    },
    cancelarFiltro() {
      // Restablecer valores y ocultar el filtro
      this.filtroAutor = false;
      this.valorAutor = "";
      this.mostrarFiltro = false;
    },
    toggleFiltro() {
      this.mostrarFiltro = !this.mostrarFiltro;
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
    practicarStockfish() {
      const url = this.lpv.practiceUrl();
      window.open(url, "_blank");
    },
    analizarPartida() {
      const url = this.lpv.analysisUrl();
      window.open(url, "_blank");
    },
    girarTablero() {
      this.lpv.flip();
    },
    async getPartidas() {
      try {
        const response = await fetch(`http://localhost:8181/api/partidas`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
            "Content-Type": "application/json",
          },
        });
        const data = await response.json();
        this.lista_partidas = data["hydra:member"];
      } catch (error) {
        console.error(error);
      }
    },
    async getPartida(id) {//funcion para obtener una partida por su id  y saber su autor en delete
      try {
        const response = await fetch(
          `http://localhost:8181/api/partidas/${id}`,
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
    async delete_id(rowId) {
      let partida = await this.getPartida(rowId);
      if (partida["autor"] === localStorage.getItem("savedUser")) {
        try {
          const response = await fetch(
            `http://localhost:8181/api/partidas/${rowId}`,
            {
              method: "DELETE",
              headers: {
                Authorization: `Bearer ${this.token}`,
                "Content-Type": "application/json",
              },
            }
          );
          await this.getPartidas();
          if (response.ok) {
            this.alertMessage = "Partida borrada exitosamente";
          } else {
            this.alertMessage = "No ha sido posible borrar la partida";
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
        this.alertMessage = "Solo puedes eliminar las partidas subidas por ti";
        this.showAlert = true; // Mostrar el cartel
        setTimeout(() => {
          this.showAlert = false;
        }, 3000);
      }
    },
    async importarPartidaPGN() {
      // Abre el selector de archivos
      this.$refs.fileInput.click();
    },
    async cargarArchivo(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Leer el contenido del archivo
      const reader = new FileReader();
      reader.onload = async () => {
        const pgn = reader.result;

        // Crear una copia de la lista de partidas
        const tempList = [...this.lista_partidas];

        // Agregar la nueva partida a la copia de la lista
        tempList.push({
          cabecera: this.removeFileExtension(file.name),
          jugadas: pgn,
          autor: this.autor,
        });

        // Asignar la lista actualizada a this.lista_partidas
        this.lista_partidas = tempList;

        try {
          const response = await fetch("http://localhost:8181/api/partidas", {
            method: "POST",
            body: JSON.stringify({
              cabecera: this.removeFileExtension(file.name),
              jugadas: pgn,
              autor: this.autor,
            }),
            headers: {
              Authorization: `Bearer ${this.token}`,
              "Content-type": "application/ld+json; charset=UTF-8",
            },
          });
          await response.json();
        } catch (error) {
          console.error(error);
        }
      };
      reader.readAsText(file);
    },
    removeFileExtension(filename) {// eliminar la extension del archivo
      return filename.replace(/\.[^.]*$/, "");
    },
    extraerMovimientos(pgn) {
      // Eliminar contenido entre corchetes
      pgn = pgn.replace(/\{[^}]*\}/g, "");

      // Eliminar información de FEN y espacios adicionales
      pgn = pgn.replace(/\[[^\]]*\]/g, "").trim();

      // Extraer el primer movimiento antes del primer espacio
      return pgn;
    },

    mostrarPartida(partida) {
      try {// eliminar el anterior contenedor de lpv
        const previousContainer = document.getElementsByClassName(
          "lpv lpv--moves-auto lpv--controls-true"  // clase del contenedor creado previamente
        )[0];
        if (previousContainer) {
          previousContainer.parentNode.removeChild(previousContainer);
        }
      } catch (error) {
        console.error(error);
      }

      // Crear un nuevo contenedor
      const container = document.createElement("div");
      container.id = "pgn-viewer-container";
      document.querySelector(".right-panel").appendChild(container);

      if (container) {
        // Limpiamos el contenido del contenedor
        let jugadas = this.extraerMovimientos(partida.jugadas);

        // Inicializamos un nuevo visor de partidas
        this.lpv = new LichessPgnViewer(container, {
          pgn: jugadas,

          translate: (key) => {
            const translations = {
              flipTheBoard: "Girar el tablero",
              analysisBoard: "Tablero de análisis",
              practiceWithComputer: "Practica con Stockfish",
              getPgn: "Obten el pgn",
              download: "Descarga",
              viewOnLichess: "Visto en Lichess",
              viewOnSite: "Visto en sitio web",
            };
            return translations[key] || key; // Retorna la clave original si no hay traducción.
          },
        });

        this.$forceUpdate();
      } else {
        console.error(
          "El contenedor del visor de partidas no se encontró en el DOM."
        );
      }
    },
  },
  async beforeMount() {
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
  async mounted() { // envia a inicio si no esta logueado
    if (localStorage.getItem("logueado") !== "si") {
      window.location = `http://localhost:8080/`;
      setTimeout(() => {
        window.location.reload(true);
      }, 500);
    }
    this.rolAdmin = localStorage.getItem("savedRol") === "ROLE_ADMIN";
    this.autor = localStorage.getItem("savedUser") || "";
    this.token = this.getToken();
    await this.getPartidas();
    if (this.lista_partidas.length > 0) {
      this.mostrarPartida(this.lista_partidas[0]);
    }
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<link rel="stylesheet" href="../output.css"/>
<style scoped>
.containerBig {
  display: flex;
  align-items: stretch;
  flex-wrap: wrap;
  width: 100vw;
  min-height: 100vh;
  background-image: url("../assets/estudio_partidas_opacity_50.png");
  background-size: cover;
  color: rgb(19, 109, 19);
  justify-content: center;
  text-shadow: 3px 3px 4px #6a4e4e;
}
.containerInterior {
  display: flex;
  width: 75vw;
  justify-content: space-between;
}
.left-panel {
  flex: 0 0 30%;
}
.right-panel {
  flex: 0 0 60%;
}
.left-panel ul {
  padding: 0;
}
.left-panel ul li {
  cursor: pointer;
  margin-bottom: 5px;
}
#pgn-viewer-container {
  width: 100%;
  height: 70vh;
}

.cabecera {
  display: flex;
  align-items: center; /* Alinea los elementos verticalmente */
  justify-content: space-between;
}

.cabecera h4 {
  margin-left: 10px; /* Margen a la derecha para separar el título del botón */
  font-weight: bold;
}

.button-container {
  display: flex;
  justify-content: flex-end;
}

.button-container button {
  margin-right: 10px; /* Espacio entre los botones */
}
.btn-outline-danger{
  display: none;
  padding: 0.1rem 0.25rem;
}
td:hover .btn-outline-danger {
  display: inline-block;
}
.alert {
  color: #000;
  text-align: end;
}
#tabla {
  overflow-y: auto; /* Añade un scrollbar vertical cuando sea necesario */
  border-radius: 8px;
  border: 1px solid #366e36;
  margin-left: auto; /* Añade esto */
  margin-right: auto; /* Añade esto */
  max-height: 53vh;
}
.table-fija {
  position: sticky;
  top: 0;
  z-index: 10;
}
th {
  background-color: #8fc196;
}
.tdFlex {
  display: flex;        /* Activa Flexbox */
  justify-content: space-between;  /* Espacia los elementos uniformemente */
  align-items: center;
}
.filtro-container {
  padding: 20px;
  background-color: #dfe2e4;
  border: 1px solid #808387;
  margin-top: 10px;
  margin-left: auto; /* Añade esto */
  margin-right: auto; /* Añade esto */
  width:50%;
  border-radius: 8px;
}
.filtro-container button{
  margin: 5px;
}
@media (max-width: 1800px) {
  .filtro-container {
  padding: 10px;
  background-color: #dfe2e4;
  border: 1px solid #808387;
  margin-top: 10px;
  margin-left: auto; /* Añade esto */
  margin-right: auto; /* Añade esto */
  width:80%;
  border-radius: 8px;
}
.filtro-container button{
  margin: 5px;
}
}

</style>
