<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStick Pro | IoT Monitoring Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce',
                            800: '#6b21a8',
                            900: '#581c87',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    },
                    animation: {
                        'gradient': 'gradient 3s ease infinite',
                        'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-in': 'slide-in 0.5s ease-out',
                        'fade-in': 'fade-in 0.8s ease-out',
                    },
                    keyframes: {
                        'gradient': {
                            '0%, 100%': {
                                'background-size': '200% 200%',
                                'background-position': 'left center'
                            },
                            '50%': {
                                'background-size': '200% 200%',
                                'background-position': 'right center'
                            }
                        },
                        'pulse-glow': {
                            '0%, 100%': {
                                opacity: '1',
                                transform: 'scale(1)',
                                'box-shadow': '0 0 20px rgba(14, 165, 233, 0.3)'
                            },
                            '50%': {
                                opacity: '0.8',
                                transform: 'scale(1.02)',
                                'box-shadow': '0 0 40px rgba(14, 165, 233, 0.6)'
                            }
                        },
                        'float': {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-20px)'
                            }
                        },
                        'slide-in': {
                            '0%': {
                                transform: 'translateX(-20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            }
                        },
                        'fade-in': {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        }
                    },
                    gridTemplateColumns: {
                        'dashboard': '1fr 300px',
                        'metrics': 'repeat(auto-fit, minmax(280px, 1fr))',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #0ea5e9 0%, #a855f7 100%);
            border-radius: 4px;
        }

        /* Glass effect */
        .glass {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #a855f7 50%, #ec4899 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            background-size: 200% auto;
        }

        /* Card hover effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Status indicators */
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Custom animations */
        .animate-gradient {
            background-size: 200% auto;
            animation: gradient 3s linear infinite;
        }

        /* Data visualization */
        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
        }
    </style>
</head>

<body class="bg-dark-950 text-gray-100 min-h-screen overflow-x-hidden">
    <!-- Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/3 w-60 h-60 bg-primary-500/5 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Main Layout -->
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Header -->
        <header class="mb-8" data-aos="fade-down">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center shadow-xl animate-pulse-glow">
                            <i class="fas fa-satellite text-2xl text-white"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-dark-950"></div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">
                            <span class="bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
                                SAFE STEP V2
                            </span>
                            <span class="bg-gradient-to-r from-red-400 to-pink-500 bg-clip-text text-transparent">
                                - NUGADUH STACK
                            </span>
                        </h1>
                        <p class="text-gray-400">IoT Smart Stick Monitoring Dashboard</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="glass rounded-xl px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="status-indicator bg-green-500 animate-pulse"></div>
                                <span class="text-sm font-medium">Live</span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Last Update</p>
                                <p class="font-mono font-semibold" id="updateTime">--:--:--</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <!-- Ultrasonic 1 -->
            <div class="glass rounded-2xl p-6 hover-lift" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                                <i class="fas fa-wave-square text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Ultrasonic #1</h3>
                                <p class="text-xs text-gray-400">Distance Sensor</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-semibold">
                        ≤ 50cm
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-bold" id="distance1Value">--</span>
                        <span class="text-gray-400 mb-1">cm</span>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm text-gray-400 mb-2">
                            <span>Distance Level</span>
                            <span id="distance1StatusText">Normal</span>
                        </div>
                        <div class="h-2 bg-dark-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full transition-all duration-500"
                                id="distance1Bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-dark-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-bell text-blue-400"></i>
                                <span class="text-sm text-gray-400">Alert Pattern</span>
                            </div>
                            <span class="text-sm font-medium">Continuous Buzzer</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ultrasonic 2 -->
            <div class="glass rounded-2xl p-6 hover-lift" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                                <i class="fas fa-wave-square text-purple-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Ultrasonic #2</h3>
                                <p class="text-xs text-gray-400">Proximity Sensor</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-400 text-xs font-semibold">
                        ≤ 20cm
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-bold" id="distance2Value">--</span>
                        <span class="text-gray-400 mb-1">cm</span>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm text-gray-400 mb-2">
                            <span>Proximity Level</span>
                            <span id="distance2StatusText">Normal</span>
                        </div>
                        <div class="h-2 bg-dark-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-pink-400 rounded-full transition-all duration-500"
                                id="distance2Bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-dark-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-purple-400"></i>
                                <span class="text-sm text-gray-400">Alert Pattern</span>
                            </div>
                            <span class="text-sm font-medium">2s ON / 1s OFF</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Soil Moisture -->
            <div class="glass rounded-2xl p-6 hover-lift" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                                <i class="fas fa-tint text-emerald-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Soil Moisture</h3>
                                <p class="text-xs text-gray-400">Wet/Dry Detection</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="space-y-4">
                    <div class="flex justify-center">
                        <div class="relative w-32 h-32">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#1e293b" stroke-width="10" />
                                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#soilGradient)"
                                    stroke-width="10" stroke-linecap="round" stroke-dasharray="283"
                                    stroke-dashoffset="283" id="soilCircle" />
                                <defs>
                                    <linearGradient id="soilGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#10b981" />
                                        <stop offset="100%" stop-color="#0ea5e9" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <i class="fas fa-leaf text-3xl mb-2" id="soilIcon"></i>
                                <span class="text-2xl font-bold" id="soilStatusText">--</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-dark-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Sensor State</span>
                            <span class="text-sm font-medium" id="soilState">Unknown</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Alert Pattern</span>
                            <span class="text-sm font-medium">1s ON / 1s OFF</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Control -->
            <div class="glass rounded-2xl p-6 hover-lift" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                                <i class="fas fa-sliders-h text-amber-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">System On</h3>
                                <p class="text-xs text-gray-400">System indicators</p>
                            </div>
                        </div>
                    </div>
                    <!-- Status Badge yang berubah sesuai database -->
                    <div class="px-3 py-1 rounded-full bg-green-500/20 text-xs font-semibold" id="systemBadge">
                        Connected
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex justify-center">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-dark-800 to-dark-900 
                          flex items-center justify-center shadow-inner">
                                <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-dark-700 to-dark-800 
                              flex items-center justify-center">
                                    <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-dark-600 to-dark-700 
                                  flex items-center justify-center cursor-pointer hover:opacity-90 
                                  transition-opacity" id="powerButton">
                                        <i class="fas fa-power-off text-2xl" id="systemIcon"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Glow effect hanya saat ON -->
                            <div class="absolute inset-0 rounded-2xl border-2 border-transparent" id="systemGlow"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <!-- Status Text yang berubah ACTIVE/INACTIVE -->
                        <div class="text-3xl font-bold mb-2" id="systemStatusText">--</div>
                        <div class="text-sm text-gray-400">System Status</div>
                    </div>

                    <!-- Status Details -->
                    <div class="pt-4 border-t border-dark-800 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">
                                <i class="fas fa-toggle-on mr-2"></i>Database
                            </span>
                            <span class="font-medium" id="systemDbStatus">smart_stick_db</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">
                                <i class="fas fa-music mr-2"></i>Toggle Feedback
                            </span>
                            <span class="font-medium">1s Beep</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status & Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- System Stats -->
            <div class="glass rounded-2xl p-6 lg:col-span-2" data-aos="fade-up">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold">System Analytics</h3>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-sm">Online</span>
                        </div>
                        <span class="text-gray-400">•</span>
                        <span class="text-sm text-gray-400">Updated every 2s</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-dark-800/50 rounded-xl p-4">
                        <div class="text-sm text-gray-400 mb-1">Uptime</div>
                        <div class="text-2xl font-bold font-mono" id="uptime">00:00:00</div>
                    </div>
                    <div class="bg-dark-800/50 rounded-xl p-4">
                        <div class="text-sm text-gray-400 mb-1">Data Points</div>
                        <div class="text-2xl font-bold" id="dataPoints">0</div>
                    </div>
                    <div class="bg-dark-800/50 rounded-xl p-4">
                        <div class="text-sm text-gray-400 mb-1">Active Sensors</div>
                        <div class="text-2xl font-bold" id="activeSensors">4</div>
                    </div>
                    <div class="bg-dark-800/50 rounded-xl p-4">
                        <div class="text-sm text-gray-400 mb-1">Alert Status</div>
                        <div class="text-2xl font-bold text-green-400" id="alertStatus">Normal</div>
                    </div>
                </div>

                <!-- Sensor Status Grid -->
                <div class="mt-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-dark-800/30 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-400">Ultrasonic #1</span>
                                <div class="w-2 h-2 rounded-full bg-green-500" id="sensor1Status"></div>
                            </div>
                            <div class="text-xl font-bold" id="sensor1Value">-- cm</div>
                        </div>
                        <div class="bg-dark-800/30 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-400">Ultrasonic #2</span>
                                <div class="w-2 h-2 rounded-full bg-green-500" id="sensor2Status"></div>
                            </div>
                            <div class="text-xl font-bold" id="sensor2Value">-- cm</div>
                        </div>
                        <div class="bg-dark-800/30 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-400">Soil Sensor</span>
                                <div class="w-2 h-2 rounded-full bg-green-500" id="sensor3Status"></div>
                            </div>
                            <div class="text-xl font-bold" id="sensor3Value">--</div>
                        </div>
                        <div class="bg-dark-800/30 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-400">Touch Sensor</span>
                                <div class="w-2 h-2 rounded-full bg-green-500" id="sensor4Status"></div>
                            </div>
                            <div class="text-xl font-bold" id="sensor4Value">--</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
                <h3 class="text-lg font-semibold mb-6">Quick Actions</h3>

                <div class="space-y-4">
                    <button onclick="updateSensorData()" class="w-full flex items-center justify-between p-4 rounded-xl 
                            bg-primary-600/20 hover:bg-primary-600/30 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-500/20 flex items-center justify-center">
                                <i class="fas fa-sync-alt text-primary-400"></i>
                            </div>
                            <div>
                                <div class="font-medium">Refresh Data</div>
                                <div class="text-sm text-gray-400">Force update sensor readings</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-primary-400"></i>
                    </button>

                    <button onclick="exportData()" class="w-full flex items-center justify-between p-4 rounded-xl 
                            bg-dark-800/50 hover:bg-dark-800/70 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-file-export text-green-400"></i>
                            </div>
                            <div>
                                <div class="font-medium">Export Data</div>
                                <div class="text-sm text-gray-400">Download sensor history</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-400"></i>
                    </button>

                    <button onclick="showSettings()" class="w-full flex items-center justify-between p-4 rounded-xl 
                            bg-dark-800/50 hover:bg-dark-800/70 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                <i class="fas fa-cog text-purple-400"></i>
                            </div>
                            <div>
                                <div class="font-medium">Settings</div>
                                <div class="text-sm text-gray-400">Configure dashboard</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-400"></i>
                    </button>

                    <button onclick="viewDocumentation()" class="w-full flex items-center justify-between p-4 rounded-xl 
                            bg-dark-800/50 hover:bg-dark-800/70 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                                <i class="fas fa-book text-amber-400"></i>
                            </div>
                            <div>
                                <div class="font-medium">Documentation</div>
                                <div class="text-sm text-gray-400">View system docs</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-amber-400"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Data History Table -->
        <div class="glass rounded-2xl p-6 mb-8" data-aos="fade-up">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-history text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-1">Sensor Data History</h3>
                        <p class="text-sm text-gray-400">Real-time readings from connected sensors</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-dark-800">
                <table class="w-full">
                    <thead class="bg-dark-800/50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span>Timestamp</span>
                                </div>
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-wave-square"></i>
                                    <span>US #1</span>
                                </div>
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-radar"></i>
                                    <span>US #2</span>
                                </div>
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tint"></i>
                                    <span>Soil</span>
                                </div>
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-power-off"></i>
                                    <span>System</span>
                                </div>
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Trend</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800" id="tableBody">
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-dark-800 flex items-center justify-center mb-4">
                                        <i class="fas fa-spinner fa-spin text-2xl text-primary-400"></i>
                                    </div>
                                    <p class="text-gray-400">Loading sensor data...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-400">
                    Showing <span id="dataCount">0</span> records
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="glass rounded-2xl p-6 border border-dark-800">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-600 to-secondary-600 
                                  flex items-center justify-center">
                            <i class="fas fa-microchip text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">SmartStick Pro</h4>
                            <p class="text-sm text-gray-400">IoT Monitoring System v3.0</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-400 mb-1">Device ID</div>
                        <div class="font-mono text-sm">ESP8266-NodeMCU</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-400 mb-1">API Endpoint</div>
                        <div class="font-mono text-sm">/smart_stick/api.php</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-400 mb-1">Update Rate</div>
                        <div class="font-semibold">2s</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="#" class="text-gray-400 hover:text-primary-400 transition-colors" title="Arduino">
                        <i class="fab fa-arduino text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-green-400 transition-colors" title="ESP8266">
                        <i class="fas fa-microchip text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors" title="MySQL">
                        <i class="fas fa-database text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors" title="PHP">
                        <i class="fab fa-php text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors" title="JavaScript">
                        <i class="fab fa-js text-xl"></i>
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-dark-800 text-center">
                <p class="text-sm text-gray-500">
                    © 2024 SmartStick IoT Monitoring System. All rights reserved.
                    <span class="mx-2">•</span>
                    <span class="text-primary-400">Real-time Dashboard</span>
                </p>
            </div>
        </footer>
    </div>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>

    <!-- JavaScript -->
    <script>
        // DOM Elements
        const elements = {
            distance1Value: document.getElementById('distance1Value'),
            distance2Value: document.getElementById('distance2Value'),
            soilStatusText: document.getElementById('soilStatusText'),
            systemStatusText: document.getElementById('systemStatusText'),
            updateTime: document.getElementById('updateTime'), // LAST UPDATE ELEMENT
            uptime: document.getElementById('uptime'),
            dataPoints: document.getElementById('dataPoints'),
            alertStatus: document.getElementById('alertStatus'),
            tableBody: document.getElementById('tableBody'),
            dataCount: document.getElementById('dataCount'),
            activeSensors: document.getElementById('activeSensors')
        };

        // Variables
        let startTime = Date.now();
        let dataCount = 0;
        let lastDataTime = null; // Waktu terakhir data diterima dari server
        let lastUpdateInterval = null; // Interval untuk update waktu
        let fetchInterval = 2000; // Interval fetch data (2 detik)
        let isConnected = false; // Status koneksi

        // Initialize
        function init() {
            updateUptime();
            updateSensorData(); // Fetch data pertama
            startRealTimeClock(); // Mulai real-time clock
            setInterval(updateUptime, 1000);
            setInterval(updateSensorData, fetchInterval); // Fetch data berkala
        }

        // ==================== PERBAIKAN: START REAL-TIME CLOCK ====================
        function startRealTimeClock() {
            // Update waktu setiap detik
            if (lastUpdateInterval) clearInterval(lastUpdateInterval);

            lastUpdateInterval = setInterval(() => {
                updateLastUpdateDisplay();
            }, 1000);
        }

        // Update last update display dengan countdown
        function updateLastUpdateDisplay() {
            if (!elements.updateTime) return;

            if (lastDataTime) {
                // Hitung berapa lama sejak data terakhir
                const now = Date.now();
                const secondsSinceUpdate = Math.floor((now - lastDataTime) / 1000);

                // Jika data baru (< 60 detik), tampilkan waktu data
                if (secondsSinceUpdate < 60) {
                    const date = new Date(lastDataTime);
                    const timeStr = date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    });
                    elements.updateTime.textContent = timeStr;

                    // Beri warna normal
                    elements.updateTime.className = "font-mono font-semibold text-green-400";
                }
                // Jika data sudah lama (60-120 detik), tampilkan dengan warning
                else if (secondsSinceUpdate < 120) {
                    const minutes = Math.floor(secondsSinceUpdate / 60);
                    elements.updateTime.textContent = `${minutes}m ago`;
                    elements.updateTime.className = "font-mono font-semibold text-yellow-400";
                }
                // Jika data sangat lama (> 120 detik), tampilkan dengan danger
                else {
                    const minutes = Math.floor(secondsSinceUpdate / 60);
                    elements.updateTime.textContent = `${minutes}m ago (Offline)`;
                    elements.updateTime.className = "font-mono font-semibold text-red-400 animate-pulse";
                }
            } else {
                // Belum ada data sama sekali
                elements.updateTime.textContent = "--:--:--";
                elements.updateTime.className = "font-mono font-semibold text-gray-400";
            }
        }
        // ==================== END PERBAIKAN ====================

        // Update uptime
        function updateUptime() {
            const now = Date.now();
            const diff = now - startTime;
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);

            elements.uptime.textContent =
                `${hours.toString().padStart(2, '0')}:` +
                `${minutes.toString().padStart(2, '0')}:` +
                `${seconds.toString().padStart(2, '0')}`;
        }

        // Update sensor data
        async function updateSensorData() {
            try {
                const response = await fetch(`get_data.php?_=${Date.now()}`); // Cache buster
                const data = await response.json();

                if (data.status === 'success') {
                    isConnected = true;
                    lastDataTime = Date.now(); // UPDATE WAKTU DATA TERAKHIR
                    processSensorData(data);

                    // Beri feedback visual
                    flashUpdateIndicator();
                }
            } catch (error) {
                console.error('Error:', error);
                isConnected = false;
                updateConnectionStatus(false);
            }
        }

        // Flash indicator saat data baru datang
        function flashUpdateIndicator() {
            if (elements.updateTime) {
                // Tambah animasi
                elements.updateTime.classList.add('animate-pulse', 'text-green-300');

                // Hapus animasi setelah 500ms
                setTimeout(() => {
                    elements.updateTime.classList.remove('animate-pulse', 'text-green-300');
                }, 500);
            }
        }

        // Process sensor data
        function processSensorData(data) {
            const sensorData = data.current;
            dataCount++;

            // Update stats
            elements.dataPoints.textContent = dataCount;

            // ========== PERBAIKAN: HAPUS INI ==========
            // JANGAN update updateTime di sini, biarkan updateLastUpdateDisplay() yang handle
            // elements.updateTime.textContent = formatTime(sensorData.created_at);
            // ==========================================

            // Update sensor values
            updateSensorDisplay(sensorData);

            // Update history table
            updateHistoryTable(data.history);

            // Update connection status
            updateConnectionStatus(true);
        }

        // Update sensor display
        function updateSensorDisplay(data) {
            // Ultrasonic 1
            const dist1 = data.distance1;
            elements.distance1Value.textContent = dist1;
            const dist1Percent = Math.min((dist1 / 50) * 100, 100);
            document.getElementById('distance1Bar').style.width = `${100 - dist1Percent}%`;

            const dist1Status = dist1 <= 50 && dist1 > 0;
            document.getElementById('distance1StatusText').textContent = dist1Status ? 'Warning' : 'Normal';
            document.getElementById('distance1StatusText').className = `text-sm ${dist1Status ? 'text-yellow-400' : 'text-green-400'}`;
            document.getElementById('sensor1Value').textContent = `${dist1} cm`;

            // Ultrasonic 2
            const dist2 = data.distance2;
            elements.distance2Value.textContent = dist2;
            const dist2Percent = Math.min((dist2 / 20) * 100, 100);
            document.getElementById('distance2Bar').style.width = `${100 - dist2Percent}%`;

            const dist2Status = dist2 <= 20 && dist2 > 0;
            document.getElementById('distance2StatusText').textContent = dist2Status ? 'Warning' : 'Normal';
            document.getElementById('distance2StatusText').className = `text-sm ${dist2Status ? 'text-yellow-400' : 'text-green-400'}`;
            document.getElementById('sensor2Value').textContent = `${dist2} cm`;

            // Soil Moisture
            const soilWet = data.soilWet;
            elements.soilStatusText.textContent = soilWet ? 'WET' : 'DRY';
            document.getElementById('soilIcon').className = `fas ${soilWet ? 'fa-tint text-blue-400' : 'fa-leaf text-emerald-400'} text-3xl mb-2`;
            document.getElementById('soilState').textContent = soilWet ? 'Wet' : 'Dry';
            document.getElementById('soilState').className = `text-sm font-medium ${soilWet ? 'text-blue-400' : 'text-emerald-400'}`;
            document.getElementById('sensor3Value').textContent = soilWet ? 'WET' : 'DRY';

            // Update soil circle
            const soilCircle = document.getElementById('soilCircle');
            const circumference = 283; // 2 * π * 45
            const offset = soilWet ? circumference * 0.7 : circumference * 0.3;
            soilCircle.style.strokeDashoffset = circumference - offset;

            // System Status
            const systemOn = data.systemON;
            elements.systemStatusText.textContent = systemOn ? 'ACTIVE' : 'INACTIVE';
            document.getElementById('systemIcon').className = `fas fa-power-off text-2xl ${systemOn ? 'text-green-400' : 'text-gray-400'}`;
            document.getElementById('systemGlow').className = `absolute inset-0 rounded-2xl border-2 ${systemOn ? 'border-green-500/30 animate-pulse-glow' : 'border-transparent'}`;
            document.getElementById('sensor4Value').textContent = systemOn ? 'ON' : 'OFF';

            let alerts = [];
            let alertClass = '';

            if (soilWet) {
                alerts.push('Water Alert');
            }

            if (dist1Status) {
                alerts.push('DIstance Alert');
            }

            if (dist2Status) {
                alerts.push('Proximity Alert');
            }

            if (dist2Status) {
                alertClass = 'text-red-500 text-xl';
            } else if (dist1Status) {
                alertClass = 'text-yellow-400 text-xl';
            } else if (soilWet) {
                alertClass = 'text-blue-400 text-lg';
            } else {
                alertClass = 'text-green-400 text-lg';
                alerts.push('Normal');
            }

            elements.alertStatus.innerHTML = alerts.join('<br>');
            elements.alertStatus.className = `${alertClass} font-bold`;
        }

        // Update history table
        function updateHistoryTable(history) {
            if (!history || history.length === 0) {
                elements.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        No data available
                    </td>
                </tr>
            `;
                elements.dataCount.textContent = '0';
                return;
            }

            elements.tableBody.innerHTML = '';
            elements.dataCount.textContent = history.length;

            history.forEach((log, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-dark-800/30 transition-colors';

                // Determine colors
                const dist1Color = log.distance1 <= 50 && log.distance1 > 0 ? 'text-yellow-400' : 'text-green-400';
                const dist2Color = log.distance2 <= 20 && log.distance2 > 0 ? 'text-yellow-400' : 'text-green-400';
                const soilColor = log.soilWet ? 'text-blue-400' : 'text-emerald-400';
                const systemColor = log.systemON ? 'text-green-400' : 'text-gray-400';

                row.innerHTML = `
                <td class="py-4 px-6">
                    <div class="font-mono text-sm">${formatDateTime(log.created_at)}</div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <span class="${dist1Color} font-bold">${log.distance1}</span>
                        <span class="text-gray-400">cm</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <span class="${dist2Color} font-bold">${log.distance2}</span>
                        <span class="text-gray-400">cm</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <i class="fas ${log.soilWet ? 'fa-tint' : 'fa-leaf'} ${soilColor}"></i>
                        <span class="${soilColor} font-bold">${log.soilWet ? 'WET' : 'DRY'}</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-power-off ${systemColor}"></i>
                        <span class="${systemColor} font-bold">${log.systemON ? 'ON' : 'OFF'}</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="w-16 h-1 bg-dark-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full" 
                             style="width: ${(index % 10) * 10}%"></div>
                    </div>
                </td>
            `;

                elements.tableBody.appendChild(row);
            });
        }

        // Update connection status
        function updateConnectionStatus(connected) {
            const sensors = ['sensor1Status', 'sensor2Status', 'sensor3Status', 'sensor4Status'];
            const color = connected ? 'bg-green-500' : 'bg-red-500';

            sensors.forEach(sensor => {
                document.getElementById(sensor).className = `w-2 h-2 rounded-full ${color}`;
            });

            elements.activeSensors.textContent = connected ? '4' : '0';
            isConnected = connected;
        }

        // Format time
        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
        }

        // Format date time
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }

        // Export data
        function exportData() {
            alert('Export feature would generate CSV file with sensor data');
        }

        // Show settings
        function showSettings() {
            alert('Settings modal would appear here');
        }

        // View documentation
        function viewDocumentation() {
            alert('Documentation would open in new tab');
        }

        // Initialize on load
        window.addEventListener('DOMContentLoaded', init);
    </script>
</body>

</html>