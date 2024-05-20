<template>
  <TheChessboard :board-config="boardConfig"  />
</template>

<script>
import { TheChessboard } from 'vue3-chessboard';
import 'vue3-chessboard/style.css';


export default {
  components: {
    TheChessboard,
  },
  data(){
   
}
};
</script>