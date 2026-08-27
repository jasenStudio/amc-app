<x-layouts::app :title="__('AMC Gestión de Riesgos | Seguridad en alturas y líneas de vida')" :description="__(
    'AMC Gestión de Riesgos SAS ofrece instalación de líneas de vida, puntos de anclaje certificados, seguridad en alturas y asesorías SG-SST para empresas.',
)" bodyBg="bg-amc-blue">

    <x-header />

    <main id="main-content">
        <x-sections.hero />

        <x-sections.metrics />

        <x-sections.about />

        <x-sections.services />

        <x-sections.projects />

        <x-sections.certification />

        <x-sections.blog />

        <x-sections.contact />
    </main>
    <x-wp-button />
    <x-footer />
</x-layouts::app>
