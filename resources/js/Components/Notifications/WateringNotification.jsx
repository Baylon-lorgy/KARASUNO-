import React from 'react';
import { format } from 'date-fns';

const WateringNotification = ({ notification }) => {
    const data = notification.data;
    const isFailed = data.status === 'FAILED';

    return (
        <div className={`p-4 rounded-lg shadow-md ${isFailed ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'}`}>
            <div className="flex items-start">
                <div className="flex-shrink-0">
                    {isFailed ? (
                        <svg className="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    ) : (
                        <svg className="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    )}
                </div>
                <div className="ml-3 w-0 flex-1">
                    <p className="text-sm font-medium text-gray-900">
                        {data.message}
                    </p>
                    <div className="mt-1 text-sm text-gray-500">
                        <p>Schedule: {data.schedule}</p>
                        <p>Duration: {data.duration}</p>
                        <p>Status: <span className={`font-semibold ${isFailed ? 'text-red-600' : 'text-green-600'}`}>
                            {data.status}
                        </span></p>
                        {isFailed && (
                            <div className="mt-2">
                                <p className="font-medium text-red-600">Recommendations:</p>
                                <p className="text-red-600">Manual watering REQUIRED.</p>
                            </div>
                        )}
                    </div>
                    <div className="mt-2 text-xs text-gray-400">
                        {format(new Date(notification.created_at), 'MMM d, yyyy h:mm a')}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default WateringNotification; 