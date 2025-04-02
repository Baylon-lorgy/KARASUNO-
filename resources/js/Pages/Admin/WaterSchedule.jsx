import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';

export default function WaterSchedule() {
    return (
        <DashboardLayout>
            <Head title="Water Schedule" />
            
            <div className="space-y-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-800 mb-4">Water Schedule</h2>
                    <p className="text-gray-600">Manage water scheduling and automation here.</p>
                </div>
            </div>
        </DashboardLayout>
    );
} 