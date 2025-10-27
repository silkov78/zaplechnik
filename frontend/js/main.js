import { views } from './views.js';
import { sidebarHandlers } from './sidebar.js';
import { MapController } from './MapController.js';
import { CampsController } from './CampsController.js';
import { AuthController } from './AuthController.js';

async function initApp() {
    const mapController = new MapController();
    const map = await mapController.init('map', {
        // style: 'https://tiles.stadiamaps.com/styles/outdoors.json',
        style: 'https://api.maptiler.com/maps/019951b6-5355-7da2-a025-329f99016a74/style.json?key=trbTEaKVIVbrXPpy6HKI',
        center: [28.051186, 53.757643],
        zoom: 7,
        maxBounds: [
            [16.884785, 40.931153], 
            [38.198261, 61.407894]  
        ]
    });

    const campsController = new CampsController(mapController);
    await campsController.addCampsToMap();

    const authController = new AuthController();
    authController.checkAuthOnLoad();

    document.addEventListener('campSelected', (event) => {
        const feature = event.detail.feature;
        sidebarHandlers.renderSidebar('camp', feature);
        if (document.getElementById('left').classList.contains('collapsed')) {
            toggleSidebar('left');
        }
        mapController.flyTo({
            center: feature.geometry.coordinates,
            zoom: 14
        });
    });

    document.addEventListener('authChanged', (event) => {
        const { isAuthenticated, user } = event.detail;
        
        if (isAuthenticated) {
            sidebarHandlers.renderSidebar('reg_user', user);
        } else {
            sidebarHandlers.renderSidebar('unreg_user');
        }
    });

    map.on('load', () => {
        toggleSidebar('left');
    });

    function toggleSidebar(id) {
        const elem = document.getElementById(id);
        const classes = elem.className.split(' ');
        const collapsed = classes.indexOf('collapsed') !== -1;

        const padding = {};

        if (collapsed) {
            // Remove the 'collapsed' class from the class list of the element, this sets it back to the expanded state.
            classes.splice(classes.indexOf('collapsed'), 1);

            padding[id] = 350; // In px, matches the width of the sidebars set in .sidebar CSS class
            mapController.easeTo({
                padding,
                duration: 1000 // In ms, CSS transition duration property for the sidebar matches this value
            });
        } else {
            padding[id] = 0;
            // Add the 'collapsed' class to the class list of the element
            classes.push('collapsed');

            mapController.easeTo({
                padding,
                duration: 1000
            });
        }

        elem.className = classes.join(' ');
    }

    document.querySelector('.sidebar-toggle').addEventListener('click', () => {
        toggleSidebar('left');
    });

    sidebarHandlers.renderSidebar('unreg_user');
    sidebarHandlers.init(mapController, authController);
}

initApp();

