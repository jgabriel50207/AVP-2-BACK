<?php
declare(strict_types=1);

/**
 * Valida se o ID é um UUID v4 válido.
 * Esta função está correta.
 */
function isValidUUID(string $uuid): bool 
{
    return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
}

/**
 * Valida a localização, checando se ela existe na lista de localidades permitidas no mapa.
 * ESTA É A VERSÃO CORRIGIDA.
 */
function isValidLocalizacao(string $localizacao): bool 
{
    $localidadesPermitidas = ['A', 'B', 'C', 'D', 'E', 'F'];
    // in_array() verifica se o valor existe dentro do array.
    // strtoupper() garante que 'a' seja tratado como 'A'.
    return in_array(strtoupper($localizacao), $localidadesPermitidas, true);
}

/**
 * Valida o nível (1 a 5).
 * Esta função já está correta.
 */
function isValidNivel(int|string $nivel): bool 
{
    $nivelInt = (int) $nivel;
    return is_numeric($nivel) && $nivelInt >= 1 && $nivelInt <= 5;
}