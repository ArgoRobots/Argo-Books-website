document.addEventListener('DOMContentLoaded', function () {
    // Premium Subscriptions Chart initialization
    const subLabels = subscriptionChartData.map(item => item.date);
    const subTotals = subscriptionChartData.map(item => parseInt(item.total));
    const subActive = subscriptionChartData.map(item => parseInt(item.active));

    const subCtx = document.getElementById('subscriptionsChart').getContext('2d');
    new Chart(subCtx, {
        type: 'line',
        data: {
            labels: subLabels,
            datasets: [
                {
                    label: 'Total Subscriptions',
                    data: subTotals,
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)'
                },
                {
                    label: 'Active Subscriptions',
                    data: subActive,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    ticks: {
                        padding: 10
                    }
                }
            },
            plugins: {
                title: {
                    display: false
                },
                legend: {
                    position: 'top',
                }
            },
        }
    });
});

// Restore scroll position after page fully loads
window.addEventListener('load', function () {
    if (sessionStorage.getItem('scrollPosition')) {
        const scrollPos = sessionStorage.getItem('scrollPosition');
        sessionStorage.removeItem('scrollPosition');

        // Use requestAnimationFrame to ensure page is fully rendered
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                window.scrollTo(0, parseInt(scrollPos));
            });
        });
    }
});