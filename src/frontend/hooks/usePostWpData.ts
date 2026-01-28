import { useMutation, UseMutationResult } from "@tanstack/react-query";
import type { AxiosResponse } from "axios";
import { WpApi } from "../lib/WpApi";

/**
 * Custom React hook to send a POST request to a WordPress API
 *
 * @param endpointPath Endpoint path after base endpoint with no leading slash (i.e "/pages/4").
 * @param queryKey Array of query key strings (i.e. ["page", "about"]).
 *
 * @returns Resulting data from request.
 */
function usePostWpData<T = any>(endpointPath: string, mutationKey: string[]): UseMutationResult<AxiosResponse<T>> {

    const mutationFcn = (data: any) => {
        return WpApi.get_instance().get_apiClient().post(endpointPath, data);
    };

    return useMutation({
        mutationFn: mutationFcn,
        mutationKey: mutationKey,
        onError: (error) => {
            console.error(error);
        }
    });

}

export { usePostWpData };
