<template>
  <div class="container mx-auto px-4">
    <!-- Debug info -->
    <div class="mb-4 p-2 bg-gray-100">
      Current View: {{ currentView }} | 
      Properties Count: {{ properties.length }}
    </div>

    <!-- Tab Navigation -->
    <div class="flex space-x-4 mb-4">
      <button 
        v-for="view in views" 
        :key="view.id"
        @click="currentView = view.id"
        :class="[
          'px-4 py-2 rounded',
          currentView === view.id 
            ? 'bg-blue-500 text-white' 
            : 'bg-gray-200 hover:bg-gray-300'
        ]"
      >
        {{ view.name }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-4">
      Loading properties...
    </div>

    <!-- Error State -->
    <div v-if="error" class="text-red-500 py-4">
      {{ error }}
    </div>

    <!-- Content Area -->
    <div v-if="!loading" class="mt-4">
      <!-- Map View -->
      <div v-if="currentView === 'map'" class="h-[600px] border rounded-lg">
        <MapView 
          v-if="properties.length > 0"
          :properties="properties"
        />
      </div>

      <!-- List View -->
      <div v-if="currentView === 'list'" class="min-h-[600px]">
        <PropertyList 
          v-if="properties.length > 0"
          :properties="properties"
        />
      </div>
    </div>
  </div>
</template>

<script>
import MapView from './MapView.vue'
import PropertyList from './PropertyList.vue'

export default {
  components: {
    MapView,
    PropertyList
  },
  data() {
    return {
      currentView: 'map',
      views: [
        { id: 'map', name: 'Map View' },
        { id: 'list', name: 'List View' }
      ],
      properties: [],
      loading: true,
      error: null
    }
  },
  async created() {
    console.log('PropertyContainer created');
    await this.fetchProperties();
  },
  methods: {
    async fetchProperties() {
      try {
        console.log('Fetching properties...');
        this.loading = true;
        this.error = null;

        const response = await axios.get('/api/properties');
        console.log('Properties response:', response.data);

        if (Array.isArray(response.data)) {
          this.properties = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.properties = response.data.data;
        } else {
          throw new Error('Invalid properties data format');
        }

        console.log('Properties loaded:', this.properties.length);
      } catch (error) {
        console.error('Error fetching properties:', error);
        this.error = 'Failed to load properties. Please try again.';
      } finally {
        this.loading = false;
      }
    }
  },
  watch: {
    currentView(newView) {
      console.log('View changed to:', newView);
    },
    properties: {
      handler(newProps) {
        console.log('Properties updated:', newProps.length);
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.container {
  min-height: calc(100vh - 4rem); /* Adjust based on your navbar height */
}
</style> 