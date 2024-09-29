jQuery(document).ready(function ($) {
  $("#btnnuevo").click(function () {
    $("#modalnuevo").modal("show");
    console.log("click nuevo");
  });

  var i = 1;

  $("#add").click(function () {
    i++;
    $("#camposdinamicos").append(`
            <tr id="row-${i}">
              <td>
                <label for="txtnombre" class="col-form-label" style="margin-right: 5px">Pregunta ${i}</label>
              </td>
              
              <td>
                <input type="text" name="name[]" id="name" class="form-control name_list">
              </td>
              
              <td>
                <select name="type[]" id="type" class="form-control type_list" style="margin-left: 5px">
                  <option value="" style="display: none">Seleccionar una opción</option>
                  <option value="1">Si - No</option>
                  <option value="2">Rango 0 - 5</option>
                </select>
              </td>
              
              <td>
                <button type="submit" name="remove" id="${i}" class="btn btn-danger btn_remove" style="margin-left: 5px">X</button>
              </td>
            </tr>
    `);

    return false;
  });

  $(document).on("click", ".btn_remove", function () {
    var button_id = $(this).attr("id");
    $("#row-" + button_id + "").remove();
  });

  $(document).on("click", "a[data-id]", function () {
    var id = this.dataset.id;
    var url = SolicitudesAjax.url;
    $.ajax({
      type: "POST",
      url,
      data: {
        action: "peticioneliminar",
        nonce: SolicitudesAjax.seguridad,
        id,
      },
      success: function () {
        alert("Datos eliminados");
        location.reload();
      },
    });

    return false;
  });
});
