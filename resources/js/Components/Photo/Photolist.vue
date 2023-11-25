<script setup>
import { Link } from '@inertiajs/vue3'
import FilePreviewDialog from './FilePreviewDialog.vue'
import FullImageDialog from './FullImage.vue'
import SlaiderPhoto from './SlaiderPhoto.vue'
</script>
<template>
  <div>
    <v-toolbar density="compact">
      <!-- <v-toolbar-title>Фотоальбомы</v-toolbar-title> -->
      <span v-if="!mobile" class="mr-4"></span>
      <v-responsive class="mx-auto" max-width="344">
        <v-text-field v-model="search" hide-details single-line placeholder="...Найти"></v-text-field>
      </v-responsive>
      <v-spacer v-if="!mobile" />
      <v-tooltip location="top">
        <template #activator="{ props: tooltip }">
          <v-btn icon="mdi-newspaper-plus" v-bind="mergeProps(tooltip)" @click="showFilePreviewDialog({}, null, null)">
          </v-btn>
        </template>
        <span>Новый альбом</span>
      </v-tooltip>
      <v-tooltip location="top">
        <template #activator="{ props: tooltip }">
          <v-btn icon="mdi-image-move" v-bind="mergeProps(tooltip)" @click="sliderShow = !sliderShow"></v-btn>
        </template>
        <span>Slaider</span>
      </v-tooltip>
      <v-switch
        v-if="!mobile"
        v-model="showtooltype"
        hide-details
        color="primary"
        inset
        compact
        label="Показать описания"
      ></v-switch>
    </v-toolbar>

    <FilePreviewDialog
      :activ-item="activItem"
      :photo-id="Object.keys(photo).length > 0 ? photo.id : 0"
      :dialog="showFilePreview"
      type="cover"
      @on-reset="onReset"
    />
    <FullImageDialog :item="activItem" :is-show="isShowFullImage" @close-full-image="CloseFullImage" />

    <SlaiderPhoto :is-show="sliderShow" :images="data" @close-slaider="sliderShow = !sliderShow" />
    <div v-if="selfdata.length !== 0">
      <v-virtual-scroll
        :items="selfdata"
        height="dynamic"
        class="list-item"
        :search="search"
        :loading="isLoadingImages"
        @update:search="loadItems"
      >
        <template #default="{ item, index }">
          <div class="row">
            <div v-for="(img, icol) in item" :key="index + '-' + img" class="image-block">
              <div class="image-content">
                <v-hover v-slot="{ isHovering, props }">
                  <v-card :elevation="isHovering ? 4 : 2" v-bind="props">
                    <div
                      :key="img.id"
                      class="image-item"
                      :class="{ active: isHovering || showtooltype }"
                      v-bind="props"
                    >
                      <v-img
                        :src="img.src_small"
                        lazy-src="/assets/default.jpg"
                        cover
                        class="bg-grey-lighten-2 img-vue"
                        :height="imgheigth"
                      >
                        <template #placeholder>
                          <v-row class="fill-height ma-0 block-loaded" center justify="center">
                            <v-progress-circular indeterminate color="grey-lighten-5"></v-progress-circular>
                          </v-row>
                        </template>

                        <v-toolbar density="compact">
                          <div class="d-flex px-2 image-toolbar">
                            <v-icon
                              icon="mdi-loupe"
                              :data-src="img.src_big ?? img.src_small"
                              :data-title="img.title"
                              :data-descr="img.descr"
                              class="mr-2"
                              @click="showFullImage(img)"
                            ></v-icon>

                            <v-icon
                              icon="mdi-newspaper"
                              class="mr-2"
                              @click="showFilePreviewDialog(img, index, icol)"
                            ></v-icon>

                            <Link v-if="Object.keys(photo).length == 0" :href="'/photos/' + img.id">
                              <v-icon icon="mdi-exit-to-app"> </v-icon>
                            </Link>
                          </div>
                        </v-toolbar>

                        <div class="image-text-block">
                          <h6>{{ img.title }} {{ img.descr }}</h6>
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
    </div>
  </div>
</template>

<script>
import { mergeProps } from 'vue'

//import axios from 'axios'

export default {
  name: 'PhotoList',
  props: {
    photo: {
      type: Object,
      // default: () => ({}),
    },
    data: {
      type: Object,
      default: () => ({}),
    },
  },
  data: () => ({
    imgheigth: 300,
    search: null,
    mobile: false,
    isLoadingImages: false,
    activItem: {},
    activeSrc: '',
    activeTitle: '',
    sliderShow: false,
    isShowFullImage: false,
    showtitle: false,
    items: [],
    selfdata: [],
    selfItemRow: null,
    selfItemCol: null,
    showFilePreview: false,
    showtooltype: false,
  }),
  watch: {
    search: function (val) {
      this.loadItems(val)
    },
  },
  created() {
    // console.log(this.data)
    // console.log(this.selfdata.length)
    if (this.data.length) {
      this.items = this.data
      this.splitChunks(this.data)
    }
  },

  mounted() {
    //console.log(this.photo)
    // console.log('---', this.items);
    // this.loadPhotos();
    // console.log('---', this.items);
  },

  methods: {
    splitChunks: function (data) {
      let chunkSize = 5
      if (window.innerWidth < 520) {
        chunkSize = 1
        this.mobile = true
        this.imgheigth = 440
      }
      const chunks = []

      for (let i = 0; i < data.length; i += chunkSize) {
        const chunk = data.slice(i, i + chunkSize)
        chunks.push(chunk)
      }
      this.selfdata = chunks
    },
    loadItems: function (search) {
      this.isLoadingImages = true
      var params = {}
      if (search) {
        params.search = search
      }
      let url = Object.keys(this.photo).length == 0 ? '/photos' : '/photos/' + this.photo.id
      this.$inertia.get(url, params, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (resp) => {
          this.isLoadingImages = false
          if (resp.props.data.length) {
            this.splitChunks(resp.props.data)
          }
        },
      })
    },
    showFilePreviewDialog(item, irow, icol) {
      this.activItem = item
      this.selfItemRow = irow
      this.selfItemCol = icol

      this.showFilePreview = true
    },
    onReset(data) {
      this.showFilePreview = false
      this.activItem = {}
      if (data.item.src_small) {
        if (this.selfItemRow !== null) {
          this.selfdata[this.selfItemRow][this.selfItemCol] = data.item
        } else {
          this.items.push(data.item)
          this.splitChunks(this.items)
        }
      }
      this.selfItemRow = null
      this.selfItemCol = null
    },
    mergeProps,
    CloseFullImage: function () {
      this.isShowFullImage = false
    },
    showFullImage: function (item) {
      this.activItem = item
      this.isShowFullImage = true
    },
    // loadPhotos: function () {
    //   const self = this
    //   let formData = new FormData()
    //   formData.append('photo', JSON.stringify({ action: 'getphotos' }))

    //   axios
    //     .post('/Photos', formData)
    //     .then(function (responce) {
    //       const $items = responce.data
    //       console.log($items)
    //       self.items = $items.data
    //       console.log('SUCCESS!!')
    //     })
    //     .catch(function (error) {
    //       console.log(error)
    //     })
    // },
  },
}
</script>

<style>
.block-loaded {
  align-items: center;
}

/* .v-virtual-scroll__container {
  display: flex;
} */

.v-virtual-scroll__item {
  /* flex: 25%; */
  /* max-width: 25%; */
}

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
  padding-top: 8px;
  padding-bottom: 8px;
}

.image-item.active .image-toolbar {
  background-color: rgb(83, 83, 83);
  opacity: 0.8;
}

.v-theme--light .image-item .v-toolbar__content {
  align-items: start;
}

/* .v-theme--light .image-item.active header {
  background-color: rgb(83, 83, 83);
  opacity: 0.5;
} */

.row {
  display: flex;
  /* flex-wrap: wrap; */
  padding: 0 4px;
}

.image-block {
  position: relative;
  flex: 20%;
  max-width: 20%;
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

.list-item > .v-virtual-scroll__container {
  flex-wrap: wrap;
  padding: 0 4px;
}
</style>
