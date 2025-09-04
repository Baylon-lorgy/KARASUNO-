import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';

export default function History() {
    return (
        <DashboardLayout>
            <Head title="History" />
            
            <div className="space-y-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-800 mb-4">History</h2>
                    <p className="text-gray-600">View historical data and activities here.</p>
                </div>
            </div>
        </DashboardLayout>
    );
} 