import { useState, useMemo } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import DataTable from 'react-data-table-component';
import { ChevronDownIcon, InformationCircleIcon, ArrowPathIcon } from '@heroicons/react/24/outline';

export default function SensorHistory({ readings }) {
    const [expandedRows, setExpandedRows] = useState([]);
    const [filterText, setFilterText] = useState('');
    const [resetPaginationToggle, setResetPaginationToggle] = useState(false);

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
        <div className="p-3 bg-[#f8fafc]">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <h4 className="text-xs font-medium text-[#1e293b] mb-2">Daily Averages</h4>
                    <dl className="grid grid-cols-2 gap-1">
                        <dt className="text-[10px] text-[#64748b]">Water Level:</dt>
                        <dd className="text-[10px] text-[#1e293b]">{data.averages.water_level}%</dd>
                        <dt className="text-[10px] text-[#64748b]">Soil Moisture:</dt>
                        <dd className="text-[10px] text-[#1e293b]">{data.averages.soil_moisture}%</dd>
                        <dt className="text-[10px] text-[#64748b]">Temperature:</dt>
                        <dd className="text-[10px] text-[#1e293b]">{data.averages.temperature}°C</dd>
                        <dt className="text-[10px] text-[#64748b]">Humidity:</dt>
                        <dd className="text-[10px] text-[#1e293b]">{data.averages.humidity}%</dd>
                    </dl>
                </div>
                <div>
                    <h4 className="text-xs font-medium text-[#1e293b] mb-2">Min/Max Values</h4>
                    <dl className="grid grid-cols-3 gap-1">
                        <dt className="text-[10px] text-[#64748b] col-span-1">Water Level:</dt>
                        <dd className="text-[10px] text-[#1e293b]">Min: {data.min_max.water_level.min}%</dd>
                        <dd className="text-[10px] text-[#1e293b]">Max: {data.min_max.water_level.max}%</dd>
                        <dt className="text-[10px] text-[#64748b] col-span-1">Soil Moisture:</dt>
                        <dd className="text-[10px] text-[#1e293b]">Min: {data.min_max.soil_moisture.min}%</dd>
                        <dd className="text-[10px] text-[#1e293b]">Max: {data.min_max.soil_moisture.max}%</dd>
                        <dt className="text-[10px] text-[#64748b] col-span-1">Temperature:</dt>
                        <dd className="text-[10px] text-[#1e293b]">Min: {data.min_max.temperature.min}°C</dd>
                        <dd className="text-[10px] text-[#1e293b]">Max: {data.min_max.temperature.max}°C</dd>
                        <dt className="text-[10px] text-[#64748b] col-span-1">Humidity:</dt>
                        <dd className="text-[10px] text-[#1e293b]">Min: {data.min_max.humidity.min}%</dd>
                        <dd className="text-[10px] text-[#1e293b]">Max: {data.min_max.humidity.max}%</dd>
                    </dl>
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

    const subHeaderComponentMemo = useMemo(() => {
        return (
            <div className="flex justify-between items-center mb-6">
                <div className="relative">
                    <input
                        type="text"
                        className="w-64 px-4 py-3 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-[#2563eb] transition-all duration-200"
                        placeholder="Search sensor data..."
                        value={filterText}
                        onChange={e => setFilterText(e.target.value)}
                    />
                    {filterText && (
                        <button
                            onClick={handleClear}
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748b] hover:text-[#1e293b] transition-colors duration-200"
                        >
                            ×
                        </button>
                    )}
                </div>
            </div>
        );
    }, [filterText, resetPaginationToggle]);

    const customStyles = {
        table: {
            style: {
                minHeight: '400px',
                borderRadius: '1rem',
                background: 'transparent',
            },
        },
        headRow: {
            style: {
                minHeight: '50px',
                backgroundColor: '#f8fafc',
                borderBottomWidth: '2px',
                borderBottomColor: '#e2e8f0',
                position: 'sticky',
                top: 0,
                zIndex: 2,
            },
        },
        headCells: {
            style: {
                paddingLeft: '20px',
                paddingRight: '20px',
                paddingTop: '16px',
                paddingBottom: '16px',
                fontSize: '14px',
                fontWeight: '600',
                textTransform: 'uppercase',
                color: '#64748b',
                background: '#f8fafc',
                letterSpacing: '0.05em',
            },
        },
        rows: {
            style: {
                minHeight: '50px',
                fontSize: '14px',
                backgroundColor: '#fff',
                transition: 'background 0.2s',
            },
            stripedStyle: {
                backgroundColor: '#f1f5f9',
            },
            highlightOnHoverStyle: {
                backgroundColor: '#e0e7ef',
                cursor: 'pointer',
            },
        },
        cells: {
            style: {
                paddingLeft: '20px',
                paddingRight: '20px',
                paddingTop: '14px',
                paddingBottom: '14px',
                whiteSpace: 'nowrap',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
            },
        },
        pagination: {
            style: {
                fontSize: '14px',
                minHeight: '50px',
                borderTop: '2px solid #e5e7eb',
                background: '#f8fafc',
                padding: '16px 20px',
            },
            pageButtonsStyle: {
                fontSize: '14px',
                minHeight: '36px',
                padding: '8px 16px',
                borderRadius: '8px',
                margin: '0 4px',
            },
        },
    };

    // Loading spinner for future async data
    const [loading, setLoading] = useState(false);

    return (
        <DashboardLayout>
            <Head title="Sensor History" />
            <div className="space-y-2">
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h2 className="text-xl font-bold text-gray-900 tracking-tight">Sensor History</h2>
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
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
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
                            subHeader
                            subHeaderComponent={
                                <div className="flex flex-col sm:flex-row gap-2 items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Filter sensor data by date, water level, soil moisture, temperature, or humidity
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                        <input
                                            type="text"
                                            placeholder="Filter by date..."
                                            value={filterText}
                                            onChange={e => setFilterText(e.target.value)}
                                            className="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs"
                                        />
                                        <button
                                            onClick={handleClear}
                                            className="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                    <div className="text-xs text-gray-500">
                                        {filteredItems.length} of {readings.length} records
                                    </div>
                                </div>
                            }
                            customStyles={customStyles}
                            responsive
                            highlightOnHover
                            pointerOnHover
                            noDataComponent={
                                <div className="text-center py-8">
                                    <InformationCircleIcon className="mx-auto h-8 w-8 text-gray-300 mb-2" />
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