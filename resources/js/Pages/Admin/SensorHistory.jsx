import { useState, useMemo } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import DataTable from 'react-data-table-component';
import { 
    InformationCircleIcon, 
    ArrowPathIcon, 
    ChartBarIcon,
    BeakerIcon,
    SunIcon,
    CloudIcon,
    MagnifyingGlassIcon,
    FunnelIcon
} from '@heroicons/react/24/outline';

export default function SensorHistory({ readings }) {
    const [expandedRows, setExpandedRows] = useState([]);
    const [filterText, setFilterText] = useState('');
    const [resetPaginationToggle, setResetPaginationToggle] = useState(false);
    const [loading, setLoading] = useState(false);

    // calculate stats from readings
    const stats = useMemo(() => {
        if (!readings || readings.length === 0) {
            return {
                totalReadings: 0,
                avgWaterLevel: 0,
                avgSoilMoisture: 0,
                avgTemperature: 0,
                avgHumidity: 0
            };
        }

        const totalReadings = readings.length;
        const avgWaterLevel = (readings.reduce((sum, r) => sum + parseFloat(r.water_level || 0), 0) / totalReadings).toFixed(1);
        const avgSoilMoisture = (readings.reduce((sum, r) => sum + parseFloat(r.soil_moisture || 0), 0) / totalReadings).toFixed(1);
        const avgTemperature = (readings.reduce((sum, r) => sum + parseFloat(r.temperature || 0), 0) / totalReadings).toFixed(1);
        const avgHumidity = (readings.reduce((sum, r) => sum + parseFloat(r.humidity || 0), 0) / totalReadings).toFixed(1);

        return {
            totalReadings,
            avgWaterLevel,
            avgSoilMoisture,
            avgTemperature,
            avgHumidity
        };
    }, [readings]);

    const columns = [
        {
            name: 'Date & Time',
            selector: row => row.date,
            sortable: true,
            cell: row => (
                <div>
                    <div className="text-xs font-medium text-[#1e293b]">{row.date}</div>
                    <div className="text-[10px] text-[#64748b]">{row.time}</div>
                </div>
            ),
        },
        {
            name: 'Water Level (%)',
            selector: row => parseFloat(row.water_level),
            sortable: true,
            cell: row => <div className="text-xs font-medium text-[#1e293b]">{row.water_level}%</div>,
        },
        {
            name: 'Soil Moisture (%)',
            selector: row => parseFloat(row.soil_moisture),
            sortable: true,
            cell: row => <div className="text-xs font-medium text-[#1e293b]">{row.soil_moisture}%</div>,
        },
        {
            name: 'Temperature (°C)',
            selector: row => parseFloat(row.temperature),
            sortable: true,
            cell: row => <div className="text-xs font-medium text-[#1e293b]">{row.temperature}°C</div>,
        },
        {
            name: 'Humidity (%)',
            selector: row => parseFloat(row.humidity),
            sortable: true,
            cell: row => <div className="text-xs font-medium text-[#1e293b]">{row.humidity}%</div>,
        },
        {
            name: '',
            cell: row => (
                <button
                    onClick={() => {
                        if (expandedRows.includes(row.id)) {
                            setExpandedRows(expandedRows.filter(id => id !== row.id));
                        } else {
                            setExpandedRows([...expandedRows, row.id]);
                        }
                    }}
                    className="text-[#2563eb] hover:text-[#1d4ed8]"
                >
                    <InformationCircleIcon className="h-4 w-4" />
                </button>
            ),
            width: '40px'
        },
    ];

    const ExpandedComponent = ({ data }) => (
        <div className="p-4 bg-gray-50 border-t border-gray-200">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 className="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <ChartBarIcon className="h-4 w-4 text-blue-500" />
                        Daily Averages
                    </h4>
                    <div className="space-y-2">
                        <div className="flex justify-between items-center py-2 px-3 bg-white rounded-lg border">
                            <span className="text-sm text-gray-600">Water Level</span>
                            <span className="text-sm font-medium text-blue-600">{data.averages?.water_level || 'N/A'}%</span>
                        </div>
                        <div className="flex justify-between items-center py-2 px-3 bg-white rounded-lg border">
                            <span className="text-sm text-gray-600">Soil Moisture</span>
                            <span className="text-sm font-medium text-green-600">{data.averages?.soil_moisture || 'N/A'}%</span>
                        </div>
                        <div className="flex justify-between items-center py-2 px-3 bg-white rounded-lg border">
                            <span className="text-sm text-gray-600">Temperature</span>
                            <span className="text-sm font-medium text-orange-600">{data.averages?.temperature || 'N/A'}°C</span>
                        </div>
                        <div className="flex justify-between items-center py-2 px-3 bg-white rounded-lg border">
                            <span className="text-sm text-gray-600">Humidity</span>
                            <span className="text-sm font-medium text-gray-600">{data.averages?.humidity || 'N/A'}%</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 className="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <InformationCircleIcon className="h-4 w-4 text-gray-500" />
                        Min/Max Values
                    </h4>
                    <div className="space-y-2">
                        <div className="py-2 px-3 bg-white rounded-lg border">
                            <div className="text-sm text-gray-600 mb-1">Water Level</div>
                            <div className="flex justify-between text-xs">
                                <span className="text-blue-500">Min: {data.min_max?.water_level?.min || 'N/A'}%</span>
                                <span className="text-blue-600">Max: {data.min_max?.water_level?.max || 'N/A'}%</span>
                            </div>
                        </div>
                        <div className="py-2 px-3 bg-white rounded-lg border">
                            <div className="text-sm text-gray-600 mb-1">Soil Moisture</div>
                            <div className="flex justify-between text-xs">
                                <span className="text-green-500">Min: {data.min_max?.soil_moisture?.min || 'N/A'}%</span>
                                <span className="text-green-600">Max: {data.min_max?.soil_moisture?.max || 'N/A'}%</span>
                            </div>
                        </div>
                        <div className="py-2 px-3 bg-white rounded-lg border">
                            <div className="text-sm text-gray-600 mb-1">Temperature</div>
                            <div className="flex justify-between text-xs">
                                <span className="text-orange-500">Min: {data.min_max?.temperature?.min || 'N/A'}°C</span>
                                <span className="text-orange-600">Max: {data.min_max?.temperature?.max || 'N/A'}°C</span>
                            </div>
                        </div>
                        <div className="py-2 px-3 bg-white rounded-lg border">
                            <div className="text-sm text-gray-600 mb-1">Humidity</div>
                            <div className="flex justify-between text-xs">
                                <span className="text-gray-500">Min: {data.min_max?.humidity?.min || 'N/A'}%</span>
                                <span className="text-gray-600">Max: {data.min_max?.humidity?.max || 'N/A'}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );

    const filteredItems = readings.filter(
        item => 
            item.date.toLowerCase().includes(filterText.toLowerCase()) ||
            item.water_level.toLowerCase().includes(filterText.toLowerCase()) ||
            item.soil_moisture.toLowerCase().includes(filterText.toLowerCase()) ||
            item.temperature.toLowerCase().includes(filterText.toLowerCase()) ||
            item.humidity.toLowerCase().includes(filterText.toLowerCase())
    );

    const handleClear = () => {
        if (filterText) {
            setResetPaginationToggle(!resetPaginationToggle);
            setFilterText('');
        }
    };

    const customStyles = {
        table: {
            style: {
                minHeight: '400px',
                borderRadius: '0.5rem',
                background: 'transparent',
            },
        },
        headRow: {
            style: {
                minHeight: '48px',
                backgroundColor: '#f8fafc',
                borderBottomWidth: '1px',
                borderBottomColor: '#e5e7eb',
            },
        },
        headCells: {
            style: {
                paddingLeft: '16px',
                paddingRight: '16px',
                paddingTop: '12px',
                paddingBottom: '12px',
                fontSize: '12px',
                fontWeight: '600',
                textTransform: 'uppercase',
                color: '#6b7280',
                background: '#f8fafc',
                letterSpacing: '0.05em',
            },
        },
        rows: {
            style: {
                minHeight: '48px',
                fontSize: '14px',
                backgroundColor: '#fff',
                transition: 'background 0.2s',
            },
            stripedStyle: {
                backgroundColor: '#f9fafb',
            },
            highlightOnHoverStyle: {
                backgroundColor: '#f3f4f6',
                cursor: 'pointer',
            },
        },
        cells: {
            style: {
                paddingLeft: '16px',
                paddingRight: '16px',
                paddingTop: '12px',
                paddingBottom: '12px',
                whiteSpace: 'nowrap',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
            },
        },
        pagination: {
            style: {
                fontSize: '14px',
                minHeight: '48px',
                borderTop: '1px solid #e5e7eb',
                background: '#f8fafc',
                padding: '12px 16px',
            },
            pageButtonsStyle: {
                fontSize: '14px',
                minHeight: '32px',
                padding: '6px 12px',
                borderRadius: '6px',
                margin: '0 2px',
            },
        },
    };

    return (
        <DashboardLayout>
            <Head title="Sensor History" />
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Sensor History</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                View historical sensor data and trends for analysis
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">View historical sensor data and trends for analysis</p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-5 gap-3">
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-600 mb-1">Total Readings</p>
                                <p className="text-lg font-bold text-gray-900">{stats.totalReadings}</p>
                            </div>
                            <ChartBarIcon className="h-6 w-6 text-blue-500" />
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-600 mb-1">Avg Water Level</p>
                                <p className="text-lg font-bold text-gray-900">{stats.avgWaterLevel}%</p>
                            </div>
                            <BeakerIcon className="h-6 w-6 text-blue-500" />
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-600 mb-1">Avg Soil Moisture</p>
                                <p className="text-lg font-bold text-gray-900">{stats.avgSoilMoisture}%</p>
                            </div>
                            <BeakerIcon className="h-6 w-6 text-green-500" />
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-600 mb-1">Avg Temperature</p>
                                <p className="text-lg font-bold text-gray-900">{stats.avgTemperature}°C</p>
                            </div>
                            <SunIcon className="h-6 w-6 text-orange-500" />
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-600 mb-1">Avg Humidity</p>
                                <p className="text-lg font-bold text-gray-900">{stats.avgHumidity}%</p>
                            </div>
                            <CloudIcon className="h-6 w-6 text-gray-500" />
                        </div>
                    </div>
                </div>
                {/* Data Table */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    {/* Search and Filter Bar */}
                    <div className="flex flex-col sm:flex-row gap-2 items-center justify-between mb-4">
                        <div className="flex items-center gap-2">
                            <div className="relative group">
                                <FunnelIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                    Filter sensor data by date, water level, soil moisture, temperature, or humidity
                                    <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                            <div className="relative">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Search sensor data..."
                                    value={filterText}
                                    onChange={e => setFilterText(e.target.value)}
                                    className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm w-64"
                                />
                            </div>
                            {filterText && (
                                <button
                                    onClick={handleClear}
                                    className="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm"
                                >
                                    Clear
                                </button>
                            )}
                        </div>
                        <div className="text-sm text-gray-500">
                            {filteredItems.length} of {readings.length} records
                        </div>
                    </div>

                    {loading ? (
                        <div className="flex items-center justify-center py-8">
                            <ArrowPathIcon className="h-8 w-8 text-blue-400 animate-spin" />
                            <span className="ml-3 text-blue-500 font-bold text-sm">Loading data...</span>
                        </div>
                    ) : (
                        <DataTable
                            columns={columns}
                            data={filteredItems}
                            expandableRows
                            expandableRowsComponent={ExpandedComponent}
                            expandableRowExpanded={row => expandedRows.includes(row.id)}
                            onRowExpandToggled={(toggled, row) => {
                                if (toggled) {
                                    setExpandedRows([...expandedRows, row.id]);
                                } else {
                                    setExpandedRows(expandedRows.filter(id => id !== row.id));
                                }
                            }}
                            pagination
                            paginationResetDefaultPage={resetPaginationToggle}
                            customStyles={customStyles}
                            responsive
                            highlightOnHover
                            pointerOnHover
                            noDataComponent={
                                <div className="text-center py-8">
                                    <ChartBarIcon className="mx-auto h-8 w-8 text-gray-300 mb-2" />
                                    <p className="text-sm text-gray-500 mb-1">No sensor data found</p>
                                    <p className="text-xs text-gray-400">Try adjusting your filters or check back later</p>
                                </div>
                            }
                        />
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
} 