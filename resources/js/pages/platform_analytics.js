let loginChart
let securityChart
let deviceChart
let schoolChart

const routes = window.analyticsRoutes || {}

document.addEventListener('DOMContentLoaded', () => {

    loadOverview()
    loadLoginTrends()
    loadSecurityTrends()
    loadDeviceAnalytics()
    loadSchoolAnalytics()

    const refreshBtn =
        document.getElementById('refreshAnalytics')

    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {

            loadOverview(true)
            loadLoginTrends(true)
            loadSecurityTrends(true)
            loadDeviceAnalytics(true)
            loadSchoolAnalytics(true)
        })
    }
})

async function loadOverview(refresh = false)
{
    try {

        const response = await fetch(
            `${routes.overview}?refresh=${refresh}`
        )

        const data = await response.json()

        const totalUsers =
            document.getElementById('totalUsers')

        const activeUsers =
            document.getElementById('activeUsers')

        const totalSchools =
            document.getElementById('totalSchools')

        const activeSessions =
            document.getElementById('activeSessions')

        const suspiciousActivities =
            document.getElementById('suspiciousActivities')

        const todayLogins =
            document.getElementById('todayLogins')

        if (totalUsers) {
            totalUsers.innerHTML = data.total_users
        }

        if (activeUsers) {
            activeUsers.innerHTML = data.active_users
        }

        if (totalSchools) {
            totalSchools.innerHTML = data.total_schools
        }

        if (activeSessions) {
            activeSessions.innerHTML = data.active_sessions
        }

        if (suspiciousActivities) {
            suspiciousActivities.innerHTML =
                data.suspicious_activities
        }

        if (todayLogins) {
            todayLogins.innerHTML =
                data.today_logins
        }

    } catch (error) {

        console.error(error)
    }
}

async function loadLoginTrends(refresh = false)
{
    try {

        const response = await fetch(
            `${routes.login}?refresh=${refresh}`
        )

        const data = await response.json()

        if (loginChart) {
            loginChart.destroy()
        }

        const canvas =
            document.getElementById('loginTrendChart')

        if (!canvas) return

        const ctx = canvas.getContext('2d')

        const labels = []
        const values = []

        data.forEach(item => {

            labels.push(item.date)
            values.push(item.total)
        })

        loginChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Logins',
                        data: values,
                        fill: true,
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        })

    } catch (error) {

        console.error(error)
    }
}

async function loadSecurityTrends(refresh = false)
{
    try {

        const response = await fetch(
            `${routes.security}?refresh=${refresh}`
        )

        const data = await response.json()

        if (securityChart) {
            securityChart.destroy()
        }

        const canvas =
            document.getElementById('securityChart')

        if (!canvas) return

        const ctx = canvas.getContext('2d')

        const labels = []
        const values = []

        data.forEach(item => {

            labels.push(item.date)
            values.push(item.total)
        })

        securityChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Security Logs',
                        data: values,
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: 40
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        })

    } catch (error) {

        console.error(error)
    }
}

async function loadDeviceAnalytics(refresh = false)
{
    try {

        const response = await fetch(
            `${routes.device}?refresh=${refresh}`
        )

        const data = await response.json()

        if (deviceChart) {
            deviceChart.destroy()
        }

        const canvas =
            document.getElementById('deviceChart')

        if (!canvas) return

        const ctx = canvas.getContext('2d')

        const labels = []
        const values = []

        data.forEach(item => {

            labels.push(item.browser)
            values.push(item.total)
        })

        deviceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        borderWidth: 2,
                        hoverOffset: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        })

    } catch (error) {

        console.error(error)
    }
}

async function loadSchoolAnalytics(refresh = false)
{
    try {

        const response = await fetch(
            `${routes.school}?refresh=${refresh}`
        )

        const data = await response.json()

        if (schoolChart) {
            schoolChart.destroy()
        }

        const canvas =
            document.getElementById('schoolChart')

        if (!canvas) return

        const ctx = canvas.getContext('2d')

        const labels = []
        const values = []

        data.forEach(item => {

            labels.push(item.name)
            values.push(item.users_count)
        })

        schoolChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Users',
                        data: values,
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: 45
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        })

    } catch (error) {

        console.error(error)
    }
}
