<?php

/**
 * 
 * Plugin Name: Surveys-of-plugin
 * Description: Este plugin se conecta con la API Rest llamada JSON Place Holder
 * Author: Luis Saravia
 * Version: 1.0.0
 * 
 */

require_once dirname(__FILE__) . "/class/shortcode.php";

function Activate()
{
  global $wpdb;

  $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas(
    `EncuestaId` INT NOT NULL AUTO_INCREMENT,
    `Nombre` VARCHAR(45) NULL,
    `ShortCode` VARCHAR(45) NULL,
    PRIMARY KEY (`EncuestaId`)
  );";

  $wpdb->query($sql);

  $sql2 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_detalle(
    `DetalleId` INT NOT NULL AUTO_INCREMENT,
    `EncuestaId` INT NULL,
    `Pregunta` VARCHAR(150) NULL,
    `Tipo` VARCHAR(45) NULL,
    PRIMARY KEY (`DetalleId`)
  );";

  $wpdb->query($sql2);

  $sql3 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_respuesta(
    `RespuestaId` INT NOT NULL AUTO_INCREMENT,
    `DetalleId` INT NULL,
    `Codigo` VARCHAR(45) NULL,
    `Respuesta` VARCHAR(45) NULL,
    PRIMARY KEY (`RespuestaId`)
  );";

  $wpdb->query($sql3);
}
function Deactivate()
{
  flush_rewrite_rules();
}

/**
 * Hook de Activación
 */
register_activation_hook(__FILE__, 'Activate');

/**
 * Hook de Desactivación
 */
register_deactivation_hook(__FILE__, 'Deactivate');

/**
 * Función add_action y hook para añadir el menú
 */
add_action('admin_menu', 'create_menu');

/**
 * Función para crear el menú de tu plugin
 */
function create_menu()
{
  add_menu_page(
    'Super Encuestas', // Título de la página
    'Super Encuentas Menu', // Título del menú
    'manage_options', // Capability
    plugin_dir_path(__FILE__) . 'admin/list-of-surveys.php', // slug
    null, // Función creada para mostrar el contenido de la página
    plugin_dir_url(__FILE__) . 'admin/img/icon.png', // Imagen que tenemos en el plugin para que se muestre en el menú
    "1" // prioridad
  );
}

/**
 * Encolado de Scripts
 */
function queue_scripts($hook) {

  /**
   * queue bootstrap
   */
  if($hook == "Surveys-of-plugin/admin/list-of-surveys.php") {

    /**
     * queue bootstrap JS
     */
    wp_enqueue_script("bootstrapJS", plugins_url('admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));

    /**
     * queue boostrap CSS
     */
    wp_enqueue_style("bootstrapCSS", plugins_url('admin/bootstrap/css/bootstrap.min.css', __FILE__));

    /**
     * queue ajax's request
     */
    wp_enqueue_script("JSExterno", plugins_url('admin/js/surveys-of-plugin.js', __FILE__), array('jquery'));
    wp_localize_script("JSExterno", "SolicitudesAjax", [
      "url" => admin_url("admin-ajax.php"),
      "seguridad" => wp_create_nonce('seg'),
    ]);
  }
}

add_action('admin_enqueue_scripts', 'queue_scripts');


// Ajax

function delete_survey() {
  $nonce = $_POST['nonce'];
  if(!wp_verify_nonce($nonce, 'seg')) {
    die('no tiene permisos para ejecutar ese ajax');
  }

  $id = $_POST['id'];
  global $wpdb;

  $tabla = $wpdb->prefix."encuestas";
  $tabla2 = $wpdb->prefix."encuestas_detalle";

  $wpdb->delete($tabla, array('EncuestaId' => $id));
  $wpdb->delete($tabla2, array('EncuestaId' => $id));
}

add_action('wp_ajax_peticioneliminar', 'delete_survey');


//ShortCode

function createShortCode($atts) {
  $_short = new Shortcode();
  $id = $atts['id'];

  if(isset($_POST['btnguardar'])) {

    $list_questions = $_short->get_survey_details($id);
    $codigo = uniqId();
    foreach($list_questions as $key => $value) {
      $id_question = $value['DetalleId'];

      if(isset($_POST[$id_question])){
        $valortxt = $_POST[$id_question];

        $datos = [
          'DetalleId' => $id_question,
          'Codigo' => $codigo,
          'Respuesta' => $valortxt
        ];
        
        $_short->saveDetail($datos);
      }
    }

    return "Encuesta enviada exitosamente";
  }

  $html = $_short->assembler($id);
  return $html;
}

add_shortcode("ENC", "createShortCode");












/**
 * Función que es llamada para Mostrar el contenido
 */
//  function MostrarContenido() {
//   echo "<h1>Contenido de la  página</h1>";
//  }


//    /**
//    * Función para crear el submenú de tu plugin
//    */
//   add_submenu_page(
//     'sp_menu', // Slug de su padre
//     'Ajustes', // Título de Menú
//     'Ajustes', // Título de la página
//     'manage_options', // Capability
//     'sp_menu_ajustes', // slug del submenú
//     'Submenu' // Call back
//   );

//  function Submenu() {
//   echo "<h1>Submenu</h1>";
//  }
