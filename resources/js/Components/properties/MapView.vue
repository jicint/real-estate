<template>
    <div class="relative">
        <!-- Filter Panel -->
        <div class="absolute top-4 left-4 bg-white p-4 rounded-lg shadow-lg w-72 z-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg">Filters</h3>
                <button @click="resetFilters" 
                        class="text-sm text-blue-500 hover:text-blue-700">
                    Reset
                </button>
            </div>

            <!-- Price Range -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Price Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <input v-model="filters.priceMin" 
                               type="number" 
                               placeholder="Min"
                               class="w-full p-2 border rounded text-sm"
                        >
                    </div>
                    <div>
                        <input v-model="filters.priceMax" 
                               type="number" 
                               placeholder="Max"
                               class="w-full p-2 border rounded text-sm"
                        >
                    </div>
                </div>
            </div>

            <!-- Property Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Property Type</label>
                <select v-model="filters.category" 
                        class="w-full p-2 border rounded text-sm">
                    <option value="">All Types</option>
                    <option v-for="category in categories" 
                            :key="category.id" 
                            :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <!-- Bedrooms & Bathrooms -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Bedrooms</label>
                    <select v-model="filters.bedrooms" 
                            class="w-full p-2 border rounded text-sm">
                        <option value="">Any</option>
                        <option v-for="n in 5" :key="n" :value="n">
                            {{ n }}+
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Bathrooms</label>
                    <select v-model="filters.bathrooms" 
                            class="w-full p-2 border rounded text-sm">
                        <option value="">Any</option>
                        <option v-for="n in 4" :key="n" :value="n">
                            {{ n }}+
                        </option>
                    </select>
                </div>
            </div>

            <!-- Area Range -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Area (m²)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input v-model="filters.areaMin" 
                           type="number" 
                           placeholder="Min"
                           class="w-full p-2 border rounded text-sm"
                    >
                    <input v-model="filters.areaMax" 
                           type="number" 
                           placeholder="Max"
                           class="w-full p-2 border rounded text-sm"
                    >
                </div>
            </div>

            <!-- Features -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Features</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               v-model="filters.isFeatured"
                               class="form-checkbox"
                        >
                        <span class="ml-2 text-sm">Featured Properties</span>
                    </label>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="mt-4 pt-4 border-t">
                <div class="text-sm text-gray-600">
                    Showing {{ filteredProperties.length }} properties
                </div>
                <div class="text-sm text-gray-600">
                    Avg. Price: ${{ formatPrice(averagePrice) }}
                </div>
            </div>
        </div>

        <!-- Compare Panel -->
        <div v-if="compareProperties.length > 0" 
             class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t z-20">
            <div class="container mx-auto p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">
                        Compare Properties ({{ compareProperties.length }})
                    </h3>
                    <div class="space-x-2">
                        <button v-if="compareProperties.length >= 2"
                                @click="openSaveModal"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            <i class="fas fa-save mr-2"></i>Save Comparison
                        </button>
                        <button @click="clearCompare"
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Properties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div v-for="property in compareProperties" 
                         :key="property.id" 
                         class="bg-gray-50 rounded-lg p-4">
                        <img :src="property.image_url" 
                             :alt="property.title"
                             class="w-full h-48 object-cover rounded">
                        <div class="mt-2">
                            <h4 class="font-bold">{{ property.title }}</h4>
                            <p class="text-gray-600">${{ property.price }}</p>
                            <div class="text-sm text-gray-500">
                                <p>{{ property.bedrooms }} beds • {{ property.bathrooms }} baths</p>
                                <p>{{ property.area }} sq ft</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save/Share Modal -->
        <div v-if="showSaveModal" 
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-[600px] max-w-full mx-4">
                <!-- Tabs -->
                <div class="border-b mb-4">
                    <div class="flex space-x-4">
                        <button @click="activeTab = 'save'"
                                :class="{'text-blue-500 border-b-2 border-blue-500': activeTab === 'save'}"
                                class="pb-2 px-2">
                            Save
                        </button>
                        <button @click="activeTab = 'share'"
                                :class="{'text-blue-500 border-b-2 border-blue-500': activeTab === 'share'}"
                                class="pb-2 px-2">
                            Share
                        </button>
                    </div>
                </div>

                <!-- Save Tab -->
                <div v-if="activeTab === 'save'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Comparison Name</label>
                        <input type="text"
                               v-model="comparisonName"
                               placeholder="Enter a name for this comparison"
                               class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Notes (optional)</label>
                        <textarea v-model="comparisonNotes"
                                placeholder="Add any notes about this comparison"
                                class="w-full p-2 border rounded h-24"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="closeSaveModal"
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button @click="saveComparison"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Save
                        </button>
                    </div>
                </div>

                <!-- Share Tab -->
                <div v-if="activeTab === 'share'" class="space-y-6">
                    <!-- QR Code -->
                    <div class="text-center">
                        <div ref="qrcode" class="inline-block bg-white p-2 rounded shadow-md"></div>
                        <button @click="downloadQRCode"
                                class="block mx-auto mt-2 text-blue-500 hover:text-blue-700">
                            <i class="fas fa-download mr-1"></i>Download QR Code
                        </button>
                    </div>

                    <!-- Share Link -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Share Link</label>
                        <div class="flex space-x-2">
                            <input type="text"
                                   ref="shareUrlInput"
                                   :value="shareUrl"
                                   readonly
                                   class="flex-1 p-2 border rounded bg-gray-50">
                            <button @click="copyShareUrl"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                    </div>

                    <!-- Social Share -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Share on Social Media</label>
                        <div class="flex justify-center space-x-4">
                            <button @click="shareToSocial('facebook')"
                                    class="p-3 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button @click="shareToSocial('twitter')"
                                    class="p-3 rounded-full bg-blue-400 text-white hover:bg-blue-500">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button @click="shareToSocial('whatsapp')"
                                    class="p-3 rounded-full bg-green-500 text-white hover:bg-green-600">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Email Share -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Share via Email</label>
                        <div v-if="emailError" class="text-red-500 text-sm mb-2">
                            {{ emailError }}
                        </div>
                        <input type="email"
                               v-model="shareEmail"
                               placeholder="Enter recipient email"
                               class="w-full p-2 border rounded mb-2">
                        <textarea v-model="shareMessage"
                                  placeholder="Add a message (optional)"
                                  class="w-full p-2 border rounded h-24 mb-2"></textarea>
                        <button @click="sendShareEmail"
                                :disabled="!shareEmail || isSharing"
                                :class="{'opacity-50 cursor-not-allowed': !shareEmail || isSharing}"
                                class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            <i class="fas fa-envelope mr-2"></i>
                            {{ isSharing ? 'Sending...' : 'Send Email' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Comparisons List -->
        <div v-if="savedComparisons.length > 0" 
             class="fixed top-4 right-4 bg-white p-4 rounded-lg shadow-lg w-80 z-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold">Saved Comparisons</h3>
                <button @click="toggleSavedList"
                        class="text-gray-500 hover:text-gray-700">
                    <i :class="showSavedList ? 'fa-chevron-up' : 'fa-chevron-down'" 
                       class="fas"></i>
                </button>
            </div>
            <div v-if="showSavedList" class="space-y-3">
                <div v-for="(comparison, index) in savedComparisons" 
                     :key="index"
                     class="bg-gray-50 p-3 rounded">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium">{{ comparison.name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ comparison.properties.length }} properties
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ formatDate(comparison.savedAt) }}
                            </div>
                        </div>
                        <div class="space-x-2">
                            <button @click="shareComparison(comparison)"
                                    class="text-green-500 hover:text-green-700"
                                    title="Share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <button @click="loadSavedComparison(comparison)"
                                    class="text-blue-500 hover:text-blue-700"
                                    title="Load">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button @click="deleteSavedComparison(index)"
                                    class="text-red-500 hover:text-red-700"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share Modal (separate from Save Modal) -->
        <div v-if="showShareModal" 
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-[600px] max-w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Share Comparison</h3>
                    <button @click="closeShareModal" 
                            class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- QR Code -->
                <div class="text-center mb-6">
                    <div ref="qrcode" class="inline-block bg-white p-2 rounded shadow-md"></div>
                    <button @click="downloadQRCode"
                            class="block mx-auto mt-2 text-blue-500 hover:text-blue-700">
                        <i class="fas fa-download mr-1"></i>Download QR Code
                    </button>
                </div>

                <!-- Share Link -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Share Link</label>
                    <div class="flex space-x-2">
                        <input type="text"
                               ref="shareUrlInput"
                               :value="shareUrl"
                               readonly
                               class="flex-1 p-2 border rounded bg-gray-50">
                        <button @click="copyShareUrl"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>

                <!-- Social Share -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Share on Social Media</label>
                    <div class="flex justify-center space-x-4">
                        <button @click="shareToSocial('facebook')"
                                class="p-3 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button @click="shareToSocial('twitter')"
                                class="p-3 rounded-full bg-blue-400 text-white hover:bg-blue-500">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button @click="shareToSocial('whatsapp')"
                                class="p-3 rounded-full bg-green-500 text-white hover:bg-green-600">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>

                <!-- Email Share -->
                <div>
                    <label class="block text-sm font-medium mb-2">Share via Email</label>
                    <div v-if="emailError" class="text-red-500 text-sm mb-2">
                        {{ emailError }}
                    </div>
                    <input type="email"
                           v-model="shareEmail"
                           placeholder="Enter recipient email"
                           class="w-full p-2 border rounded mb-2">
                    <textarea v-model="shareMessage"
                              placeholder="Add a message (optional)"
                              class="w-full p-2 border rounded h-24 mb-2"></textarea>
                    <button @click="sendShareEmail"
                            :disabled="!shareEmail || isSharing"
                            :class="{'opacity-50 cursor-not-allowed': !shareEmail || isSharing}"
                            class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ isSharing ? 'Sending...' : 'Send Email' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div ref="mapContainer" class="w-full h-[calc(100vh-4rem)]"></div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" 
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500"></div>
                <p class="mt-2 text-gray-600">Loading comparison...</p>
            </div>
        </div>
    </div>
</template>

<script>
import QRCode from 'qrcode'

export default {
    props: {
        properties: {
            type: Array,
            required: true
        },
        token: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            map: null,
            markers: [],
            markerCluster: null,
            selectedProperty: null,
            selectedCluster: null,
            mapType: 'roadmap',
            clustering: true,
            categories: [],
            filters: {
                priceMin: '',
                priceMax: '',
                category: '',
                bedrooms: '',
                bathrooms: '',
                areaMin: '',
                areaMax: '',
                isFeatured: false
            },
            compareProperties: [],
            showCompareModal: false,
            maxCompareItems: 4,
            activeInfoWindow: null,
            showSaveModal: false,
            showSavedList: true,
            comparisonName: '',
            comparisonNotes: '',
            savedComparisons: [],
            showShareModal: false,
            currentSharedComparison: null,
            shareUrl: '',
            shareEmail: '',
            shareMessage: '',
            shareSettings: {
                expirable: false,
                expiryDate: null,
                requirePassword: false,
                password: '',
            },
            shareAnalytics: {
                totalViews: 0,
                totalShares: 0,
                lastViewed: null,
            },
            activeTab: 'save',
            QRCode: null,
            emailError: null,
            isSharing: false,
            isSharedComparison: false,
            sharedComparisonData: null,
            isLoading: false
        }
    },
    computed: {
        filteredProperties() {
            return this.properties.filter(property => {
                if (this.filters.priceMin && property.price < Number(this.filters.priceMin)) return false;
                if (this.filters.priceMax && property.price > Number(this.filters.priceMax)) return false;
                if (this.filters.category && property.category_id !== Number(this.filters.category)) return false;
                if (this.filters.bedrooms && property.bedrooms < Number(this.filters.bedrooms)) return false;
                if (this.filters.bathrooms && property.bathrooms < Number(this.filters.bathrooms)) return false;
                if (this.filters.areaMin && property.area < Number(this.filters.areaMin)) return false;
                if (this.filters.areaMax && property.area > Number(this.filters.areaMax)) return false;
                if (this.filters.isFeatured && !property.is_featured) return false;
                return true;
            });
        },
        averagePrice() {
            if (!this.filteredProperties.length) return 0;
            const sum = this.filteredProperties.reduce((acc, prop) => acc + prop.price, 0);
            return Math.round(sum / this.filteredProperties.length);
        }
    },
    methods: {
        initMap() {
            this.map = new google.maps.Map(this.$refs.mapContainer, {
                zoom: 12,
                center: { lat: 40.7128, lng: -74.0060 }, // New York coordinates
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });
            this.addMarkers();
        },
        addMarkers() {
            // Clear existing markers
            if (this.markerCluster) {
                this.markerCluster.clearMarkers();
            }
            this.markers.forEach(marker => marker.setMap(null));
            this.markers = [];

            // Create markers for filtered properties
            this.filteredProperties.forEach(property => {
                if (property.latitude && property.longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(property.latitude),
                            lng: parseFloat(property.longitude)
                        },
                        map: this.map,
                        title: property.title,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: this.getPriceRangeColor(property.price),
                            fillOpacity: 1,
                            strokeColor: '#ffffff',
                            strokeWeight: 2,
                        }
                    });

                    // Create info window content
                    const infoWindowContent = `
                        <div class="p-3 min-w-[200px]">
                            <h3 class="font-bold">${property.title}</h3>
                            <p class="text-blue-600 font-bold">$${this.formatPrice(property.price)}</p>
                            <div class="mt-2 flex justify-between items-center">
                                <button onclick="window.addToCompareProperty(${property.id})" 
                                        class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                    Add to Compare
                                </button>
                            </div>
                        </div>
                    `;

                    const infoWindow = new google.maps.InfoWindow({
                        content: infoWindowContent
                    });

                    marker.addListener('click', () => {
                        if (this.activeInfoWindow) {
                            this.activeInfoWindow.close();
                        }
                        infoWindow.open(this.map, marker);
                        this.activeInfoWindow = infoWindow;
                    });

                    this.markers.push(marker);
                }
            });

            // Initialize clustering
            this.initializeClustering();

            // Fit map to markers
            if (this.markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                this.markers.forEach(marker => bounds.extend(marker.getPosition()));
                this.map.fitBounds(bounds);
            }
        },
        getPriceRangeColor(price) {
            if (price < 500000) return '#10B981'; // green
            if (price < 1000000) return '#3B82F6'; // blue
            if (price < 1500000) return '#8B5CF6'; // purple
            return '#EF4444'; // red
        },
        formatPrice(price) {
            return Number(price).toLocaleString();
        },
        resetFilters() {
            this.filters = {
                priceMin: '',
                priceMax: '',
                category: '',
                bedrooms: '',
                bathrooms: '',
                areaMin: '',
                areaMax: '',
                isFeatured: false
            };
        },
        toggleMapType() {
            this.mapType = this.mapType === 'roadmap' ? 'satellite' : 'roadmap';
            this.map.setMapTypeId(this.mapType);
        },
        toggleClustering() {
            this.clustering = !this.clustering;
            this.updateClustering();
        },
        viewDetails(id) {
            console.log('Viewing property:', id);
            // Implement view details functionality later
        },
        viewProperty(property) {
            this.selectedCluster = null;
            this.selectedProperty = property;
        },
        updateClustering() {
            // Implementation of updateClustering method
        },
        initCategories() {
            // Get unique categories with proper names
            const categoryMap = new Map();
            this.properties.forEach(property => {
                if (property.category_id && property.category_name) {
                    categoryMap.set(property.category_id, {
                        id: property.category_id,
                        name: property.category_name
                    });
                }
            });
            this.categories = Array.from(categoryMap.values());
        },
        addToCompare(property) {
            if (this.compareProperties.length >= this.maxCompareItems) {
                alert(`You can compare up to ${this.maxCompareItems} properties at once`);
                return;
            }
            if (!this.compareProperties.find(p => p.id === property.id)) {
                this.compareProperties.push(property);
            }
        },
        removeFromCompare(property) {
            this.compareProperties = this.compareProperties.filter(p => p.id !== property.id);
            if (this.compareProperties.length < 2) {
                this.showCompareModal = false;
            }
        },
        clearCompare() {
            if (confirm('Are you sure you want to clear all compared properties?')) {
                this.compareProperties = [];
            }
        },
        initializeClustering() {
            this.markerCluster = new markerClusterer.MarkerClusterer({
                map: this.map,
                markers: this.markers,
                algorithm: new markerClusterer.SuperClusterAlgorithm({
                    radius: 100
                }),
                renderer: {
                    render: ({ count, position }) => {
                        return new google.maps.Marker({
                            position,
                            label: {
                                text: String(count),
                                color: 'white',
                                fontSize: '12px'
                            },
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: Math.min(count * 3, 30),
                                fillColor: '#3B82F6',
                                fillOpacity: 0.9,
                                strokeColor: '#ffffff',
                                strokeWeight: 2,
                            },
                            zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count
                        });
                    }
                }
            });
        },
        async loadQRCode() {
            try {
                const QRCodeModule = await import('qrcode')
                this.QRCode = QRCodeModule.default
            } catch (error) {
                console.error('Failed to load QR Code library:', error)
            }
        },
        async openSaveModal() {
            this.showSaveModal = true
            this.activeTab = 'save'
            this.comparisonName = ''
            this.comparisonNotes = ''
            
            // Generate share URL for the current comparison
            this.shareUrl = `${window.location.origin}/compare/${btoa(JSON.stringify({
                properties: this.compareProperties.map(p => p.id)
            }))}`

            // Wait for QR Code to be loaded before generating
            if (!this.QRCode) {
                await this.loadQRCode()
            }
            await this.generateQRCode()
        },
        closeSaveModal() {
            this.showSaveModal = false
            this.activeTab = 'save'
            this.comparisonName = ''
            this.comparisonNotes = ''
            this.shareEmail = ''
            this.shareMessage = ''
        },
        saveComparison() {
            if (!this.comparisonName) {
                alert('Please enter a name for the comparison')
                return
            }

            const comparison = {
                id: Date.now(),
                name: this.comparisonName,
                notes: this.comparisonNotes,
                properties: [...this.compareProperties],
                savedAt: new Date().toISOString()
            }

            this.savedComparisons.push(comparison)
            this.saveToLocalStorage()
            this.closeSaveModal()
            
            // Show success message
            alert('Comparison saved successfully!')
        },
        loadSavedComparison(comparison) {
            if (confirm('Load this comparison? Current comparison will be replaced.')) {
                this.compareProperties = [...comparison.properties]
            }
        },
        deleteSavedComparison(index) {
            if (confirm('Are you sure you want to delete this saved comparison?')) {
                this.savedComparisons.splice(index, 1)
                this.saveToLocalStorage()
            }
        },
        saveToLocalStorage() {
            localStorage.setItem('savedComparisons', JSON.stringify(this.savedComparisons))
        },
        loadFromLocalStorage() {
            const saved = localStorage.getItem('savedComparisons')
            if (saved) {
                this.savedComparisons = JSON.parse(saved)
            }
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            })
        },
        async shareComparison(comparison) {
            this.currentSharedComparison = comparison
            this.showShareModal = true
            
            // Generate share URL for the selected comparison
            this.shareUrl = `${window.location.origin}/compare/${btoa(JSON.stringify({
                id: comparison.id,
                properties: comparison.properties.map(p => p.id)
            }))}`

            // Generate QR code
            if (!this.QRCode) {
                await this.loadQRCode()
            }
            await this.$nextTick()
            await this.generateQRCode()
        },
        closeShareModal() {
            this.showShareModal = false
            this.shareEmail = ''
            this.shareMessage = ''
            this.currentSharedComparison = null
            this.emailError = null
            this.isSharing = false
        },
        async generateQRCode() {
            if (!this.QRCode || !this.$refs.qrcode) return
            
            try {
                await this.QRCode.toCanvas(this.$refs.qrcode, this.shareUrl, {
                    width: 200,
                    margin: 1,
                    color: {
                        dark: '#000000',
                        light: '#ffffff'
                    }
                })
            } catch (error) {
                console.error('Error generating QR code:', error)
            }
        },
        async downloadQRCode() {
            if (!this.QRCode) return
            
            try {
                const dataUrl = await this.QRCode.toDataURL(this.shareUrl)
                const link = document.createElement('a')
                link.download = `comparison-${Date.now()}.png`
                link.href = dataUrl
                link.click()
            } catch (error) {
                console.error('Error downloading QR code:', error)
            }
        },
        async copyShareUrl() {
            try {
                await navigator.clipboard.writeText(this.shareUrl)
                alert('Link copied to clipboard!')
            } catch (err) {
                console.error('Failed to copy:', err)
            }
        },
        shareToSocial(platform) {
            let url = ''
            const text = `Check out this property comparison: ${this.currentSharedComparison.name}`
            
            switch (platform) {
                case 'facebook':
                    url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.shareUrl)}`
                    break
                case 'twitter':
                    url = `https://twitter.com/intent/tweet?url=${encodeURIComponent(this.shareUrl)}&text=${encodeURIComponent(text)}`
                    break
                case 'whatsapp':
                    url = `https://wa.me/?text=${encodeURIComponent(text + ' ' + this.shareUrl)}`
                    break
            }

            window.open(url, '_blank', 'width=600,height=400')
        },
        async sendShareEmail() {
            if (!this.shareEmail) {
                this.emailError = 'Please enter an email address';
                return;
            }

            this.emailError = null;
            this.isSharing = true;

            try {
                const response = await axios.post('/api/share-comparison', {
                    email: this.shareEmail,
                    comparison: this.currentSharedComparison,
                    shareUrl: this.shareUrl,
                    message: this.shareMessage || ''
                });

                if (response.data.success) {
                    alert('Email sent successfully!');
                    this.closeShareModal();
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error('Email sending error:', error);
                this.emailError = error.response?.data?.message || 
                                'Failed to send email. Please try again.';
            } finally {
                this.isSharing = false;
            }
        },
        toggleSavedList() {
            this.showSavedList = !this.showSavedList
        },
        generateShareUrl() {
            const comparisonData = {
                id: Date.now(),
                properties: this.compareProperties.map(p => p.id)
            };
            
            const token = btoa(JSON.stringify(comparisonData));
            this.shareUrl = `${window.location.origin}/compare/${token}`;
        },
        async checkForSharedComparison() {
            // Check for initial comparison data
            if (window.INITIAL_DATA?.type === 'comparison') {
                console.log('Loading initial comparison data:', window.INITIAL_DATA.data);
                await this.loadSharedComparison(window.INITIAL_DATA.data);
            }
            // Also check URL for comparison token
            else if (this.$route.params.token) {
                try {
                    console.log('Loading comparison from URL token:', this.$route.params.token);
                    const data = JSON.parse(atob(this.$route.params.token));
                    console.log('Decoded comparison data:', data);
                    await this.loadSharedComparison(data);
                } catch (error) {
                    console.error('Error loading comparison from URL:', error);
                }
            }
        },
        async loadSharedComparison(data) {
            this.isLoading = true;
            try {
                console.log('Loading properties:', data.properties);
                const response = await axios.post('/api/properties/details', {
                    propertyIds: data.properties
                });

                this.compareProperties = response.data;
                this.isSharedComparison = true;
                
                // Force the comparison panel to show
                this.$nextTick(() => {
                    this.showCompareModal = true;
                    // Scroll to the comparison panel
                    const panel = document.querySelector('.fixed.bottom-0');
                    if (panel) {
                        panel.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            } catch (error) {
                console.error('Error loading shared properties:', error);
            } finally {
                this.isLoading = false;
            }
        }
    },
    watch: {
        filteredProperties: {
            handler() {
                this.addMarkers();
            },
            deep: true
        }
    },
    created() {
        this.checkForSharedComparison();
    },
    mounted() {
        // Add global methods for the info window buttons
        window.addToCompareProperty = (propertyId) => {
            const property = this.properties.find(p => p.id === propertyId);
            if (property) {
                this.addToCompare(property);
            }
        };

        window.viewPropertyDetails = (propertyId) => {
            const property = this.properties.find(p => p.id === propertyId);
            if (property) {
                this.selectedProperty = property;
            }
        };

        this.initMap();
        this.initCategories();
        this.loadFromLocalStorage();

        // Load QR Code library
        this.loadQRCode()
    }
}
</script>

<style scoped>
.marker-price {
    white-space: nowrap;
}

.compare-panel-enter-active,
.compare-panel-leave-active {
    transition: transform 0.3s ease-in-out;
}
.compare-panel-enter-from,
.compare-panel-leave-to {
    transform: translateY(100%);
}
</style> 