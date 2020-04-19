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
    <h1 class="left">formato de solicitud de servicios(archivo digital)</h1>
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
                            {{ $datos['nombre'] }}
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
                            {{ $datos['domicilio'] }},
                            {{ $datos['ciudad'] }}
                            {{ $datos['estado'] }}.
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
                            {{ $datos['telefono']!=''?$datos['telefono']:'No capturado' }}
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
                            {{ $datos['celular']!=''?$datos['celular']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-35 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-32 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                tel. oficina:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-68 center">
                            {{ $datos['tel_oficina']!=''?$datos['tel_oficina']:'No capturado' }}
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
                            {{ $datos['rfc']!=''?$datos['rfc']:'No capturado' }}
                        </div>
                    </div>
                </td>
                <td class="w-42 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-18 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                email:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-82 center lowercase">
                            {{ $datos['email']!=''?$datos['email']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-22 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                fecha nac.:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_abr(($datos['fecha_nac'])) }}
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
                            {{ calculaedad((String)($datos['fecha_nac'])) }} años
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-black text-white py-1 px-2 bold mb-1">
            información de la propiedad
        </div>
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
                            {{ $datos['propiedad']['tipo_propiedad']['tipo'] }}
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
                            {{ $datos['propiedad']['tipo_propiedad']['capacidad'] }}
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
                                {{ $datos['mensualidades']==0? 'contado': ($datos['mensualidades'].' meses' ) }}
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
                                @foreach ($datos['pagos_programados'] as $programado)
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
                    </table>
                </td>
                <td class="w-60 px-2 py-2">
                    <div class="radius-5 border-black-1 p-2">


                        <div class="uppercase bg-black text-white bold mb-1 center">
                            anticipo capturado al momento de la venta
                        </div>
                        @php
                        /*buscando el anticipo que se capturo durante la venta*/
                        $anticipo=0;
                        $metodo_pago='';
                        @endphp
                        @foreach ($datos['pagos_programados'] as $programado)
                        @foreach ($programado['pagos_realizados'] as $realizado)
                        @if ($realizado['status']==1 && $realizado['fecha_registro']==$datos['fecha_registro'])
                        @php
                        $anticipo=$realizado['total'];
                        $metodo_pago=$realizado['tipo_pago_sat']['forma'];
                        break;
                        @endphp
                        @endif
                        @endforeach
                        @if ($anticipo>0)
                        @php
                        break;
                        @endphp
                        @endif
                        @endforeach
                        @if ($anticipo>0)
                        <div class="my-8">
                            <div class="uppercase mt-3 bg-gray  px-2">
                                <span class="bold">$ {{number_format($anticipo,2)}}</span>
                                ({{ NumerosEnLetras::convertir($anticipo,'pesos mexicanos',false) }})<span class="bold">
                                    | </span>método de pago <span class="bold">{{$metodo_pago}}</span>.
                            </div>
                            <div class="uppercase mt-3  px-2 justificar texto-xs line-lg">
                                misma que se debió entregar en la caja de <span
                                    class="bold">{{$empresa->razon_social}}</span>. en un plazo menor de <span
                                    class="bold">48 hrs.</span> a partir de la fecha de la venta.
                            </div>
                        </div>
                        @else
                        <div class="bg-gray bold my-18 center">
                            no se capturó anticipo durante la venta
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-40 px-2 pt-2 center">
                    <span>{{$datos['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        firma del cliente
                    </div>
                </td>
                <td class="w-60 px-2 pt-2 center">
                    <span>{{$datos['vendedor']['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        nombre y firma del vendedor
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 texto-xs2  px-3 py-2 justificar">
        El contratante, dentro de un plazo de 5 días hábiles, contados a partir del siguiente día hábil de la firma del
        contrato, puede cancelar la contratación de los servicios funerarios, sin que sufra menoscabo de los pagos que
        haya realizado a la fecha, sin perjuicio de que el prestador de los servicios pueda ampliar este plazo.
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-black text-white py-1 px-2 bold mb-1">
            programación de pagos
        </div>
        <table class="w-100">
            <tr>
                <td class="w-37 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-53 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                {{$datos['pagos_programados'][0]['tipo_pagos_id']==1?'enganche acordado':'pago único'}}
                                :
                            </span>
                        </div>
                        <div class="float-right bg-gray w-47 center">
                            {{number_format($datos['pagos_programados'][0]['total'],2)}} mxn
                        </div>
                    </div>
                </td>
                <td class="w-35 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-50 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                el resto a cubrir en:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-50 center">
                            {{ $datos['mensualidades']!='0'?$datos['mensualidades'].' meses':'n/a' }}
                        </div>
                    </div>
                </td>
                <td class="w-28 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-38 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                a pagos de:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-62 center">
                            {{ $datos['mensualidades']!='0'? number_format($datos['pagos_programados'][1]['total']).' mxn':'n/a' }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="w-100">
                        @foreach ($datos['pagos_programados'] as $key=>$programado)
                        <div class="w-48 px-2 py-1 {{($key%2>0)?'float-right':'float-left'}}">
                            <div class="left">
                                <div class="float-left w-20 left bg-nada">
                                    <div class="bold uppercase texto-sm center">
                                        @if ($programado['tipo_pagos_id']==3)
                                        pago único
                                        @else
                                        {{$programado['tipo_pagos_id']==1?'enganche':'abono '.($key)}}
                                        @endif

                                    </div>
                                </div>
                                <div class="float-right bg-nada w-40">
                                    <div class="bold uppercase texto-sm center">
                                        {{number_format($programado['total'],2)}} mxn
                                    </div>
                                </div>
                                <div class="float-right bg-gray w-40">
                                    <div class="bold uppercase texto-sm center">
                                        {{ fecha_abr(($programado['fecha_programada'])) }}

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-black text-white py-1 px-2 bold mb-1">
            beneficiarios
        </div>
        <table class="w-100">
            @if (count($datos['beneficiarios'])>0)
            @foreach ($datos['beneficiarios'] as $key=>$beneficiario)
            <tr>
                <td class="w-40 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-19 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                nombre:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{$beneficiario['nombre']}}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-35 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                parentesco:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-65 center">
                            {{$beneficiario['parentesco']}}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-35 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                teléfono:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-65 center">
                            {{$beneficiario['telefono']}}
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <div class="bg-gray bold my-8 center">
                no se han registrado beneficiarios
            </div>
            @endif
        </table>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>