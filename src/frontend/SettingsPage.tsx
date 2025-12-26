import { useEffect, useRef, useState } from "@wordpress/element";

export  function SettingsPage() {
	const [isUnlocked, setIsUnlocked] = useState(false);
	const [template, setTemplate] = useState(`<!DOCTYPE html>
<html>
<head>
	<title>Thank you for your submission!</title>
</head>
<body>
	<h1>Hi {{ name }},</h1>
	<p>Thank you for contacting us. We have received your message:</p>
	<blockquote>{{ message }}</blockquote>
	<p>We will get back to you shortly.</p>
	<p>Best regards,<br>The Team</p>
</body>
</html>`);
	const [showTemplateContent, setShowTemplateContent] = useState(false);
	const iframeRef = useRef(null);

	useEffect(() => {
		const linkId = "poppins-font-link";
		if (!document.getElementById(linkId)) {
			const link = document.createElement("link");
			link.id = linkId;
			link.rel = "stylesheet";
			link.href =
				"https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap";
			document.head.appendChild(link);
		}
	}, []);

	useEffect(() => {
		if (!iframeRef.current) return;
		const iframe = iframeRef.current;
		const previewDoc =
			iframe.contentDocument || iframe.contentWindow?.document;
		if (!previewDoc) return;
		previewDoc.open();
		let previewHtml = template.replace(/\{\{\s*name\s*\}\}/g, "John Doe");
		previewHtml = previewHtml.replace(
			/\{\{\s*message\s*\}\}/g,
			"This is a test message for the live preview."
		);
		previewDoc.write(previewHtml);
		previewDoc.close();
	}, [template]);

	const handleUnlock = () => {
		if (
			confirm(
				"Are you sure you want to edit the HTML template? This can break the email layout."
			)
		) {
			setIsUnlocked(true);
			setShowTemplateContent(true);
		}
	};

	return (
		<div className="wrap">
			<style>{`
				:root {
					--primary-gradient-start: #8e2de2;
					--primary-gradient-end: #4a00e0;
					--accent-pink: #ff5b95;
					--accent-red: #ff4961;
					--white: #ffffff;
					--light-gray-1: #f5f7fa;
					--light-gray-2: #e9ecef;
					--medium-gray: #6b6f82;
					--dark-gray: #343a40;
					--success: #28a745;
					--font-family: "Poppins", sans-serif;
					--base-unit: 8px;
				}

				body {
					font-family: var(--font-family);
					background-color: var(--light-gray-1);
					color: var(--medium-gray);
					margin: 0;
					/* padding: calc(var(--base-unit) * 3); */
					font-size: 14px;
				}

				.wrap {
					max-width: 1200px;
					margin: 0 auto;
				}

				h1 {
					font-size: 24px;
					font-weight: 500;
					color: var(--dark-gray);
					margin-bottom: calc(var(--base-unit) * 3);
				}

				.main-container {
					display: grid;
					grid-template-columns: 1fr 1fr;
					gap: calc(var(--base-unit) * 3);
					align-items: start;
				}

				.card {
					background: var(--white);
					border-radius: var(--base-unit);
					box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.1);
					padding: calc(var(--base-unit) * 3);
					margin-bottom: calc(var(--base-unit) * 3);
				}

				.sticky-card {
					position: sticky;
					top: calc(var(--base-unit) * 3);
				}

				.card h2 {
					font-size: 18px;
					font-weight: 500;
					color: var(--dark-gray);
					margin-top: 0;
					margin-bottom: calc(var(--base-unit) * 2);
				}

				.form-group {
					margin-bottom: calc(var(--base-unit) * 2);
				}

				.form-group label {
					display: block;
					font-weight: 500;
					margin-bottom: var(--base-unit);
					color: var(--dark-gray);
				}

				.form-group input[type="email"],
				.form-group textarea {
					width: 100%;
					padding: calc(var(--base-unit) * 1.5);
					border: 1px solid var(--light-gray-2);
					border-radius: calc(var(--base-unit) / 2);
					font-size: 14px;
					font-family: var(--font-family);
					box-sizing: border-box;
				}

				.form-group textarea {
					height: 350px;
					resize: vertical;
				}

				.form-group textarea:disabled {
					background-color: var(--light-gray-1);
				}

				.button {
					border-radius: 20px;
					padding: 10px 24px;
					font-weight: 500;
					font-size: 14px;
					border: none;
					cursor: pointer;
					text-decoration: none;
					display: inline-block;
					transition: transform 0.1s ease;
				}

				.button:active {
					transform: scale(0.98);
				}

				.button-primary {
					background-color: var(--accent-pink);
					color: var(--white);
				}

				.button-ghost {
					background-color: var(--white);
					color: var(--medium-gray);
					border: 1px solid var(--light-gray-2);
				}

				.header-with-action {
					display: flex;
					justify-content: space-between;
					align-items: center;
					margin-bottom: calc(var(--base-unit) * 3);
				}

				.header-with-action h1 {
					margin-bottom: 0;
				}

				#preview-iframe {
					width: 100%;
					height: 600px;
					border: none;
					background-color: var(--white);
				}

				.notice {
					background-color: #fff3cd;
					border: 1px solid #ffeeba;
					color: #856404;
					padding: 15px;
					border-radius: 4px;
					margin-bottom: 20px;
					font-size: 13px;
				}

				.actions {
					margin-top: calc(var(--base-unit) * 3);
					padding-top: calc(var(--base-unit) * 3);
					border-top: 1px solid var(--light-gray-2);
				}
			`}</style>
			<div className="header-with-action">
				<h1>Auto Email Settings</h1>
				<button
					className="button button-primary"
					id="save-settings-btn"
				>
					Save All Settings
				</button>
			</div>
			<div className="main-container">
				<div className="settings-column">
					<div className="card">
						<h2>General Settings</h2>
						<div className="form-group">
							<label htmlFor="notification-email">
								Notification Email
							</label>
							<input
								type="email"
								id="notification-email"
								placeholder="e.g., admin@example.com"
								defaultValue="admin@example.com"
							/>
						</div>
					</div>

					<div className="card">
						<h2>Email Template</h2>
						<button
							className="button button-ghost"
							id="edit-template-dropdown-btn"
							onClick={handleUnlock}
							disabled={isUnlocked}
						>
							{isUnlocked ? "Template Unlocked" : "Edit Template"}
						</button>
						<div
							id="email-template-content"
							style={{
								display: showTemplateContent ? "block" : "none",
								marginTop: "16px",
							}}
						>
							<div className="notice">
								<strong>Warning:</strong> Only edit the template
								if you are familiar with HTML.
							</div>
							<div className="form-group">
								<label htmlFor="email-template">
									Email Body HTML
								</label>
								<textarea
									id="email-template"
									disabled={!isUnlocked}
									value={template}
									onChange={(e) =>
										setTemplate(e.target.value)
									}
								/>
							</div>
						</div>
					</div>
				</div>
				<div className="preview-column">
					<div className="card sticky-card">
						<h2>Live Preview</h2>
						<iframe id="preview-iframe" ref={iframeRef}></iframe>
					</div>
				</div>
			</div>
		</div>
	);
}
