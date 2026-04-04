import axios, { AxiosResponse } from 'axios';

const baseUrl = 'https://app.omakeikka.fi';

export const getOccupations = async (): Promise<{[key: string]: string}> => {
  try {
    const response: AxiosResponse<{[key: string]: string}> = await axios
      .request({
        method: 'GET',
        url: `${baseUrl}/api/occupations?existing`,
        headers: { Accept: 'application/json' },
      });

    return response.data || {};
  } catch (error) {
    // eslint-disable-next-line no-console
    console.error(error);
    return {};
  }
};
