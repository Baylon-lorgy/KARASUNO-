export default function ContactModal({ onClose }) {
    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-2xl font-semibold text-gray-800">Contact Us</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-gray-500">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div className="space-y-6">
                <div>
                    <h3 className="text-lg font-medium text-gray-900">Visit Us</h3>
                    <p className="mt-2 text-gray-600">
                        Botanical Garden & Herbarium<br />
                        Bukidnon State University<br />
                        Malaybalay City, Bukidnon<br />
                        Philippines
                    </p>
                </div>
                
                <div>
                    <h3 className="text-lg font-medium text-gray-900">Contact Information</h3>
                    <div className="mt-2 space-y-2">
                        <p className="flex items-center text-gray-600">
                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            bgh@buksu.edu.ph
                        </p>
                        <p className="flex items-center text-gray-600">
                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            (088) 813-5661
                        </p>
                    </div>
                </div>

                <div>
                    <h3 className="text-lg font-medium text-gray-900">Hours of Operation</h3>
                    <div className="mt-2 text-gray-600">
                        <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
                        <p>Saturday: 9:00 AM - 3:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>
    );
} 