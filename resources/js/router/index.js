import { createRouter, createWebHistory } from 'vue-router';
import PropertyList from '../Components/properties/PropertyList.vue';
import MapView from '../Components/properties/MapView.vue';
import PropertyContainer from '../Components/properties/PropertyContainer.vue';
import PropertyDetail from '../components/properties/PropertyDetail.vue';

const routes = [
  {
    path: '/',
    redirect: '/properties'
  },
  {
    path: '/properties',
    name: 'properties',
    component: () => import('../Components/properties/PropertyContainer.vue')
  },
  {
    path: '/properties/:id',
    name: 'property-detail',
    component: PropertyDetail
  },
  {
    path: '/compare/:token',
    component: PropertyContainer
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/properties'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router; 