@foreach ($datos as $area)
    <div class="break-after">
        <div class="py-3">
            {{ $area['nombre_area'] }}
        </div>
        @if ($area['tipo_propiedades_id'] != 4)
            <div class="w-100">
                <table class="w-100 size-14px table-modulo">
                    <thead>
                        <tr>
                            <td class="center"><span class="bold uppercase px-2">Área</span>
                            </td>
                            <td class="center"><span class="bold uppercase px-2">Status</span></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                for ($i = 1; $i <= $area['filas']; $i++) {
                    $esta=false;
                ?>
                        @foreach ($area['propiedades'] as $venta)
                            @if ($venta['fila_raw'] == $i)
                                <tr>
                                    <td class="center">
                                        {{ $area['nombre_area'] }}, Módulo <span
                                            class="bold px-2">{{ $i }}</span>
                                    </td>
                                    <td class="center"><span class="bold px-2">Vendida</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="py-4 px-2">
                                        <table class="w-100 titular">
                                            <tr>
                                                <td class="w-8 bold">
                                                    Titular
                                                </td>
                                                <td class="w-60">
                                                    {{ $venta['cliente'] }}
                                                </td>
                                                <td class="w-16 center bold">
                                                    Fecha de la venta
                                                </td>
                                                <td class="w-16">
                                                    {{ $venta['fecha_venta_texto'] }}
                                                </td>
                                            </tr>
                                        </table>
                                        @if ($venta['num_servicios'] > 0)
                                            <div class="py-4 uppercase bold">
                                                Servicios funerarios en esta propiedad
                                            </div>
                                            <table class="w-100 titular pagos_tabla">
                                                <thead>
                                                    <tr>
                                                        <td class="center"><span
                                                                class="bold uppercase px-2">#</span></td>
                                                        <td class="center"><span
                                                                class="bold uppercase px-2">Fallecido</span></td>
                                                        <td class="center"><span class="bold uppercase px-2">
                                                                Fecha
                                                                del servicio</span></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($venta['servicios_funerarios'] as $index => $servicio)
                                                        <tr>
                                                            <td class="w-8 bold center">
                                                                {{ $index + 1 }}
                                                            </td>
                                                            <td class="center">
                                                                {{ $servicio['nombre_afectado'] }}
                                                            </td>
                                                            <td class="center">
                                                                {{ $servicio['fecha_inhumacion_texto'] }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </td>
                                </tr>
                                <?php
                                $esta = true;
                                ?>
                            @break;
                        @endif
        @endforeach
        @if (!$esta)
            <tr class="bg-gray">
                <td class="center">
                    {{ $area['nombre_area'] }}, Módulo <span class="bold px-2">{{ $i }}</span>
                </td>
                <td class="center"><span class="uppercase px-2">Disponible</span>
                </td>
            </tr>
        @endif
        <?php
                }
                ?>
        </tbody>
        </table>
    </div>
@else
    <!--Pintamos el mapa para los cuadriplex, terrazas-->
    <table class="tabla-cuadriplex w-100 center">
        <?php
        /**se necesita crear un arregle con el abecedario para cuadrar las propiedades segun su fila "en alfabeto" */
        $alfabeto = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $columnas = $area['columnas'];
        $filas = $area['filas'];
        ?>
        <?php
        for ($fila=$area['filas']; $fila >0; $fila--) {
        ?>
        <tr>
            <td class="bg-header uppercase">{{ $alfabeto[$fila - 1] }}</td>
            <?php
            for ($columna=$area['columnas']; $columna >0; $columna--) {
            ?>

            <!--Vemos en que parte empieza la columna-->
            @if ($columna >= $area['filas_columnas'][$fila - 1]['empieza_columna'] && $columna <= $area['filas_columnas'][$fila - 1]['fin_columna'])

                <!--Vemos si ya esta vendida la propiedad o esta disponible-->
                <?php
                $esta = false;
                ?>
                @foreach ($area['propiedades'] as $venta)
                    @if ($venta['fila_raw'] == $fila && $venta['lote_raw'] == $columna)
                        <?php
                        $esta = true;
                        ?>
                        <td class="vendida">
                            {{ $columna }}
                        </td>
                        <?php
                        break;
                        ?>
                    @endif
                @endforeach
                @if (!$esta)
                    <td>
                        {{ $columna }}
                    </td>
                @endif
            @else
                <td class="bg-gray">
                </td>
            @endif
            <?php
            }
            ?>
            <td class="bg-header uppercase">{{ $alfabeto[$fila - 1] }}</td>
        </tr>
        <?php
        }
        ?>
        @foreach ($area['filas_columnas'] as $fico)
        @endforeach
    </table>
@endif
</div>
@endforeach
