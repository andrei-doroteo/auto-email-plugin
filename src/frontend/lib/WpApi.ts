import axios, { type AxiosInstance } from "axios";

class WpApi {
    private static singleton: WpApi = new WpApi();
    private apiClient: AxiosInstance;

    private constructor() {
        this.apiClient = axios.create({
            baseURL: window.wp_autoemail.baseUrl,
            timeout: 1000 * 15,
            headers: {},

        });
    }

    public static get_instance(): WpApi {
        return this.singleton;
    }

    public get_apiClient(): AxiosInstance {
        return this.apiClient;
    }
}

export { WpApi };
