<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Instalación de Puntos de Anclaje',
                'slug' => 'puntos-de-anclaje',
                'description' => 'Instalación de puntos de anclaje certificados para trabajo seguro en alturas.',
                'excerpt' => 'Instalación de puntos de anclaje certificados para trabajo seguro en alturas.',
                'status' => 'active',
                'order' => 1,
            ],
            [
                'title' => 'Líneas de Vida Certificadas',
                'slug' => 'lineas-de-vida-certificadas',
                'description' => 'Diseño e instalación de líneas de vida certificadas para proteger cada desplazamiento.',
                'excerpt' => 'Diseño e instalación de líneas de vida certificadas para proteger cada desplazamiento.',
                'status' => 'active',
                'order' => 2,
            ],
            [
                'title' => 'Capacitación y Entrenamiento',
                'slug' => 'capacitacion-y-entrenamiento',
                'description' => 'Formación práctica y certificada para equipos que trabajan en alturas.',
                'excerpt' => 'Formación práctica y certificada para equipos que trabajan en alturas.',
                'status' => 'active',
                'order' => 3,
            ],
            [
                'title' => 'Asesoría en Prevención de Riesgos',
                'slug' => 'asesoria-en-prevencion-de-riesgos',
                'description' => 'Acompañamiento especializado para fortalecer la prevención y el cumplimiento legal.',
                'excerpt' => 'Acompañamiento especializado para fortalecer la prevención y el cumplimiento legal.',
                'status' => 'active',
                'order' => 4,
            ],
            [
                'title' => 'Mantenimiento y Recertificación',
                'slug' => 'mantenimiento-y-recertificacion',
                'description' => 'Inspección, mantenimiento y recertificación para conservar sus sistemas operativos.',
                'excerpt' => 'Inspección, mantenimiento y recertificación para conservar sus sistemas operativos.',
                'status' => 'active',
                'order' => 5,
            ],
            [
                'title' => 'Asesoría en Gestión de Riesgos',
                'slug' => 'asesoria-en-gestion-de-riesgos',
                'description' => 'Soluciones de gestión para identificar, controlar y reducir los riesgos de su operación.',
                'excerpt' => 'Soluciones de gestión para identificar, controlar y reducir los riesgos de su operación.',
                'status' => 'active',
                'order' => 6,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::query()->updateOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData,
            );
        }
    }
}
