<script setup>
import FilePreviewDialog from './FilePreviewDialog.vue'
</script>
<template>
  <v-toolbar density="compact">
    <!-- <v-toolbar-title>Фотоальбомы</v-toolbar-title> -->
    <v-text-field hide-details prepend-icon="mdi-magnify" single-line placeholder="...Найти"></v-text-field>
    <v-spacer />
    <v-tooltip location="top">
      <template #activator="{ props: tooltip }">
        <v-btn
          icon="mdi-newspaper-plus"
          v-bind="mergeProps(tooltip)"
          @click="showFilePreview = !showFilePreview"
        ></v-btn>
      </template>
      <span>Новый альбом</span>
    </v-tooltip>

    <v-switch v-model="showtooltype" hide-details inset compact label="Показать описания"></v-switch>
  </v-toolbar>
  <FilePreviewDialog :id="items.length" :dialog="showFilePreview" :photo_id="0" @on-reset="showFilePreview = false" />
  <v-virtual-scroll :items="data" height="dynamic">
    <template #default="{ item, index }">
      <div class="row">
        <div v-for="n in item" :key="index + '-' + n" class="image-block">
          <div class="image-content">
            <v-hover v-slot="{ isHovering, props }">
              <v-card :elevation="isHovering ? 4 : 2" v-bind="props">
                <div
                  :key="index + '-image-item-' + n"
                  class="image-item"
                  :class="{ active: isHovering || showtooltype }"
                  v-bind="props"
                >
                  <v-img :src="n.src_tmb" :lazy-src="n.src_big" cover class="bg-grey-lighten-2 img-vue" height="220">
                    <template #placeholder>
                      <v-row class="fill-height ma-0" center justify="center">
                        <v-progress-circular indeterminate color="grey-lighten-5"></v-progress-circular>
                      </v-row>
                    </template>

                    <v-toolbar density="compact">
                      <div class="d-flex px-2 image-toolbar">
                        <v-icon
                          icon="mdi-loupe"
                          :data-src="n.src_big"
                          :data-title="n.title"
                          :data-descr="n.descr"
                          class="mr-2"
                          @click="showFullImage"
                        ></v-icon>
                        <v-icon
                          icon="mdi-newspaper-plus"
                          class="mr-2"
                          @click="showFilePreviewDialog(n.src_tmb)"
                        ></v-icon>
                        <!-- <RouterLink to="/images/1"> -->
                        <v-icon icon="mdi-exit-to-app"></v-icon>
                        <!-- </RouterLink> -->
                      </div>
                    </v-toolbar>

                    <div class="image-text-block">
                      <h6>{{ n.title }} {{ n.descr }}</h6>
                    </div>
                  </v-img>
                </div>
              </v-card>
            </v-hover>
          </div>
        </div>
      </div>
    </template>
  </v-virtual-scroll>
</template>

<script>
import { mergeProps } from 'vue'
import axios from 'axios'
//import FullImage from './FullImage.vue'

export default {
  name: 'PhotoList',
  props: {
    data: {
      type: Object,
    },
  },
  data: () => ({
    activeSrc: '',
    activeTitle: '',
    FullImage: false,
    showtitle: false,
    items: [],
    showFilePreview: false,
    showtooltype: false,
  }),
  created() {},
  mounted() {
    // console.log('---', this.items);
    // this.loadPhotos();
    // console.log('---', this.items);
  },
  methods: {
    mergeProps,
    CloseFullImage: function () {
      this.FullImage = false
    },
    showFullImage: function (e) {
      this.activeSrc = e.target.dataset.src
      this.activeTitle = e.target.dataset.title
      this.FullImage = true
    },
    loadPhotos: function () {
      const self = this
      let formData = new FormData()
      formData.append('photo', JSON.stringify({ action: 'getphotos' }))

      axios
        .post('/Photos', formData)
        .then(function (responce) {
          const $items = responce.data
          console.log($items)
          self.items = $items.data
          console.log('SUCCESS!!')
        })
        .catch(function (error) {
          console.log(error)
        })
    },
  },
}
</script>

<style>
.v-theme--light .image-item header {
  background-color: transparent;
  color: floralwhite;
}

.v-theme--light .image-item header a {
  color: floralwhite;
}

.image-toolbar {
  opacity: 0;
  transition: opacity 0.5s;

  align-items: center;
  width: 100%;
  justify-content: end;
}

.image-item.active .image-toolbar {
  opacity: 1;
}

.row {
  display: flex;
  flex-wrap: wrap;
  padding: 0 4px;
}

.image-block {
  position: relative;
  flex: 25%;
  max-width: 25%;
  padding: 0 4px;
  text-align: center;
}

.image-content {
  display: block;
  position: relative;
  padding: 4px 0;
}

.image-item {
  vertical-align: middle;
  width: 100%;
}

.image-block.active .image-text-block {
  display: block !important;
}

/* .image-block {
  
  } */
/* .image-block:hover .image-text-block {
    opacity: 0.7;
  } */

.image-text-block {
  width: 100%;
  font-family: Roboto;
  text-align: center;
  color: white;
  opacity: 0.7;
  background-color: rgb(19, 18, 18);
  position: absolute;
  bottom: 0;
  z-index: 2;
  font-size: 24px;
  opacity: 0;
  transition: opacity 0.5s;
}

.image-item.active .image-text-block {
  opacity: 0.6;
}

.image-item {
  opacity: 1;
  transition: all 0.5s;
  cursor: pointer;
}

.image-item:hover {
  opacity: 0.8;
}

/* Responsive layout - makes a two column-layout instead of four columns */
@media screen and (max-width: 800px) {
  .image-block {
    flex: 50%;
    max-width: 50%;
  }
}

/* Responsive layout - makes the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .image-block {
    flex: 100%;
    max-width: 100%;
  }
}
</style>
