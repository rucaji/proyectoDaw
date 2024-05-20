<template>
  <Menu />
  <router-view/>
<footerProyecto/>
</template>

<script>
import Menu from './components/Menu.vue';
import footerProyecto from './components/footerProyecto.vue';
export default {
  components: {
    Menu,footerProyecto
  },
};
</script>


<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

nav {
  padding: 15px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}
</style>
