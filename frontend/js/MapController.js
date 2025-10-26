export class MapController {
  constructor() {
    this.map = null;
    this.geocoderApi = null;
  }

  async init(container, options) {
    this.map = new maplibregl.Map({
      container,
      ...options
    });

    this.setupGeocoder();

    return this.map;
  }

  setupGeocoder() {
    this.geocoderApi = {
      forwardGeocode: async (config) => {
        const features = [];
        try {
          const request =
            `https://nominatim.openstreetmap.org/search?q=${
              config.query
            }&format=geojson&polygon_geojson=1&addressdetails=1`;
          const response = await fetch(request);
          const geojson = await response.json();
          for (const feature of geojson.features) {
            const center = [
              feature.bbox[0] +
              (feature.bbox[2] - feature.bbox[0]) / 2,
              feature.bbox[1] +
              (feature.bbox[3] - feature.bbox[1]) / 2
            ];
            const point = {
              type: 'Feature',
              geometry: {
                type: 'Point',
                coordinates: center
              },
              place_name: feature.properties.display_name,
              properties: feature.properties,
              text: feature.properties.display_name,
              place_type: ['place'],
              center,
              zoom: 7
            };
            features.push(point);
          }
        } catch (e) {
          console.error(`Failed to forwardGeocode with error: ${e}`);
        }

        return {
          features
        };
      }
    };

    this.map.addControl(
      new MaplibreGeocoder(this.geocoderApi, {
        maplibregl
      }),
    );
  }

  addSource(id, source) {
    this.map.addSource(id, source);
  }

  addLayer(layer) {
    this.map.addLayer(layer);
  }

  on(event, layer, callback) {
    this.map.on(event, layer, callback);
  }

  flyTo(options) {
    this.map.flyTo(options);
  }

  easeTo(options) {
    this.map.easeTo(options);
  }

  getSource(id) {
    return this.map.getSource(id);
  }

  getCanvas() {
    return this.map.getCanvas();
  }

  queryRenderedFeatures(point, options) {
    return this.map.queryRenderedFeatures(point, options);
  }
}
