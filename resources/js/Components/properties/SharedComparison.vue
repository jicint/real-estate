<template>
    <div>
        <div class="mb-6">
            <h2 class="text-xl font-semibold">{{ comparisonName }}</h2>
            <p class="text-gray-600">{{ properties.length }} properties</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="property in properties" 
                 :key="property.id" 
                 class="bg-white rounded-lg shadow overflow-hidden">
                <img :src="property.image_url" 
                     :alt="property.title"
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ property.title }}</h3>
                    <p class="text-gray-600">${{ formatPrice(property.price) }}</p>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>{{ property.bedrooms }} beds • {{ property.bathrooms }} baths</p>
                        <p>{{ property.area }} sq ft</p>
                    </div>
                    <a :href="'/properties/' + property.id" 
                       class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        initialProperties: {
            type: Array,
            required: true
        },
        comparisonName: {
            type: String,
            default: 'Shared Comparison'
        }
    },

    data() {
        return {
            properties: []
        }
    },

    async created() {
        try {
            // Fetch full property details using the IDs
            const response = await axios.post('/api/properties/details', {
                propertyIds: this.initialProperties
            });
            this.properties = response.data;
        } catch (error) {
            console.error('Error loading properties:', error);
        }
    },

    methods: {
        formatPrice(price) {
            return new Intl.NumberFormat().format(price);
        }
    }
}
</script> 