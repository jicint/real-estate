<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="property" class="bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Image Gallery -->
      <div class="relative h-96">
        <img 
          :src="property.images ? property.images[0] : '/default-property.jpg'"
          class="w-full h-full object-cover"
          alt="Property Image"
        >
      </div>

      <!-- Property Info -->
      <div class="p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold mb-2">{{ property.title }}</h1>
            <p class="text-gray-600 text-lg mb-4">{{ property.address }}</p>
          </div>
          <div class="text-right">
            <p class="text-3xl font-bold text-blue-600">${{ property.price.toLocaleString() }}</p>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
              {{ property.status }}
            </span>
          </div>
        </div>

        <!-- Property Details -->
        <div class="grid grid-cols-3 gap-4 my-6 p-4 bg-gray-50 rounded-lg">
          <div class="text-center">
            <p class="text-gray-600">Bedrooms</p>
            <p class="text-xl font-semibold">{{ property.bedrooms }}</p>
          </div>
          <div class="text-center">
            <p class="text-gray-600">Bathrooms</p>
            <p class="text-xl font-semibold">{{ property.bathrooms }}</p>
          </div>
          <div class="text-center">
            <p class="text-gray-600">Area</p>
            <p class="text-xl font-semibold">{{ property.area }} m²</p>
          </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <h2 class="text-2xl font-semibold mb-4">Description</h2>
          <p class="text-gray-700 leading-relaxed">{{ property.description }}</p>
        </div>

        <!-- Contact Form -->
        <div class="bg-gray-50 p-6 rounded-lg">
          <h2 class="text-2xl font-semibold mb-4">Contact Agent</h2>
          <form @submit.prevent="submitInquiry" class="space-y-4">
            <div>
              <input 
                v-model="inquiry.name"
                type="text" 
                placeholder="Your Name" 
                class="w-full p-2 border rounded"
                required
              >
            </div>
            <div>
              <input 
                v-model="inquiry.email"
                type="email" 
                placeholder="Your Email" 
                class="w-full p-2 border rounded"
                required
              >
            </div>
            <div>
              <textarea 
                v-model="inquiry.message"
                placeholder="Your Message" 
                class="w-full p-2 border rounded h-32"
                required
              ></textarea>
            </div>
            <button 
              type="submit" 
              class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
            >
              Send Inquiry
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      property: null,
      loading: true,
      inquiry: {
        name: '',
        email: '',
        message: ''
      }
    }
  },
  created() {
    this.fetchProperty();
  },
  methods: {
    async fetchProperty() {
      try {
        this.loading = true;
        const response = await axios.get(`/api/properties/${this.$route.params.id}`);
        this.property = response.data;
      } catch (error) {
        console.error('Error fetching property:', error);
      } finally {
        this.loading = false;
      }
    },
    async submitInquiry() {
      try {
        // Add your inquiry submission logic here
        alert('Inquiry submitted successfully!');
        this.inquiry = { name: '', email: '', message: '' };
      } catch (error) {
        console.error('Error submitting inquiry:', error);
      }
    }
  }
}
</script> 