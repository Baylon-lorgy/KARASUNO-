import { useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import { format } from 'date-fns';
import axios from 'axios';
import { toast } from 'react-hot-toast';
import { 
    DocumentArrowDownIcon, 
    InformationCircleIcon,
    PhotoIcon,
    DocumentTextIcon,
    UserIcon,
    BuildingOfficeIcon,
    CalendarIcon,
    CogIcon
} from '@heroicons/react/24/outline';

export default function SensorReport() {
    const [startDate, setStartDate] = useState(format(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'));
    const [endDate, setEndDate] = useState(format(new Date(), 'yyyy-MM-dd'));
    const [reportFormat, setReportFormat] = useState('pdf');
    const [orientation, setOrientation] = useState('portrait');
    const [includeHeader, setIncludeHeader] = useState(true);
    const [includeLogo, setIncludeLogo] = useState(true);
    const [includeSignatories, setIncludeSignatories] = useState(true);
    const [clientName, setClientName] = useState('');
    const [reportTitle, setReportTitle] = useState('Sensor Data Report');
    const [isGenerating, setIsGenerating] = useState(false);

    const handleGenerateReport = async () => {
        setIsGenerating(true);
        try {
            const response = await axios.get(`/admin/pdf-report`, {
                params: {
                    start_date: startDate,
                    end_date: endDate,
                    orientation: orientation,
                    client_name: clientName,
                    report_title: reportTitle
                },
                responseType: 'blob'
            });
            
            // Check if the response is actually a PDF
            const contentType = response.headers['content-type'];
            if (contentType && contentType.includes('application/json')) {
                // This is an error response
                const reader = new FileReader();
                reader.onload = () => {
                    try {
                        const errorData = JSON.parse(reader.result);
                        toast.error(errorData.error || 'Failed to generate report');
                    } catch (e) {
                        toast.error('Failed to generate report');
                    }
                };
                reader.readAsText(response.data);
                return;
            }
            
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${reportTitle.toLowerCase().replace(/\s+/g, '-')}-${startDate}-to-${endDate}.${reportFormat}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            toast.success('Report generated successfully');
        } catch (error) {
            console.error('Error generating report:', error);
            if (error.response?.data) {
                // Try to read error message from response
                const reader = new FileReader();
                reader.onload = () => {
                    try {
                        const errorData = JSON.parse(reader.result);
                        toast.error(errorData.error || 'Failed to generate report');
                    } catch (e) {
                        toast.error('Failed to generate report');
                    }
                };
                reader.readAsText(error.response.data);
            } else {
                toast.error('Failed to generate report. Please try again.');
            }
        } finally {
            setIsGenerating(false);
        }
    };

    return (
        <DashboardLayout>
            <Head title="Sensor Report" />
            <div className="space-y-2">
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h2 className="text-xl font-bold text-gray-900 tracking-tight">Generate Report</h2>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Download sensor data reports with custom headers and signatories
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Create professional reports with custom headers, logos, and signatories</p>
                </div>
                
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200 max-w-4xl mx-auto">
                    <form
                        onSubmit={e => {
                            e.preventDefault();
                            handleGenerateReport();
                        }}
                    >
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            {/* Basic Report Settings */}
                            <div className="space-y-3">
                                <h3 className="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <DocumentTextIcon className="h-4 w-4" />
                                    Report Settings
                                </h3>
                                
                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">Report Title</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Custom title for your report
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input
                                        type="text"
                                        value={reportTitle}
                                        onChange={e => setReportTitle(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter report title"
                                    />
                                </div>

                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">Client Name</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Client name for the report header
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input
                                        type="text"
                                        value={clientName}
                                        onChange={e => setClientName(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter client name"
                                    />
                                </div>

                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">Start Date</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Select the beginning date for your report period
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input
                                        type="date"
                                        value={startDate}
                                        onChange={e => setStartDate(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">End Date</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Select the ending date for your report period
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input
                                        type="date"
                                        value={endDate}
                                        onChange={e => setEndDate(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                            </div>

                            {/* Format & Layout Settings */}
                            <div className="space-y-3">
                                <h3 className="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <CogIcon className="h-4 w-4" />
                                    Format & Layout
                                </h3>

                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">Format</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Choose the file format for your report download
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <select
                                        value={reportFormat}
                                        onChange={e => setReportFormat(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="pdf">PDF Document</option>
                                    </select>
                                </div>

                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <label className="block text-sm font-bold text-gray-900">Orientation</label>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Choose page orientation for your report
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="orientation"
                                                value="portrait"
                                                checked={orientation === 'portrait'}
                                                onChange={e => setOrientation(e.target.value)}
                                                className="text-blue-600 focus:ring-blue-500"
                                            />
                                            <span className="text-sm text-gray-700">Portrait</span>
                                        </label>
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="orientation"
                                                value="landscape"
                                                checked={orientation === 'landscape'}
                                                onChange={e => setOrientation(e.target.value)}
                                                className="text-blue-600 focus:ring-blue-500"
                                            />
                                            <span className="text-sm text-gray-700">Landscape</span>
                                        </label>
                                    </div>
                                </div>

                               
                            </div>
                        </div>

                        {/* Generate Button */}
                        <div className="mt-6 pt-4 border-t border-gray-200">
                            <button
                                type="submit"
                                disabled={isGenerating}
                                className="w-full px-4 py-3 bg-gradient-to-r from-[#09203f] to-[#537895] text-white text-sm font-bold rounded-lg hover:from-[#0a2a4a] hover:to-[#4a6b8a] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                            >
                                {isGenerating ? (
                                    <>
                                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                        Generating Report...
                                    </>
                                ) : (
                                    <>
                                        <DocumentArrowDownIcon className="h-4 w-4" />
                                        Generate Report
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
} 