export default function PrivacyModal({ onClose }) {
    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-2xl font-semibold text-gray-800">Privacy Policy</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-gray-500">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div className="prose max-w-none space-y-4">
                <section>
                    <h3 className="text-lg font-medium text-gray-900">Information Collection and Use</h3>
                    <p className="text-gray-600">
                        We collect and use information solely for the purpose of monitoring and maintaining our rainwater catchment system
                        and botanical garden operations. This includes sensor data, system logs, and user authentication information for
                        administrative access.
                    </p>
                </section>

                <section>
                    <h3 className="text-lg font-medium text-gray-900">Data Security</h3>
                    <p className="text-gray-600">
                        We implement appropriate security measures to protect against unauthorized access, alteration, disclosure,
                        or destruction of your personal information and data collected by our systems.
                    </p>
                </section>

                <section>
                    <h3 className="text-lg font-medium text-gray-900">Data Retention</h3>
                    <p className="text-gray-600">
                        Sensor data and system logs are retained for research and maintenance purposes. Personal information of
                        administrative users is retained only for as long as necessary to provide access to the system.
                    </p>
                </section>

                <section>
                    <h3 className="text-lg font-medium text-gray-900">User Rights</h3>
                    <p className="text-gray-600">
                        Administrative users have the right to:
                    </p>
                    <ul className="list-disc pl-6 text-gray-600">
                        <li>Access their personal information</li>
                        <li>Update or correct their information</li>
                        <li>Request deletion of their account</li>
                        <li>Receive an export of their data</li>
                    </ul>
                </section>

                <section>
                    <h3 className="text-lg font-medium text-gray-900">Contact Us</h3>
                    <p className="text-gray-600">
                        If you have any questions about this Privacy Policy, please contact us at bgh@buksu.edu.ph
                    </p>
                </section>
            </div>
        </div>
    );
} 