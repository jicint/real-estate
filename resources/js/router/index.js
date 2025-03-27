import { createRouter, createWebHashHistory } from 'vue-router';
import PropertyList from '../components/properties/PropertyList.vue';
import PropertyDetail from '../components/properties/PropertyDetail.vue';
import MapView from '../Components/properties/MapView.vue';

const routes = [
  {
    path: '/',
    name: 'properties',
    component: PropertyList
  },
  {
    path: '/properties/:id',
    name: 'property-detail',
    component: PropertyDetail
  },
  {
    path: '/compare/:token',
    component: MapView,
    props: true
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
];

const router = createRouter({
  history: createWebHashHistory(),
  routes
});

export default router; 