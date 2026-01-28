import { useQuery, UseQueryResult } from "@tanstack/react-query";
import type { AxiosResponse } from "axios";
import { WpApi } from "../lib/WpApi";

/**
 * Custom React hook to send a GET request to a WordPress API
 *
 * @param endpointPath Endpoint path after base endpoint with no leading slash (i.e "/pages/4").
 * @param queryKey Array of query key strings (i.e. ["page", "about"]).
 *
 * @returns Resulting data from request.
 */
function useGetWpData<T = any>(endpointPath: string, queryKey: string[]): UseQueryResult<AxiosResponse<T>> {

    const queryFcn = () => {
        return WpApi.get_instance().get_apiClient().get(endpointPath, { params: { security: window.wp_autoemail.nonce } });
    };

    return useQuery({
        queryKey: queryKey,
        queryFn: queryFcn,
    });

}

export { useGetWpData };
