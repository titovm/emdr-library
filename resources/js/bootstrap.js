/**
 * This bootstrap file sets up JavaScript dependencies that Laravel's frontend scaffolding requires.
 */

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo configuration would go here if using Laravel Echo for real-time events.
 */