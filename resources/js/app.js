import Alpine from "alpinejs";
import axios from "axios";

window.axios = axios;
window.Alpine = Alpine;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Set CSRF token for all Axios requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}

Alpine.start();
