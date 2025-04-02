<template>
    <div class="relative h-[800px]">
        <!-- Map Container -->
        <div ref="mapContainer" class="w-full h-full"></div>

        <!-- Saved Comparisons Button -->
        <button 
            @click="showSaved"
            class="absolute top-4 right-4 bg-white px-4 py-2 rounded shadow-lg z-10 hover:bg-gray-50"
        >
            Saved Comparisons
        </button>

        <!-- Property Info Window -->
        <div v-if="selectedProperty" class="absolute bottom-20 left-4 bg-white p-4 rounded-lg shadow-lg max-w-md z-10">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold">{{ selectedProperty.title }}</h3>
                <button @click="selectedProperty = null" class="text-gray-500 hover:text-gray-700">
                    <span>&times;</span>
                </button>
            </div>
            <p class="text-gray-600 mb-2">${{ selectedProperty.price?.toLocaleString() }}</p>
            <div class="flex space-x-2">
                <button 
                    @click="addToCompare(selectedProperty)"
                    :disabled="isPropertyInComparison(selectedProperty.id)"
                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 disabled:bg-gray-400"
                >
                    {{ isPropertyInComparison(selectedProperty.id) ? 'Added to Compare' : 'Add to Compare' }}
                </button>
            </div>
        </div>

        <!-- Comparison Panel -->
        <div v-if="showCompareModal" class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t z-20">
            <div class="container mx-auto p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Compare Properties ({{ compareProperties.length }})</h3>
                    <div class="flex space-x-2">
                        <!-- Add Save Button -->
                        <button 
                            v-if="compareProperties.length >= 2"
                            @click="showSaveModal = true"
                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                        >
                            Save Comparison
                        </button>
                        <button 
                            @click="showCompareModal = false"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            Close
                        </button>
                    </div>
                </div>
                
                <div class="flex space-x-4 overflow-x-auto">
                    <div v-for="property in compareProperties" :key="property.id" class="min-w-[250px]">
                        <div class="bg-gray-100 rounded p-4">
                            <h4 class="font-semibold mb-2">{{ property.title }}</h4>
                            <p class="text-gray-600 mb-2">${{ property.price?.toLocaleString() }}</p>
                            <div class="space-y-2">
                                <p><span class="font-medium">Bedrooms:</span> {{ property.bedrooms }}</p>
                                <p><span class="font-medium">Bathrooms:</span> {{ property.bathrooms }}</p>
                                <p><span class="font-medium">Area:</span> {{ property.square_feet }} sq ft</p>
                            </div>
                            <button 
                                @click="removeFromCompare(property.id)"
                                class="mt-3 text-red-500 text-sm hover:text-red-600"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Comparison Modal -->
        <div v-if="showSaveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Save Comparison</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Comparison Name
                    </label>
                    <input 
                        v-model="comparisonName"
                        type="text"
                        class="w-full px-3 py-2 border rounded-md"
                        placeholder="Enter a name for this comparison"
                    >
                </div>
                <div class="flex justify-end space-x-2">
                    <button 
                        @click="showSaveModal = false"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="saveComparison"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Saved Comparisons Panel -->
        <div v-if="showSavedComparisons" class="fixed right-4 top-4 bg-white p-4 rounded-lg shadow-lg z-20 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Saved Comparisons</h3>
                <button @click="showSavedComparisons = false" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="space-y-2">
                <div v-for="comp in savedComparisons" :key="comp.id" class="p-2 bg-gray-50 rounded">
                    <div class="flex justify-between items-center">
                        <span>{{ comp.name }}</span>
                        <div class="flex space-x-2">
                            <button 
                                @click="loadComparison(comp)"
                                class="text-blue-500 hover:text-blue-600 text-sm"
                            >
                                Load
                            </button>
                            <button 
                                @click="deleteComparison(comp.id)"
                                class="text-red-500 hover:text-red-600 text-sm"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        properties: {
            type: Array,
            required: true
        }
    },

    data() {
        return {
            map: null,
            markers: [],
            selectedProperty: null,
            compareProperties: [],
            showCompareModal: false,
            showSaveModal: false,
            showSavedComparisons: false,
            comparisonName: '',
            savedComparisons: []
        }
    },

    created() {
        // Load saved comparisons from localStorage
        this.loadSavedComparisons();
    },

    mounted() {
        this.initializeMap();
    },

    methods: {
        initializeMap() {
            this.map = new google.maps.Map(this.$refs.mapContainer, {
                zoom: 12,
                center: this.getMapCenter(),
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true
            });

            this.updateMarkers();
        },

        getMapCenter() {
            if (this.properties.length > 0) {
                return {
                    lat: parseFloat(this.properties[0].latitude),
                    lng: parseFloat(this.properties[0].longitude)
                };
            }
            return { lat: -34.397, lng: 150.644 };
        },

        createMarker(property) {
            const marker = new google.maps.Marker({
                position: { 
                    lat: parseFloat(property.latitude), 
                    lng: parseFloat(property.longitude) 
                },
                map: this.map,
                title: property.title
            });

            marker.addListener('click', () => {
                this.selectedProperty = { ...property };
            });

            return marker;
        },

        updateMarkers() {
            this.markers.forEach(marker => marker.setMap(null));
            this.markers = [];

            this.properties.forEach(property => {
                const marker = this.createMarker(property);
                this.markers.push(marker);
            });
        },

        addToCompare(property) {
            if (!this.isPropertyInComparison(property.id)) {
                this.compareProperties.push({ ...property });
                this.showCompareModal = true;
            }
        },

        removeFromCompare(propertyId) {
            this.compareProperties = this.compareProperties.filter(p => p.id !== propertyId);
            if (this.compareProperties.length === 0) {
                this.showCompareModal = false;
            }
        },

        isPropertyInComparison(propertyId) {
            return this.compareProperties.some(p => p.id === propertyId);
        },

        loadSavedComparisons() {
            const saved = localStorage.getItem('savedComparisons');
            this.savedComparisons = saved ? JSON.parse(saved) : [];
        },

        saveComparison() {
            if (!this.comparisonName) {
                alert('Please enter a name for the comparison');
                return;
            }

            const comparison = {
                id: Date.now(), // Use timestamp as ID
                name: this.comparisonName,
                properties: this.compareProperties,
                date: new Date().toISOString()
            };

            this.savedComparisons.push(comparison);
            localStorage.setItem('savedComparisons', JSON.stringify(this.savedComparisons));

            this.showSaveModal = false;
            this.comparisonName = '';
            alert('Comparison saved successfully!');
        },

        loadComparison(comparison) {
            this.compareProperties = [...comparison.properties];
            this.showCompareModal = true;
            this.showSavedComparisons = false;
        },

        deleteComparison(id) {
            if (confirm('Are you sure you want to delete this comparison?')) {
                this.savedComparisons = this.savedComparisons.filter(c => c.id !== id);
                localStorage.setItem('savedComparisons', JSON.stringify(this.savedComparisons));
            }
        },

        // Add button to show saved comparisons
        showSaved() {
            this.loadSavedComparisons();
            this.showSavedComparisons = true;
        }
    },

    watch: {
        properties: {
            handler(newProperties) {
                if (this.map) {
                    this.updateMarkers();
                }
            },
            deep: true
        }
    }
}
</script>

<style scoped>
.comparison-panel {
    max-height: 50vh;
    overflow-y: auto;
}

.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
}
</style> 