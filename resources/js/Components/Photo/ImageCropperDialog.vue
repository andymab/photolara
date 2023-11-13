<script setup>
import VueCropper from 'vue-cropperjs'
</script>
<template>
  <div class="crop-image-dialog">
    <v-dialog v-model="showCropper" max-width="500" persistent>
      <v-card class="pt-6 pb-3">
        <v-card-title>
          <v-toolbar density="compact" class="image-tools">
            <div class="d-flex justify-space-around">
              <v-btn size="small" variant="text" @click="$refs.cropper.setAspectRatio(1.77777)">16/9</v-btn>
              <v-btn size="small" variant="text" @click="$refs.cropper.setAspectRatio(1.33333)">4/3</v-btn>
            </div>
          </v-toolbar>
        </v-card-title>
        <v-card-text class="pb-3">
          <VueCropper
            ref="cropper"
            class="image-container"
            :aspect-ratio="16 / 9"
            :guides="true"
            :background="false"
            :view-mode="3"
            drag-mode="move"
            :src="chosenImage"
            alt="Image not available"
          >
          </VueCropper>
        </v-card-text>
        <v-card-actions class="py-0 mx-10">
          <v-btn text color="red" @click="resetCropper"> Cancel </v-btn>
          <v-spacer></v-spacer>
          <v-btn text color="blue" @click="cropChosenImage"> Crop </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import 'cropperjs/dist/cropper.css'

export default {
  name: 'ImageCropperDialog',
  props: {
    chosenImage: {
      type: String,
      default: null,
    },
  },
  emits: ['onReset', 'onCrop'],
  data() {
    return {
      ARatio: '16 / 9',
      showCropper: false,
      imageFileType: null,
    }
  },
  watch: {
    chosenImage: function () {
      this.initCropper(this.chosenImage.type)
    },
  },
  methods: {
    async initCropper(imageFileType) {
      this.showCropper = true
      this.imageFileType = imageFileType
      await new Promise((resolve) => setTimeout(resolve, 50))
      this.$refs.cropper.replace(this.chosenImage)
    },

    async resetCropper() {
      this.$emit('onReset')
      this.showCropper = false
    },

    async cropChosenImage() {
      this.$emit('onCrop', this.$refs.cropper.getCroppedCanvas().toDataURL(this.imageFileType))
      this.resetCropper()
    },

    async rotate90Cropper() {
      this.$refs.cropper.rotate(-90)
    },
  },
}
</script>

<style>
.image-container {
  max-width: 450px;
}

.image-container img {
  /* This is important */
  width: 100%;
}
</style>
