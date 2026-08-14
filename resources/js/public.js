import Alpine from 'alpinejs';
import navbar from './alpine/navbar';

window.Alpine = Alpine;

Alpine.data('navbar', navbar);

Alpine.start();
