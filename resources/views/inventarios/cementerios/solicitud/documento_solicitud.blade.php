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
    <p class="fecha  right">
        <span class="bg-gray bold uppercase texto-sm  pl-2">
            fecha de la operación: <span
                class="w-normal">{{ fecha_only($datos['fecha_venta']) }}</span>
        </span>.
    </p>
    <div class="border-black-1 p-1 radius-5 uppercase texto-sm">
        <table class="w-100 center">
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['nombre'] }}
                        </div>
                        <div class="mt-1">
                            nombre completo del titular
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['domicilio'] }}
                        </div>
                        <div class="mt-1">
                            domicilio completo
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="py-2 w-30 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['telefono']!=''?$datos['telefono']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            Tel. dom. partícular
                        </div>
                    </div>
                </td>
                <td class="py-2 w-40 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['celular']!=''?$datos['celular']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            celular
                        </div>
                    </div>
                </td>
                <td class="py-2 w-30 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['tel_oficina']!=''?$datos['tel_oficina']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            Tel. oficina
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['rfc']!=''?$datos['rfc']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            r.f.c
                        </div>
                    </div>
                </td>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['email']!=''?$datos['email']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            email
                        </div>
                    </div>
                </td>

            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="py-1 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ ((String)($datos['fecha_nac'])) }}
                        </div>
                        <div class="mt-1">
                            Fecha de Nacimiento
                        </div>
                    </div>
                </td>
                <td class="py-1 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ calculaedad((String)($datos['fecha_nac'])) }} años
                        </div>
                        <div class="mt-1">
                            edad
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <div class="border-black-1 p-1 radius-5 uppercase texto-sm">
        <table class="w-100 center">
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['nombre'] }}
                        </div>
                        <div class="mt-1">
                            nombre completo del titular
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['domicilio'] }}
                        </div>
                        <div class="mt-1">
                            domicilio completo
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="py-2 w-30 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['telefono']!=''?$datos['telefono']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            Tel. dom. partícular
                        </div>
                    </div>
                </td>
                <td class="py-2 w-40 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['celular']!=''?$datos['celular']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            celular
                        </div>
                    </div>
                </td>
                <td class="py-2 w-30 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['tel_oficina']!=''?$datos['tel_oficina']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            Tel. oficina
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1 letter-spacing-2">
                            {{ $datos['rfc']!=''?$datos['rfc']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            r.f.c
                        </div>
                    </div>
                </td>
                <td class="py-2 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ $datos['email']!=''?$datos['email']:'No capturado' }}
                        </div>
                        <div class="mt-1">
                            email
                        </div>
                    </div>
                </td>

            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="py-1 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ ((String)($datos['fecha_nac'])) }}
                        </div>
                        <div class="mt-1">
                            Fecha de Nacimiento
                        </div>
                    </div>
                </td>
                <td class="py-1 w-50 px-2">
                    <div class="bold uppercase texto-sm  pl-2">
                        <div class="w-normal border-bottom-black-1 pb-1">
                            {{ calculaedad((String)($datos['fecha_nac'])) }} años
                        </div>
                        <div class="mt-1">
                            edad
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>
