<?php
declare(strict_types=1);

/**
 * Valida se o ID é um UUID v4 válido.
 */
function isValidUUID(string $uuid): bool {
    return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
}

/**
 * Valida a localização (letra A-Z).
 */
function isValidLocalizacao(string $localizacao): bool {
    return (bool) preg_match('/^[A-Z]$/i', $localizacao);
}

/**
 * Valida o nível (1 a 5).
 */
function isValidNivel(int|string $nivel): bool {
    $nivelInt = (int) $nivel;
    return is_numeric($nivel) && $nivelInt >= 1 && $nivelInt <= 5;
}