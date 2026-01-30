<?php
// include "../../includes/conexionbd.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Tu código existente...
?>

<form id='formEquipo' method="POST" class="">
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="unique-tab-list" data-tabs-toggle="#unique-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="unique-tab-ubicacion" data-tabs-target="#unique-content-ubicacion" type="button" role="tab" aria-controls="unique-content-ubicacion" aria-selected="false">Ubicación</button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="unique-tab-datos" data-tabs-target="#unique-content-datos" type="button" role="tab" aria-controls="unique-content-datos" aria-selected="false">Datos de la computadora</button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="unique-tab-cuentas" data-tabs-target="#unique-content-cuentas" type="button" role="tab" aria-controls="unique-content-cuentas" aria-selected="false">Cuentas</button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="unique-tab-componentes" data-tabs-target="#unique-content-componentes" type="button" role="tab" aria-controls="unique-content-componentes" aria-selected="false">Componentes</button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="unique-tab-seguimiento" data-tabs-target="#unique-content-seguimiento" type="button" role="tab" aria-controls="unique-content-seguimiento" aria-selected="false">Seguimiento</button>
            </li>
        </ul>
    </div>

    <div class="hidden p-4 rounded-lg  dark:bg-gray-100" id="unique-content-ubicacion" role="tabpanel" aria-labelledby="unique-tab-ubicacion">
        <p class="text-sm text-gray-500 dark:text-gray-400">Ubicacion</p>
        <div class="col-span-2 sm:col-span-1 hidden">
                <label for="asignado_a_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray"> Id Prod <span class="text-red-500">*</span></label>
                <input type="text" name="asignado_a_edit" id="id_equipo" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80  focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
        <div class="grid gap-5 mb-4 grid-cols-2">
        <?php
                                                                            try {
                                                                                $consulta = $conexion->query("SELECT * FROM departamentos");
                                                                                $departamentos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                                            } catch (PDOException $e) {
                                                                                die('Error en la consulta: ' . $e->getMessage());
                                                                            }
                                                                            ?>
            <div class="col-span-2 sm:col-span-1">
                <label for="departamento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Departamento <span class="text-red-500">*</span></label>
                <select id="departamento_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option class="dark:text-gray" selected="">Selecciona Departamento</option>
                    <?php foreach ($departamentos as $departamento) : ?>
                        <option class="dark:text-gray" value="<?= $departamento['Id_departamento'] ?>"><?= htmlspecialchars($departamento['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php
            try {
                $consulta = $conexion->query("SELECT * FROM oficina");
                $oficinas = $consulta->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Error en la consulta: ' . $e->getMessage());
            }
            ?>
            <div class="col-span-2 sm:col-span-1">
                <label for="oficina" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Oficina <span class="text-red-500">*</span></label>
                <select id="oficina_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option class="dark:text-gray" selected="">Selecciona Oficina</option>
                    <?php foreach ($oficinas as $oficina) : ?>
                        <option class="dark:text-gray" value="<?= $oficina['Id_Oficina'] ?>"><?= htmlspecialchars($oficina['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-100" id="unique-content-datos" role="tabpanel" aria-labelledby="unique-tab-datos">
        <p class="text-sm text-gray-500 dark:text-gray">Datos de la computadora</p>
        <div class="grid gap-5 mb-4 grid-cols-2">
           
        <div class="col-span-2 sm:col-span-1">
                <label for="asignado_a_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Asignado a <span class="text-red-500">*</span></label>
                <input type="text" name="asignado_a_edit" id="asignado_a_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="tipo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Tipo <span class="text-red-500">*</span></label>
                <select
                    name="tipo"
                    id="tipo_edit"
                    class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
                    <option value="" disabled selected>Selecciona un tipo</option>
                    <option value="All in One">All-in-One</option>
                    <option value="Custom">Custom</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Mini PC">Mini PC</option>
                    <option value="Servidor">Servidor</option>
                    <option value="Workstation">Workstation</option>
                    <option value="Micro CPU">Micro CPU</option>
                    <option value="Tablet">Tablet</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="marca_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Marca <span class="text-red-500">*</span></label>
                <input type="text" name="marca" id="marca_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="modelo_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Modelo <span class="text-red-500">*</span></label>
                <input type="text" name="modelo_edit" id="modelo_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="condicion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Condición <span class="text-red-500">*</span></label>
                <select
                    name="condicion"
                    id="condicion_edit"
                    class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
                    <option value="" disabled selected>Selecciona una condición</option>
                    <option value="NUEVA">Nueva</option>
                    <option value="BUENA">Buena</option>
                    <option value="REGULAR">Regular</option>
                    <option value="Mala">Mala</option>
                    <option value="Defectuosa">Defectuosa</option>
                    <option value="Excelente">Excelente</option>
                </select>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="costoEquipoActual" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Costo Equipo Actual <span class="text-red-500">*</span></label>
                <input type="number" name="costoEquipoActual" id="costoEquipoActual_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="fechaDeAsignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Fecha De Asignación <span class="text-red-500">*</span></label>
                <input type="date" name="fechaDeAsignacion" id="fechaDeAsignacion_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="anoDeProcesador" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Año De Procesador <span class="text-red-500">*</span></label>
                <input type="number" name="anoDeProcesador" id="anoDeProcesador_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="fechaDeLanzamiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Fecha De Lanzamiento <span class="text-red-500">*</span></label>
                <input type="date" name="fechaDeLanzamiento" id="fechaDeLanzamiento_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Status <span class="text-red-500">*</span></label>
                <select
                    name="status"
                    id="status_edit"
                    class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
                    <option value="" disabled selected>Selecciona un status</option>
                    <option value="ACTIVA">Activa</option>
                    <option value="Vendida">Vendida</option>
                    <option value="VENTA">Venta</option>
                    <option value="STOCK">Stock</option>
                    <option value="Domicilio Fiscal">Domicilio Fiscal</option>
                    <option value="Reservada">Reservada</option>
                    <option value="Retirada">Retirada</option>
                    <option value="Descontinuada">Descontinuada</option>
                    <option value="Otra">Otra</option>
                </select>
            </div>
        </div>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-100" id="unique-content-cuentas" role="tabpanel" aria-labelledby="unique-tab-cuentas">
        <p class="text-sm text-gray-500 dark:text-gray-400">Cuentas</p>
        <div class="grid gap-5 mb-4 grid-cols-2">
            <div class="col-span-2 sm:col-span-1">
                <label for="correo_asociado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Correo Asociado <span class="text-red-500">*</span></label>
                <input type="text" name="correo_asociado" id="correo_asociado_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500 " required placeholder="desarrollo@example.org">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="contrasenaGmail1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Contraseña Gmail <span class="text-red-500">*</span></label>
                <input type="text" name="contrasenaGmail1" id="contrasenaGmail1_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="contrasenaOutlook1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Contraseña Outlook <span class="text-red-500">*</span></label>
                <input type="text" name="contrasenaOutlook1" id="contrasenaOutlook1_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="correoAsociado2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Correo Asociado 2</label>
                <input type="text" name="correoAsociado2" id="correoAsociado2_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="desarrollo@example.org">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="contrasenaGmail2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Contraseña Gmail 2</label>
                <input type="text" name="contrasenaGmail2" id="contrasenaGmail2_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="********">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="contrasenaOutlook2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Contraseña Outlook 2</label>
                <input type="text" name="contrasenaOutlook2" id="contrasenaOutlook2_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="********">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="correoAsociado3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Correo Asociado 3</label>
                <input type="text" name="correoAsociado3" id="correoAsociado3_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="desarrollo@example.org">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="contrasenaWindow" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Contraseña Windows <span class="text-red-500">*</span></label>
                <input type="text" name="contrasenaWindow" id="contrasenaWindow_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
            </div>
        </div>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-100" id="unique-content-componentes" role="tabpanel" aria-labelledby="unique-tab-componentes">
        <p class="text-sm text-gray-500 dark:text-gray-400">Componentes</p>
        <div class="grid gap-5 mb-4 grid-cols-2">
            <div class="col-span-2 sm:col-span-1">
                <label for="tipoDeDisco" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Tipo de disco <span class="text-red-500">*</span></label>
                <select
                    name="tipoDeDisco"
                    id="tipoDeDisco_edit"
                    class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
                    <option value="" disabled selected>Selecciona un tipo de disco</option>
                    <option value="SSD">SSD</option>
                    <option value="HDD">HDD</option>
                    <option value="Hybrid (SSHD)">Hybrid (SSHD)</option>
                    <option value="NVMe">NVMe</option>
                    <option value="SATA">SATA</option>
                    <option value="Otra">Otra</option>
                </select>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="procesador" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Procesador <span class="text-red-500">*</span></label>
                <input type="text" name="procesador" id="procesador_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <div class="col-span-2 sm:col-span-1 relative">
                <label for="ram" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Ram <span class="text-red-500">*</span></label>
                <div class="flex items-center">
                    <input
                        type="number"
                        name="ram"
                        id="ram_edit"
                        class="block p-2 pe-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Ejemplo: 4"
                        min="1"
                        required>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">GB</span>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-100" id="unique-content-seguimiento" role="tabpanel" aria-labelledby="unique-tab-seguimiento">
        <p class="text-sm text-gray-500 dark:text-gray-400">Datos para seguimiento</p>
        <div class="grid gap-5 mb-4 grid-cols-2">
            <div class="col-span-2 sm:col-span-1">
                <label for="posibleFechaParaVenta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Posible Fecha Para Venta <span class="text-red-500">*</span></label>
                <input type="date" name="posibleFechaParaVenta" id="posibleFechaParaVenta_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="nuevaCompra" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Nueva Compra <span class="text-red-500">*</span></label>
                <input type="text" name="nuevaCompra" id="nuevaCompra_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <!-- Foto php -->
            <div class="col-span-2 sm:col-span-1">
                <label for="foto" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Foto <span class="text-red-500">*</span></label>
                <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none   dark:border-gray-300 dark:placeholder-gray-400" name="foto" id="foto_edit" accept="image/*" >
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="pcAnterior" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">PC Anterior <span class="text-red-500">*</span></label>
                <input type="text" name="pcAnterior" id="pcAnterior_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="posibleAsignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Posible Asignación <span class="text-red-500">*</span></label>
                <input type="text" name="posibleAsignacion" id="posibleAsignacion_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="total" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Total <span class="text-red-500">*</span></label>
                <input type="text" name="total" id="total_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$2,050.98" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="costoAlComprar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Costo Al Comprar <span class="text-red-500">*</span></label>
                <input type="number" name="costoAlComprar" id="costoAlComprar_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$1,999" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="costoALaVenta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Costo A La Venta <span class="text-red-500">*</span></label>
                <input type="number" name="costoALaVenta" id="costoALaVenta_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$2,000" required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="disponibilidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Disponibilidad <span class="text-red-500">*</span></label>
                <select name="disponibilidad" id="disponibilidad_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="" disabled selected>Selecciona disponibilidad</option>
                    <option value="Disponible">Disponible</option>
                    <option value="N/A">No Disponible</option>
                    <option value="En Uso">En Uso</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Asignada">Asignada</option>
                    <option value="Reservada">Reservada</option>
                    <option value="Descontinuada">Descontinuada</option>
                    <option value="VENDIDA">VENDIDA</option>
                    <option value="DOMICILIOS">DOMICILIO</option>
                    <option value="REVISION">REVISION</option>
                    <option value="COTIZACION VENTA">COTIZACION VENTA</option>
                </select>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="propietario_Destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Propietario Destino <span class="text-red-500">*</span></label>
                <input type="text" name="propietario_Destino" id="propietario_Destino_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <!-- Foto2 php -->
            <div class="col-span-2 sm:col-span-1">
                <label for="foto2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Foto <span class="text-red-500">*</span></label>
                <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none   dark:border-gray-300 dark:placeholder-gray-400" name="foto2" id="foto2_edit" accept="image/*" >
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="fechaDeReasignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray">Fecha Reasignación <span class="text-red-500">*</span></label>
                <input type="date" name="fechaDeReasignacion" id="fechaDeReasignacion_edit" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500   dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>
        </div>

        <button type="button" id="actualizarEquipo" class="text-gray inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
    Actualizar Equipo
</button>

    </div>
</form>