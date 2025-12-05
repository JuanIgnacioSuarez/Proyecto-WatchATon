<?php
require_once('../../classes/Conexion.php');
$conexion = new Conexion();

// Attempt to add column directly. If it exists, it might error or we can filter logic.
// Simplest way in raw SQL without stored procedures in MariaDB/MySQL is often just try/catch or check information_schema
// BUT Conexio class methods are limited.

// Let's try to query it first.
$sql = "SELECT nombre FROM anuncios LIMIT 1";
$res = $conexion->consultar($sql);

if ($res === [] || isset($res['error'])) { 
    // Usually Conexion returns [] for no rows, but if query fails it might return something else depending on implementation
    // Assuming if query fails (column missing), we need to add it.
    // However, custom Conexion implementation might just die() or return empty.
    
    // Safer approach: Run ALTER IGNORE or just ALTER and silence error
   
    $sqlAlter = "ALTER TABLE anuncios ADD COLUMN nombre VARCHAR(100) NOT NULL DEFAULT '' AFTER ID_anuncio";
    // Using mysqli directly since Conexion::consultar might expect SELECT
    // check Conexion source code?
    
    // Let's check Conexion.php
}
?>
