<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moderación automática de reseñas
    |--------------------------------------------------------------------------
    | Si MODERACION_IA_ACTIVA es false (o no hay OPENAI_API_KEY), las reseñas quedan
    | pendientes y las aprueba o rechaza una persona desde el panel, como antes.
    */

    'activa' => env('MODERACION_IA_ACTIVA', false),

    /*
    | Etapa 1: API de moderación de OpenAI (gratuita, acepta texto e imágenes).
    | Usa OPENAI_API_KEY, pero NO consume crédito.
    */
    'openai_key' => env('OPENAI_API_KEY'),
    'modelo_moderacion' => 'omni-moderation-latest',

    /*
    | Etapa 2: quién evalúa spam, fuera de tema, datos personales y fotos.
    |   'gemini'  -> Google Gemini (tiene nivel gratuito, sin tarjeta)
    |   'openai'  -> modelo de OpenAI (requiere crédito en la cuenta)
    |   'ninguno' -> sin etapa 2: lo que pasa la etapa 1 queda pendiente para revisión manual
    */
    'proveedor' => env('MODERACION_PROVEEDOR', 'gemini'),

    // Gemini
    'gemini_key' => env('GEMINI_API_KEY'),
    // Verificá en Google AI Studio qué modelos tiene habilitados tu clave.
    'modelo_gemini' => env('MODERACION_MODELO_GEMINI', 'gemini-3.5-flash'),

    // OpenAI (solo si proveedor = openai)
    'modelo' => env('MODERACION_MODELO', 'gpt-5.4-mini'),

    // Segundos máximos de espera por cada llamada a una API.
    'timeout' => 30,
];