export class CampsController {
  constructor(mapController) {
    this.mapController = mapController;
    this.campsData = null;
    this.existingImages = {};
  }

  async loadCamps() {
    try {
      const response = await fetch('data/camp_site_geocoded.geojson');
      this.campsData = await response.json();
      return this.campsData;
    } catch (error) {
      console.error('Error loading camps data:', error);
      throw error;
    }
  }

  async addCampsToMap() {
    if (!this.campsData) {
      await this.loadCamps();
    }

    this.setupImageHandler();

    this.mapController.addSource('camps', {
      type: 'geojson',
      data: this.campsData,
      cluster: true,
      clusterMaxZoom: 14, 
      clusterRadius: 50, 
      clusterProperties: {
        'sum': ['+', 1]
      }
    });

    this.addCampsLayers();

    this.setupEventHandlers();
  }

  setupImageHandler() {
    this.mapController.on('styleimagemissing', async (e) => {
      if (this.existingImages[e.id]) return;
      
      this.existingImages[e.id] = true;
      const response = await fetch(e.id);
      const svgText = await response.text();
      const svg = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgText);
      const image = new Image();
      const promise = new Promise((resolve) => {
        image.onload = resolve;
      });
      image.src = svg;
      await promise;
      this.mapController.map.addImage(e.id, image);
    });
  }

  addCampsLayers() {
    this.mapController.addLayer({
      id: 'clusters',
      type: 'symbol',
      source: 'camps',
      filter: ['has', 'point_count'],
      layout: {
        'icon-image': 'tent_new.svg',
        'icon-size': 0.07,
        'icon-allow-overlap': true,
        'icon-overlap': 'always'
      }
    });

    this.mapController.addLayer({
      id: 'cluster-count',
      type: 'symbol',
      source: 'camps',
      filter: ['has', 'point_count'],
      layout: {
        'text-field': '{point_count_abbreviated}',
        'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
        'text-size': 12,
        'text-offset': [1.2, 0],
        'text-anchor': 'left',
        'text-allow-overlap': true
      },
      paint: {
        'text-color': '#000000',
        'text-halo-color': '#ffffff',
        'text-halo-width': 2
      }
    });

    this.mapController.addLayer({
      id: 'unclustered-point',
      type: 'symbol',
      source: 'camps',
      filter: ['!', ['has', 'point_count']],
      layout: {
        'icon-image': 'tent_new.svg',
        'icon-size': 0.07,
        'icon-allow-overlap': true,
        'icon-overlap': 'always'
      }
    });
  }

  setupEventHandlers() {
    this.mapController.on('click', 'clusters', (e) => {
      const features = this.mapController.queryRenderedFeatures(e.point, {
        layers: ['clusters']
      });
      
      if (features.length > 0) {
        const clusterId = features[0].properties.cluster_id;
        
        this.mapController.getSource('camps').getClusterExpansionZoom(
          clusterId,
          (err, zoom) => {
            if (err) return;

            this.mapController.flyTo({
              center: features[0].geometry.coordinates,
              zoom: zoom
            });
          }
        );
      }
    });

    this.mapController.on('click', 'unclustered-point', (e) => {
      if (e.features.length > 0) {
        const feature = e.features[0];
        this.onCampClick(feature);
      }
    });

    this.setupHoverEffects();
  }

  setupHoverEffects() {
    this.mapController.on('mouseenter', 'clusters', () => {
      this.mapController.getCanvas().style.cursor = 'pointer';
    });

    this.mapController.on('mouseleave', 'clusters', () => {
      this.mapController.getCanvas().style.cursor = '';
    });

    this.mapController.on('mouseenter', 'unclustered-point', () => {
      this.mapController.getCanvas().style.cursor = 'pointer';
    });

    this.mapController.on('mouseleave', 'unclustered-point', () => {
      this.mapController.getCanvas().style.cursor = '';
    });
  }

  onCampClick(feature) {
    const event = new CustomEvent('campSelected', {
      detail: { feature }
    });
    document.dispatchEvent(event);
  }

  getCampsData() {
    return this.campsData;
  }
}
