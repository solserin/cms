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
            border: 2px solid #{{env('MAINCOLOR')}};
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
            <table class="w-100">
                <tr>
                    <!--<td style="width:23%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                -->
                <td width="70%">
                </td>
                    <td width="30%">
                        <div class="numeros-contrato">
                            <div class="control uppercase bg-primary text-white size-20px letter-spacing-2">
                               título
                            </div>
                            <p class="control-valor py-1 size-18px bold text-primary">
                                {{ $datos['numero_titulo'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>

     <table class="w-100 mt-10">
                <tr>
                <td width="40%" class="right">
                      <img src="{{ public_path(env('LOGOJPG')) }}"  class="w-80 mr-8">
                </td>
                <td width="60%">
                         <h1 class="left text-primary">
                            {{ $empresa->razon_social }}
                        </h1>
                        <p class="datos-header left bold size-15px capitalize">
                            r.f.c. {{ $empresa->rfc }}
                        </p>
                        <p class="datos-header left bold size-15px capitalize">
                            {{ $empresa->calle }} Núm. Ext {{ $empresa->num_ext }}
                        </p>
                        <p class="datos-header left bold size-15px capitalize">
                            Col. {{ $empresa->colonia }}. cp. {{ $empresa->cp }}.
                            {{ $empresa->ciudad }}
                            {{ $empresa->estado }}
                        </p>
                        <p class="datos-header left bold size-15px capitalize">
                            Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }}
                        </p>
                         <h1 class="left text-primary size-20px mt-4">
                            {{ $empresa->cementerio['cementerio']}}
                        </h1>
                        <p class="datos-header left bold size-15px capitalize">
                            {{ $empresa->cementerio['calle'] }} Núm. Ext {{ $empresa->cementerio['num_ext'] }}
                        </p>
                        <p class="datos-header left bold size-15px capitalize">
                            {{ $empresa->cementerio['ciudad'] }}
                            {{ $empresa->cementerio['estado'] }}
                        </p>
                </td>
                </tr>
            </table>

            <h1 class="titulo-aportacion text-primary bold size-48px word-spacing-10 letter-spacing-4">
                título de aportación
            </h1>

            <table class="w-100  border-black-2">
                <tr>
                    <td class="p-3 center uppercase">
                        titular
                    </td>
                    <td colspan="3" class="px-3">
                        <div class="border-bottom-black-1 uppercase px-3">
                            {{$datos['cliente_nombre']}}
                        </div>
                    </td>
                </tr>
                 <tr>
                    <td width="25%" class="bg-black center py-1 text-white uppercase bold">
                          {{$datos['tipo_texto']}}
                    </td>
                    <td width="25%" class="bg-black center py-1 text-white uppercase bold">
                          fila
                    </td>
                    <td width="25%" class="bg-black center py-1 text-white uppercase bold">
                          lote
                    </td>
                    <td width="25%" class="bg-black center py-1 text-white uppercase bold">
                          capacidad
                    </td>
                </tr>

                 <tr>
                    <td width="25%" class=" center py-6  uppercase border-black-1">
                          {{$datos['area_nombre']}}
                    </td>
                    <td width="25%" class=" center py-6  uppercase border-black-1">
                          {{$datos['fila_texto']}}
                    </td>
                    <td width="25%" class=" center py-6  uppercase border-black-1">
                           {{$datos['lote_texto']}}
                    </td>
                    <td width="25%" class=" center py-6  uppercase border-black-1">
                          {{$datos['tipo_propiedad_capacidad']}}
                    </td>
                </tr>
            </table>
            <p class="uppercase size-17px justificar letter-spacing-2 mt-4">
                Este título forma parte integrante del convenio de derecho de uso mortuorio perpetuo y está sujeto a lo que en él se estipula.
            </p>
            <table class="w-100 pt-7">
                <tr>
                    <td class="w-20 left uppercase size-18px bold text-primary">convenio No.</td>
                    <td class="w-30 center border-bottom-primary-2  size-18px uppercase">{{$datos['numero_convenio']}}</td>
                    <td class="w-50"></td>
                </tr>
               
            </table>
             <table class="w-100 pt-7">
                <tr>
                    <td class="w-20 left uppercase size-18px bold text-primary">solicitud No.</td>
                <td class="w-30 center border-bottom-primary-2  size-18px uppercase">{{$datos['numero_solicitud']}}</td>
                    <td class="w-50"></td>
                </tr>
            </table>
    <p class="justificar italic mt-20">
        Esta impresión es solo una copia del título original emitido por la empresa y no es válido sin el membrete oficial de la empresa.
    </p>
</body>

</html>
<span class="uppercase bold texto-sm"></span>