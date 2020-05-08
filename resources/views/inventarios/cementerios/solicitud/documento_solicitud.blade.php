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
                <td class="w-42 px-2 py-2">
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

                <td class="w-22 px-2 py-2">
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
            información del titular Sustituto
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-100 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-8 left">
                            <span class="bold uppercase texto-sm">
                                nombre:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-92 center">
                            {{ $datos['titular_sustituto'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="w-50 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-23 left">
                            <span class="bold uppercase texto-sm">
                               parentesco:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-77 center">
                           {{ $datos['parentesco_titular_sustituto'] }}
                        </div>
                    </div>
                </td>
                <td class="w-50 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-19 left">
                            <span class="bold uppercase texto-sm">
                                teléfono:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{ $datos['telefono_titular_sustituto'] }}
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
                        @foreach ($datos['programacion_pagos'][0]['pagos_programados'] as $programado)
                        @foreach ($programado['pagos_realizados'] as $realizado)
                        @if ($realizado['status']==1 && $realizado['fecha_registro']==$datos['fecha_venta'])
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
                    <span>{{$datos['cliente_nombre']}}</span>
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
                               
                                {{ $datos['programacion_pagos'][0]['pagos_programados'][0]['conceptos_pagos_id']==1?'enganche acordado':'pago único'}}
                                :
                            </span>
                        </div>
                        <div class="float-right bg-gray w-47 center">
                            {{number_format($datos['programacion_pagos'][0]['pagos_programados'][0]['total'],2)}} mxn
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
                            {{ $datos['programacion_pagos'][0]['mensualidades']!='0'?$datos['programacion_pagos'][0]['mensualidades'].' meses':'n/a' }}
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
                            {{ $datos['programacion_pagos'][0]['mensualidades']!='0'? number_format($datos['programacion_pagos'][0]['pagos_programados'][1]['total']).' mxn':'n/a' }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="w-100">
                        @foreach ($datos['programacion_pagos'][0]['pagos_programados'] as $key=>$programado)
                        <div class="w-48 px-2 py-1 {{($key%2>0)?'float-right':'float-left'}}">
                            <div class="left">
                                <div class="float-left w-20 left bg-nada">
                                    <div class="bold uppercase texto-sm center">
                                        @if ($programado['conceptos_pagos_id']==3)
                                        pago único
                                        @else
                                        {{$programado['conceptos_pagos_id']==1?'enganche':'abono '.($key)}}
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
    <div class="border-black-1 radius-5 texto-sm  px-3 py-2">
        <p class="texto-xs justificar line-base">
            <span class="- bold texto-sm pr-2 uppercase">Nota.- </span>
        Debo y pagaré incondicionalmente por este pagaré a la orden de <span class="bold uppercase">{{$empresa->razon_social}}</span> en Mazatlán, Sinaloa o en cualquier otra
        ciudad que se me requiera el pago por la cantidad de: $ <span class="bold uppercase">{{number_format($datos['total'],2)}}</span>({{ NumerosEnLetras::convertir($datos['total'],'Pesos MXN',false) }}). Este pagaré tendrá vencimiento los días <span class="bold uppercase">{{dia_numero($datos['fecha_venta'])}}</span> de cada mes hasta cubrir la totalidad
de este documento. El primer pago vencerá el <span class="bold uppercase">{{fecha_no_day($datos['programacion_pagos'][0]['pagos_programados'][0]['fecha_programada'])}}</span> ({{$datos['programacion_pagos'][0]['pagos_programados'][0]['concepto_pago']['concepto']}}), en caso de falta de pago de <span class="bold uppercase">{{ $datos['ajustes_intereses']['maximo_pagos_vencidos'] }}({{ NumerosEnLetras::convertir($datos['ajustes_intereses']['maximo_pagos_vencidos'],'',false) }})</span> o más vencimientos sucesivos, se entenderá
pagadero este documento a la vista por el saldo insoluto del mismo, en los
términos señalados por el artículo 79 de la Ley General de Títulos y Operaciones
de crédito. Este pagaré es causal y no negociable desde la fecha del primer
vencimiento no pagado hasta el día de la solución del adeudo. Este causará interés
        moratorio a tasa mensual del <span class="bold uppercase">{{($datos['ajustes_intereses']['tasa_fija_anual']/12)}}%</span> en Mazatlán, Sinaloa <span class="bold lowercase capitalize">{{fecha_no_day($datos['fecha_venta'])}}</span>.
        </p>
         <p class="texto-xs justificar line-base">
        El contratante se obliga a pagar a la agencia funeraria las parcialidades contratadas dentro
         de los primeros <span class="bold">{{$datos['ajustes_intereses']['dias_antes_vencimiento']}}</span> días hábiles naturales a la fecha de vencimiento mensual que le
corresponda. 
        </p>

         <p class="texto-xs justificar line-base">
         El monto a pagar a la Agencia Funeraria comprende todas las cantidades y conceptos que
el contratante se ha obligado a cubrir, no existiendo ningún gasto, interés o cualquier
cargo adicional, siempre que el contratante pague en tiempo las parcialidades convenidas.
En caso de retraso mensual, el contratante se obliga a pagar a la agencia funeraria interés
moratorio del <span class="bold">{{$datos['ajustes_intereses']['tasa_fija_anual']}}</span>% ({{ NumerosEnLetras::convertir($datos['ajustes_intereses']['tasa_fija_anual'],'',false) }} por ciento) fija anual, la que se calculará y liquidará sobre
cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
        </p>

        <p class="texto-xs justificar line-base">
      En caso de que el retraso supere los <span class="bold">{{$datos['ajustes_intereses']['maximo_dias_retraso']}}</span> días, la agencia funeraria podrá elegir entre exigir
el pago de todas las mensualidades aun no pagadas por el contratante y los intereses
moratorios acumulados o bien rescindir el contrato y aplicar como pena convencional por
incumplimiento el <span class="bold">{{$datos['ajustes_intereses']['porcentaje_pena_convencional_minima']}}%</span> del monto pagado por el contratante, debiendo a la Agencia
Funeraria regresar las cantidades en exceso y que sobren de dicha pena al contratante. En
caso de que el retraso en el pago sea superior a los <span class="bold">{{$datos['ajustes_intereses']['maximo_dias_retraso']}}</span> días, la Agencia Funeraria podrá
igualmente rescindir el Contrato y aplicar como pena convencional la totalidad de los
pagos efectuados por el contratante. 
        </p>

         <p class="texto-xs justificar line-base">
       En los términos de los dispuestos por el artículo 71 de la Ley Federal de Protección al
consumidor, cuando el contratante haya pagado más de la tercera parte del precio o
número total de los pagos convenidos ante la notificación de rescisión que le realice la
Agencia Funeraria, el contratante podrá optar porque se aplique el mecanismo indicado
en el párrafo anterior o bien pagar el saldo del contrato más los intereses moratorios
generados por su incumplimiento. En el primer caso (rescisión con penalidad) solo si el 
retraso fuera menor a <span class="bold">{{$datos['ajustes_intereses']['maximo_dias_retraso']}}</span> días, la agencia funeraria devolverá al contratante la cantidad
que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
caso (pago total de saldo insoluto), la Agencia Funeraria entregará al contratante el recibo
de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
contratado más los intereses moratorios).
        </p>

        <p class="texto-xs justificar line-base">
       Sólo podrán reconocerse los pagos de mensualidades por los recibos firmados y sellados
por la empresa, cuando se efectúen en cajas de la Agencia Funeraria, o los recibos
firmados por el Banco Santander (México), S.A. a la cuenta <span class="bold">{{$empresa['cuenta']}}</span> a más tardar en
la fecha límite establecida. Ninguna otra persona está autorizada para recibir pagos y, por
lo tanto, estos no podrán ser reconocidos por la Agencia Funeraria. 
        </p>

        <p class="texto-xs justificar line-base">
       El contratante o Titular Sustituto será quien al requerir los servicios para el usuario que se
solicitaron, deberá entregar el contrato, recibos de pagos efectuados o en su caso liquidar el
saldo total que persista hasta la fecha y cualquier otro adeudo del servicio contratado. 
        </p>

        @php
            /*
        @endphp
        <div class="lista pl-11 -mt-1">
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El convenio que ampara dicho servicio de inhumación
                </span>
            </p>
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    El certificado de defunción de la persona que recibirá el servicio amparado por el convenio en
                    cuestión
                </span>
            </p>
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    Cualquier otra documentación que sea necesaria para efectuar los trámites correspondientes y/o
                    conseguir los permisos necesarios para la realización de dicho servicio.
                </span>
            </p>
        </div>
        @php
            */
        @endphp
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>