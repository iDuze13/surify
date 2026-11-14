{{--
    Animaciones compartidas (mismas que la página de provincia).
    Uso: <x-animaciones /> al principio de @section('content').

    Clases disponibles:
      Entrada al cargar:   hero-in, gal-in (con style="--d:.2s" para el retraso)
      Al hacer scroll:     reveal, reveal from-left, reveal from-right, reveal zoom
                           (el observer de layouts/app agrega .visible; retraso con style="transition-delay: 100ms")
      En cascada:          pop (con style="--pd:.1s")
      Continuas:           kenburns, float-y, nudge-down, pin-bounce
      Modales:             modal-in (se reproduce cada vez que el modal se muestra)
      Contadores:          data-count="25" (opcional data-suffix=" días")
--}}
<style>
    /* Variantes del .reveal global (el observer del layout les agrega .visible) */
    .reveal.from-left {
        transform: translateX(-32px);
    }

    .reveal.zoom {
        transform: scale(.94);
    }

    @media (min-width: 1024px) {
        .reveal.from-right {
            transform: translateX(32px);
        }
    }

    .reveal.from-left.visible,
    .reveal.from-right.visible,
    .reveal.zoom.visible {
        transform: none;
    }

    /* Barra de progreso de scroll */
    #scroll-progress {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 9990;
        background: #28628f;
        transform: scaleX(0);
        transform-origin: left;
        pointer-events: none;
    }

    /* Solo se animan si la persona NO pidió reducir movimiento */
    @media (prefers-reduced-motion: no-preference) {
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes imgIn {
            from {
                opacity: 0;
                transform: scale(.92);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes kenburns {
            from {
                transform: scale(1);
            }

            to {
                transform: scale(1.12) translate(-1%, -1%);
            }
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes nudgeDown {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(4px);
            }
        }

        @keyframes pinBounce {

            0%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-4px);
            }

            60% {
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .hero-in {
            animation: fadeUp .7s cubic-bezier(.2, .7, .2, 1) both;
            animation-delay: var(--d, 0s);
        }

        .gal-in {
            animation: imgIn .8s cubic-bezier(.2, .7, .2, 1) both;
            animation-delay: var(--d, 0s);
        }

        .kenburns {
            animation: kenburns 20s ease-in-out infinite alternate;
        }

        .float-y {
            animation: floatY 3.5s ease-in-out infinite;
        }

        .nudge-down {
            animation: nudgeDown 1.4s ease-in-out infinite;
        }

        .pin-bounce {
            animation: pinBounce 2.2s ease-in-out infinite;
        }

        .pop {
            animation: popIn .45s cubic-bezier(.2, .7, .2, 1) both;
            animation-delay: var(--pd, 0s);
        }

        .modal-in {
            animation: modalIn .35s cubic-bezier(.2, .7, .2, 1) both;
        }
    }
</style>
<div id="scroll-progress"></div>

{{-- Contadores animados + barra de progreso de scroll --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Saca .pop / .hero-in cuando termina la animación de entrada,
        // así no pisan los efectos hover (ej: hover:-translate-y)
        document.querySelectorAll('.pop, .hero-in').forEach(function(el) {
            el.addEventListener('animationend', function(e) {
                if (e.target === el) el.classList.remove('pop', 'hero-in');
            });
        });

        // Contadores (data-count): arrancan cuando aparecen en pantalla
        function animarContador(el) {
            const fin = parseFloat(el.dataset.count);
            if (isNaN(fin)) return;
            const sufijo = el.dataset.suffix || '';
            const duracion = 1200;
            const t0 = performance.now();

            function paso(t) {
                const p = Math.min((t - t0) / duracion, 1);
                const suave = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(fin * suave) + sufijo;
                if (p < 1) requestAnimationFrame(paso);
            }
            requestAnimationFrame(paso);
        }

        const contadores = document.querySelectorAll('[data-count]');
        if (!reducirMovimiento && 'IntersectionObserver' in window) {
            const obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        animarContador(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.4
            });
            contadores.forEach(function(el) {
                obs.observe(el);
            });
        }

        // Barra de progreso de scroll
        const barra = document.getElementById('scroll-progress');
        if (barra) {
            let pendiente = false;

            function actualizarBarra() {
                const total = document.documentElement.scrollHeight - window.innerHeight;
                barra.style.transform = 'scaleX(' + (total > 0 ? window.scrollY / total : 0) + ')';
                pendiente = false;
            }
            window.addEventListener('scroll', function() {
                if (!pendiente) {
                    requestAnimationFrame(actualizarBarra);
                    pendiente = true;
                }
            }, {
                passive: true
            });
            actualizarBarra();
        }
    });
</script>