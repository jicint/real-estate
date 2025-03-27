import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import App from './App.vue';
import router from './router';

// Configure Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const app = createApp(App)
    .use(ZiggyVue)
    .use(router);
app.mount('#app');

console.log('Vue app initialized');

import SharedComparison from './Components/properties/SharedComparison.vue';

// Register component
app.component('shared-comparison', SharedComparison);
