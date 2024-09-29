<?php

class Shortcode {
  public function get_survey($surveyId) {
    global $wpdb;

    $table = $wpdb->prefix . "encuestas";
    $query = "SELECT * FROM $table  WHERE EncuestaId = $surveyId";
    
    $data = $wpdb->get_results($query, ARRAY_A);
    if (empty($data)) {
      $data = array();
    }

    return $data[0];
  }

  public function get_survey_details($surveyId) {
    global $wpdb;

    $table = $wpdb->prefix . "encuestas_detalle";
    $query = "SELECT * FROM $table  WHERE EncuestaId = '$surveyId'";
    
    $data = $wpdb->get_results($query, ARRAY_A);
    if (empty($data)) {
      $data = array();
    }

    return $data;
  }

  public function FormOpen($title) {
    $html = "     
      <div class='wrap'>
        <h4>$title</h4>
        <br>
        <form method='POST'>
    ";

    return $html;
  }

  public function FormClose() {
    $html = "
        <br>
        <button type='submit' id='btnguardar' name='btnguardar' class='page-title-action'>enviar</button>
        </form>
      </div>
    ";

    return $html;
  }

  public function FromInput($detailId, $question, $type) {
    $html = "";

    switch($type) {
      case $type == '1':
        $html .="
          <div class='form-group'>
            <p><b>$question</b></p>
            <div class='col-sm-8'>
              <select class='form-control' id='$detailId' name='$detailId'>
                <option value='' style='display: none'> Seleccionar opción </option>
                <option value='SI'> SI </option>
                <option value='NO'> NO </option>
              </select>
            <div>
          </div>
        ";
        break;
      case $type == "2":
        $html = "";
        break;
      case $type == "3":
        $html = "";
        break;
    }

    return $html;
  }

  public function assembler($surveyId) {
    $enc = $this->get_survey($surveyId);
    $name = $enc['Nombre'];

    $questions = "";
    $list_questions = $this->get_survey_details($surveyId);

    foreach($list_questions as $key => $value) {
      $detailId = $value['DetalleId'];
      $question = $value['Pregunta'];
      $type = $value['Tipo'];
      $encId = $value['EncuestaId'];

      if($encId == $surveyId) {
        $questions .= $this->FromInput($detailId, $question, $type);
      }
    }
    
    $html = $this->FormOpen($name);
    $html .= $questions;
    $html .= $this->FormClose();

    return $html;
  }

  public function saveDetail($data) {
    global $wpdb;

    $table = $wpdb->prefix .'encuestas_respuesta';
    
    return $wpdb->insert($table, $data);
  }
}