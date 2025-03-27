<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Search and Filter Section -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Search properties..." 
          class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
        <select 
          v-model="filters.priceRange" 
          class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Prices</option>
          <option value="0-200000">Under $200,000</option>
          <option value="200000-500000">$200,000 - $500,000</option>
          <option value="500000-1000000">$500,000 - $1,000,000</option>
          <option value="1000000+">Over $1,000,000</option>
        </select>
        <select 
          v-model="filters.category" 
          class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Categories</option>
          <option value="2">Apartments</option>
          <option value="3">Houses</option>
          <option value="6">Offices</option>
          <option value="7">Retail</option>
        </select>
      </div>
      
      <!-- View Toggle -->
      <div class="flex justify-end mt-4">
        <button 
          @click="viewMode = 'grid'"
          :class="['px-4 py-2 rounded-l', viewMode === 'grid' ? 'bg-blue-500 text-white' : 'bg-gray-200']"
        >
          <i class="fas fa-th-large mr-2"></i> Grid
        </button>
        <button 
          @click="viewMode = 'map'"
          :class="['px-4 py-2 rounded-r', viewMode === 'map' ? 'bg-blue-500 text-white' : 'bg-gray-200']"
        >
          <i class="fas fa-map-marker-alt mr-2"></i> Map
        </button>
      </div>
    </div>

    <!-- View Modes -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="property in filteredProperties" 
           :key="property.id" 
           class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
      >
        <!-- Property Image -->
        <div class="relative h-48 bg-gray-200">
          <img 
            :src="property.images ? property.images[0] : 'https://via.placeholder.com/400x300'" 
            class="w-full h-full object-cover"
            alt="Property Image"
          >
          <div class="absolute top-0 right-0 m-2">
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
              {{ property.status }}
            </span>
          </div>
        </div>

        <!-- Property Details -->
        <div class="p-6">
          <h3 class="text-xl font-bold mb-2 text-gray-800">{{ property.title }}</h3>
          <p class="text-gray-600 mb-4">
            <i class="fas fa-map-marker-alt mr-2"></i>
            {{ property.address }}
          </p>
          
          <!-- Property Features -->
          <div class="flex justify-between mb-4 text-gray-600 border-b pb-4">
            <span v-if="property.bedrooms">
              <i class="fas fa-bed mr-1"></i> {{ property.bedrooms }} beds
            </span>
            <span v-if="property.bathrooms">
              <i class="fas fa-bath mr-1"></i> {{ property.bathrooms }} baths
            </span>
            <span v-if="property.area">
              <i class="fas fa-ruler-combined mr-1"></i> {{ property.area }} m²
            </span>
          </div>

          <!-- Price and Action -->
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-blue-600">
              ${{ Number(property.price).toLocaleString() }}
            </span>
            <button 
              @click="viewDetails(property.id)"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200"
            >
              View Details
            </button>
          </div>
        </div>
      </div>
    </div>

    <MapView 
      v-else
      :properties="filteredProperties"
      class="h-[600px] rounded-lg overflow-hidden shadow-lg"
    />
  </div>
</template>

<script>
import MapView from './MapView.vue'

export default {
  components: {
    MapView
  },
  data() {
    return {
      properties: [],
      filters: {
        search: '',
        priceRange: '',
        category: ''
      },
      viewMode: 'grid' // 'grid' or 'map'
    }
  },
  computed: {
    filteredProperties() {
      let filtered = this.properties;

      // Search filter
      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase();
        filtered = filtered.filter(property => 
          property.title.toLowerCase().includes(searchTerm) ||
          property.description.toLowerCase().includes(searchTerm) ||
          property.address.toLowerCase().includes(searchTerm)
        );
      }

      // Category filter
      if (this.filters.category) {
        filtered = filtered.filter(property => 
          property.category_id.toString() === this.filters.category
        );
      }

      // Price range filter
      if (this.filters.priceRange) {
        const [min, max] = this.filters.priceRange.split('-');
        filtered = filtered.filter(property => {
          if (max === '+') {
            return property.price >= Number(min);
          }
          return property.price >= Number(min) && property.price <= Number(max);
        });
      }

      return filtered;
    }
  },
  async created() {
    try {
      const response = await window.axios.get('/api/properties');
      this.properties = response.data.data;
    } catch (error) {
      console.error('Error fetching properties:', error);
    }
  },
  methods: {
    viewDetails(id) {
      console.log('Viewing property:', id);
      // We'll implement this later with routing
    }
  }
}
</script> 