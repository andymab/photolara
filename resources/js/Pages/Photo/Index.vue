<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import PhotoList from '@/Components/Photo/Photolist1.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Photo" />
  <AuthenticatedLayout>
    <div class="mb-5">
      <h5 class="text-h5 font-weight-bold">Фото альбомы</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <PhotoList :data="data" />
  </AuthenticatedLayout>
</template>

<script>
export default {
  name: 'PhotoIndex',
  props: {
    data: {
      type: Object,
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
        {
          title: 'Фото альбомы',
          disabled: true,
        },
      ],
    }
  },
  methods: {},
}
</script>
