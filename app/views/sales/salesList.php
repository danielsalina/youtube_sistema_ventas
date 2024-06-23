<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1>Listado de ventas</h1>
        <a href="SalesController.php?name=sale_new" class="btn btn-primary">Nueva venta</a>
    </div>
    <hr class="mb-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table">
                    <thead>
                        <tr style="background-color:#213a53; color: #fff">
                            <th class="text-center">#</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Total Con Descuento</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows = getSales();
                        foreach ($rows as $row) :
                            // Mostrar todos los registros si el rol es 1
                            // Si el rol es diferente de 1, mostrar solo los registros que coincidan con el branchId
                            /* if ($_SESSION['role'] === 1 && $_SESSION['branchId'] == $row['branchId']) : */
                        ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['date']) ?></td>
                                <td class="text-center">$ <?= htmlspecialchars(number_format($row['total'], 2)) ?></td>
                                <td class="text-center">$ <?= htmlspecialchars(number_format($row['totalWithDiscount'], 2)) ?></td>
                                <td class="text-center">
                                    <div class="d-flex flex-direction-row justify-content-center">
                                        <button type="button" class="btn btn-primary download_invoice" totalWithDiscount="<?= htmlspecialchars($row['totalWithDiscount']) ?>" clientId="<?= htmlspecialchars($row['clientId']) ?>" invoiceId="<?= htmlspecialchars($row['id']) ?>">Descargar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        /* endif; */
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>