<div class="row wrapper border-bottom white-bg page-heading animated fadeInRight">
    <div>
        <a href="#" class="btn btn-primary btn-sm m-3" id="client_new_btn"><i class="fa fa-user-plus"> Nuevo Cliente </i> </a>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group my-4">
                <h1 class="text-center">Datos Del Cliente</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="action" value="client_register">
                        <input type="hidden" name="name" value="sale_new">
                        <div id="alertContainer">
                            <div id="alertContainer">
                                <?= isset($alert) ? $alert : ''; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><b>DNI</b></label>
                                    <input type="number" name="client_dni" id="client_dni" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><b>Nombre (Razón social)</b></label>
                                    <input type="text" name="client_name" id="client_name" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><b>Teléfono</b></label>
                                    <input type="number" name="client_phone" id="client_phone" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><b>Dirección</b></label>
                                    <input type="text" name="client_address" id="client_address" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><b>Correo</b></label>
                                    <input type="text" name="client_email" id="client_email" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="mt-4" id="client_register_div" style="display: none;">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"> Registrar Nuevo Cliente </i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <h1 class="text-center mt-4">Datos De La Venta</h1>
            <div class="row d-flex justify-content-between">
                <div class="mx-5">
                    <div class="form-group">
                        <label><i class="fa fa-user"></i> VENDEDOR</label>
                        <p><?php echo $_SESSION['name']; ?></p>
                    </div>
                </div>
                <div class="mx-5">
                    <div id="acciones_venta" class="form-group">
                        <a href="#" class="btn btn-danger mx-2" id="cancel_sale_btn"><i class="fa fa-times" aria-hidden="true"></i> Anular Venta</a>
                        <a href="#" class="btn btn-primary" id="sale_btn"><i class="fa fa-dollar"></i> Generar Venta</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr style="background-color:#293846; color: #fff">
                            <th colspan="2">Nombre / Código del producto</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th width="100px">Cantidad</th>
                            <th>Precio</th>
                            <th>Precio Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        <tr>
                            <td><input type="text" name="product_number" id="product_number"></td>
                            <td id="name" colspan="2">-</td>
                            <td id="stock">-</td>
                            <td><input type="text" name="product_quantity" id="product_quantity" value="0" min="1" disabled></td>
                            <td id="price">0.00</td>
                            <td id="total_price">0.00</td>
                            <td class="text-center"><a href="#" id="product_add" class="btn btn-primary" style="display: none;"><i class="fa fa-check" aria-hidden="true"></i> Agregar</a></td>
                        </tr>
                        <tr style="background-color:#293846; color: #fff">
                            <th colspan="2">Nombre / Código del producto</th>
                            <th colspan="2">Descripción</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Precio Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="sale_detail">
                        <!-- Contenido ajax -->
                    </tbody>
                    <tfoot id="detalle_totales">
                        <!-- Contenido ajax -->
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>