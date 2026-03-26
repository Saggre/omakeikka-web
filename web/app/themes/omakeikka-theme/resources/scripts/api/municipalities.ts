import axios, { AxiosResponse } from 'axios';

const baseUrl = 'https://app.omakeikka.fi';

export interface MunicipalityOccupation {
  id: string | null
  title: string
  fraction: number
}

export interface MunicipalityOccupationsResult {
  occupations: MunicipalityOccupation[]
}

export const getMunicipalityOccupations = async (municipalityId: string): Promise<MunicipalityOccupation[]> => {
  try {
    const response: AxiosResponse<MunicipalityOccupationsResult> = await axios
      .request({
        method: 'GET',
        url: `${baseUrl}/api/municipalities/${municipalityId}/occupations`,
      });

    return response.data?.occupations || [];
  } catch (error) {
    // eslint-disable-next-line no-console
    console.error(error);
    return [];
  }
};
