<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrarse - Surify</title>
    <!-- Google Fonts (Clonadas del Login) -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Tailwind Config (Exactamente igual a la del Login) -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-surface": "#191c1d",
                        "on-secondary": "#ffffff",
                        "on-primary-container": "#003e63",
                        "secondary": "#7d5800",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#725000",
                        "inverse-primary": "#97ccfe",
                        "tertiary": "#a7373b",
                        "primary": "#28628f",
                        "surface-container-highest": "#e1e3e4",
                        "surface-container-lowest": "#ffffff",
                        "inverse-surface": "#2e3132",
                        "secondary-fixed-dim": "#f9bc47",
                        "secondary-fixed": "#ffdea9",
                        "surface-tint": "#28628f",
                        "secondary-container": "#ffc24c",
                        "on-tertiary-fixed": "#410007",
                        "error": "#ba1a1a",
                        "surface-container-high": "#e7e8e9",
                        "on-primary-fixed-variant": "#004a75",
                        "tertiary-fixed-dim": "#ffb3b0",
                        "surface-container-low": "#f3f4f5",
                        "primary-container": "#75aadb",
                        "tertiary-container": "#ff7e7d",
                        "surface-container": "#edeeef",
                        "on-tertiary-container": "#76121b",
                        "surface": "#f8f9fa",
                        "primary-fixed": "#cee5ff",
                        "on-secondary-fixed": "#271900",
                        "on-primary-fixed": "#001d32",
                        "on-primary": "#ffffff",
                        "background": "#f8f9fa",
                        "surface-bright": "#f8f9fa",
                        "on-secondary-fixed-variant": "#5e4100",
                        "surface-dim": "#d9dadb",
                        "primary-fixed-dim": "#97ccfe",
                        "inverse-on-surface": "#f0f1f2",
                        "on-background": "#191c1d",
                        "outline": "#71787f",
                        "on-surface-variant": "#41474f",
                        "on-tertiary": "#ffffff",
                        "on-error": "#ffffff",
                        "tertiary-fixed": "#ffdad8",
                        "on-tertiary-fixed-variant": "#861f25",
                        "surface-variant": "#e1e3e4",
                        "on-error-container": "#93000a",
                        "outline-variant": "#c1c7d0"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "md": "24px",
                        "xs": "4px",
                        "lg": "48px",
                        "xl": "80px",
                        "margin-desktop": "64px",
                        "sm": "12px",
                        "base": "8px",
                        "gutter": "24px",
                        "margin-mobile": "16px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Inter", "sans-serif"],
                        "display-xl": ["Inter", "sans-serif"],
                        "headline-md": ["Inter", "sans-serif"],
                        "body-md": ["Plus Jakarta Sans", "sans-serif"],
                        "body-lg": ["Plus Jakarta Sans", "sans-serif"],
                        "label-bold": ["Inter", "sans-serif"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", {
                            "lineHeight": "1.2",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "display-xl": ["64px", {
                            "lineHeight": "1.1",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "1.3",
                            "fontWeight": "600"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "1.5",
                            "fontWeight": "400"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "1.6",
                            "fontWeight": "400"
                        }],
                        "label-bold": ["14px", {
                            "lineHeight": "1",
                            "letterSpacing": "0.05em",
                            "fontWeight": "700"
                        }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutToRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(40px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .animate-fade-out {
            animation: fadeOut 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .animate-slide-in-left {
            animation: slideInFromLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .animate-slide-out-right {
            animation: slideOutToRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
    </style>
</head>

<body class="bg-background min-h-screen text-on-background selection:bg-primary-container selection:text-on-primary-container">
    <div class="flex min-h-screen w-full">

        <!-- Right Side: Register Form Canvas -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-8 bg-surface-container-lowest page-transition-container animate-slide-in-left" style="opacity:0;">
            <div class="w-full max-w-[440px] flex flex-col gap-4">

                <!-- Brand & Welcome (Calco exacto del Login) -->
                <div class="flex flex-col gap-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">explore</span>
                        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tighter">Surify</h1>
                    </div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">Crear una cuenta</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Únete a nuestra comunidad de viajeros conscientes.</p>
                </div>

                <!-- Primary Action: Google Login -->
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-sm py-sm px-md bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low transition-colors duration-200 rounded-xl shadow-sm text-decoration-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                    </svg>
                    <span class="font-label-bold text-label-bold text-on-surface">Continuar con Google</span>
                </a>

                <!-- Divider -->
                <div class="flex items-center gap-md">
                    <div class="flex-1 h-px bg-outline-variant"></div>
                    <span class="font-body-md text-body-md text-on-surface-variant text-[12px] uppercase tracking-widest">O REGÍSTRATE CON TU CORREO</span>
                    <div class="flex-1 h-px bg-outline-variant"></div>
                </div>

                <!-- Formulario Laravel -->
                <!-- Formulario Laravel Compacto -->
                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-2">
                    @csrf

                    <!-- Nombre completo -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-xs" for="name">Nombre completo</label>
                        <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline"
                            id="name" placeholder="ej., Sofía Gómez" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @if ($errors->get('name'))
                        <p class="text-xs text-red-600 mt-0.5 ml-xs">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <!-- Correo electrónico -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-xs" for="email">Correo electrónico</label>
                        <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline"
                            id="email" placeholder="nombre@ejemplo.com" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @if ($errors->get('email'))
                        <p class="text-xs text-red-600 mt-0.5 ml-xs">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <!-- Contraseña -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-xs" for="password">Contraseña</label>
                        <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline"
                            id="password" placeholder="Mín. 8 caracteres" type="password" name="password" required autocomplete="new-password">
                        @if ($errors->get('password'))
                        <p class="text-xs text-red-600 mt-0.5 ml-xs">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-xs" for="password_confirmation">Confirmar contraseña</label>
                        <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline"
                            id="password_confirmation" placeholder="Repite tu contraseña" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="flex items-center gap-2 mb-4">
                        <input type="checkbox" name="acepta_terminos" id="acepta_terminos" required
                            class="rounded border-gray-300">
                        <label for="acepta_terminos" class="text-sm">
                            He leído y acepto los
                            <button type="button" id="abrir-terminos" class="text-primary underline">
                                Términos y Condiciones
                            </button>
                        </label>
                    </div>
                    @error('acepta_terminos')
                    <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                    @enderror

                    <button class="w-full mt-1 py-2 px-md bg-primary hover:bg-on-primary-fixed-variant text-on-primary transition-colors duration-200 rounded-xl shadow-sm flex items-center justify-center gap-xs" type="submit">
                        <span class="font-label-bold text-label-bold">Crear cuenta</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <!-- Link al Login -->
                <div class="text-center mt-sm">
                    <span class="font-body-md text-body-md text-on-surface-variant">¿Ya tienes cuenta?</span>
                    <a class="font-body-md text-body-md text-primary hover:text-on-primary-fixed-variant font-semibold ml-xs transition-colors" href="{{ route('login') }}">Inicia sesión</a>
                </div>
            </div>
        </div>

        <!-- Left Side: Mendoza Vineyard Image (Fija y a COLOR para echar al carnicero) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-neutral-200 overflow-hidden image-transition-container animate-fade-in" style="opacity:0;">
            <div class="absolute inset-0 bg-gradient-to-r from-on-surface/20 to-transparent z-10"></div>
            <!-- Link fijo ultra-HD de un viñedo real en Mendoza a puro color -->
            <img alt="Mendoza Vineyard" class="absolute inset-0 w-full h-full object-cover" src="https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=1200&q=80">
            <div class="absolute bottom-xl left-margin-desktop z-20 max-w-md">
                <p class="font-headline-md text-headline-md text-white mb-sm text-shadow-sm">Comienza tu aventura argentina hoy.</p>
                <p class="font-body-md text-body-md text-white/90">Desbloquea itinerarios exclusivos, estancias boutique y experiencias auténticas diseñadas para el explorador moderno.</p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const googleBtn = document.querySelector('a[href*="auth/google"]');
            const loginLink = document.querySelector('a[href*="login"]');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-90', 'cursor-not-allowed');

                        // Reemplazar texto por spinner
                        btn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:inline-block; animation: spin 1s linear infinite;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-label-bold text-label-bold">Creando cuenta...</span>
                    `;
                    }

                    // Opacar los demás elementos
                    const formElements = this.querySelectorAll('input, label, a');
                    formElements.forEach(el => {
                        el.style.transition = 'opacity 0.4s ease';
                        el.style.opacity = '0.5';
                        el.style.pointerEvents = 'none';
                    });
                });
            }

            if (googleBtn) {
                googleBtn.addEventListener('click', function(e) {
                    this.style.pointerEvents = 'none';
                    this.classList.add('opacity-90', 'bg-surface-container-low');
                    this.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:inline-block; animation: spin 1s linear infinite;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-label-bold text-label-bold text-on-surface">Conectando con Google...</span>
                `;
                });
            }

            // Animación de salida al ir al login
            if (loginLink) {
                loginLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetUrl = this.href;

                    const pageContainer = document.querySelector('.page-transition-container');
                    const imageContainer = document.querySelector('.image-transition-container');

                    if (pageContainer) {
                        pageContainer.classList.remove('animate-slide-in-left');
                        pageContainer.classList.add('animate-slide-out-right');
                    }
                    if (imageContainer) {
                        imageContainer.classList.remove('animate-fade-in');
                        imageContainer.classList.add('animate-fade-out');
                    }

                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 400);
                });
            }
        });
    </script>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <!-- Modal Términos y Condiciones -->
    <div id="modal-terminos" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-2xl max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
                <h2 class="font-headline-md text-headline-md text-on-surface">Términos y Condiciones</h2>
                <button type="button" id="cerrar-terminos" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="overflow-y-auto px-6 py-4 text-sm text-on-surface-variant space-y-3">
                <p>El presente documento establece los Términos y Condiciones de Uso (en adelante, los "Términos") que regulan el acceso y la utilización de la plataforma web Surify (en adelante, la "Plataforma" o "Surify"). Al registrarse, acceder o utilizar la Plataforma, el usuario (en adelante, el "Usuario") manifiesta su conformidad plena e irrestricta con estos Términos. En caso de no aceptarlos, deberá abstenerse de utilizar la Plataforma.</p>

                <p><strong>1. Definiciones.</strong> A los efectos de este documento, se entiende por "Plataforma" al sitio web Surify y todas sus funcionalidades; por "Usuario" a toda persona que se registre o navegue en la Plataforma; y por "Contenido" a toda foto, comentario, valoración o dato que el Usuario cargue en la Plataforma.</p>

                <p><strong>2. Objeto del servicio.</strong> Surify es una plataforma de turismo interactivo cuyo objeto es permitir a los Usuarios explorar destinos turísticos de la República Argentina mediante un mapa georreferenciado, filtrar dichos destinos por categoría, y compartir experiencias propias a través de Contenido generado por ellos mismos, con el fin de enriquecer la información disponible para la comunidad de viajeros.</p>

                <p><strong>3. Capacidad y registro de cuenta.</strong> El Usuario declara contar con capacidad legal suficiente para aceptar estos Términos. Para acceder a las funcionalidades que requieren registro, el Usuario deberá proporcionar información veraz, exacta, actualizada y completa (nombre, correo electrónico y contraseña), asumiendo la responsabilidad exclusiva por la veracidad de dichos datos. El Usuario es el único responsable de la custodia de sus credenciales de acceso y de toda actividad que se realice desde su cuenta, debiendo notificar a Surify de forma inmediata ante cualquier uso no autorizado de la misma.</p>

                <p><strong>4. Seguridad de la cuenta.</strong> Surify podrá ofrecer mecanismos adicionales de seguridad, tales como la autenticación de dos factores (2FA), cuya activación queda a criterio y responsabilidad del Usuario. Surify no será responsable por los perjuicios que pudieran derivarse de la falta de activación de dichos mecanismos por parte del Usuario.</p>

                <p><strong>5. Obligaciones del Usuario y conductas prohibidas.</strong> El Usuario se obliga a utilizar la Plataforma de conformidad con la ley, la moral, el orden público y estos Términos. En particular, queda expresamente prohibido: (a) publicar Contenido falso, engañoso, difamatorio, discriminatorio u ofensivo; (b) infringir derechos de propiedad intelectual o industrial de terceros; (c) suplantar la identidad de otra persona, entidad u organización; (d) utilizar la Plataforma con fines comerciales no autorizados por Surify; (e) intentar vulnerar, eludir o comprometer la seguridad, integridad o disponibilidad de la Plataforma o de las cuentas de otros Usuarios; y (f) recopilar datos de otros Usuarios sin su consentimiento expreso.</p>

                <p><strong>6. Contenido generado por el Usuario.</strong> El Usuario es el único responsable del Contenido que publique en la Plataforma, y declara contar con la titularidad de los derechos correspondientes o con las autorizaciones necesarias para su publicación. Mediante la carga de Contenido, el Usuario otorga a Surify una licencia de uso no exclusiva, gratuita y revocable, para reproducir y exhibir dicho Contenido dentro de la Plataforma, con el único fin de prestar el servicio. El Usuario podrá editar o eliminar su propio Contenido en cualquier momento, sin que ello genere obligación indemnizatoria alguna a cargo de Surify.</p>

                <p><strong>7. Moderación de contenido.</strong> Con el fin de preservar la calidad y confiabilidad de la información compartida, ningún comentario ni fotografía será publicado de manera inmediata. Todo Contenido será sometido a un proceso de moderación automatizada mediante inteligencia artificial y, cuando resulte pertinente, a una revisión manual adicional, previo a su publicación definitiva. Surify se reserva el derecho de rechazar, suspender o eliminar Contenido que, a su exclusivo criterio, resulte contrario a estos Términos, sin que ello genere derecho a reclamo o indemnización alguna en favor del Usuario.</p>

                <p><strong>8. Perfiles y configuración de privacidad.</strong> Cada Usuario podrá configurar la visibilidad de su perfil (público o privado), determinando qué información, Contenido e insignias resultan visibles para otros Usuarios de la Plataforma.</p>

                <p><strong>9. Sistema de reconocimiento (insignias).</strong> Surify podrá implementar un sistema de reconocimiento de la participación de sus Usuarios mediante insignias, otorgadas conforme a criterios definidos por la Plataforma, los cuales podrán ser modificados sin previo aviso.</p>

                <p><strong>10. Propiedad intelectual.</strong> El diseño, la estructura, el código fuente, las marcas, los logotipos y demás elementos distintivos de Surify son de titularidad exclusiva del equipo desarrollador, encontrándose protegidos por la normativa vigente en materia de propiedad intelectual. Queda prohibida su reproducción, distribución o explotación sin autorización expresa, con excepción del Contenido generado por los propios Usuarios conforme a lo establecido en la Cláusula 6.</p>

                <p><strong>11. Limitación de responsabilidad.</strong> Surify no garantiza la exactitud, vigencia, veracidad o integridad de la información turística cargada por los Usuarios, ni se responsabiliza por los daños o perjuicios que pudieran derivarse de decisiones adoptadas en base a dicha información. Asimismo, Surify no se responsabiliza por el contenido, la disponibilidad o las políticas de sitios web de terceros a los que la Plataforma pudiera enlazar.</p>

                <p><strong>12. Suspensión y cancelación de cuenta.</strong> Surify podrá suspender, restringir o cancelar, de forma temporal o definitiva, el acceso de un Usuario que incumpla lo dispuesto en estos Términos, sin perjuicio de las demás acciones que pudieran corresponder conforme a derecho.</p>

                <p><strong>13. Centro de soporte.</strong> Ante cualquier consulta, reclamo o inconveniente relacionado con el uso de la Plataforma, el Usuario podrá dirigirse al centro de soporte disponible dentro de Surify.</p>

                <p><strong>14. Modificaciones de los Términos.</strong> Surify se reserva el derecho de modificar los presentes Términos en cualquier momento. Las modificaciones serán comunicadas a través de la Plataforma y entrarán en vigencia a partir de su publicación, siendo responsabilidad del Usuario revisarlas periódicamente.</p>

                <p><strong>15. Legislación aplicable y jurisdicción.</strong> Los presentes Términos se rigen por las leyes de la República Argentina. Para cualquier controversia derivada de su interpretación o aplicación, las partes se someten a la jurisdicción de los tribunales ordinarios competentes.</p>
            </div>

            <div class="px-6 py-4 border-t border-outline-variant flex justify-end">
                <button type="button" id="cerrar-terminos-btn"
                    class="py-2 px-md bg-primary hover:bg-on-primary-fixed-variant text-on-primary rounded-xl">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    <script>
        const abrirTerminos = document.getElementById('abrir-terminos');
        const modalTerminos = document.getElementById('modal-terminos');
        const cerrarTerminos = document.getElementById('cerrar-terminos');
        const cerrarTerminosBtn = document.getElementById('cerrar-terminos-btn');

        function toggleModal(mostrar) {
            modalTerminos.classList.toggle('hidden', !mostrar);
            modalTerminos.classList.toggle('flex', mostrar);
        }

        abrirTerminos?.addEventListener('click', () => toggleModal(true));
        cerrarTerminos?.addEventListener('click', () => toggleModal(false));
        cerrarTerminosBtn?.addEventListener('click', () => toggleModal(false));

        // Cerrar al clickear fuera del cuadro
        modalTerminos?.addEventListener('click', (e) => {
            if (e.target === modalTerminos) toggleModal(false);
        });
    </script>
</body>

</html>