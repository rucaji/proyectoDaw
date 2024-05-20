<template>
  <div class="containerBig">
    <br />
    <h1>Formulario para Editar Ejercicios de ajedrez</h1>
    <br />
    <h3>Autor del ejercicio: {{ nouElement.autor }}</h3>
    
    <div class="container mt-3">
      <form v-if="!añadido && !ejercicioErrorServidor" @submit.prevent="afegirElement">
        <div class="form-group">
        </div>
        <div class="form-group radio-group">
          <label>Dificultad:</label>
          <div class="radio-options">
            <div>
              <input
                type="radio"
                id="baja"
                value="baja"
                v-model="nouElement.dificultad"
              />
              <label for="baja">Baja</label>
            </div>
            <div>
              <input
                type="radio"
                id="intermedia"
                value="intermedia"
                v-model="nouElement.dificultad"
              />
              <label for="intermedia">Intermedia</label>
            </div>
            <div>
              <input
                type="radio"
                id="alta"
                value="alta"
                v-model="nouElement.dificultad"
              />
              <label for="alta">Alta</label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="tema">Tema del ejercicio:</label>
          <select
            v-model="nouElement.tema"
            id="tema"
            class="form-control"
            :class="{ 'is-invalid': procesando && temaInvalido }"
          >
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
            <option value="Desvio">Desvio</option>
            <option value="Encaminamiento">Encaminamiento</option>
            <option value="Intercepcion">Intercepcion</option>
            <option value="Acorralamiento">Acorralamiento</option>
            <option value="Eliminacion de la defensa">
              Eliminacion de la defensa
            </option>
            <option value="Sobrecarga">Sobrecarga</option>
            <option value="Combinación de elementos">
              Combinación de elementos
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="fen">Codigo FEN de la posicion: </label>
          <a href="https://lichess.org/editor" target="_blank" title="le conduce al editor de posiciones de lichess "
            >(obten el FEN)</a
          >
          <input
            v-model="nouElement.fen"
            type="text"
            class="form-control"
            id="fen"
            placeholder="pega del enlace el código fen"
            :class="{ 'is-invalid': procesando && fenInvalido }"
            @focus="resetEstado"
            @input="updatePgnLink"
          />
        </div>
        <div class="form-group">
          <label>Bando al que le toca mover:</label>
          <div class="radio-options">
            <div>
              <input
                type="radio"
                id="white"
                value="white"
                v-model="nouElement.color"
              />
              <label for="white">Blancas</label>
            </div>
            <div>
              <input
                type="radio"
                id="black"
                value="black"
                v-model="nouElement.color"
              />
              <label for="black">Negras</label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="enunciado">Enunciado del ejercicio:</label>
          <input
            v-model="nouElement.enunciado"
            class="form-control"
            id="enunciado"
            placeholder="Introduce el enunciado"
            :class="{ 'is-invalid': procesando && enunciadoInvalido }"
            @focus="resetEstado"
            @input="saneaEnunciado"
            title="Caracteres prohibidos <>&/\'etc "
          />
        </div>
        <div class="form-group">
          <label for="solucion">Solucion del ejercicio:</label>
          <a :href="enlacePgn" target="_blank" title="le conduce al tablero de análisis de lichess con la posición , para poder introducir las judas y copiarlas ">obten PGN solucion</a>
          <input
            v-model="nouElement.solucion"
            type="text"
            class="form-control"
            id="solucion"
            placeholder="pega del enlace el pgn con la solución"
            :class="{ 'is-invalid': procesando && solucionInvalido }"
            @focus="resetEstado"
            @input="corregirSolucion"
          />
        </div>
        <div class="form-group">
          <label for="texto">Texto explicativo del ejercicio (opcional) :</label>
          <textarea
            v-model="nouElement.texto"
            class="form-control"
            id="texto"
            placeholder="Introduce la explicacion"
            :class="{ 'is-invalid': procesando && textoInvalido }"
            @focus="resetEstado"
            @input="saneaTextoExplicativo"
            title="Caracteres prohibidos <>&/\'etc "
          ></textarea>
        </div>
        <br />
        <button type="submit" class="btn btn-success" title="añadir el ejercicio a la base de datos ">
          Actualizar ejercicio
        </button>
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div
                v-if="error && procesando"
                class="alert alert-danger"
                role="alert"
              >
                Debes rellenar todos los campos correctamente!
              </div>
            </div>
          </div>
        </div>
      </form>
      <div class="alert alert-success" role="alert" v-if="añadido">
        Ejercicio actualizado correctamente
      </div>
      <div class="alert alert-danger" role="alert" v-if="ejercicioErrorServidor">
        No se ha podido actualizar el ejercicio, pruebe de nuevo
      </div>
    </div>
  </div>
</template>
  
<script>
import { Chess } from "chess.js";//para enviar la posicion al lichess  de analisis
export default {
  name: "EditaEjercicio",
  data() {
    return {
      nouElement: {
        id: 999999,
        dificultad: "",
        tema: "",
        autor: "",
        fen: "",
        solucion: "",
        texto: "",
        color: "",
        enunciado: "",
      },
      chess: new Chess(),
      enlacePgn: "",
      añadido: false,
      ejercicioErrorServidor: false,
      isFENValid: false,
      isPgnValid: false,
      procesando: false,
      correcto: false,
      error: false,
      token:"",
    };
  },

  methods: {
    saneaTextoExplicativo(){
      this.nouElement.texto = this.sanitizarDatos(this.nouElement.texto);
    },
    saneaEnunciado(){
      this.nouElement.enunciado = this.sanitizarDatos(this.nouElement.enunciado);
    },
    sanitizarDatos(datos){
      return datos.replace(/[<>&/"']/g, '');//eliminar caracteres peligrosos
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
    extraerMovimientos(pgn) {
      // Eliminamos contenido entre corchetes
      pgn = pgn.replace(/\{[^}]*\}/g, "");

      // Eliminamos información de FEN y espacios adicionales
      pgn = pgn.replace(/\[[^\]]*\]/g, "").trim();

      return pgn;
    },
    updatePgnLink() {
      this.enlacePgn = `https://lichess.org/analysis/${this.nouElement.fen}?color=${this.nouElement.color}`;
    },
    checkPgnValidity(cadena) {
      // eliminamos parentesis y su contenido 
      let cadenaPrueba = cadena;
      cadenaPrueba = cadenaPrueba.replace(/\([^()]*\)/g, '');
      cadenaPrueba = cadenaPrueba.replace(/[+#x=]/g, '');
    // Divide la cadena en partes basadas en espacios
    const partes = cadenaPrueba.trim().split(/\s+/);

    // Expresión regular que verifica las secuencias permitidas
    const regex = /^(?:[a-zA-Z]{1,2}\d|\d{1,2}\.|(?:\d{1,2})\.{3}|0-0|0-0-0|[a-zA-Z]{2}\d[a-zA-Z]|[a-zA-Z]\d[a-zA-Z])$/;

    // Verifica cada parte con la expresión regular
    this.isPgnValid = !partes.every(parte => regex.test(parte)) ;
},
    corregirSolucion() {
      this.nouElement.solucion = this.extraerMovimientos(
        this.nouElement.solucion
      );
    },
    afegirElement() {

      this.procesando = true;
      this.resetEstado();
      if (
        this.temaInvalido ||
        this.textoInvalido ||
        this.enunciadoInvalido ||
        this.solucionInvalido ||
        this.colorInvalido ||
        this.dificultadInvalido ||
        this.fenInvalido
      ) {
        this.error = true;
        return;
      } 

      this.nouElement.id = this.id;
      this.putEjercicio(this.nouElement);
      
      this.error = false;
      this.correcto = true;
      this.procesando = false;
      // Reiniciamos los datos del formulario
      this.nouElement = {
        id: 999999,
        dificultad: "",
        tema: "",
        autor: "",
        fen: "",
        solucion: "",
        texto: "",
        color: "",
        enunciado: "",
      };
    },
    resetEstado() {
      this.correcto = false;
      this.error = false;
    },
    //Para insertar ejercicio en la base
    async putEjercicio(ejercicio) {
      try {
        const response = await fetch(
          `http://localhost:8181/api/ejercicios/${this.id}`,
          {
            method: "PUT",
            body: JSON.stringify(ejercicio),
            headers: { Authorization: `Bearer ${this.token}`, "Content-type": "application/ld+json; charset=UTF-8" },
          }
        );
        if (response.ok) {
          this.añadido = true;
        } else {
          this.ejercicioErrorServidor = true; 
        }
        await response.json();
       
      } catch (error) {
        console.error(error);
        this.ejercicioErrorServidor = true;
      }
    },
    async getEjercicio() {
      try {
        const response = await fetch(
          `http://localhost:8181/api/ejercicios/${this.id}`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
            "Content-Type": "application/json",
          },
        }
        );
        this.nouElement = await response.json();
        this.enlacePgn = `https://lichess.org/analysis/${this.nouElement.fen}?color=${this.nouElement.color}`;
        this.chess.load(this.nouElement.fen);
      } catch (error) {
        console.error(error);
      }
    },
  },
  computed: {

    solucionInvalido() {
      this.checkPgnValidity(this.nouElement.solucion);
      return this.isPgnValid;
    },
    textoInvalido() {
      return this.nouElement.texto.length < 0;//si queremos que sea obligado pondré otro, ahora no hace nada
    },
    enunciadoInvalido() {
      return this.nouElement.enunciado.length < 1;
    },
    dificultadInvalido() {
      return !this.nouElement.dificultad;
    },
    temaInvalido() {
      return this.nouElement.tema === "";
    },
    colorInvalido() {
      return !this.nouElement.color;
    },

    fenInvalido() {
      const parts = this.nouElement.fen.split(" ");
      if (parts.length !== 6) return true;

      // Validación de la disposición de las piezas
      const rows = parts[0].split("/");
      if (rows.length !== 8) return true;

      // Validación del turno de juego
      if (!["w", "b"].includes(parts[1])) return true;

      return false;
    },
  },
  
  mounted() {
    this.token = this.getToken();
    this.id = this.$route.params.id;
    this.getEjercicio();
  },
};
</script>
 
<style scoped>
.form-group {
  display: flex;
  justify-content: space-between; /* Alinea los elementos a los extremos del contenedor */
  align-items: center;
  padding: 10px;
  
}
h1, h3{
  color: rgb(24, 86, 24);
  text-shadow: 3px 3px 4px #363131;
}
a {
  color: rgb(24, 86, 24);
}
.containerBig {
  background-image: url("../assets/estudio_partidas_opacity_50.png");
  background-size: cover;
  height: 100vh;
}
form {
  background-color: rgb(228, 223, 223);
  padding-bottom: 20px;
  border: 1px solid #165a24;
  border-radius: 9px;
}
input,
select,
textarea {
  max-width: 300px; /* Los elementos no serán más anchos que esto */
  width: 100%; /* Se expanden hasta el ancho máximo dentro de su contenedor */
  display: flex;
}
.container {
  width: 50%;
  
}

.radio-options {
  display: flex;
  gap: 20px; /* Espacio entre las opciones */
  align-items: center; /* Alinea verticalmente las etiquetas con los botones */
}

.radio-options div {
  display: flex;
  align-items: center;
}
.is-invalid {
  border: 1px solid red;
  padding: 2px;
  border-radius: 5px;
}
::placeholder {
  color: rgba(0, 0, 0, 0.4); /* Gris más claro */
}
</style>