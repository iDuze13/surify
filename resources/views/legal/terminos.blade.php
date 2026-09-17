@extends('layouts.app')

@section('title', 'Surify - Términos y Condiciones')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10 text-gray-800 dark:text-gray-200">
    <h1 class="text-3xl font-bold mb-2">Términos y Condiciones de Uso</h1>
    <p class="text-sm text-gray-500 mb-8">Última actualización: {{ now()->format('d/m/Y') }}</p>

    <p class="mb-6">
        Bienvenido/a a Surify. Al crear una cuenta en esta plataforma, aceptás los siguientes
        Términos y Condiciones. Te pedimos que los leas con atención antes de continuar.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">1. Aceptación de los Términos</h2>
    <p class="mb-4">El registro y uso de Surify implica la aceptación total de estos Términos y Condiciones. Si no estás de acuerdo con alguno de los puntos aquí detallados, te pedimos que no completes el registro.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">2. Objeto del servicio</h2>
    <p class="mb-4">Surify es una plataforma web de turismo interactivo que permite explorar lugares turísticos de Argentina en un mapa, filtrar destinos por categoría, y compartir experiencias propias mediante comentarios y fotografías.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">3. Registro de cuenta</h2>
    <p class="mb-4">Para crear una cuenta, el usuario debe proporcionar información veraz, completa y actualizada (nombre, correo electrónico y contraseña). El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso y de toda actividad realizada desde su cuenta. Surify puede ofrecer autenticación de dos factores (2FA) como capa adicional de seguridad, cuya activación es responsabilidad del usuario.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">4. Uso de la plataforma</h2>
    <p class="mb-2">El usuario se compromete a utilizar Surify de forma lícita, respetuosa y conforme a estos Términos. Queda prohibido:</p>
    <ul class="list-disc list-inside mb-4 space-y-1">
        <li>Publicar contenido falso, ofensivo, difamatorio o que infrinja derechos de terceros.</li>
        <li>Suplantar la identidad de otra persona u organización.</li>
        <li>Utilizar la plataforma para fines comerciales no autorizados.</li>
        <li>Intentar vulnerar la seguridad del sitio o acceder a cuentas ajenas.</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">5. Contenido generado por el usuario</h2>
    <p class="mb-4">Al subir fotos o escribir comentarios sobre un lugar turístico, el usuario declara ser titular de ese contenido (o contar con los permisos necesarios) y le otorga a Surify una licencia no exclusiva para mostrarlo dentro de la plataforma. El usuario puede editar o eliminar sus propios comentarios cuando lo desee.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">6. Moderación de contenido</h2>
    <p class="mb-4">Ningún comentario ni fotografía se publica de forma inmediata: todo contenido pasa primero por un proceso de moderación automática mediante inteligencia artificial y, en caso de resultar necesario, por una revisión manual antes de su publicación definitiva. Surify se reserva el derecho de rechazar o eliminar contenido que incumpla estos Términos.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">7. Perfiles y visibilidad</h2>
    <p class="mb-4">Cada usuario puede configurar la visibilidad de su perfil (público o privado), decidiendo quién puede ver su actividad, fotos e insignias dentro de la plataforma.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">8. Sistema de insignias</h2>
    <p class="mb-4">Surify reconoce la participación de sus usuarios mediante un sistema de insignias otorgadas según distintos criterios de uso de la plataforma. Cada insignia incluye una descripción de cómo obtenerla, disponible al hacer clic sobre ella.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">9. Propiedad intelectual</h2>
    <p class="mb-4">El diseño, la marca, el código fuente y los contenidos propios de Surify son propiedad del equipo desarrollador. El contenido generado por los usuarios (fotos, comentarios) sigue perteneciendo a sus autores, conforme a lo indicado en el punto 5.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">10. Limitación de responsabilidad</h2>
    <p class="mb-4">Surify no garantiza la exactitud, vigencia o veracidad de la información turística cargada por los usuarios, y no se responsabiliza por decisiones tomadas en base a dicha información. Tampoco se responsabiliza por el contenido de sitios externos a los que pueda enlazar la plataforma.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">11. Suspensión o cancelación de cuenta</h2>
    <p class="mb-4">Surify podrá suspender o eliminar cuentas que incumplan estos Términos, sin perjuicio de otras acciones que pudieran corresponder.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">12. Centro de soporte</h2>
    <p class="mb-4">Ante cualquier duda, reclamo o inconveniente con la plataforma, el usuario puede contactarse a través del centro de soporte disponible dentro de Surify.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">13. Modificaciones</h2>
    <p class="mb-4">Estos Términos y Condiciones pueden ser modificados en cualquier momento. Los cambios se comunicarán dentro de la plataforma y entrarán en vigencia desde su publicación.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">14. Ley aplicable</h2>
    <p class="mb-4">Este acuerdo se rige por las leyes de la República Argentina.</p>
</div>
@endsection