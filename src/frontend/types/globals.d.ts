export { };

declare global {
    interface Window {
        wp_autoemail: {
            baseUrl: string;
            ajaxUrl: string;
            nonce: string;
        };
    }
}
