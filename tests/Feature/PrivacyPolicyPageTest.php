<?php

namespace Tests\Feature;

use Tests\TestCase;

class PrivacyPolicyPageTest extends TestCase
{
    public function test_page_is_accessible(): void
    {
        $this->get(route('privacy.policy'))
            ->assertOk();
    }

    public function test_page_contains_responsible_identification(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('Amc Gestion De Riesgos S A S', false)
            ->assertSee('901787366', false)
            ->assertSee('CARRERA 37 CL 116-128', false)
            ->assertSee('gerencia@amcgestiondelriesgo.com.co', false)
            ->assertSee('+573147874006', false);
    }

    public function test_page_references_legal_framework(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('Ley 1581 de 2012', false)
            ->assertSee('Decreto Reglamentario 1377 de 2013', false);
    }

    public function test_page_contains_all_seven_sections(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('1. Identificación del Responsable', false)
            ->assertSee('2. Datos que recopilamos', false)
            ->assertSee('3. Uso de Cookies y Herramientas de Terceros', false)
            ->assertSee('4. Finalidad del tratamiento', false)
            ->assertSee('5. Derechos del Titular', false)
            ->assertSee('6. Procedimiento para ejercer tus derechos', false)
            ->assertSee('7. Modificaciones', false);
    }

    public function test_page_mentions_google_analytics(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('Google Analytics', false);
    }

    public function test_page_contains_data_subject_rights(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('Conocer, actualizar y rectificar', false)
            ->assertSee('Solicitar la supresión', false)
            ->assertSee('Revocar la autorización', false);
    }

    public function test_page_contains_last_update_date(): void
    {
        $this->get(route('privacy.policy'))
            ->assertSee('27 de agosto de 2026', false);
    }
}
