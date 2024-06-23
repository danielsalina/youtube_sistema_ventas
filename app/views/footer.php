<!-- Todo esto lo usa inspinia tras bambalina -->
<script src="../../public/js/jquery-3.7.1.min.js"></script>
<script src="../../public/js/popper.min.js"></script>
<script src="../../public/js/bootstrap.js"></script>
<script src="../../public/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="../../public/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- AJAX, FUNCTIONS Y DataTable BABY -->
<script type="text/javascript" src="../../public/js/ajax.js"></script>
<script type="text/javascript" src="../../public/js/functions.js"></script>
<script type="text/javascript" src="../../public/js/dataTables.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/datatables.min.js"></script> -->

<!-- Flot -->
<script src="../../public/js/plugins/flot/jquery.flot.js"></script>
<!-- Custom and plugin javascript -->
<script src="../../public/js/inspinia.js"></script>
<!-- ChartJS-->
<script src="../../public/js/plugins/chartJs/Chart.min.js"></script>

<?php

// Obtener los archivos incluidos o requeridos
$archivos_incluidos = get_included_files();
// Variable para indicar si se encontró el archivo NewSaleController.php
$encontrado = false;
// Buscar el archivo NewSaleController.php en los archivos incluidos o requeridos
foreach ($archivos_incluidos as $archivo) {
    /* print_r(basename($archivo)); */
    if (basename($archivo) === 'DashboardController.php') {
        // Si se encuentra el archivo, marcar como encontrado
        $encontrado = true;
        break; // Salir del bucle una vez que se encuentre el archivo
    }
}
// Si el archivo no se encontró, imprimir el script JavaScript
if ($encontrado) {
?>
    <script>
        $(document).ready(function() {
            let lineData = {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [{
                    label: 'Dinero recaudado por mes',
                    backgroundColor: 'rgba(26,179,148,0.5)',
                    borderColor: 'rgba(26,179,148,0.7)',
                    pointBackgroundColor: 'rgba(26,179,148,1)',
                    pointBorderColor: '#fff',
                    data: <?php echo $valores_grafico; ?>
                }],
            };

            let lineOptions = {
                responsive: true,
            };

            let ctx = document.getElementById('lineChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar', // line, bar, pie, doughnut, radar, polarArea, scatter, bubble
                data: lineData,
                options: lineOptions
            });
        });
    </script>
<?php
}

?>

</div>

</body>

</html>