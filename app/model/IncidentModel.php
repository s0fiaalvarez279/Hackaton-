<?php
// app/controllers/HomeController.php
require_once __DIR__ . '/../models/IncidentModel.php';

class HomeController {
    public function index() {
        $model = new IncidentModel();
        $data = [
            'APP_NAME'      => 'RutaX · Medellín Movilidata OS',
            'totalIncidents'=> $model->getTotalIncidents(),
            'criticalCount' => $model->getCriticalCount(),
            'baseCongestion'=> $model->getCongestion(),
            'avgSpeed'      => $model->getAvgSpeed(),
            'geoJsonData'   => $model->getGeoJsonData()
        ];
        // Pasar variables a la vista
        extract($data);
        require_once __DIR__ . '/../views/home/index.php';
    }
}