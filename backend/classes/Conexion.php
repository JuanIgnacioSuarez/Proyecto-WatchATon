<?php
class Conexion {
    private $conn;

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "watchaton";

        $this->conn = new mysqli($host, $user, $pass, $db);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
    }

    // Método para insertar
    public function insertar($sql, $tipos, $parametros) {
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($tipos, ...$parametros);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // Método para obtener el último ID insertado
    public function ultimoId() {
        return $this->conn->insert_id;
    }

    // Método para actualizar
    public function actualizar($sql, $tipos, $parametros) {
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($tipos, ...$parametros);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // Método para eliminar
    public function eliminar($sql, $tipos, $parametros) {
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($tipos, ...$parametros);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // Método para consultar (SELECT)
    public function consultar($sql, $tipos = "", $parametros = []) {
        $stmt = $this->conn->prepare($sql);
        if (!empty($tipos)) {
            $stmt->bind_param($tipos, ...$parametros);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $datos;
    }

    // Método para ejecutar consultas sin parámetros (CREATE, ALTER, etc.)
    public function ejecutar($sql) {
        return $this->conn->query($sql);
    }

    // Verificar sanciones activas (tipo 1)
    public function verificarSanciones($email) {
        // Primero obtenemos el ID del usuario
        $idUsuario = $this->existeDato('usuarios', 'ID', 'Correo', $email);
        
        if (!$idUsuario) return 0;

        $sql = "SELECT COUNT(*) as total FROM sanciones WHERE id_usuario = ? AND tipo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        
        return $fila['total'];
    }

    //Esta funcion permite  saber si un dato esta o no en la base
    public function existeDato($tabla, $campo1, $campo2, $valor) {
    $sql = "SELECT $campo1 FROM $tabla WHERE $campo2 = ?";
    $stmt = $this->conn->prepare($sql);

    // Detecta el tipo de dato para bind_param
    if (is_int($valor)) {
        $tipo = "i";
    } elseif (is_double($valor)) {
        $tipo = "d";
    } else {
        $tipo = "s";
    }

    $stmt->bind_param($tipo, $valor);
    $stmt->execute();
    $stmt->bind_result($ID);

    if ($stmt->fetch()) {
        $stmt->close();
        return $ID;
    } else {
        $stmt->close();
        return null;
    }
}

    public function cerrarConexion() {
        $this->conn->close();
    }
}
?>
