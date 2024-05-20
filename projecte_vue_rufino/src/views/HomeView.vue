<template>
  <div  class="fondo">
    <br /><br /><br />
    <div class="home">
      <img alt="Vue logo" src="../assets/logo.png" />
    </div>
    <Inicio />
  </div>
</template>

<script>
import Inicio from "../components/Inicio.vue";
export default {
  name: "HomeView",
  components: {
    Inicio,
  },
};
</script>
<style scoped>
.fondo {
  width: 100vw; /* Asegura que el contenedor tome toda la anchura de la ventana */
  min-height: 100vh; /* Asegura que el contenedor tome toda la altura */
  background-image: url("../assets/estudio_partidas_opacity_50.png");
  background-size: cover;
  color: rgb(28, 104, 28);
  text-shadow: 3px 3px 4px #2c2b2b;
}
</style>