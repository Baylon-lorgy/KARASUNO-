export default function AboutModal({ onClose }) {
    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-2xl font-semibold text-gray-800">About Us</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-gray-500">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div className="prose max-w-none">
                <p className="mb-4">
                    Welcome to the Botanical Garden & Herbarium at Bukidnon State University. Our facility serves as a living museum 
                    and research center dedicated to the preservation, study, and celebration of plant life.
                </p>
                <p className="mb-4">
                    Our mission is to:
                </p>
                <ul className="list-disc pl-6 mb-4">
                    <li>Conserve and protect native plant species</li>
                    <li>Provide educational opportunities for students and researchers</li>
                    <li>Promote sustainable gardening practices</li>
                    <li>Maintain a diverse collection of plant specimens</li>
                    <li>Support environmental research and conservation efforts</li>
                </ul>
                <p>
                    Through our innovative rainwater catchment system, we demonstrate our commitment to sustainable practices
                    and environmental stewardship. This system helps us maintain our gardens while conserving precious water resources.
                </p>
            </div>
        </div>
    );
} 