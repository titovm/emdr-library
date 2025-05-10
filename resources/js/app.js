import './bootstrap';
import { createApp } from 'vue';
import LineChart from './components/LineChart';

// Create Vue application
const app = createApp({});

// Register components
app.component('line-chart', LineChart);

// Mount the application
app.mount('#app');