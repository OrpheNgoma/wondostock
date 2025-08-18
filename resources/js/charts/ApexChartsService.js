/**
 * ApexCharts Service pour WondoStock Dashboard
 * 
 * Service professionnel pour la gestion des graphiques ApexCharts
 * Compatible avec Livewire 3.6.4 et Alpine.js
 * 
 * @author WondoStock Team
 * @version 1.0.0
 */

class ApexChartsService {
    constructor() {
        this.charts = new Map();
        this.defaultColors = [
            '#10B981', // Emerald-500 (primary)
            '#3B82F6', // Blue-500
            '#8B5CF6', // Violet-500
            '#F59E0B', // Amber-500
            '#EF4444', // Red-500
            '#06B6D4', // Cyan-500
            '#84CC16', // Lime-500
            '#F97316'  // Orange-500
        ];
        
        this.isReady = false;
        this.init();
    }

    /**
     * Initialise le service ApexCharts
     */
    init() {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts library not loaded');
            return;
        }
        
        // Configuration globale d'ApexCharts
        this.setGlobalConfig();
        this.isReady = true;
        
        console.log('✅ ApexChartsService initialized successfully');
    }

    /**
     * Configuration globale pour tous les graphiques
     */
    setGlobalConfig() {
        // Thème sombre/clair automatique
        const isDark = document.documentElement.classList.contains('dark');
        
        ApexCharts.exec = ApexCharts.exec || {};
        
        // Configuration par défaut pour tous les graphiques
        this.defaultOptions = {
            chart: {
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                foreColor: isDark ? '#E5E7EB' : '#374151',
                background: 'transparent',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                toolbar: {
                    show: false
                }
            },
            colors: this.defaultColors,
            grid: {
                borderColor: isDark ? '#374151' : '#E5E7EB',
                strokeDashArray: 3,
                xaxis: {
                    lines: {
                        show: false
                    }
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                }
            },
            legend: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
                labels: {
                    colors: isDark ? '#E5E7EB' : '#374151'
                }
            }
        };
    }

    /**
     * Crée un graphique linéaire pour les ventes
     * 
     * @param {string} elementId - ID de l'élément HTML
     * @param {Array} labels - Labels pour l'axe X
     * @param {Array} values - Valeurs pour l'axe Y  
     * @param {Object} options - Options supplémentaires
     */
    createLineChart(elementId, labels, values, options = {}) {
        if (!this.isReady) {
            console.error('ApexChartsService not ready');
            return null;
        }

        const element = document.getElementById(elementId);
        if (!element) {
            console.error(`Element with ID '${elementId}' not found`);
            return null;
        }

        // Configuration spécifique au graphique linéaire
        const chartOptions = this.mergeDeep(this.defaultOptions, {
            chart: {
                type: 'line',
                height: 320,
                sparkline: {
                    enabled: false
                }
            },
            series: [{
                name: options.seriesName || 'Chiffre d\'Affaires',
                data: values || []
            }],
            xaxis: {
                categories: labels || [],
                labels: {
                    style: {
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    formatter: (value) => {
                        return new Intl.NumberFormat('fr-FR', {
                            style: 'currency',
                            currency: 'XAF',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                    },
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    gradientToColors: ['#34D399'],
                    inverseColors: false,
                    opacityFrom: 0.8,
                    opacityTo: 0.1
                }
            },
            markers: {
                size: 6,
                colors: ['#10B981'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 8
                }
            },
            ...options
        });

        try {
            const chart = new ApexCharts(element, chartOptions);
            chart.render();
            
            this.charts.set(elementId, chart);
            console.log(`✅ Line chart '${elementId}' created successfully`);
            
            return chart;
        } catch (error) {
            console.error(`Error creating line chart '${elementId}':`, error);
            return null;
        }
    }

    /**
     * Crée un graphique donut pour les catégories
     * 
     * @param {string} elementId - ID de l'élément HTML  
     * @param {Array} labels - Labels des catégories
     * @param {Array} values - Valeurs des catégories
     * @param {Object} options - Options supplémentaires
     */
    createDonutChart(elementId, labels, values, options = {}) {
        if (!this.isReady) {
            console.error('ApexChartsService not ready');
            return null;
        }

        const element = document.getElementById(elementId);
        if (!element) {
            console.error(`Element with ID '${elementId}' not found`);
            return null;
        }

        // Configuration spécifique au graphique donut
        const chartOptions = this.mergeDeep(this.defaultOptions, {
            chart: {
                type: 'donut',
                height: 280
            },
            series: values || [],
            labels: labels || [],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '14px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 600
                            },
                            value: {
                                show: true,
                                fontSize: '16px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 700,
                                formatter: (val) => {
                                    return new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'XAF',
                                        minimumFractionDigits: 0
                                    }).format(val);
                                }
                            },
                            total: {
                                show: true,
                                showAlways: false,
                                label: 'Total',
                                fontSize: '14px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 600,
                                formatter: (w) => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'XAF',
                                        minimumFractionDigits: 0
                                    }).format(total);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    width: 8,
                    height: 8,
                    strokeWidth: 0,
                    radius: 2
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 250
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px'
                    }
                }
            }],
            ...options
        });

        try {
            const chart = new ApexCharts(element, chartOptions);
            chart.render();
            
            this.charts.set(elementId, chart);
            console.log(`✅ Donut chart '${elementId}' created successfully`);
            
            return chart;
        } catch (error) {
            console.error(`Error creating donut chart '${elementId}':`, error);
            return null;
        }
    }

    /**
     * Met à jour un graphique existant
     * 
     * @param {string} chartId - ID du graphique
     * @param {Object} newData - Nouvelles données
     */
    updateChart(chartId, newData) {
        const chart = this.charts.get(chartId);
        if (!chart) {
            console.error(`Chart with ID '${chartId}' not found`);
            return false;
        }

        try {
            if (newData.series) {
                chart.updateSeries(newData.series, true);
            }
            
            if (newData.options) {
                chart.updateOptions(newData.options, false, true);
            }
            
            console.log(`✅ Chart '${chartId}' updated successfully`);
            return true;
        } catch (error) {
            console.error(`Error updating chart '${chartId}':`, error);
            return false;
        }
    }

    /**
     * Détruit un graphique
     * 
     * @param {string} chartId - ID du graphique
     */
    destroyChart(chartId) {
        const chart = this.charts.get(chartId);
        if (chart) {
            chart.destroy();
            this.charts.delete(chartId);
            console.log(`✅ Chart '${chartId}' destroyed successfully`);
            return true;
        }
        return false;
    }

    /**
     * Détruit tous les graphiques
     */
    destroyAllCharts() {
        this.charts.forEach((chart, id) => {
            chart.destroy();
            console.log(`✅ Chart '${id}' destroyed`);
        });
        this.charts.clear();
    }

    /**
     * Redimensionne tous les graphiques (utile pour les changements de taille)
     */
    resizeAllCharts() {
        this.charts.forEach((chart) => {
            chart.resize();
        });
    }

    /**
     * Merge profond d'objets (utilitaire)
     */
    mergeDeep(target, ...sources) {
        if (!sources.length) return target;
        const source = sources.shift();

        if (this.isObject(target) && this.isObject(source)) {
            for (const key in source) {
                if (this.isObject(source[key])) {
                    if (!target[key]) Object.assign(target, { [key]: {} });
                    this.mergeDeep(target[key], source[key]);
                } else {
                    Object.assign(target, { [key]: source[key] });
                }
            }
        }

        return this.mergeDeep(target, ...sources);
    }

    /**
     * Vérifie si une valeur est un objet
     */
    isObject(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    }

    /**
     * Obtient le nombre de graphiques actifs
     */
    getChartsCount() {
        return this.charts.size;
    }

    /**
     * Obtient tous les IDs des graphiques actifs
     */
    getChartsIds() {
        return Array.from(this.charts.keys());
    }
}

// Export de la classe et création de l'instance globale
window.ApexChartsService = ApexChartsService;
window.apexChartsService = new ApexChartsService();

// Nettoyage automatique lors du déchargement de la page
window.addEventListener('beforeunload', () => {
    if (window.apexChartsService) {
        window.apexChartsService.destroyAllCharts();
    }
});

// Redimensionnement automatique lors du resize
window.addEventListener('resize', () => {
    if (window.apexChartsService) {
        clearTimeout(window.resizeTimeout);
        window.resizeTimeout = setTimeout(() => {
            window.apexChartsService.resizeAllCharts();
        }, 250);
    }
});

console.log('📊 ApexChartsService loaded successfully');