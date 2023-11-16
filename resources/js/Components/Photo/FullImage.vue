<template>
  <v-dialog v-model="dialogShow" fullscreen :scrim="false" transition="dialog-bottom-transition">
    <div class="img-block-full">
      <v-img :src="item.src_big ?? '/assets/default.jpg'" contain class="bg-grey-darken-4 img-full">
        <template #placeholder>
          <v-row class="fill-height ma-0" align="center" justify="center">
            <v-progress-circular indeterminate color="black"></v-progress-circular>
          </v-row>
        </template>
        <div class="image-text-block">
          <h6>
            {{ item.title }} <small>{{ item.descr }}</small>
          </h6>
        </div>

        <v-toolbar density="compact" class="grey-darken-4 image-tools">
          <div class="d-flex px-2 image-full-toolbar">
            <v-icon icon="$close" size="large" class="mr-2" @click="$emit('CloseFullImage')"></v-icon>
          </div>
        </v-toolbar>
      </v-img>
    </div>
  </v-dialog>
</template>

<script>
export default {
  props: {
    isShow: {
      type: Boolean,
      default: false,
    },
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['CloseFullImage'],
  data: () => ({
    dialogShow: false,
  }),
  watch: {
    isShow: function (val) {
      this.dialogShow = val
    },
  },
  methods: {},
}
</script>

<style scope>
html {
  overflow-y: auto;
}

.img-full {
  position: absolute;
  top: 0;
  width: 100vw;
  height: 100vh;
  /* z-index: 20000; */
  overflow: hidden;
}

.image-tools {
  top: 0;
}

.img-block-full header.v-theme--light {
  background-color: transparent;
  color: floralwhite;
}
.v-theme--light .image-full-toolbar {
  color: floralwhite;
}

.image-full-toolbar {
  opacity: 1;
  transition: opacity 0.5s;

  align-items: center;
  width: 100%;
  justify-content: end;
}
</style>
