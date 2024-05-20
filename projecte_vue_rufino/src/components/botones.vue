<template>
  <div class="botones"><button title="pulse para intentar resolver el ejercico" class="btn btn-outline-success" @click="visualiza_id"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
  <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
</svg><br>Resolver</button>
  <button title="si es su autor puede editar los datos del ejercicio" class="btn btn-outline-success" @click="edita_id" v-if="rolAdmin"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
  <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
</svg><br>Editar</button>
  <button title="si es el ator puede eliminar el ejercicio de la base de datos" style="margin-left: 15px;" class="btn btn-outline-danger" v-if="rolAdmin" @click="delete_id"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg> <br> Borrar</button></div>
  
</template>

<script>
export default {
  name: "_botones",
  data() {
    return {
     rolAdmin:false,//para gestionar la visibilidad en caso de admin/user
    };
  },
  props: ["rowId"],
  methods: {
    visualiza_id() {
      this.$emit("visualiza", this.rowId);// envia la fila al componente padre
    },
    edita_id() {
      this.$emit("edita", this.rowId);
    },
    delete_id() {
      this.$emit("delete", this.rowId);
    },
  },
  mounted(){
    this.rolAdmin = (localStorage.getItem("savedRol" )=== "ROLE_ADMIN");
  }
};
</script>
<style scoped>
.btn {
  font-size: large;
  font-weight: bold;
  font-family: Arial, Helvetica, sans-serif;
  padding: 3px;
}
</style>