<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import PhotoList from '@/Components/Photo/Photolist.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Photo" />
  <AuthenticatedLayout>
    <div class="mb-5">
      <h5 class="text-h5 font-weight-bold">Фото альбомы</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <PhotoList :data="data" :photo="photo" />
  </AuthenticatedLayout>
</template>

<script>
export default {
  name: 'PhotoIndex',
  props: {
    photo: {
      type: Object,
      default: () => ({}),
    },
    data: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Dashboard',
          disabled: false,
          href: '/dashboard',
        },
      ],
    }
  },
  created() {
    if (Object.keys(this.photo).length == 0) {
      this.breadcrumbs.push({
        title: 'Фото альбомы',
        disabled: true,
      })
    } else {
      this.breadcrumbs.push({
        title: 'Фото альбомы',
        disabled: false,
        href: '/photos',
      })
      this.breadcrumbs.push({
        title: this.photo.title,
        disabled: true,
      })
    }
  },
  methods: {},
}
</script>
