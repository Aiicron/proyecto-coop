<?php
class UsuarioControlador {
    // Muestra la página del formulario (por defecto)
    public function mostrarFormulario() {
        require_once __DIR__ . "/../Vistas/form.php";
    }

    // Podés agregar otros métodos si luego querés mostrar más vistas
    public function mostrarNosotros() {
        require_once __DIR__ . "/../Vistas/nosotros.php";
    }
}
?>
