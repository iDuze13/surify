<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moderación automática de reseñas
    |--------------------------------------------------------------------------
    | Si MODERACION_IA_ACTIVA es false (o no hay API key), las reseñas quedan
    | pendientes y las aprueba o rechaza una persona desde el panel, como antes.
    */

    'activa' => env('MODERACION_IA_ACTIVA', false),

    'openai_key' => env('OPENAI_API_KEY'),

    // Modelo de lenguaje que evalúa spam, fuera de tema, datos personales y fotos.
    // Verificá el nombre vigente en la documentación de OpenAI antes de usarlo.
    'modelo' => env('MODERACION_MODELO', 'gpt-5.4-mini'),

    // API de moderación de OpenAI (gratuita, acepta texto e imágenes).
    'modelo_moderacion' => 'omni-moderation-latest',

    // Segundos máximos de espera por cada llamada a la API.
    'timeout' => 30,
];