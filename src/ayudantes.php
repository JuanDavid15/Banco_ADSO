<?php

declare(strict_types=1);

// Funciones sueltas del proyecto: se cargan con el autoload "files" de Composer
// (no son clases, por eso no van por PSR-4).

// Escapa todo lo que se imprima en HTML (evita XSS).
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// Solo para MOSTRAR dinero (los cálculos nunca se hacen con float).
function formatoMoneda(string $valor): string
{
    return '$ ' . number_format((float) $valor, 2, ',', '.');
}
