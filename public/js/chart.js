// public/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
    const rawData = window.dataJamkot;
    if (!rawData || rawData.length === 0) {
        if (document.querySelector("#chart-jamkot")) {
            document.querySelector("#chart-jamkot").innerHTML =
                "<div style='text-align: center; color: #6b7280; padding: 2rem 0; font-size: 0.875rem;'>Belum ada data sensor untuk menampilkan grafik.</div>";
        }
    } else {
        const chartData = rawData;
        const waktuLabels = chartData.map(item => {
            let date = new Date(item.created_at);
            return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        });

        const suhuSeries = chartData.map(item => item.suhu);
        const kelembapanSeries = chartData.map(item => item.kelembapan);
        const warnaUtama = getComputedStyle(document.documentElement).getPropertyValue('--warna-utama').trim() || '#10b981';

        const isLightMode = document.body.classList.contains('light-mode');
        const gridColor = isLightMode ? '#e5e7eb' : '#1f1f1f';
        const labelColor = isLightMode ? '#6b7280' : '#6b7280';
        const textColor = isLightMode ? '#111827' : '#ededed';

        var optionsArea = {
            series: [
                { name: 'Kelembapan (%)', data: kelembapanSeries },
                { name: 'Suhu (°C)', data: suhuSeries }
            ],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 900,
                    animateGradually: {
                        enabled: true,
                        delay: 120
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 450
                    }
                }
            },
            theme: { mode: isLightMode ? 'light' : 'dark' },
            colors: [warnaUtama, '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: waktuLabels,
                labels: { style: { colors: labelColor } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: labelColor } }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            legend: { position: 'top', horizontalAlign: 'right' }
        };

        if (document.querySelector("#chart-jamkot")) {
            window.chartArea = new ApexCharts(document.querySelector("#chart-jamkot"), optionsArea);
            window.chartArea.render();
        }
    }

    const currentSuhu = window.currentSuhu || 0;
    const currentKelembapan = window.currentKelembapan || 0;
    if (document.querySelector("#gauge-suhu")) {
        var optionsGaugeSuhu = {
            series: [currentSuhu],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#10b981'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: isLightMode ? '#e5e7eb' : '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -30,
                            fontSize: '40px',
                            fontWeight: 600,
                            color: textColor,
                            formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; }
                        }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#ef4444'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'square' }
        };
        new ApexCharts(document.querySelector("#gauge-suhu"), optionsGaugeSuhu).render();
    }

    if (document.querySelector("#gauge-kelembapan")) {
        var optionsGaugeKelembapan = {
            series: [currentKelembapan],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#ef4444'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: isLightMode ? '#e5e7eb' : '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -30,
                            fontSize: '40px',
                            fontWeight: 600,
                            color: textColor,
                            formatter: function (val) { return (Number(val).toFixed(1)) + "%"; }
                        }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#3b82f6'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'square' }
        };
        new ApexCharts(document.querySelector("#gauge-kelembapan"), optionsGaugeKelembapan).render();
    }

    const avgSuhu = window.avgSuhu || 0;
    const avgKelembapan = window.avgKelembapan || 0;
    if (document.querySelector("#gauge-avg-suhu")) {
        var optionsAvgSuhu = {
            series: [avgSuhu],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#10b981'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: isLightMode ? '#e5e7eb' : '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: textColor, formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; } }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#ef4444'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'round' }
        };
        new ApexCharts(document.querySelector("#gauge-avg-suhu"), optionsAvgSuhu).render();
    }

    if (document.querySelector("#gauge-avg-kelembapan")) {
        var optionsAvgKelembapan = {
            series: [avgKelembapan],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#ef4444'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: isLightMode ? '#e5e7eb' : '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: textColor, formatter: function (val) { return (Number(val).toFixed(1)) + "%"; } }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#3b82f6'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'round' }
        };
        new ApexCharts(document.querySelector("#gauge-avg-kelembapan"), optionsAvgKelembapan).render();
    }

    window.addEventListener('themeChanged', function() {
        if (window.chartArea) {
            const isLightMode = document.body.classList.contains('light-mode');
            window.chartArea.updateOptions({
                theme: { mode: isLightMode ? 'light' : 'dark' },
                grid: { borderColor: isLightMode ? '#e5e7eb' : '#1f1f1f' }
            });
            // Due to limitations with RadialBar partial updates in ApexCharts, 
            // a full reload is safer if radial charts are present to ensure they adopt the new track/textColor
            if (document.querySelector(".meter-card")) {
                setTimeout(() => location.reload(), 100);
            }
        }
    });

});
