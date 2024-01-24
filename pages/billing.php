<?php

require('../php-includes/connect.php');
include('../php-includes/check-login.php');
$userid = $_SESSION['userid'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        Sistema de Facturacion Cegepa SRL
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
    <!-- VueJs -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
    body {
        background-color: #e9ecef;
    }

    .container {
        max-width: 960px;
        margin-top: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .bordered-table {
        border: 1px solid #000;
    }

    .bordered-table th,
    .bordered-table td {
        border: 1px solid #000;
        padding: .5rem;
    }

    .bordered-table th {
        background-color: #f2f2f2;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #000;
    }

    .table td,
    .table th {
        padding: .5rem;
        vertical-align: top;
        border-top: 1px solid #000;
    }

    .text-end {
        text-align: end;
    }

    .fw-bold {
        font-weight: bold;
    }

    .subtotal-row td {
        padding: .5rem;
        vertical-align: top;
        border-top: 2px solid #000;
    }

    /* Estilo para el mensaje de NIT activo */
    .nit-activo {
        color: green;
        display: none;
        margin-left: 5px;
    }
    </style>
    <script>
    function verificarNIT() {
        var nit = document.getElementById('nit_ci_cex').value;
        var codClienteInput = document.getElementById('cod_cliente');
        var nitActivoIcon = document.getElementById('nitActivoIcon');

        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                nitActivoIcon.style.display = 'none';

                if (this.status == 200) {
                    try {
                        var respuesta = JSON.parse(this.responseText);
                        if (respuesta.estado === "NIT ACTIVO") {
                            nitActivoIcon.style.display = 'inline';
                            // Si el NIT es activo, asigna el valor al campo Cod. Cliente
                            codClienteInput.value = nit;
                        } else {
                            alert("El NIT no está activo o INEXISTENTE.");
                        }
                    } catch (e) {
                        if (this.responseText === "NIT ACTIVO") {
                            nitActivoIcon.style.display = 'inline';
                            // Si el NIT es activo, asigna el valor al campo Cod. Cliente
                            codClienteInput.value = nit;
                        } else {
                            alert("El NIT no está activo o INEXISTENTE.");
                        }
                    }
                } else {
                    alert("Ocurrió un error al verificar el NIT.");
                }
            }
        };

        xhttp.open("GET", "http://localhost:8591/check?nit=" + nit, true);
        xhttp.send();
    }
    </script>

</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>
    <?php include '../php-includes/menu-lateral.php'; ?>
    <main class="main-content position-relative border-radius-lg ">
        <?php include '../php-includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div id="app" class="row">
                <div class="col-lg-12">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 mx-auto">
                                <h2 class="text-center mb-4">Factura de Venta</h2>
                                <form>
                                    <div class="mb-3">
                                        <label for="tipo_actividad" class="form-label">Seleccione Actividad:</label>
                                        <select class="form-control" id="tipo_actividad">
                                            <!-- Opciones cargadas desde la base de datos -->

                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nombre_razon_social" class="form-label">Nombre/Razón Social:</label>
                                        <input type="text" class="form-control" id="nombre_razon_social">
                                    </div>

                                    <div class="mb-3">
                                        <label for="nit_ci_cex" class="form-label">NIT/CI/CEX:</label>
                                        <input type="text" class="form-control" id="nit_ci_cex" onblur="verificarNIT()"
                                            pattern="[0-9]+" title="Por favor, ingrese solo números">
                                        <!-- Ícono de verificación que se mostrará cuando el NIT sea activo -->
                                        <span id="nitActivoIcon" class="nit-activo">&#10004;</span>
                                    </div>

                                    <div class="mb-3">
                                        <label for="cod_cliente" class="form-label">Cod. Cliente:</label>
                                        <input type="text" class="form-control" id="cod_cliente" readonly>
                                    </div>
                                    <!-- Envoltura responsiva para la tabla -->
                                    <div class="table-responsive">
                                        <table id="tablaProductos" class="table bordered-table">
                                            <thead>
                                                <tr>
                                                    <th>CÓDIGO PRODUCTO/SERVICIO</th>
                                                    <th>CANTIDAD</th>
                                                    <th>UNIDAD DE MEDIDA</th>
                                                    <th>DESCRIPCIÓN</th>
                                                    <th>PRECIO UNITARIO</th>
                                                    <th>DESCUENTO</th>
                                                    <th>SUBTOTAL</th>
                                                    <th>QUITAR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="form-control" name="codigo_producto_servicio[]">
                                                            <!-- Las opciones se cargarán dinámicamente aquí -->

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control cantidad"
                                                            name="cantidad[]" oninput="calcularSubtotal(this)">
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="unidad_medida[]">
                                                            <!-- Las opciones se cargarán dinámicamente aquí -->
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="descripcion"
                                                            name="descripcion[]" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control precio-unitario"
                                                            name="precio_unitario[]" oninput="calcularSubtotal(this)">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="descuento[]"></td>
                                                    <td>
                                                        <input type="text" class="form-control subtotal"
                                                            name="subtotal[]" readonly>
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger"
                                                            onclick="quitarFila(this)">X</button></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <!-- ... Tus filas de totales aquí ... -->
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td>TOTAL</td>
                                                    <td><input type="text" class="form-control" id="total" name="total"
                                                            readonly></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="text-center">
                                        <button type="button" onclick="agregarFila()" class="btn btn-secondary">Agregar
                                            Producto/Servicio</button>
                                        <button type="submit" class="btn btn-primary">Enviar Factura</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 mt-4">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <h6 class="mb-0">Billing Information</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <ul class="list-group">
                                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-3 text-sm">Oliver Liam</h6>
                                        <span class="mb-2 text-xs">Company Name: <span
                                                class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                                        <span class="mb-2 text-xs">Email Address: <span
                                                class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                                        <span class="text-xs">VAT Number: <span
                                                class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <a class="btn btn-link text-danger text-gradient px-3 mb-0"
                                            href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                        <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i
                                                class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-3 text-sm">Lucas Harper</h6>
                                        <span class="mb-2 text-xs">Company Name: <span
                                                class="text-dark font-weight-bold ms-sm-2">Stone Tech Zone</span></span>
                                        <span class="mb-2 text-xs">Email Address: <span
                                                class="text-dark ms-sm-2 font-weight-bold">lucas@stone-tech.com</span></span>
                                        <span class="text-xs">VAT Number: <span
                                                class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <a class="btn btn-link text-danger text-gradient px-3 mb-0"
                                            href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                        <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i
                                                class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-3 text-sm">Ethan James</h6>
                                        <span class="mb-2 text-xs">Company Name: <span
                                                class="text-dark font-weight-bold ms-sm-2">Fiber Notion</span></span>
                                        <span class="mb-2 text-xs">Email Address: <span
                                                class="text-dark ms-sm-2 font-weight-bold">ethan@fiber.com</span></span>
                                        <span class="text-xs">VAT Number: <span
                                                class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <a class="btn btn-link text-danger text-gradient px-3 mb-0"
                                            href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                        <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i
                                                class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="card h-100 mb-4">
                        <div class="card-header pb-0 px-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-0">Your Transaction's</h6>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end align-items-center">
                                    <i class="far fa-calendar-alt me-2"></i>
                                    <small>23 - 30 March 2020</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
                            <ul class="list-group">
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-arrow-down"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                                            <span class="text-xs">27 March 2020, at 12:30 PM</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                                        - $ 2,500
                                    </div>
                                </li>
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-arrow-up"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Apple</h6>
                                            <span class="text-xs">27 March 2020, at 04:30 AM</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                                        + $ 2,000
                                    </div>
                                </li>
                            </ul>
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder my-3">Yesterday</h6>
                            <ul class="list-group">
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-arrow-up"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Stripe</h6>
                                            <span class="text-xs">26 March 2020, at 13:45 PM</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                                        + $ 750
                                    </div>
                                </li>
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-arrow-up"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">HubSpot</h6>
                                            <span class="text-xs">26 March 2020, at 12:30 PM</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                                        + $ 1,000
                                    </div>
                                </li>
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-arrow-up"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Creative Tim</h6>
                                            <span class="text-xs">26 March 2020, at 08:30 AM</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                                        + $ 2,500
                                    </div>
                                </li>
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-dark mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i
                                                class="fas fa-exclamation"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Webflow</h6>
                                            <span class="text-xs">26 March 2020, at 05:00 AM</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                        Pending
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../php-includes/footer.php'; ?>
        </div>
    </main>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarActividades();
        document.getElementById('tipo_actividad').addEventListener('change', cargarCodigosProductos);
    });

    function cargarActividades() {
        fetch('../php-includes/listaActividades.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                cargarActividadesSelect(data.actividades);
                cargarCodigosProductos();
            })
            .catch(error => console.error('Error de conexión:', error));
    }

    function cargarActividadesSelect(actividades) {
        var selectElement = document.getElementById('tipo_actividad');
        selectElement.innerHTML = '';

        actividades.forEach(function(actividad) {
            var option = document.createElement('option');
            option.value = actividad;
            option.textContent = actividad;
            selectElement.appendChild(option);
        });
    }

    function cargarCodigosProductos() {
        var codigoActividad = document.getElementById('tipo_actividad').value;
        fetch('../php-includes/listaActividades.php?codigoActividad=' + encodeURIComponent(codigoActividad))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.status);
                }
                return response.json();
            })
            .then(data => cargarCodigosProductosSelect(data.codigosProductos))
            .catch(error => console.error('Error de conexión:', error));
    }

    function cargarCodigosProductosSelect(codigosProductos) {
        var selects = document.querySelectorAll('select[name="codigo_producto_servicio[]"]');
        selects.forEach(function(selectElement) {
            selectElement.innerHTML = '<option value="">Selecciona un producto</option>';
            codigosProductos.forEach(function(codigo) {
                var option = document.createElement('option');
                option.value = codigo;
                option.textContent = codigo;
                selectElement.appendChild(option);
                selectElement.onchange = cargarDescripcionProducto;
            });
        });
    }

    function cargarDescripcionProducto() {
        var selectElement = this;
        var codigoProducto = this.value;
        var codigoActividadElement = document.getElementById('tipo_actividad');
        var codigoActividad = '';

        // Mapear la selección del usuario a los valores numéricos correctos
        if (codigoActividadElement.value === 'Actividad Principal') {
            codigoActividad = '464300';
        } else if (codigoActividadElement.value === 'Actividad Secundaria') {
            codigoActividad = '001220';
        }

        console.log('Código de Producto:', codigoProducto);
        console.log('Código de Actividad:', codigoActividad);

        fetch(
                `../php-includes/obtenerDescripcionProducto.php?codigoProducto=${encodeURIComponent(codigoProducto)}&codigoActividad=${encodeURIComponent(codigoActividad)}`
            )
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.status);
                }
                return response.text(); // Recibir la respuesta como texto
            })
            .then(descripcion => {
                var descripcionInput = selectElement.closest('tr').querySelector('input[name="descripcion[]"]');
                descripcionInput.value = descripcion;
            })
            .catch(error => console.error('Error de conexión:', error));
    }

    function cargarUnidadesDeMedida() {
        // Asegúrate de que la URL sea la correcta para tu configuración
        fetch('../php-includes/obtenerUnidadesMedida.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.statusText);
                }
                return response.text(); // Recibir la respuesta como texto plano
            })
            .then(texto => {
                console.log('Unidades de medida recibidas:', texto); // Para depuración
                var unidades = texto.trim().split("\n").filter(Boolean); // Filtra líneas vacías
                var selectsUnidadMedida = document.querySelectorAll('select[name="unidad_medida[]"]');
                console.log('Selects encontrados:', selectsUnidadMedida.length); // Para depuración

                selectsUnidadMedida.forEach(select => {
                    select.innerHTML = ''; // Limpia las opciones existentes
                    unidades.forEach(unidad => {
                        var option = document.createElement('option');
                        option.value = unidad.trim();
                        option.textContent = unidad.trim();
                        select.appendChild(option);
                    });
                });
            })
            .catch(error => console.error('Error de conexión:', error));
    }

    document.addEventListener('DOMContentLoaded', cargarUnidadesDeMedida);

    function calcularSubtotal(elemento) {
        // Encuentra la fila actual en la que el input está ubicado
        var filaActual = elemento.closest('tr');

        // Obtén los valores de cantidad y precio unitario
        var cantidad = filaActual.querySelector('.cantidad').value;
        var precioUnitario = filaActual.querySelector('.precio-unitario').value;

        // Asegúrate de que ambos valores son números y calcula el subtotal
        var subtotal = (cantidad && precioUnitario) ? (cantidad * precioUnitario) : 0;

        // Actualiza el campo de subtotal en la fila actual
        filaActual.querySelector('.subtotal').value = subtotal.toFixed(2); // Redondea a dos decimales
    }


    // Función para agregar una fila a la tabla de productos
    function agregarFila() {
        // Obtener la tabla y su cuerpo
        var tabla = document.getElementById("tablaProductos").getElementsByTagName('tbody')[0];

        // Crear una nueva fila
        var fila = tabla.insertRow();

        // Agregar celdas a la fila
        var celdaCodigo = fila.insertCell(0);
        var celdaCantidad = fila.insertCell(1);
        var celdaUnidadMedida = fila.insertCell(2);
        var celdaDescripcion = fila.insertCell(3);
        var celdaPrecioUnitario = fila.insertCell(4);
        var celdaDescuento = fila.insertCell(5);
        var celdaSubtotal = fila.insertCell(6);
        var celdaQuitar = fila.insertCell(7);

        // Llenar las celdas con elementos HTML (puedes personalizar esto según tus necesidades)
        celdaCodigo.innerHTML = '<select class="form-control" name="codigo_producto_servicio[]"></select>';
        celdaCantidad.innerHTML =
            '<input type="number" class="form-control cantidad" name="cantidad[]" oninput="calcularSubtotal(this)">';
        celdaUnidadMedida.innerHTML = '<select class="form-control" name="unidad_medida[]"></select>';
        celdaDescripcion.innerHTML = '<input type="text" class="form-control" name="descripcion[]" readonly>';
        celdaPrecioUnitario.innerHTML =
            '<input type="text" class="form-control precio-unitario" name="precio_unitario[]" oninput="calcularSubtotal(this)">';
        celdaDescuento.innerHTML = '<input type="text" class="form-control" name="descuento[]">';
        celdaSubtotal.innerHTML = '<input type="text" class="form-control subtotal" name="subtotal[]" readonly>';
        celdaQuitar.innerHTML = '<button type="button" class="btn btn-danger" onclick="quitarFila(this)">X</button>';

        // Después de agregar la fila, cargar códigos de productos y unidades de medida
        cargarCodigosProductos();
        cargarUnidadesDeMedida();
        // calcularTotal();
    }

    // Función para quitar una fila de la tabla
    function quitarFila(btn) {
        // Obtener la fila padre del botón
        var fila = btn.parentNode.parentNode;

        // Obtener la tabla y su cuerpo
        var tabla = document.getElementById("tablaProductos").getElementsByTagName('tbody')[0];

        // Quitar la fila de la tabla
        tabla.removeChild(fila);
    }

    // Resto de tu código para cargar datos dinámicos (cargarActividades, cargarCodigosProductos, cargarUnidadesDeMedida, calcularSubtotal, etc.)

    // Event listener para cargar unidades de medida al cargar la página
    document.addEventListener('DOMContentLoaded', cargarUnidadesDeMedida);
    </script>
    <script>
    // Función para calcular y actualizar el total
    function calcularTotal() {
        var filasSubtotal = document.querySelectorAll('.subtotal');
        var total = 0;

        filasSubtotal.forEach(function(subtotalInput) {
            var subtotal = parseFloat(subtotalInput.value) ||
            0; // Convierte a número o usa 0 si no se puede convertir
            total += subtotal;
        });

        // Actualizar el campo TOTAL
        var totalInput = document.getElementById('total');
        totalInput.value = total.toFixed(2); // Redondear a dos decimales y mostrar en el campo TOTAL
    }

    // Función para calcular el subtotal cuando cambie la cantidad o el precio unitario
    function calcularSubtotal(input) {
        var cantidad = parseFloat(input.parentElement.parentElement.querySelector('.cantidad').value) || 0;
        var precioUnitario = parseFloat(input.parentElement.parentElement.querySelector('.precio-unitario').value) || 0;
        var subtotal = cantidad * precioUnitario;
        input.parentElement.parentElement.querySelector('.subtotal').value = subtotal.toFixed(2);

        // Llamar a calcularTotal después de actualizar el subtotal
        calcularTotal();
    }

    // Función para quitar una fila de la tabla
    function quitarFila(button) {
        var fila = button.parentElement.parentElement;
        fila.remove();

        // Llamar a calcularTotal después de quitar una fila
        calcularTotal();
    }

    // Llamar a la función para calcular el total inicialmente y cada vez que cambie un subtotal
    document.addEventListener('DOMContentLoaded', calcularTotal);
    </script>

</body>

</html>