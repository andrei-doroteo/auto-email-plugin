import { useEffect, useState } from "react";
import { useGetWpData } from "./hooks/useGetWpData";
import { usePostWpData } from "./hooks/usePostWpData";

interface PluginSettings {
    businessOwnerEmail: string;
}

export function SettingsPage() {
    // Component variables
    const [settings, setSettings] = useState<PluginSettings>({
        businessOwnerEmail: "",
    });

    // Server requests
    const { data: getResponse, isLoading, isFetched } = useGetWpData<{ business_owner_email: string }>("/wp-admin/admin-ajax.php?action=get_business_owner_email", ["wp-admin", "admin-ajax.php", "get_business_owner_email"]);
    const { mutate, data: postResponse, reset, isSuccess, error, isPending, isIdle } = usePostWpData("/wp-admin/admin-ajax.php", ["wp-admin", "admin-ajax.php", "autoemail_save_options"]);

    /** Updates settings when server responds with the data.*/
    /* TODO:
    *       - Ensure settings is only updated once on intial fetch
    *       - OR refactor to not use useEffect().
    */
    useEffect(() => {

        if (isLoading || !isFetched || !getResponse?.data) return;
        setSettings((prev) => ({ ...prev, businessOwnerEmail: getResponse.data.business_owner_email }));

    }, [isLoading, isFetched, getResponse])

    // Button handlers
    const handleSendTestEmail = async () => {
        // TODO: Implement API call to send test email
        console.log("Sending test email to:", settings.businessOwnerEmail);
    };

    const handleSave = () => {
        console.log("Saving settings:", settings);
        const postData = new URLSearchParams();
        postData.append('action', "autoemail_save_options")
        postData.append('security', window.wp_autoemail.nonce)
        postData.append('business_owner_email', settings.businessOwnerEmail)
        mutate(postData, {
            onSuccess: onMutateSuccess,
            onError: onMutateError,
        });
    };

    // handleSave() helpers
    function onMutateSuccess() {
        alert("Saved Successfully.");
        reset();
    }
    function onMutateError(error: Error) {
        alert("Failed to save. Please try again.");
        console.error(error);
        reset();
    }


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
                    <form method="post" onSubmit={(e) => {
                        e.preventDefault();
                        handleSave();
                    }}>
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
                                    type="submit"
                                >
                                    {isPending ? "Saving..." : "Save"}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    );
}