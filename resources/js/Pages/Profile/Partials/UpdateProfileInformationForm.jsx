import { useEffect, useState, useRef } from 'react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { useForm } from '@inertiajs/react';
import { PhotoIcon, CheckCircleIcon, KeyIcon, EyeIcon, EyeSlashIcon } from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

export default function UpdateProfileInformationForm({ auth, onClose }) {
    const user = auth.user;
    const defaultProfileImage = 'https://ui-avatars.com/api/?name=User&background=2E7D32&color=fff';
    const { data, setData, patch, processing, errors, reset } = useForm({
        name: user.name || '',
        email: user.email || '',
        photo: null,
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [previewUrl, setPreviewUrl] = useState(user.profile_photo_url || defaultProfileImage);
    const [photoError, setPhotoError] = useState('');
    const [saving, setSaving] = useState(false);
    const [saved, setSaved] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showNewPassword, setShowNewPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [showPasswordSection, setShowPasswordSection] = useState(false);
    const [formError, setFormError] = useState('');
    const fileInputRef = useRef(null);

    useEffect(() => {
        if (data.photo) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreviewUrl(reader.result);
            };
            reader.readAsDataURL(data.photo);
        } else {
            setPreviewUrl(user.profile_photo_url || defaultProfileImage);
        }
        // eslint-disable-next-line
    }, [data.photo]);

    const validatePhoto = (file) => {
        if (!file) return true;
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (!validTypes.includes(file.type)) {
            setPhotoError('Please upload a valid image file (JPEG, PNG, or GIF)');
            return false;
        }
        if (file.size > maxSize) {
            setPhotoError('Image size should be less than 2MB');
            return false;
        }
        setPhotoError('');
        return true;
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (validatePhoto(file)) {
            setData('photo', file);
        } else {
            setData('photo', null);
        }
    };

    const handleCancel = () => {
        setPhotoError('');
        setPreviewUrl(user.profile_photo_url || defaultProfileImage);
        reset();
        setShowPasswordSection(false);
        setFormError('');
        if (onClose) onClose();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setSaving(true);
        setFormError('');
        // Debug log: show what is being sent
        console.log('Submitting profile update:', data);
        patch(route('profile.update'), {
            onSuccess: () => {
                setSaving(false);
                setSaved(true);
                toast.success('Profile updated successfully!');
                setTimeout(() => setSaved(false), 2000);
                reset();
                if (onClose) onClose();
            },
            onError: (errors) => {
                setSaving(false);
                setFormError('Please check the highlighted fields.');
                toast.error('Failed to update profile. Please check the form for errors.');
                if (errors.photo) {
                    setPhotoError(errors.photo);
                }
            },
            preserveScroll: true,
            forceFormData: true,
        });
    };

    return (
        <form onSubmit={handleSubmit} className="p-0 w-full max-w-md">
            <div className="rounded-2xl bg-white shadow-xl border border-green-100 p-6 sm:p-8">
                <h3 className="text-xl font-bold mb-6 text-green-800 flex items-center gap-2">Edit Profile</h3>
                <div className="flex flex-col items-center mb-6">
                    <div className="relative h-28 w-28 rounded-full overflow-hidden border-4 border-green-700 shadow bg-gray-100 flex items-center justify-center">
                        <img
                            src={previewUrl}
                            alt={data.name}
                            className="h-full w-full object-cover"
                        />
                        <label htmlFor="photo" className="absolute bottom-2 right-2 bg-white/90 text-green-700 p-1 rounded-full shadow cursor-pointer hover:bg-green-50 transition">
                            <PhotoIcon className="h-5 w-5" />
                            <input
                                ref={fileInputRef}
                                type="file"
                                id="photo"
                                accept="image/jpeg,image/png,image/gif"
                                onChange={handleFileChange}
                                className="hidden"
                            />
                        </label>
                    </div>
                    {photoError && (
                        <span className="block text-xs text-red-500 mt-1 text-center">{photoError}</span>
                    )}
                </div>
                {formError && (
                    <div className="mb-4 text-center text-sm text-red-600 font-semibold">{formError}</div>
                )}
                <div className="mb-4">
                    <InputLabel htmlFor="name" value="Name" />
                    <TextInput
                        id="name"
                        value={data.name}
                        onChange={e => setData('name', e.target.value)}
                        required
                        className="mt-1 block w-full"
                    />
                    <InputError message={errors.name} className="mt-2" />
                </div>
                <div className="mb-4">
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={e => setData('email', e.target.value)}
                        required
                        className="mt-1 block w-full"
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>
                {/* Collapsible Password Section */}
                <div className="mb-2">
                    <button
                        type="button"
                        onClick={() => setShowPasswordSection((v) => !v)}
                        className="flex items-center gap-2 text-green-700 font-semibold focus:outline-none mb-2"
                    >
                        <KeyIcon className="h-5 w-5" />
                        {showPasswordSection ? 'Hide Password Fields' : 'Change Password'}
                        {showPasswordSection ? (
                            <EyeSlashIcon className="h-4 w-4" />
                        ) : (
                            <EyeIcon className="h-4 w-4" />
                        )}
                    </button>
                    {showPasswordSection && (
                        <div className="space-y-4 animate-fade-in">
                            <div>
                                <InputLabel htmlFor="current_password" value="Current Password" />
                                <div className="relative">
                                    <TextInput
                                        id="current_password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.current_password}
                                        onChange={e => setData('current_password', e.target.value)}
                                        className="mt-1 block w-full pr-10"
                                        autoComplete="current-password"
                                    />
                                    <button
                                        type="button"
                                        className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-green-700"
                                        onClick={() => setShowPassword((v) => !v)}
                                        tabIndex={-1}
                                    >
                                        {showPassword ? 'Hide' : 'Show'}
                                    </button>
                                </div>
                                <InputError message={errors.current_password} className="mt-2" />
                            </div>
                            <div>
                                <InputLabel htmlFor="password" value="New Password" />
                                <div className="relative">
                                    <TextInput
                                        id="password"
                                        type={showNewPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="mt-1 block w-full pr-10"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-green-700"
                                        onClick={() => setShowNewPassword((v) => !v)}
                                        tabIndex={-1}
                                    >
                                        {showNewPassword ? 'Hide' : 'Show'}
                                    </button>
                                </div>
                                <InputError message={errors.password} className="mt-2" />
                            </div>
                            <div>
                                <InputLabel htmlFor="password_confirmation" value="Confirm New Password" />
                                <div className="relative">
                                    <TextInput
                                        id="password_confirmation"
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        className="mt-1 block w-full pr-10"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-green-700"
                                        onClick={() => setShowConfirmPassword((v) => !v)}
                                        tabIndex={-1}
                                    >
                                        {showConfirmPassword ? 'Hide' : 'Show'}
                                    </button>
                                </div>
                                <InputError message={errors.password_confirmation} className="mt-2" />
                            </div>
                        </div>
                    )}
                </div>
                <div className="flex justify-end gap-2 mt-8">
                    <PrimaryButton type="submit" disabled={processing || saving} className="bg-green-700 hover:bg-green-800 px-6 py-2 text-base rounded-lg shadow-md">
                        {saving ? 'Saving...' : 'Save Changes'}
                    </PrimaryButton>
                    <button type="button" onClick={handleCancel} className="px-6 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 shadow-md">Cancel</button>
                </div>
                {saved && (
                    <span className="flex items-center gap-1 text-sm text-green-600 animate-fade-in mt-4">
                        <CheckCircleIcon className="h-5 w-5" /> Saved!
                    </span>
                )}
            </div>
        </form>
    );
}
