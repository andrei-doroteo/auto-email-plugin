import domReady from "@wordpress/dom-ready";
import { createRoot } from "@wordpress/element";
import SettingsPage from "./components/SettingsPage";

domReady(() => {
	const root = createRoot(document.getElementById("auto-email-settings-menu"));
	root.render(<SettingsPage />);
});
