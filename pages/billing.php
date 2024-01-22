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
                            alert("El NIT no está activo.");
                        }
                    } catch (e) {
                        if (this.responseText === "NIT ACTIVO") {
                            nitActivoIcon.style.display = 'inline';
                            // Si el NIT es activo, asigna el valor al campo Cod. Cliente
                            codClienteInput.value = nit;
                        } else {
                            alert("El NIT no está activo.");
                        }
                    }
                } else {
                    alert("Ocurrió un error al verificar el NIT.");
                }
            }
        };

        xhttp.open("GET", "http://localhost:4010/verificaNit?nit=" + nit, true);
        xhttp.send();
    }
    </script>
    <script>
    // Función para cargar los códigos de productos en el combo box
    function cargarCodigosProductos() {
        // Realizar la solicitud AJAX a tu servidor para obtener los códigos de productos
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../php-includes/listaProductos.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                var codigos = JSON.parse(this.responseText); // Asumiendo que es una lista simple

                // Se asume que ya hay un elemento select en el DOM
                var select = document.querySelector('select[name="codigo_producto_servicio[]"]');
                if (select) {
                    // Limpia las opciones existentes primero
                    select.innerHTML = '';

                    // Crea una opción por defecto (opcional)
                    var opcionPorDefecto = document.createElement('option');
                    opcionPorDefecto.value = '';
                    opcionPorDefecto.textContent = 'Selecciona un producto';
                    select.appendChild(opcionPorDefecto);

                    // Agrega los códigos de productos como opciones
                    codigos.forEach(function(codigo) {
                        var option = document.createElement('option');
                        option.value = codigo;
                        option.textContent = codigo;
                        select.appendChild(option);
                    });
                }
            } else {
                console.error('No se pudieron cargar los códigos de productos:', this.statusText);
            }
        };
        xhr.onerror = function() {
            console.error('Ocurrió un error durante la transacción AJAX.');
        };
        xhr.send();
    }

    // Llamar a la función al cargar la página
    document.addEventListener('DOMContentLoaded', cargarCodigosProductos);
    </script>
    <script>
    // Esta función se llama cada vez que se selecciona un producto en el combo box
    function cargarDescripcionProducto(selectElement) {
        var codigoProducto = selectElement.value;
        var descripcionInput = selectElement.parentNode.parentNode.querySelector('input[name="descripcion[]"]');

        if (codigoProducto) {
            // Aquí haces la llamada AJAX para obtener la descripción del producto
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../php-includes/getDescripcionProducto.php?codigoProducto=' + encodeURIComponent(
                codigoProducto), true);
            xhr.onload = function() {
                if (this.status === 200) {
                    // Suponiendo que tu script PHP devuelve un objeto JSON con la descripción
                    var respuesta = JSON.parse(this.responseText);
                    if (respuesta.descripcionProducto) {
                        descripcionInput.value = respuesta.descripcionProducto;
                    }
                }
            };
            xhr.send();
        } else {
            // Si no se ha seleccionado un producto, limpia el campo de descripción
            descripcionInput.value = '';
        }
    }

    // Agrega el evento 'change' a todos los combo box existentes y futuros de la tabla
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tablaProductos').addEventListener('change', function(e) {
            if (e.target && e.target.name === 'codigo_producto_servicio[]') {
                cargarDescripcionProducto(e.target);
            }
        });
    });
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
                                                    <td><input type="number" class="form-control" name="cantidad[]">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="unidad_medida[]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="descripcion[]"
                                                            value="<?php echo htmlspecialchars($descripcionProducto); ?>">
                                                    </td>
                                                    <td><input type="text" class="form-control"
                                                            name="precio_unitario[]"></td>
                                                    <td><input type="text" class="form-control" name="descuento[]"></td>
                                                    <td><input type="text" class="form-control" name="subtotal[]"></td>
                                                    <td><button type="button" class="btn btn-danger"
                                                            onclick="quitarFila(this)">X</button></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <!-- ... Tus filas de totales aquí ... -->
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
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../php-includes/listaActividades.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                var respuesta = JSON.parse(this.responseText);
                var actividades = respuesta.actividades;
                cargarActividadesSelect(actividades);
                cargarCodigosProductos();
            } else {
                console.error('Error en la solicitud: ' + this.status);
            }
        };
        xhr.onerror = function() {
            console.error('Error de conexión');
        };
        xhr.send();
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
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../php-includes/listaActividades.php?codigoActividad=' + encodeURIComponent(codigoActividad), true);
        xhr.onload = function() {
            if (this.status === 200) {
                var respuesta = JSON.parse(this.responseText);
                var codigosProductos = respuesta.codigosProductos;
                cargarCodigosProductosSelect(codigosProductos);
            } else {
                console.error('Error en la solicitud: ' + this.status);
            }
        };
        xhr.onerror = function() {
            console.error('Error de conexión');
        };
        xhr.send();
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
            });
        });
    }

    function agregarFila() {
        // Código para agregar una fila a la tabla de productos
    }

    function quitarFila(btn) {
        // Código para quitar una fila de la tabla
    }
</script>

</body>

</html>