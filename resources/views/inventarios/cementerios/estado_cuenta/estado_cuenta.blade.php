<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        #header,
        #header section table {
            width: 100% !important;
            padding-top: 0px;
        }

        #header section table {
            border-collapse: collapse !important;
        }

        .logo {
            max-width: 100% !important;
        }


        h1 {
            font-size: 1em;
            line-height: .8em !important;
            text-transform: uppercase;
            text-align: center;
        }

        .datos-header {
            text-align: center !important;
            font-size: .9em;
            line-height: 0.7em !important;
            text-transform: uppercase !important;
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

        /*parrafos**/
        .contenido {
            padding: 10px 0 0 0 !important;
            margin: 0 !important;
        }

        /*fin de parrafos*/
    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table>
                <tr>
                    <td style="width:23%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:53%;">
                        <h1>
                            {{ $empresa->razon_social }}
                        </h1>
                        <p class="datos-header">
                            r.f.c. {{ $empresa->rfc }}
                        </p>
                        <p class="datos-header">
                            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
                        </p>
                        <p class="datos-header">
                            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                            {{ $empresa->ciudad }}
                            {{ $empresa->estado }}
                        </p>
                        <p class="datos-header">
                            Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <div class="numeros-contrato">
                            <div class="control bg-gray">
                                solicitud de servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_solicitud'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Número de convenio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_convenio'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-gray-dark text-black py-1 px-2 bold mb-1 texto-sm">
            estado de cuenta {{fechahora_completa()}}
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-55 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-15 left">
                            <span class="bold uppercase texto-sm">
                                titular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-85 center">
                            {{ $datos['cliente_nombre'] }}
                        </div>
                    </div>
                </td>
                <td class="w-45 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left">
                            <span class="bold uppercase texto-sm">
                                fecha de la venta:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_no_day($datos['fecha_venta']) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="w-50 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-20 left">
                            <span class="bold uppercase texto-sm">
                                ubicación:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-80 center">
                            {{ $datos['ubicacion_texto'] }}
                        </div>
                    </div>
                </td>
                <td class="w-25 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-20 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                tipo:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-80 center">
                            {{ $datos['tipo_propiedad_des'] }}
                        </div>
                    </div>
                </td>
                <td class="w-25 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                capacidad:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ $datos['tipo_propiedad_capacidad'] }}
                            gaveta (s)
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100">
            <tr>
                <td class="w-40 px-0 py-2">
                    <div class="uppercase bg-gray-dark text-black py-1 px-2 bold mb-1 texto-sm">
                        costos acordados
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="w-70 py-1 left bg-nada">

                                <span class="bold"> plan de venta:</span>
                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                {{ $datos['programacion_pagos'][0]['mensualidades']==0? 'contado': ($datos['programacion_pagos'][0]['mensualidades'].' meses' ) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">
                                <span class="bold"> sub-total propiedad:</span>

                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['subtotal'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">

                                <span class="bold">iva:</span>
                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['iva'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">
                                <span class="bold">descuento neto:</span>

                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['descuento'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">

                                <span class="bold">costo neto propiedad:</span>
                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['total'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">
                                <span class="bold">intereses generados(<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>
                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['intereses_generados'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-70 py-1 left bg-nada">
                                <span class="bold">intereses pagados(<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>
                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['intereses_pagados'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">total recibido (<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>

                            </td>

                            <td class="w-55 py-1 right bg-gray">
                               
                                $ {{ number_format($datos['total_pagado']+$datos['intereses_pagados'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">saldo (<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>

                            </td>
                            <td class="w-55 py-1 right bg-gray">
                                $ {{ number_format( ($datos['saldo_pagar_neto_con_intereses']),2)}}
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="w-60">
                    <div class="uppercase bg-gray-dark text-black py-1 px-2 bold mb-1 texto-sm">
                       resumen general
                    </div>
                    <table class="w-100">
                        <tr>
                            <td>
                                @if ($datos['pagos_vencidos']>0)
                                    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 {{$datos['pagos_vencidos']>=3?'py-3':'py-14'}} justificar line-base">
                                        <span class="bold texto-sm">ojo. </span>
                                        este contrato cuenta con (<span class="bold texto-sm"> {{$datos['pagos_vencidos']}} </span>) pagos vencidos. 
                                        En base a la cláusula  <span class="bold texto-sm">VIGÉSIMA TERCERA</span> del contrato, “La Empresa” tiene el derecho de aumentar <span class="bold texto-sm">1 (un)</span>
                                        cargo extra como aportación complementaria por concepto de pago 
                                        impuntual por cada uno de los pagos vencidos.
                                    </div>
                                     @if ($datos['pagos_vencidos']>=3)
                                    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-3">
                                    <span class="bold texto-sm">ojo. </span>hasta la fecha de esta impresión se han detectado <span class="bold texto-sm">{{$datos['pagos_vencidos']}}</span> pagos vencidos. 
                                         En base a lo estipulado en la cláusula <span class="bold texto-sm">VIGÉSIMA CUARTA</span> del contrato del convenio, 
                                         “La Empresa” tiene el derecho de cancelar el presente convenio por motivo de falta de 
                                         pago.
                                    </div>
                                    @endif
                                @else
                                     <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-14 justificar line-base">
                                        <span class="bold texto-sm">estado actual de su convenio. </span>
                                       hemos encontrado que su cuenta <span class="bold texto-sm text-success">está al corriente con sus aportaciones </span>, le invitamos a continuar así, 
                                       de esa manera evitamos aumentar comisiones o cancelar servicios por falta de pago. 
                                       Gracias. <span class="bold texto-sm">atentamente: gerencia de {{$empresa->nombre_comercial}}</span>.
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @php
        /*
        @endphp
        <table class="w-100">
            <tr>
                <td class="w-50 px-2 pt-2 center">
                    <span>{{$datos['cliente_nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        firma del cliente
                    </div>
                </td>
                <td class="w-50 px-2 pt-2 center">
                    <span>{{$datos['vendedor']['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        nombre y firma del vendedor
                    </div>
                </td>
            </tr>
        </table>
        @php
        */
        @endphp
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-gray-dark text-black py-1 px-2 bold mb-1 texto-sm">
            detalle de pagos programados y pagos cobrados
        </div>
        <table class="w-100">
            <thead>
                <tr>
                    <th class=" center py-1">#</th>
                    <th class=" center py-1">estatus</th>
                    <th class=" center py-1">referencia de pago</th>
                    <th class=" center py-1">concepto</th>
                    <th class=" center py-1">fecha programada</th>
                    <th class=" right py-1">intereses</th>
                    <th class=" right py-1">subtotal</th>
                    <th class=" right py-1">iva</th>
                    <th class=" right py-1">descuento</th>
                    <th class=" right py-1">importe</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($datos['programacion_pagos'][0]['pagos_programados'] as $programado)
                <tr>
                    <td class="center">
                        <span class="uppercase bold texto-sm">{{$programado['num_pago']}}</span>
                    </td>
                    <td class="center py-2">
                       @if (1==$programado['pagado'])
                            <span class="text-success bold">
                               pagado
                            </span> 
                       @else
                        @if (1==$programado['vencido'])
                                <span class="text-danger bold">
                                vencido
                                </span> 
                        @else
                                <span class="text-black bold">
                                pendiente
                                </span> 
                        @endif
                       @endif
                    </td>
                    <td class="center py-2 letter-spacing-2">{{$programado['referencia_pago']}}</td>
                    <td class="center py-2">{{$programado['concepto_pago']['concepto']}}</td>
                    <td class="center py-2">
                        {{ fecha_abr(($programado['fecha_programada'])) }}
                    </td>
                     <td class="right py-2">
                        {{number_format($programado['intereses_a_pagar'],2)}}
                    </td>
                    <td class="right py-2">
                        {{number_format($programado['subtotal'],2)}}
                    </td>
                    <td class="right py-2">
                        {{number_format($programado['iva'],2)}}
                    </td>
                    <td class="right py-2">
                        {{number_format($programado['descuento'],2)}}
                    </td>
                   
                    <td class="right py-2">
                        {{number_format($programado['total']+$programado['intereses_a_pagar'],2)}}
                    </td>
                </tr>
            
                @foreach ($programado['pagos_realizados'] as $realizado)
                @if ($realizado['tipo_pagos_id']==2)
                    <!--es de intereses-->
                     <tr class="{{$realizado['status']==0?'italic text-danger':''}}">
                        <td class="right texto-xs bold py-2" colspan="5">
                            abono a intereses
                        </td>
                        <td class="right texto-xs bold" colspan="1">
                            pago realizado el {{ fecha_abr(($realizado['fecha_pago'])) }} <span>{{$realizado['status']==0?'(cancelado)':''}}</span>
                        </td>
                        <td class="right">{{number_format($realizado['subtotal'],2)}}</td>
                        <td class="right">{{number_format($realizado['iva'],2)}}</td>
                        <td class="right">{{number_format(0,2)}}</td>
                        <td class="right">{{number_format($realizado['total'],2)}}</td>
                        </td>
                    </tr>
                 @else
                    <!--es de capital-->
                    <tr class="{{$realizado['status']==0?'italic text-danger':''}}">
                        <td class="right texto-xs bold py-2" colspan="5">
                            abono a capital
                        </td>
                        <td class="right texto-xs bold" colspan="1">
                            pago realizado el {{ fecha_abr(($realizado['fecha_pago'])) }} <span>{{$realizado['status']==0?'(cancelado)':''}}</span>
                        </td>
                        <td class="right">{{number_format($realizado['subtotal'],2)}}</td>
                        <td class="right">{{number_format($realizado['iva'],2)}}</td>
                        <td class="right">{{number_format($realizado['descuento'],2)}}</td>
                        <td class="right">{{number_format($realizado['total'],2)}}</td>
                        </td>
                    </tr>
                @endif
                @endforeach
                <tr class="bg-gray">
                    <td colspan="5" class="right">
                        <span class="uppercase bold texto-sm">totales</span>
                    </td>
                     <td class="right">
                        <span class="uppercase bold texto-sm">{{number_format($programado['intereses_pagado'],2)}}</span>
                    </td>
                    <td class="right">
                        <span class="uppercase bold texto-sm">{{number_format($programado['subtotal_pagado'],2)}}</span>
                    </td>
                    <td class="right">
                        <span class="uppercase bold texto-sm">{{number_format($programado['iva_pagado'],2)}}</span>
                    </td>
                    <td class="right">
                        <span class="uppercase bold texto-sm">{{number_format($programado['descuento_pagado'],2)}}</span>
                    </td>
                    <td class="right">
                        <span class="uppercase bold texto-sm">{{number_format($programado['total_pagado'],2)}}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>