import './bootstrap';
import { createApp } from 'vue';
import LineChart from './components/LineChart';
import Multiselect from 'vue-multiselect';
import TaxonomySelector from './components/forms/TaxonomySelector.vue';
import 'vue-multiselect/dist/vue-multiselect.css';

// Create Vue application
const app = createApp({});

// Register components
app.component('line-chart', LineChart);
app.component('multiselect', Multiselect);
app.component('taxonomy-selector', TaxonomySelector);

// Mount the application
app.mount('#app');