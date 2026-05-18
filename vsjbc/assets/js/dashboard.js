document.addEventListener('DOMContentLoaded', function () {
    if (typeof statsData === 'undefined') return;

    const statusColors = {
        pendente:     '#f59e0b',
        em_andamento: '#3b82f6',
        concluida:    '#10b981',
        cancelada:    '#9ca3af',
    };
    const priorityColors = {
        baixa:   '#10b981',
        media:   '#06b6d4',
        alta:    '#f59e0b',
        critica: '#ef4444',
    };

    const statusCtx = document.getElementById('chartStatus');
    if (statusCtx) {
        const labels = Object.keys(statsData.by_status).map(k => {
            const map = {pendente:'Pendente',em_andamento:'Em Andamento',concluida:'Concluída',cancelada:'Cancelada'};
            return map[k] || k;
        });
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: Object.values(statsData.by_status),
                    backgroundColor: Object.keys(statsData.by_status).map(k => statusColors[k] || '#94a3b8'),
                    borderWidth: 2,
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
                cutout: '60%',
            }
        });
    }

    const priorityCtx = document.getElementById('chartPriority');
    if (priorityCtx) {
        const pLabels = Object.keys(statsData.by_priority).map(k => {
            const map = {baixa:'Baixa',media:'Média',alta:'Alta',critica:'Crítica'};
            return map[k] || k;
        });
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: pLabels,
                datasets: [{
                    label: 'Demandas',
                    data: Object.values(statsData.by_priority),
                    backgroundColor: Object.keys(statsData.by_priority).map(k => priorityColors[k] || '#94a3b8'),
                    borderRadius: 6,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                },
            }
        });
    }
});
