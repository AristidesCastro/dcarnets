// resources/js/app.js
import "./bootstrap";
import Alpine from "alpinejs";
import interact from "interactjs"; // <-- Importa interactjs

window.Alpine = Alpine;
window.interact = interact; // <-- Hazlo globalmente accesible (opcional pero útil para depurar)

Alpine.start();
