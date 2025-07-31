import { useEffect, useState } from 'react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { useForm } from '@inertiajs/react';

export default function UpdatePasswordForm({ className = '' }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [showCurrent, setShowCurrent] = useState(false);
    const [showNew, setShowNew] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    useEffect(() => {
        return () => {
            reset('current_password', 'password', 'password_confirmation');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('password.update'));
    };

    return (
        <form onSubmit={submit} className={`max-w-xl mx-auto bg-white/95 shadow-xl rounded-2xl p-6 sm:p-8 border border-green-100 ${className}`}>
            <h2 className="text-xl font-bold text-[#1B5E20] mb-6 text-center">Change Password</h2>
            <div className="space-y-6">
                <div>
                    <InputLabel htmlFor="current_password" value="Current Password" className="text-gray-700" />
                    <div className="relative">
                        <TextInput
                            id="current_password"
                            type={showCurrent ? 'text' : 'password'}
                            className="mt-1 block w-full border-gray-300 focus:border-[#1B5E20] focus:ring-[#1B5E20] rounded-xl shadow-sm pr-10"
                            value={data.current_password}
                            onChange={(e) => setData('current_password', e.target.value)}
                            required
                        />
                        <button
                            type="button"
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-[#1B5E20]"
                            onClick={() => setShowCurrent((v) => !v)}
                            tabIndex={-1}
                        >
                            {showCurrent ? 'Hide' : 'Show'}
                        </button>
                    </div>
                    <InputError message={errors.current_password} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password" value="New Password" className="text-gray-700" />
                    <div className="relative">
                        <TextInput
                            id="password"
                            type={showNew ? 'text' : 'password'}
                            className="mt-1 block w-full border-gray-300 focus:border-[#1B5E20] focus:ring-[#1B5E20] rounded-xl shadow-sm pr-10"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            required
                        />
                        <button
                            type="button"
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-[#1B5E20]"
                            onClick={() => setShowNew((v) => !v)}
                            tabIndex={-1}
                        >
                            {showNew ? 'Hide' : 'Show'}
                        </button>
                    </div>
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" className="text-gray-700" />
                    <div className="relative">
                        <TextInput
                            id="password_confirmation"
                            type={showConfirm ? 'text' : 'password'}
                            className="mt-1 block w-full border-gray-300 focus:border-[#1B5E20] focus:ring-[#1B5E20] rounded-xl shadow-sm pr-10"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            required
                        />
                        <button
                            type="button"
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-[#1B5E20]"
                            onClick={() => setShowConfirm((v) => !v)}
                            tabIndex={-1}
                        >
                            {showConfirm ? 'Hide' : 'Show'}
                        </button>
                    </div>
                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="flex items-center gap-4 mt-4">
                    <PrimaryButton 
                        disabled={processing}
                        className="bg-[#1B5E20] hover:bg-[#388E3C] focus:bg-[#388E3C] active:bg-[#1B5E20] rounded-xl px-6 py-2 text-base"
                    >
                        {processing ? 'Saving...' : 'Save Changes'}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    );
}
