<template>
  <div>
    <nav class="bg-white shadow-lg mb-8">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <a href="/" class="text-2xl font-bold text-blue-600">
            Real Estate
          </a>
        </div>
      </div>
    </nav>

    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold text-red-500">Vue is Working!</h1>
      <PropertyList />
    </div>

    <router-view></router-view>
  </div>
</template>

<script>
import PropertyList from './components/properties/PropertyList.vue'

export default {
  name: 'App',
  components: {
    PropertyList
  }
}
</script> 