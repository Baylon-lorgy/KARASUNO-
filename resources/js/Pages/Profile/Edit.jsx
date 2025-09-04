import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import Modal from '@/Components/Modal';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import { PencilSquareIcon } from '@heroicons/react/24/outline';

export default function Edit({ auth, mustVerifyEmail, status }) {
    const [showEditModal, setShowEditModal] = useState(false);
    const user = auth.user;
    const defaultProfileImage = 'https://ui-avatars.com/api/?name=User&background=2E7D32&color=fff';

    return (
        <DashboardLayout>
            <Head title="Profile Settings" />
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#f8fafc] to-[#e8f5e9] py-8">
                <div className="w-full max-w-xl mx-auto p-8 bg-white/95 backdrop-blur-xl shadow-2xl rounded-3xl border border-green-100 flex flex-col items-center relative">
                    <div className="absolute top-4 right-4">
                        <button
                            onClick={() => setShowEditModal(true)}
                            className="flex items-center gap-1 px-4 py-2 rounded-lg bg-green-50 text-green-800 hover:bg-green-100 shadow transition"
                        >
                            <PencilSquareIcon className="h-5 w-5" /> Edit Profile
                        </button>
                    </div>
                    <div className="flex flex-col items-center mb-6">
                        <div className="h-32 w-32 rounded-full overflow-hidden border-4 border-green-700 shadow-lg bg-gray-100 flex items-center justify-center">
                            <img
                                src={user.profile_photo_url || defaultProfileImage}
                                alt={user.name}
                                className="h-full w-full object-cover"
                            />
                        </div>
                        <h2 className="text-2xl font-bold text-green-800 mt-4">{user.name}</h2>
                        <p className="text-sm text-gray-500">{user.email}</p>
                    </div>
                  
                </div>
            </div>
            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} maxWidth="sm">
                <UpdateProfileInformationForm auth={auth} onClose={() => setShowEditModal(false)} />
            </Modal>
        </DashboardLayout>
    );
}
