<script setup>
import ImageCropperDialog from './ImageCropperDialog.vue'
</script>

<template>
  <v-dialog v-model="showPreview" width="auto" persistent>
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span> File Upload Preview.</span>

        <v-btn icon="$close" @click="oncloseDialog"> </v-btn>
      </v-card-title>
      <v-card-text>
        <div class="d-flex flex-column justify-center align-center fill-height">
          <input ref="filePickerField" type="file" accept="image/*" hidden @change="launchCropper" />

          <div style="height: 350px; width: 350px; border: 2px solid black">
            <v-img :src="avatarImage" class="avatar-image"></v-img>
          </div>
          <v-container fluid class="px-0">
            <v-select
              v-if="activItem.id"
              v-model="selectType"
              label="Выберите формат"
              :items="selectItems"
              item-title="type"
              item-value="abbr"
              density="compact"
              return-object
            ></v-select>
            <v-text-field v-model="formPhoto.title" clearable label="Наименование"></v-text-field>
            <v-textarea
              ref="descr"
              v-model="formPhoto.descr"
              label="Описание"
              auto-grow
              :rules="rules"
              rows="2"
            ></v-textarea>

            <v-btn class="mr-2" @click="$refs.filePickerField.click()"> Загрузить </v-btn>
            <v-btn class="" @click="submitFile()">
              <div class="text-center" width="100%">
                <span class="mr-4">Save php</span
                ><v-progress-circular v-if="loading" indeterminate color="primary"></v-progress-circular>
              </div>
            </v-btn>
          </v-container>
          <ImageCropperDialog
            :chosen-image="chosenImage"
            :curfile="curfile"
            :a-ratio="selectType"
            @on-reset="onReset"
            @on-crop="
              (croppedImage) => {
                avatarImage = croppedImage
              }
            "
          />
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FilePreviewDialog',
  props: {
    activItem: {
      type: Object,
      default: () => ({}),
    },
    photoId: {
      type: Number,
      default: () => 0,
    },
    dialog: {
      type: Boolean,
      default: () => false,
    },
    type: {
      type: String,
      default: () => 'cover',
    },
  },
  emits: ['onReset'],
  data() {
    return {
      loading: false,
      selectType: { type: '4x3 Показ в группе', abbr: 'square' },
      selectItems: [
        { type: '4x3 Показ в группе', abbr: 'square' },
        { type: '16x9 Показ для полного экрана', abbr: 'rectangle' },
      ],
      formPhoto: {
        id: '',
        title: '',
        descr: '',
      },
      titleImage: '',
      descrImage: '',
      showPreview: false,
      imagePreview: '',
      avatarImage: '/assets/default.jpg',
      chosenImage: null,
      curfile: null,
      item: {},

      rules: [(v) => v.length <= 25 || 'Max 25 characters'],
    }
  },
  watch: {
    dialog: function (val) {
      this.showPreview = val
    },
    // showPreview: function (val) {
    //   if (val === false) {
    //     this.oncloseDialog({})
    //   }
    // },
    activItem: function () {
      this.item = this.activItem
      if (this.activItem.src_small != undefined) {
        this.avatarImage = this.activItem.src_small
        this.formPhoto.id = this.activItem.id
        this.formPhoto.title = this.activItem.title
        this.formPhoto.descr = this.activItem.descr
      } else {
        this.selectType = { type: '4x3 Показ в группе', abbr: 'square' }
        this.avatarImage = '/assets/default.jpg'
        this.formPhoto.id = 0
        this.formPhoto.title = ''
        this.formPhoto.descr = ''
      }
    },
  },
  created() {
    this.formPhoto.id = this.id ?? 0
  },
  mounted() {},
  methods: {
    // async uploadBlob(base64) {
    //   var link = document.createElement('a')
    //   document.body.appendChild(link) // for Firefox
    //   link.setAttribute('href', base64)
    //   link.setAttribute('download', 'download.jpg')
    //   link.click()
    // },
    onReset: function () {
      this.$refs.filePickerField.value = null
    },
    async launchCropper(event) {
      if (!event) return

      this.chosenImage = '/assets/default.jpg' //именно на смене картики стоит whach если одна и таже то не работало
      var file = event.target.files[0]
      this.curfile = file
      this.chosenImage = await this.toBase64(file)
    },
    async toBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = () => resolve(reader.result)
        reader.onerror = (error) => reject(error)
      })
    },

    handleFileUpload() {
      this.file = this.$refs.file.files[0]
      let reader = new FileReader()
      reader.addEventListener(
        'load',
        function () {
          this.showPreview = true
          this.imagePreview = reader.result
        }.bind(this),
        false,
      )
      if (this.file) {
        if (/\.(jpe?g|png|gif)$/i.test(this.file.name)) {
          reader.readAsDataURL(this.file)
        }
      }
    },
    submitFile() {
      this.loading = true
      const self = this
      let formData = new FormData()
      formData.append('type_photos', self.type)
      formData.append('type_image', self.selectType.abbr)
      formData.append('data', JSON.stringify(self.formPhoto))
      formData.append('base64', self.avatarImage)
      let link = '/photos'
      if (self.photoId) {
        link = '/photos/' + self.photoId
      }
      axios
        .post(link, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        .then(function (responce) {
          //console.log(responce.data)
          console.log('SUCCESS!!')
          self.loading = false
          self.oncloseDialog(responce.data)
        })
      // .catch(function () {
      //   console.log('FAILURE!!')
      // })
    },
    oncloseDialog(data) {
      const self = this
      self.avatarImage = null //self.srcpreview
      self.$refs.filePickerField.value = null
      self.$emit('onReset', { item: data })
    },
  },
}
</script>

<style scope>
.container img {
  max-width: 200px;
  max-height: 200px;
}
</style>
