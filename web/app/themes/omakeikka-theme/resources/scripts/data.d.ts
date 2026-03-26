import { GeoJSON } from '@types/geojson';

declare module '@data/municipalities_geo.json' {
  export default GeoJSON;
}

export type Municipality = {
  municipality_id: string
  subregion_id: string
  region_id: string
  title: {
    fi: string
    se: string
  },
  population: number
  location: {
    lat: number
    lng: number
  }
}

export type Municipalities = { [key: string]: Municipality };

declare module '@data/municipalities_full.json' {
  export default Municipalities;
}

declare module '@data/map.json' {
  export default Array<{
    municipality_id: string,
    region_id: string,
    subregion_id: string,
    polygons: Array<[number, number]>[]
  }>;
}
