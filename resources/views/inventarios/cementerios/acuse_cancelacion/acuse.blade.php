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
    <h1 class="left">ACUSE DE CANCELACIÓN DE VENTA DE  PROPIEDAD</h1>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-black text-white py-1 px-2 bold mb-1">
            información del titular
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
                                fecha de la operación:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_no_day($datos['fecha_venta']) }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-100 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-19 left">
                            <span class="bold uppercase texto-sm">
                                domicilio partícular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{ $datos['cliente_direccion'] }},
                            {{ $datos['cliente_ciudad'] }}
                            {{ $datos['cliente_estado'] }}.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="w-35 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-53 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                tel. dom. partícular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-47 center">
                            {{ $datos['cliente_telefono']!=''?$datos['cliente_telefono']:'No capturado' }}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-28 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                celular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-72 center">
                            {{ $datos['cliente_celular']!=''?$datos['cliente_celular']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-35 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-32 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                Tel. Extra:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-68 center">
                            {{ $datos['cliente_telefono_extra']!=''?$datos['cliente_telefono_extra']:'No capturado' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="w-21 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-22 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                rfc:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-78 center">
                            {{ $datos['cliente_rfc']!=''?$datos['cliente_rfc']:'No capturado' }}
                        </div>
                    </div>
                </td>
                <td class="w-40 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-18 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                email:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-82 center lowercase">
                            {{ $datos['cliente_email']!=''?$datos['cliente_email']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-24 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                fecha nac.:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_abr(($datos['cliente_fecha_nac'])) }}
                        </div>
                    </div>
                </td>
                <td class="w-15 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-40 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                edad:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-60 center">
                            {{ calculaedad((String)($datos['cliente_fecha_nac'])) }} años
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-black text-white py-1 px-2 bold mb-1">
            información de la venta
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-50 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-20 left">
                            <span class="bold uppercase texto-sm">
                                id venta:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-80 center">
                            {{ $datos['id'] }}
                        </div>
                    </div>
                </td>
                <td class="w-50 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-35 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                número de título:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-65 center">
                            {{ $datos['numero_titulo'] }}
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
                <td class="w-40 px-2 py-2">
                    <div class="uppercase bg-black text-white bold mb-1 center">
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
                                <span class="bold"> costo:</span>

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
                                <span class="bold">importe total:</span>

                            </td>
                            <td class="w-30 py-1 right bg-gray">
                                $ {{ number_format( $datos['total'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">total recibido (<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>

                            </td>

                            <td class="w-55 py-1 right bg-gray">
                                @php
                                /*calculando el total recibido hasta la fecha*/
                                $recibido=0;
                                @endphp
                                @foreach ($datos['programacion_pagos'][0]['pagos_programados'] as $programado)
                                    @foreach ($programado['pagos_realizados'] as $realizado)
                                        @if ($realizado['status']==1)
                                            @php
                                                $recibido+=$realizado['total'];
                                            @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                                $ {{ number_format( $recibido,2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">saldo (<span class="texto-xs w-normal capitalize">fecha
                                        impresión</span>):</span>

                            </td>
                            <td class="w-55 py-1 right bg-gray">
                                $ {{ number_format( ($datos['total']-$recibido),2)}}
                            </td>
                        </tr>
                         <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">pagos vencidos:</span>

                            </td>
                            <td class="w-55 py-1 right bg-gray">
                                {{  ($datos['pagos_vencidos'])}}
                            </td>
                        </tr>
                          <tr>
                            <td class="w-45 py-1 left bg-nada">
                                <span class="bold">intereses generados:</span>

                            </td>
                            <td class="w-55 py-1 right bg-gray">
                                 $ {{ number_format( ($datos['intereses_generados']),2)}}
                            </td>
                        </tr>
                    </table>
                     <table class="w-100 mt-2">
                        <tr>
                            <td class="w-55 py-1 left bg-nada size-14px">
                                <span class="bold">cantidad a devolver:</span>

                            </td>
                            <td class="w-45 py-1 right bg-gray size-20px">
                                $ {{ number_format( ($datos['cantidad_a_regresar_cancelacion']),2)}}
                            </td>
                        </tr>
                    </table>
                  
                </td>
                <td class="w-60 px-2 py-2">
                    <div class="radius-5 border-black-1 p-2 py-4">


                        <div class="uppercase bg-black text-white bold mb-1 center">
                           motivos de cancelación
                        </div>
                        

                        <table class="w-100">
                            <tr>
                                <td class=" px-2 w-55 py-1 left bg-nada">
                                    <span class="bold">motivos de cancelación:</span>
                                </td>
                                <td class="w-45 py-1 center bg-gray">
                                    <span>{{$datos['motivo_cancelacion']['motivo']}}</span>
                                </td>
                            </tr>
                              <tr>
                                <td colspan="2" class="w-100  py-1 pt-2 left bg-nada">
                                    <div class="p-1 bg-gray py-2">
                                        <div class="bold py-1">motivos de cancelación:</div>
                                       <span>{{$datos['nota_cancelacion']}}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-40 px-2 pt-25 center">
                    <span>{{$datos['cliente_nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        firma del cliente
                    </div>
                </td>
                <td class="w-60 px-2 pt-25 center">
                    <span>{{$datos['cancelador']['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        firma del operador
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 texto-xs2  px-3 py-2 justificar">
       A través de este documento queda por terminado el contrato previamente adquirido con la empresa "<span class="bold uppercase"> {{ $empresa->razon_social }}</span>", 
       quién hace de conocimiento del cliente "<span class="bold uppercase">{{$datos['cliente_nombre']}}</span>" que no podrá gozar de ningún beneficio ni derecho sobre la 
       propiedad que se describe en el presente documento.
    </div>

</body>

</html>
<span class="uppercase bold texto-sm"></span>