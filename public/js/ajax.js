$(function () {
  // Buscar Cliente EN LA SECCION DE VENTA Y PRESUPUESTOS
  $('#client_dni').keyup(function (e) {
    e.preventDefault();

    let client_dni = $(this).val();
    let action = 'clientSearch';

    $.ajax({
      url: '../models/ClientModel.php',
      type: 'POST',
      async: true,
      data: { action, client_dni },
      success: function (response) {
        if (response == 0) {
          $('#client_id').val('');
          $('#client_name').val('');
          $('#client_phone').val('');
          $('#client_address').val('');
          $('#client_email').val('');
          $('.client_new_btn').slideDown(); // Aqui mostramos el boton de agregar
        } else {
          let data = $.parseJSON(response);

          $('#client_id').val(data.id);
          $('#client_name').val(data.name);
          $('#client_phone').val(data.phoneNumber);
          $('#client_address').val(data.address);
          $('#client_email').val(data.email);
          $('.client_new_btn').slideUp(); // Aqui ocultamos el boton de agregar un nuevo cliente
          $('#client_name').attr('disabled', 'disabled');
          $('#client_phone').attr('disabled', 'disabled');
          $('#client_address').attr('disabled', 'disabled');
          $('#client_email').attr('disabled', 'disabled');
          $('#client_register_div').slideUp(); // Aqui mostramos el boton de agregar
        }
      },
      error: function (error) {
        console.log('Tenemos el siguiente error:', error);
      },
    });
  });

  // Activamos los campos para registrar a un nuevo cliente desde el menu de ventas y presupuestos
  $('#client_new_btn').click(function (e) {
    e.preventDefault();
    $('#client_name').removeAttr('disabled');
    $('#client_phone').removeAttr('disabled');
    $('#client_address').removeAttr('disabled');
    $('#client_email').removeAttr('disabled');
    $('#client_register_div').slideDown();
  });

  $('#sale_btn').hide();
  $('#cancel_sale_btn').hide();
  $('#cancel_estimate_btn').hide();
  $('#estimate_btn').hide();

  // BOTON DE ANULAR VENTA
  $('#cancel_sale_btn').click(function (e) {
    e.preventDefault();

    let rows = $('#sale_detail tr').length;

    if (!rows > 0) {
      alert('Aún no has agregado productos para anular');
      return;
    }

    let action = 'cancelSale';

    $.ajax({
      url: '../models/SaleModel.php',
      type: 'POST',
      async: true,
      data: { action },
      success: function (response) {
        if (response != 0) {
          location.reload();
        }
      },
      error: function (error) {
        console.log('Tenemos el siguiente error:', error);
      },
    });
  });

  // BOTON DE ANULAR presupuesto
  $('#cancel_estimate_btn').click(function (e) {
    e.preventDefault();

    let rows = $('#sale_detail tr').length;
    let action = 'cancelEstimate';

    if (!rows > 0) {
      alert('Aún no has agregado productos para anular');
      return;
    }

    $.ajax({
      url: '../models/EstimateModel.php',
      type: 'POST',
      async: true,
      data: { action },
      success: function (response) {
        if (response != 0) {
          location.reload();
        }
      },
      error: function (error) {
        console.log('Tenemos el siguiente error:', error);
      },
    });
  });

  // Búsqueda de productos en la sección de ventas
  $('#product_number').keyup(function (e) {
    e.preventDefault();

    let product = $(this).val().trim();
    let action = /[0-9]/.test(product) ? 'productInfowihtCode' : 'productInfowihtName';

    if (/[0-9a-zA-Z]/.test(product)) {
      searchProduct(product, action);
    } else {
      console.log('El valor ingresado no contiene números ni letras');
      // Aquí puedes agregar alguna lógica adicional si el valor no contiene números ni letras
    }
  });

  // Búsqueda de productos en la sección de presupuesto
  $('#budget_product_code').keyup(function (e) {
    e.preventDefault();

    let product = $(this).val().trim();
    let action = /[0-9]/.test(product) ? 'productInfowihtCode' : 'productInfowihtName';

    if (/[0-9a-zA-Z]/.test(product)) {
      searchProduct(product, action);
    } else {
      console.log('El valor ingresado no contiene números ni letras');
      // Aquí puedes agregar alguna lógica adicional si el valor no contiene números ni letras
    }
  });

  // Agregar un producto al detalle de la venta
  $('#product_add').click(function (e) {
    e.preventDefault();

    if ($('#product_quantity').val() > 0) {
      // Obtener el valor actual de #product_number y limpiar espacios al inicio y final
      let productNumberValue = $('#product_number').val().trim();

      // Verificar si el valor no es un número
      if (!$.isNumeric(productNumberValue)) {
        // Asignar el valor actual de #name al campo de entrada #product_number
        $('#product_number').val($('#name').html().trim());
      }

      // Obtener el valor actual de #product_number después de la posible asignación
      let product = $('#product_number').val().trim(); // Aquí se usa el valor de #product_number

      let quantity = $('#product_quantity').val();
      let action = 'addProductToDetail';

      $.ajax({
        url: '../models/ProductModel.php',
        type: 'POST',
        async: true,
        data: { action, product, quantity },
        success: function (response) {
          if (response != 'error') {
            let info = JSON.parse(response);

            $('#sale_detail').html(info.detalle);
            $('#detalle_totales').html(info.totales);
            $('#product_number').val('');
            $('#name').html('-');
            $('#stock').html('-');
            $('#product_quantity').val('0');
            $('#price').html('0.00');
            $('#total_price').html('0.00');
            $('#product_quantity').attr('disabled', 'disabled');
            $('#product_add').slideUp();
          } else {
            console.log('No hay dato');
          }
        },
        error: function (error) {
          console.log('Tenemos el siguiente error:', error);
        },
      });
    }
  });

  // Boton para agregar producto al detalle del presupuesto
  $('#add_product_estimate').click(function (e) {
    e.preventDefault();

    if ($('#product_quantity').val() > 0) {
      // Obtener el valor actual de #budget_product_code y limpiar espacios al inicio y final
      let productCodeValue = $('#budget_product_code').val().trim();

      // Verificar si el valor no es un número
      if (!$.isNumeric(productCodeValue)) {
        // Asignar el valor actual de #name al campo de entrada #budget_product_code
        $('#budget_product_code').val($('#name').html().trim());
      }

      // Obtener el valor actual de #budget_product_code después de la posible asignación
      let product = $('#budget_product_code').val().trim(); // Aquí se usa el valor de #budget_product_code

      let quantity = $('#product_quantity').val();
      let action = 'addProductToDetail';

      $.ajax({
        url: '../models/EstimateModel.php',
        type: 'POST',
        async: true,
        data: { action, product, quantity },
        success: function (response) {
          if (response != 'error') {
            let info = JSON.parse(response);

            $('#sale_detail').html(info.detalle);
            $('#detalle_totales').html(info.totales);
            $('#budget_product_code').val('');
            $('#name').html('-');
            $('#stock').html('-');
            $('#product_quantity').val('0');
            $('#price').html('0.00');
            $('#total_price').html('0.00');
            $('#product_quantity').attr('disabled', 'disabled');
            $('#add_product_estimate').slideUp();
          } else {
            console.log('No hay dato');
          }
        },
        error: function (error) {
          console.log('Tenemos el siguiente error:', error);
        },
      });
    }
  });

  // Generar nueva venta
  $('#sale_btn').click(function (e) {
    e.preventDefault();

    let rows = $('#sale_detail tr').length;
    let totalWithDiscount = $('#totalWithDiscount').val();
    let action = 'processSale';
    let client_dni = $('#client_dni').val();

    if (!rows > 0) {
      alert('Aún no has agregado productos para generar una venta');
      return;
    }

    if (!client_dni > 0) {
      alert('Aún no has seleccionado un cliente');
      return;
    }

    if (!confirm('Presiona aceptar para generar la venta')) {
      return false;
    }

    // Primero hacemos una pegada para saber el id del cliente para asociarlo a la factura (venta)
    $.ajax({
      url: '../models/ClientModel.php',
      type: 'POST',
      data: { action: 'clientSearch', client_dni },
      success: function (response) {
        if (response == 0) {
          console.log('No hay datos');
          return;
        }

        let datos = $.parseJSON(response);

        // Luego generamos la venta con los datos del cliente
        $.ajax({
          url: '../models/SaleModel.php',
          type: 'POST',
          async: true,
          data: { action, clientId: datos.id, totalWithDiscount }, // EL ACTION DE ACA VIENE DE ARRIBA
          success: function (response) {
            if (response != 0) {
              let info = JSON.parse(response);

              generateInvoicePDF(info.clientId, info.id, totalWithDiscount);
              location.reload();
            } else {
              console.log('No hay datos');
            }
          },
          error: function (error) {
            console.log('Tenemos el siguiente error:', error);
          },
        });
      },
      error: function (error) {
        console.log('Tenemos el siguiente error:', error);
      },
    });
  });

  // Generar nuevo presupuesto
  $('#estimate_btn').click(function (e) {
    e.preventDefault();

    let rows = $('#sale_detail tr').length;
    let totalWithDiscount = $('#totalWithDiscount').val();
    let client_dni = $('#client_dni').val();

    if (!rows > 0) {
      alert('Aún no has agregado productos para generar un presupuesto');
      return;
    }

    if (!client_dni > 0) {
      alert('Aún no has seleccionado un cliente');
      return;
    }

    if (!confirm('Presiona aceptar para generar el presupuesto')) {
      return false;
    }

    // Primero hacemos una pegada para saber el id del cliente para asociarlo a la factura (presupuesto)
    $.ajax({
      url: '../models/ClientModel.php',
      type: 'POST',
      data: { action: 'clientSearch', client_dni },
      success: function (response) {
        if (response == 0) {
          console.log('No hay datos');
          return;
        }

        let datos = $.parseJSON(response);
        console.log('datos', datos);

        // Luego generamos el presupuesto con los datos del cliente
        $.ajax({
          url: '../models/EstimateModel.php',
          type: 'POST',
          async: true,
          data: { action: 'procesarPresupuesto', clientId: datos.id, totalWithDiscount },
          success: function (response) {
            if (response != 0) {
              let info = JSON.parse(response);
              console.log('info', info);
              generateEstimatePDF(info.clientId, info.id, totalWithDiscount);
              location.reload();
            } else {
              console.log('No hay datos');
            }
          },
          error: function (error) {
            console.log('Tenemos el siguiente error:', error);
          },
        });
      },
      error: function (error) {
        console.log('Tenemos el siguiente error:', error);
      },
    });
  });

  /* DataTable BABY */
  $('#table').DataTable({
    dom: 'Bfrtilp',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '<i class="fa fa-file-excel-o"> </i> ',
        titleAttr: 'Exportar a Excel',
        className: 'btn btn-primary ',
        exportOptions: {
          columns: ':visible',
          /* modifier: {
            page: 'all', // Exporta todas las filas, no solo la página visible
          }, */
        },
      },
      {
        extend: 'pdfHtml5',
        text: '<i class="fa fa-file-pdf-o"> </i>',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        exportOptions: {
          columns: ':visible',
          /* modifier: {
            page: 'all', // Exporta todas las filas, no solo la página visible
          }, */
        },
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print icon-print"> </i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-secondary',
        exportOptions: {
          columns: ':visible',
          /* modifier: {
            page: 'all', // Exporta todas las filas, no solo la página visible
          }, */
        },
      },
    ],
    order: [[0, 'desc']], // Asegúrate de que la columna de date tenga el índice 0
    pageLength: 10, // Mostrar 50 registros por página
    language: {
      decimal: '',
      emptyTable: 'No hay datos',
      info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
      infoEmpty: 'Mostrando 0 a 0 de 0 registros',
      infoFiltered: '(Filtro de _MAX_ total registros)',
      infoPostFix: '',
      thousands: ',',
      lengthMenu: 'Mostrar _MENU_ registros',
      loadingRecords: 'Cargando...',
      processing: 'Procesando...',
      search: 'Buscar:',
      zeroRecords: 'No se encontraron coincidencias',
      paginate: {
        first: 'Primero',
        last: 'Ultimo',
        next: 'Siguiente',
        previous: 'Anterior',
      },
      aria: {
        sortAscending: ': Activar orden de columna ascendente',
        sortDescending: ': Activar orden de columna desendente',
      },
    },
  });

  //Descargar factura
  $('.download_invoice').click(function (e) {
    e.preventDefault();

    var clientId = $(this).attr('clientId');
    var invoiceId = $(this).attr('invoiceId');
    var totalWithDiscount = $(this).attr('totalWithDiscount');

    generateInvoicePDF(clientId, invoiceId, totalWithDiscount);
  });

  //Descargar presupuesto
  $('.download_estimate').click(function (e) {
    e.preventDefault();

    var clientId = $(this).attr('clientId');
    var estimateId = $(this).attr('estimateId');
    var totalWithDiscount = $(this).attr('totalWithDiscount');

    generateEstimatePDF(clientId, estimateId, totalWithDiscount);
  });
}); // fin ready
