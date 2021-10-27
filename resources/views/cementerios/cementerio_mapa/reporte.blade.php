<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        .logos {
            min-width: 200px;
            max-width: 200px;
        }

        .banco {
            border: 2px solid #E5E8E8 !important;
        }


        .banco-logo {
            display: block;
        }

        .logo {
            display: block;
            margin-right: auto;
        }

        .santander {
            color: #D31413 !important;
        }

        .digito {
            padding: 3px 7px 3px 7px;
            border: 1px solid #dae1e7;
            font-size: 1em;
            line-height: 1.5em;
        }

        .barcode div {
            min-height: 40px !important;
        }

        .bg-total {
            background-color: #FE0000;
            color: #fff;
        }


        .numeros-contrato {
            width: 100% !important;
        }

        .numeros-contrato .control {
            text-align: center;
            text-transform: uppercase !important;
            font-size: .8em;
            line-height: 1.9em !important;
            font-weight: 600 !important;
        }

        .control-valor {
            text-align: center;
            font-size: .9em;
            line-height: .3em !important;
            text-transform: uppercase;
        }

    </style>
</head>

<body>
    @include('layouts.estilos')

    <div class="pagos relative">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="w-25">
                        <img class="logo logos -mt-6" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Descripción
                            </div>
                            <p class="control-valor">
                                Reporte de cementerio
                            </p>
                            <div class="control bg-header size-13px">
                                Fecha de reporte
                            </div>
                            <p class="control-valor">
                                {{ fecha_abr(today()) }}
                            </p>
                        </div>
                    </th>

                </tr>
            </thead>
        </table>
        <div class="w-100">
            <table class="w-100">
                <tr>
                    <td class="w-50">

                    </td>
                    <td class="w-50">

                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">Desglose de información:</span>
        </div>
        @foreach ($cementerio as $area)
            @if ($area['tipo_propiedades_id'] != 4)
                <div class="py-3">
                    {{ $area['nombre_area'] }}
                </div>
                <div class="w-100">
                    <table class="w-100 size-14px pagos_tabla">
                        <thead>
                            <tr>
                                <td class="center"><span class="bold uppercase px-2">Número de Módulo</span>
                                </td>
                                <td class="center"><span class="bold uppercase px-2">Estatus</span></td>
                                <td class="center"><span class="bold uppercase px-2">Fecha de Venta</span></td>
                                <td class="center"><span class="bold uppercase px-2">Servicios Brindados</span>
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
                                            <span class="bold px-2">{{ $i }}</span>
                                        </td>
                                        <td class="center"><span class="bold px-2">Vendida</span></td>
                                        <td class="center"><span
                                                class="px-2">{{ $venta['fecha_venta_texto'] }}</span></td>
                                        <td class="center"><span
                                                class="px-2">{{ $venta['num_servicios'] }}</span>
                                        </td>
                                    </tr>
                                    <?php
                                    $esta = true;
                                    ?>
                                @break;
                            @endif
            @endforeach
            @if (!$esta)
                <tr class="">
                    <td class="center">
                        <span class="bold uppercase px-2">{{ $i }}</span>
                    </td>
                    <td class="center"><span class="px-2">Disponible</span></td>
                    <td class="center"><span class="px-2">N/A</span></td>
                    <td class="center"><span class="px-2">0</span>
                    </td>
                </tr>
            @endif

            <?php
                            }
                            ?>
            </tbody>
            </table>
    </div>
    @endif
    @endforeach
    </div>
</body>

</html>
