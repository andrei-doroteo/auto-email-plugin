import { useState } from "react";

interface PluginSettings {
	businessOwnerEmail: string;
}

export function SettingsPage() {
	const [settings, setSettings] = useState<PluginSettings>({
		businessOwnerEmail: "",
	});

	const handleSendTestEmail = async () => {
		// TODO: Implement API call to send test email
		console.log("Sending test email to:", settings.businessOwnerEmail);
	};

	const handleSave = async () => {
		// TODO: Implement API call to save settings
		console.log("Saving settings:", settings);
	};

	return (
		<section id="notification-settings" className="p-4 space-y-8">
			{/* Description */}
			<div className="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
				<p className="text-gray-700">
					Configure the email address to receive notifications when
					someone submits your online form. Use the "Send Test Email"
					button to verify you're receiving notifications correctly.
				</p>
			</div>

			{/* Automatic Notifications */}
			<div className="bg-white shadow rounded p-6">
				<div className="flex justify-between items-center mb-4">
					<h2 className="text-xl font-semibold">
						Form Submission Automatic Notifications
					</h2>
					<button
						id="send-test-email-btn"
						className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
						onClick={handleSendTestEmail}
					>
						Send Test Email
					</button>
				</div>
				<div id="auto-list" className="space-y-6">
					<div className="flex gap-4 items-end">
						<div className="flex-1">
							<label className="block text-sm font-medium text-gray-700 mb-1">
								Business Owner Email
							</label>
							<input
								type="email"
								id="owner-email-input"
								placeholder="Enter email address"
								className="border rounded p-2 w-full"
								value={settings.businessOwnerEmail}
								onChange={(e) =>
									setSettings({
										...settings,
										businessOwnerEmail: e.target.value,
									})
								}
							/>
						</div>
						<div>
							<button
								id="save-btn"
								className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
								onClick={handleSave}
							>
								Save
							</button>
						</div>
					</div>
				</div>
			</div>
		</section>
	);
}
