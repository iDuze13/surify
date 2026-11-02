<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Surify - Iniciar sesión</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
                        "headline-lg": ["32px", { "lineHeight": "1.2", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "display-xl": ["64px", { "lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-md": ["24px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
                        "label-bold": ["14px", { "lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700" }]
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
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes slideInFromRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOutToLeft {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(-40px); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .animate-fade-out {
            animation: fadeOut 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .animate-slide-in-right {
            animation: slideInFromRight 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .animate-slide-out-left {
            animation: slideOutToLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
    </style>
</head>
<body class="bg-background min-h-screen text-on-background selection:bg-primary-container selection:text-on-primary-container">
<div class="flex min-h-screen w-full">
    <div class="hidden lg:flex lg:w-1/2 relative bg-surface-variant overflow-hidden image-transition-container animate-fade-in" style="opacity:0;">
        <div class="absolute inset-0 bg-gradient-to-r from-on-surface/20 to-transparent z-10"></div>
     <img alt="Argentine Landscape" class="absolute inset-0 w-full h-full object-cover" src="https://images.unsplash.com/photo-1544198365-f5d60b6d8190?auto=format&fit=crop&w=1200&q=80">
        <div class="absolute bottom-xl left-margin-desktop z-20 max-w-md">
            <p class="font-headline-md text-headline-md text-white mb-sm text-shadow-sm">Te espera un gran descubrimiento</p>
            <p class="font-body-md text-body-md text-white/90">Desbloquea las experiencias más auténticas a través de los impresionantes paisajes de Argentina.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-10 bg-surface-container-lowest page-transition-container animate-slide-in-right" style="opacity:0;">
        <div class="w-full max-w-[420px] flex flex-col gap-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1 self-start font-body-md text-[14px] text-on-surface-variant hover:text-primary transition-colors text-decoration-none">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Volver al inicio
            </a>
            <div class="flex flex-col gap-sm">
                <div class="flex items-center gap-2 mb-md">
                    <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">explore</span>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tighter">Surify</h1>
                </div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Bienvenido de nuevo</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Ingresa tus datos a continuación para acceder a tu cuenta y continuar tu viaje.</p>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-sm py-sm px-md bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low transition-colors duration-200 rounded-xl shadow-sm text-decoration-none">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                </svg>
                <span class="font-label-bold text-label-bold text-on-surface">Continuar con Google</span>
            </a>

            <div class="flex items-center gap-md">
                <div class="flex-1 h-px bg-outline-variant"></div>
                <span class="font-body-md text-body-md text-on-surface-variant text-[12px] uppercase tracking-widest">O INICIA SESIÓN CON TU CORREO</span>
                <div class="flex-1 h-px bg-outline-variant"></div>
            </div>

            @if (session('status'))
                <div class="text-sm font-medium text-green-600 mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-md">
                @csrf

                <div class="flex flex-col gap-xs">
                    <label class="font-label-bold text-label-bold text-on-surface-variant ml-xs" for="email">Correo electrónico</label>
                    <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-sm font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline" 
                           id="email" placeholder="nombre@ejemplo.com" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    
                    @if ($errors->get('email'))
                        <p class="text-sm text-red-600 mt-1 ml-xs">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div class="flex flex-col gap-xs">
                    <div class="flex justify-between items-center ml-xs">
                        <label class="font-label-bold text-label-bold text-on-surface-variant" for="password">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a class="font-body-md text-body-md text-primary hover:text-on-primary-fixed-variant transition-colors text-[14px]" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>
                    <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-sm py-sm font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline" 
                           id="password" placeholder="••••••••" type="password" name="password" required autocomplete="current-password">
                    
                    @if ($errors->get('password'))
                        <p class="text-sm text-red-600 mt-1 ml-xs">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <button class="w-full mt-sm py-sm px-md bg-primary hover:bg-on-primary-fixed-variant text-on-primary transition-colors duration-200 rounded-xl shadow-sm flex items-center justify-center gap-xs" type="submit">
                    <span class="font-label-bold text-label-bold">Iniciar sesión</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </form>

            <div class="text-center mt-sm">
                <span class="font-body-md text-body-md text-on-surface-variant">¿No tienes cuenta?</span>
                <a class="font-body-md text-body-md text-primary hover:text-on-primary-fixed-variant font-semibold ml-xs transition-colors" href="{{ route('register') }}">Regístrate</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const googleBtn = document.querySelector('a[href*="auth/google"]');

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
                        <span class="font-label-bold text-label-bold">Iniciando sesión...</span>
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
                // Forzar estado de carga en botón Google
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

        // Animación de salida al ir al registro
        const registerLink = document.querySelector('a[href*="register"]');
        if (registerLink) {
            registerLink.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = this.href;
                
                const pageContainer = document.querySelector('.page-transition-container');
                const imageContainer = document.querySelector('.image-transition-container');
                
                if (pageContainer) {
                    pageContainer.classList.remove('animate-slide-in-right');
                    pageContainer.classList.add('animate-slide-out-left');
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
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
</body>
</html>