import path from 'path';
import fs from 'fs';
import SphericalMercator from '@mapbox/sphericalmercator';
import municipalitiesGeo from '@data/municipalities_geo.json';
import municipalitiesData from '@data/municipalities_full.json';
import {
  BBox, Feature, FeatureCollection, GeoJSON, Position,
} from 'geojson';
import geojsonExtent from '@mapbox/geojson-extent';
import { Municipality } from '@/data';

const getGeoMunicipalitiesBounds = (): BBox => geojsonExtent(municipalitiesGeo as GeoJSON);

const getGeoMunicipalitiesPixelBounds = (mercator: SphericalMercator): BBox => {
  const bounds = getGeoMunicipalitiesBounds();
  return [
    ...mercator.px([bounds[0], bounds[1]], 0),
    ...mercator.px([bounds[2], bounds[3]], 0),
  ];
};

const mercator = new SphericalMercator({
  size: 2 ** 16,
  antimeridian: true,
});

const pixelBounds = getGeoMunicipalitiesPixelBounds(mercator);

const projectPolygon = (polygon: Position[], pixelBounds: BBox, mercator: SphericalMercator, xBase = 1000.0): Position[] => polygon.map((position: Position) => {
  const point = mercator.px([position[0], position[1]], 0);
  const pixelWidth = (pixelBounds[2] - pixelBounds[0]);

  return [
    ((point[0] - pixelBounds[0]) * xBase) / pixelWidth,
    ((point[1] - pixelBounds[3]) * xBase) / pixelWidth,
  ];
});

const data = Object.entries(municipalitiesData).map(([id, municipality]: [string, Municipality]) => {
  let polygons: Position[][] = [];

  const feature = (municipalitiesGeo as FeatureCollection).features
    .find((feature: Feature) => feature.properties.Name === municipality.title.fi);

  if (feature.geometry.type === 'Polygon') {
    polygons = [
      ...polygons,
      ...feature.geometry.coordinates.map((polygon) => projectPolygon(polygon, pixelBounds, mercator)),
    ];
  } else if (feature.geometry.type === 'MultiPolygon') {
    for (const multiPolygon of feature.geometry.coordinates) {
      polygons = [
        ...polygons,
        ...multiPolygon.map((polygon) => projectPolygon(polygon, pixelBounds, mercator)),
      ];
    }
  }

  return {
    municipality_id: municipality.municipality_id,
    region_id: municipality.region_id,
    subregion_id: municipality.subregion_id,
    polygons,
  };
});

const targetDir = path.resolve(process.argv[2]);
const targetFile = path.resolve(targetDir, 'map.json');

fs.mkdirSync(targetDir, { recursive: true });
fs.writeFileSync(targetFile, JSON.stringify(data, null, 2));
