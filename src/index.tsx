import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import domReady from "@wordpress/dom-ready";
import { createRoot } from "@wordpress/element";
import "./frontend/index.css";
import { SettingsPage } from "./frontend/SettingsPage";

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            retry: 1,
            refetchOnWindowFocus: false,
        },
    },
});

domReady(() => {
    const settingsMenu = document.getElementById("auto-email-settings-menu");
    if (settingsMenu) {
        const root = createRoot(settingsMenu);
        root.render(
            <QueryClientProvider client={queryClient}>
                <SettingsPage />
            </QueryClientProvider>
        );
    }
});
