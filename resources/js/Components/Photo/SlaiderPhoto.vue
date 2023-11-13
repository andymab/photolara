<template>
  <div class="grey-darken-2">
    <v-toolbar density="compact" class=" slider-toolbar">
                <div class="d-flex px-2 image-full-toolbar">
                    <v-icon icon="$close" size="large" class="mr-2" @click="CloseSlaider"></v-icon>
                </div>
            </v-toolbar>
    <v-carousel
      cycle
      height="100vh"
      hide-delimiter-background
      show-arrows="hover"
      interval="10000"
    >
    <v-carousel-item
      v-for="(item,i) in images"
      :key="i"
      :src="item.src_big"
      contain
    ></v-carousel-item>

    </v-carousel>
  </div>
  </template>

<script>


export default {
    props: {
        images: {
            type: Array,
            default:()=>([])
        },
        curitem:{
            type:String,
            default: () => '/public/default.jpg'
        }
    },

  data: () => ({  }),

  methods: {
    CloseSlaider:function(){
      return this.$emit('CloseSlaider');
    }
  }
}
</script>

<style scoped>
.slider-toolbar{
  position: absolute;
  background-color: transparent;
  top: 0;
  z-index: 1;
}

.grey-darken-2{
  background-color: #616161;
}

</style>