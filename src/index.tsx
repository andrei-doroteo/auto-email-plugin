import domReady from "@wordpress/dom-ready";
import { createRoot } from "@wordpress/element";
import { SettingsPage } from "./frontend/SettingsPage";

domReady(() => {
	const settingsMenu = document.getElementById("auto-email-settings-menu");
	if (settingsMenu) {
		const root = createRoot(settingsMenu);
		root.render(<SettingsPage />);
	}
});
