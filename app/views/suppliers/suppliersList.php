<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1>Listado de proveedores</h1>
        <a href="SuppliersController.php?name=supplier_new" class="btn btn-primary">Nuevo proveddor</a>
    </div>
    <hr class="mb-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table">
                    <thead>
                        <tr style="background-color:#213a53; color: #fff">
                            <th class="text-center">#</th>
                            <th class="text-center">NOMBRE</th>
                            <th class="text-center">TELEFONO</th>
                            <th class="text-center">DIRECCION</th>

                            <?php if ($_SESSION['role'] == 1) : ?>
                                <th class="text-center">ACCIONES</th>
                            <?php endif ?>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows = getSuppliers();
                        if ($rows > 0) :
                            foreach ($rows as $row) :
                                /* if ($_SESSION['role'] == 1 && $_SESSION['branchId'] == $row['branchId']) : */
                        ?>
                                <tr>
                                    <td class="text-center"><?= $row['id']; ?></td>
                                    <td class="text-center"><?= $row['name']; ?></td>
                                    <td class="text-center"><?= $row['phoneNumber']; ?></td>
                                    <td class="text-center"><?= $row['address']; ?></td>
                                    <?php
                                    if ($_SESSION['role'] == 1) : ?>
                                        <td>
                                            <div style="font-size: 12px; display:flex; flex-direction:row; justify-content: center;">
                                                <a href="SuppliersController.php?id=<?= $row['id']; ?>&id_user=<?= $_SESSION['id_user']; ?>" class="btn btn-primary mx-2">Editar <i class='fa fa-edit'></i></a>
                                                <form action="SuppliersController.php?delete=<?= $row['id']; ?>" method="POST" class="confirmar fa-trash">
                                                    <button class="btn btn-danger" type="submit">Eliminar <i class='fa fa-trash-o'></i> </button>
                                                </form>
                                            </div>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            <?php
                            /* endif; */
                            endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>