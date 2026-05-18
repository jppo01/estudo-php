<?php
class DashboardController
{
    private DemandModel $demands;

    public function __construct()
    {
        $this->demands = new DemandModel();
    }

    public function index(): void
    {
        Auth::require();
        $stats    = $this->demands->getStats();
        $activity = $this->demands->getRecentActivity(10);
        $dueSoon  = $this->demands->getDueSoon(7);
        require __DIR__ . '/../views/dashboard/index.php';
    }

    public function apiStats(): void
    {
        Auth::require();
        Response::json($this->demands->getStats());
    }
}
