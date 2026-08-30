<x-layouts::app :title="__('Política de Privacidad | AMC Gestión de Riesgos')" :description="__(
    'Política de Tratamiento de Datos Personales de AMC Gestión de Riesgos SAS, en cumplimiento de la Ley 1581 de 2012.',
)" bodyBg="bg-amc-gray-bg">
    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">
        <div class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-sm text-zinc-500">
                    <li><a href="{{ route('home') }}" class="transition hover:text-amc-orange">{{ __('Inicio') }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-amc-blue">{{ __('Política de Privacidad') }}</li>
                </ol>
            </nav>

            <article class="prose prose-amc max-w-none text-amc-blue/80">
                <h1 class="text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                    {{ __('POLÍTICA DE TRATAMIENTO DE DATOS PERSONALES') }}
                </h1>

                <p class="mt-4 text-sm italic text-zinc-500">
                    {{ __('Fecha de última actualización:') }} 27 de agosto de 2026
                </p>

                <p>
                    En cumplimiento de la Ley 1581 de 2012 y el Decreto Reglamentario 1377 de 2013,
                    <strong>Amc Gestion De Riesgos S A S</strong> (en adelante, el Responsable), establece la presente
                    Política de Privacidad y Tratamiento de Datos Personales para su sitio web (en adelante, el Sitio Web).
                </p>

                <h2>1. Identificación del Responsable</h2>
                <ul>
                    <li><strong>Razón Social / Nombre:</strong> Amc Gestion De Riesgos S A S</li>
                    <li><strong>NIT / Cédula:</strong> 901787366</li>
                    <li><strong>Domicilio / Dirección:</strong> CARRERA 37 CL 116-128, Barranquilla, Colombia</li>
                    <li><strong>Correo electrónico:</strong> gerencia@amcgestiondelriesgo.com.co</li>
                    <li><strong>Teléfono:</strong> +573147874006</li>
                </ul>

                <h2>2. Datos que recopilamos</h2>
                <p>
                    El Sitio Web recopila datos personales exclusivamente a través de formularios de contacto,
                    comentarios en el blog o suscripciones a boletines informativos. Estos datos incluyen:
                </p>
                <ul>
                    <li><strong>Datos de contacto:</strong> Nombre, apellido y dirección de correo electrónico.</li>
                    <li><strong>Datos técnicos y de navegación:</strong> Dirección IP, tipo de navegador y datos de
                        comportamiento mediante el uso de cookies.</li>
                </ul>

                <h2>3. Uso de Cookies y Herramientas de Terceros (Google Analytics)</h2>
                <p>
                    Este Sitio Web utiliza y utilizará herramientas de análisis estadístico, específicamente
                    <strong>Google Analytics</strong>, un servicio analítico de web prestado por Google, Inc.
                </p>
                <ul>
                    <li>Google Analytics utiliza "cookies", que son archivos de texto ubicados en tu ordenador, para
                        ayudar al Sitio Web a analizar el uso que hacen los usuarios del sitio.</li>
                    <li>La información que genera la cookie acerca de tu uso del Sitio Web (incluyendo tu dirección IP)
                        será directamente transmitida y archivada por Google.</li>
                    <li>Esta información se utiliza exclusivamente de forma agregada y anónima con el propósito de seguir
                        la pista del uso del Sitio Web, recopilar informes sobre la actividad de la página y prestar
                        servicios relacionados con la actividad del Sitio Web y el uso de Internet.</li>
                    <li>Puedes deshabilitar el uso de cookies configurando las opciones de tu navegador web.</li>
                </ul>

                <h2>4. Finalidad del tratamiento</h2>
                <p>
                    Los datos personales recolectados serán utilizados estrictamente para los siguientes fines:
                </p>
                <ul>
                    <li>Responder a las consultas, mensajes y solicitudes enviadas a través de la página de servicios.</li>
                    <li>Gestionar y moderar los comentarios publicados por los usuarios en el blog.</li>
                    <li>Enviar actualizaciones de contenido, artículos informativos o novedades del blog (solo si el
                        usuario se ha suscrito voluntariamente).</li>
                    <li>Analizar y mejorar la experiencia de navegación del usuario en el Sitio Web mediante métricas
                        estadísticas de tráfico.</li>
                </ul>
                <p>
                    <em>(Nota: Este Sitio Web es de carácter netamente informativo y de promoción de servicios. No se
                        realizan transacciones económicas, pasarelas de pago, ni ventas directas a través de la
                        plataforma).</em>
                </p>

                <h2>5. Derechos del Titular</h2>
                <p>
                    Como titular de los datos, la legislación colombiana te otorga los siguientes derechos:
                </p>
                <ul>
                    <li><strong>Conocer, actualizar y rectificar</strong> tus datos personales en cualquier momento.</li>
                    <li><strong>Solicitar la supresión</strong> de tus datos de nuestras bases de datos cuando consideres
                        que no están siendo tratados conforme a la ley.</li>
                    <li><strong>Revocar la autorización</strong> otorgada para el envío de boletines o correos del blog.</li>
                </ul>

                <h2>6. Procedimiento para ejercer tus derechos</h2>
                <p>
                    Para actualizar, rectificar o eliminar tus datos de nuestra lista de contactos, puedes enviar una
                    solicitud directamente al correo electrónico
                    <a href="mailto:gerencia@amcgestiondelriesgo.com.co">gerencia@amcgestiondelriesgo.com.co</a>.
                    Tu requerimiento será atendido en un plazo máximo de diez (10) días hábiles, conforme a lo
                    estipulado por la Ley 1581 de 2012.
                </p>

                <h2>7. Modificaciones</h2>
                <p>
                    El Responsable se reserva el derecho de modificar esta política en cualquier momento para adaptarla
                    a novedades legislativas o cambios técnicos en el Sitio Web. Cualquier cambio será publicado en esta
                    misma sección.
                </p>
            </article>

            <div class="mt-12 border-t border-zinc-200 pt-8">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                    <span aria-hidden="true">&larr;</span>
                    {{ __('Volver al inicio') }}
                </a>
            </div>
        </div>
    </main>

    <x-footer />
</x-layouts::app>
