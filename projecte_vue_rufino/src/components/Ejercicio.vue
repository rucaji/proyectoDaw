<template>
  <div class="containerBig">
    <div class="containerInterior">
      <br />
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Autor</th>
            <th>Dificultad</th>
            <th>Tema</th>
            <th>Color</th>
            <th>Enunciado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ ejercicio.autor }}</td>
            <td>{{ ejercicio.dificultad }}</td>
            <td>{{ ejercicio.tema }}</td>
            <td>{{ ejercicio.colorCastellano }}</td>
            <td>{{ ejercicio.enunciado }}</td>
          </tr>
        </tbody>
      </table>

      <div v-if="pos">
        <table style="margin-left: 10%">
          <tr>
            <td>
              <TheChessboard
                v-show="!analisis"
                :board-config="getCustomBoardConfig()"
                @move="movimientoUsuario"
                style="width: 35vw; height: 35vh"
              />
              <div v-show="analisis" id="pgn-viewer-container"></div>
            </td>
            <td class="organizarVertical">
              <center v-if="correcto">
                <h3 class="correcto">Correcto!</h3>
                <p>{{ ejercicio.texto }}</p>
              </center>
              <center v-else-if="!correcto && analisis">
                <h3 class="incorrecto">No solucionado</h3>
              </center>
              <div v-if="!analisis">
                <br />
                <br />
                <br />
              </div>
              <button
                v-if="!analisis"
                style="margin-left: 10vh"
                class="btn btn-outline-success"
                @click="this.AnalizarSolucion()"
                title="ver solución del ejercicio, contará como incorrecto "
              >
                Ver solución
              </button>
              <div class="botones-container">
                <button
                  @click="girarTablero"
                  class="btn btn-outline-success"
                  v-if="analisis"
                >
                  Girar tablero
                </button>
                <button
                  @click="analizarPartida"
                  class="btn btn-outline-success"
                  v-if="analisis"
                >
                  Analizar posición
                </button>
                <button
                  @click="practicarStockfish"
                  class="btn btn-outline-success"
                  v-if="analisis"
                >
                  Practicar contra Stockfish
                </button>
                <button
                  v-if="analisis"
                  class="btn btn-success"
                  @click="siguiente"
                >
                  Siguiente
                </button>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <div v-else>
        <TheChessboard />
      </div>
    </div>
  </div>
</template>
  
<script>
import LichessPgnViewer from "lichess-pgn-viewer";
import "../output.css";
import TheChessboard from "./TheChessboard.vue";

export default {
  name: "_Ejercicio",
  data() {
    return {
      msg: "Llista de ejercicios",
      id: 1,
      ejercicio: {
        id: 1,
        dificultad: "",
        tema: "",
        autor: "",
        fen: "",
        solucion: "",
        texto: "",
        color: "",
        enunciado: "",
        colorCastellano: "",
      },

      pos: false,
      correcto: false,
      positionInfo: "",
      moveDetails: "",
      analisis: false,
      ejerciciosdisponibles: [],
      resueltos: [],
      token: "",
    };
  },
  components: {
    TheChessboard,
  },
  methods: {
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
    siguiente() {
      let siguiente = this.siguienteDisponibleNoResuelto(this.ejercicio.id);
      if (siguiente > 0) {
        window.location = `http://localhost:8080/ejercicio/${siguiente}`;
        setTimeout(() => {
          window.location.reload(true); //necesario para que se actualice el ejercicio en pantalla
        }, 500);
      } else {
        alert("No tienes ejercicios disponibles sin resolver");
        window.location = `http://localhost:8080/ejercicios`;
      }
    },
    siguienteDisponibleNoResuelto(id) {
      // Copia profunda del array de disponibles
      let disponiblesCopia = this.ejerciciosdisponibles;
      let i = disponiblesCopia.indexOf(id) + 1;
      // Comprueba si hay algún elemento en el array de disponibles que no esté en el array de resueltos
      for (i; i < disponiblesCopia.length; i++) {
        // Si encuentra un elemento que no está en el array de resueltos, lo devuelve
        if (!this.resueltos.includes(disponiblesCopia[i])) {
          return disponiblesCopia[i];
        }
      }

      // Si no hay ninguno disponible que no haya sido resuelto, reinicia la búsqueda desde el principio
      for (let j = 0; j < disponiblesCopia.length; j++) {
        // Devuelve el primer elemento que no está en el array de resueltos
        if (!this.resueltos.includes(disponiblesCopia[j])) {
          return disponiblesCopia[j];
        }
      }

      // Si no hay disponibles que no hayan sido resueltos, devuelve -1
      return -1;
    },
    getCustomBoardConfig() {
      let color = "white";
      if (this.ejercicio.color == "black") {
        color = "black";
      }
      return {
        fen: this.ejercicio.fen,
        orientation: color,
        coordinates: true,
      };
    },
    VisualizaEjercicio() {
      this.correcto = false;
      this.VerSolucion();
    },
    extraerMovimientos(pgn) {
      // Eliminar contenido entre corchetes
      pgn = pgn.replace(/\{[^}]*\}/g, "");

      // Eliminar información de FEN y espacios adicionales
      pgn = pgn.replace(/\[[^\]]*\]/g, "").trim();

      // Eliminar el número de movimiento y el punto (incluyendo variante con tres puntos)
      pgn = pgn.replace(/^\d+\.\.\. /, "").replace(/^\d+\. /, "");

      // Extraer el primer movimiento antes del primer espacio
      return pgn.split(" ");
    },
    movimientoUsuario(move) {
      let movimientos = this.extraerMovimientos(this.ejercicio.solucion);
      if (move.san == movimientos[0]) {
        this.correcto = true;
        this.afegirResolt(this.ejercicio.id);
      }
      this.AnalizarSolucion();
      this.VerSolucion();
    },
    AnalizarSolucion() {
      // cambiamos el chessVue por el lichessPgnViewer para poder analizar
      this.analisis = true;
      this.inicializarPgnViewer();
    },
    afegirResolt(id) {
      //para gestionar los realizados por el usuario
      const numbersJSON = localStorage.getItem("numbers");
      // Convertir la cadena JSON de vuelta a un array
      this.resueltos = JSON.parse(numbersJSON) || [];

      this.resueltos.push(id);
      const newnumbersJSON = JSON.stringify(this.resueltos);

      // Guardar la cadena JSON en el almacenamiento local
      localStorage.setItem("numbers", newnumbersJSON);
    },

    inicializarPgnViewer() {
      if (!this.lpv && this.analisis) {
        // Para que el contenedor esté listo y solo inicialízalo una vez
        const container = document.getElementById("pgn-viewer-container");// lo vuelca en este div pero le cambia el nombre RECORDAR!
        this.lpv = LichessPgnViewer(container, {
          pgn: this.ejercicio.solucion,
          fen: this.ejercicio.fen,
          orientation: this.ejercicio.color,
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
      }
    },

    async getEjercicio() {
      try {
        const response = await fetch(
          `http://localhost:8181/api/ejercicios/${this.ejercicio.id}`,
          {
            headers: {
              Authorization: `Bearer ${this.token}`,
              "Content-Type": "application/json",
            },
          }
        );
        this.ejercicio = await response.json();
        this.ejercicio.colorCastellano =
          this.ejercicio.color == "black" ? "negras" : "blancas";
        this.pos = true;
      } catch (error) {
        console.error(error);
      }
    },
    VerSolucion() {
      this.analisis = true;
    },
  },
  async mounted() {
    this.token = this.getToken();
    this.ejercicio.id = this.$route.params.id;
    await this.getEjercicio();
    const cookie = document.cookie
      .split(";")
      .find((cookie) => cookie.trim().startsWith("idsEjercicios="));

    if (cookie) {
      this.ejerciciosdisponibles = JSON.parse(cookie.split("=")[1]);
    }
    const numbersJSON = localStorage.getItem("numbers");

    // Convertir la cadena JSON de vuelta a un array
    this.resueltos = JSON.parse(numbersJSON) || [];
  },
  
};
</script>
  <!-- Importante para el lpv tenga su css -->
  <link rel="stylesheet" href="../output.css"/>
<style scoped>
.containerBig {
  display: flex;
  align-items: stretch; /* Asegura que los ítems llenen el contenedor verticalmente */
  flex-wrap: wrap; /* Permite múltiples líneas si es necesario */
  width: 100vw; /* Asegura que el contenedor tome toda la anchura de la ventana */
  min-height: 100vh; /* Asegura que el contenedor tome toda la altura */
  background-image: url("../assets/estudio_partidas_opacity_50.png");
  background-size: cover;
  color: rgb(19, 109, 19);
  justify-content: center;
}
.containerInterior {
  width: 75vw;
  justify-content: center;
}
.incorrecto{
  color: red;
}
#pgn-viewer-container {
  width: 75%;  /* Tomará el 80% del ancho de su contenedor padre */
  height: 30vh; /* 50% de la altura de la ventana de visualización */
}
.botones-container {
  display: flex;
  flex-direction: column;
}

/* Estilos adicionales para los botones */
.btn {
  margin: 5px; /* Espacio entre los botones */
}
.organizarVertical {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
p {
  margin-left: 15px;
}
</style>