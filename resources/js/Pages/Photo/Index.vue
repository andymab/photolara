<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
//import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Photo" />
  <AuthenticatedLayout>
    <div class="mb-5">
      <h5 class="text-h5 font-weight-bold">Фотоальбомы</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
  </AuthenticatedLayout>
</template>

<script>
export default {
  name: 'PeopleIndex',
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
          title: 'Фотоальбомы',
          disabled: true,
        },
      ],
    }
  },
  methods: {},
}
</script>
