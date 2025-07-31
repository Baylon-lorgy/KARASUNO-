import { useEffect, useRef, useState } from 'react';
import { Line, Bar, Doughnut } from 'react-chartjs-2';

export default function ResponsiveChart({ 
    type = 'line',
    data, 
    options = {},
    title = "Chart",
    height = 'auto',
    className = ""
}) {
    const [chartHeight, setChartHeight] = useState(height);
    const [isMobile, setIsMobile] = useState(false);
    const chartRef = useRef(null);

    useEffect(() => {
        const checkMobile = () => {
            setIsMobile(window.innerWidth < 768);
        };

        checkMobile();
        window.addEventListener('resize', checkMobile);

        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    useEffect(() => {
        if (height === 'auto') {
            setChartHeight(isMobile ? '300px' : '400px');
        }
    }, [isMobile, height]);

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: isMobile ? 8 : 20,
                    font: { 
                        size: isMobile ? 10 : 12, 
                        weight: '600' 
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1F2937',
                bodyColor: '#4B5563',
                borderColor: '#E5E7EB',
                borderWidth: 1,
                padding: isMobile ? 8 : 12,
                cornerRadius: 8,
                titleFont: { size: isMobile ? 11 : 13 },
                bodyFont: { size: isMobile ? 10 : 12 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: { 
                    font: { size: isMobile ? 9 : 11 },
                    maxTicksLimit: isMobile ? 5 : 10
                }
            },
            x: {
                grid: { display: false },
                ticks: { 
                    font: { size: isMobile ? 9 : 11 },
                    maxTicksLimit: isMobile ? 6 : 12
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        },
        elements: {
            point: {
                radius: isMobile ? 2 : 4,
                hoverRadius: isMobile ? 4 : 6
            },
            line: {
                tension: 0.4
            }
        }
    };

    const mergedOptions = {
        ...defaultOptions,
        ...options,
        plugins: {
            ...defaultOptions.plugins,
            ...options.plugins
        }
    };

    const renderChart = () => {
        const chartProps = {
            data,
            options: mergedOptions
        };

        switch (type) {
            case 'line':
                return <Line {...chartProps} />;
            case 'bar':
                return <Bar {...chartProps} />;
            case 'doughnut':
                return <Doughnut {...chartProps} />;
            default:
                return <Line {...chartProps} />;
        }
    };

    return (
        <div className={`card-responsive card-mobile ${className}`}>
            {title && (
                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-sm sm:text-base font-semibold text-gray-900">{title}</h3>
                </div>
            )}
            <div 
                className="chart-responsive"
                style={{ height: chartHeight }}
                ref={chartRef}
            >
                {renderChart()}
            </div>
        </div>
    );
} 