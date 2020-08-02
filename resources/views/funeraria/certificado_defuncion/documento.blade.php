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

        /*
        estilos de la tabla de datos
        **/
        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
        }
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
                                Número Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['servicio_id'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Tipo Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['tipo_solicitud_texto'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <h1 class="center mt-5">información necesaria para elaborar el certificado de defunción
        <div class="mt-2 w-normal">(information needed to prepare the death certificate)</div>
    </h1>
    <table class="w-100 texto-base mt-5 datos_tabla uppercase">
        @if (!is_null($datos['folio_certificado']))
               <tr class="size-15px">
            <td class="w-25 bold px-2 py-1">
                Folio del Certificado
                <div class="w-normal">
                    (Certificate Folio)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['folio_certificado'] }}</td>
        </tr>
        @endif
        <tr class="size-15px">
            <td class="w-25 bold px-2 py-1">
                nombre del fallecido
                <div class="w-normal">
                    (name of the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['nombre_afectado'] }}</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-15px">
            <td class="w-25  bold px-2 py-1">
               fecha de nacimiento
                 <div class="w-normal">
                    (date of birth)
                </div>
            </td>
            <td class="w-25 px-2">
                @if (!is_null($datos['fecha_nacimiento']))
                     {{ $datos['fecha_nacimiento_texto'] }}
                @endif
            </td>
            <td class="w-25  bold px-2 py-1">
              sexo
                 <div class="w-normal">
                    (gender)
                </div>
            </td>
            <td class="w-25 px-2">
                  @if (!is_null($datos['generos_id']))
                     {{ $datos['genero_texto'] }}
                @endif
            </td>
        </tr>
         <tr class="size-15px">
            <td class="w-25  bold px-2 py-1">
               lugar de nacimiento
                 <div class="w-normal">
                    (place of birth)
                </div>
            </td>
            <td class="w-25 px-2">{{ $datos['lugar_nacimiento'] }}</td>
            <td class="w-25  bold px-2 py-1">
              nacionalidad
                 <div class="w-normal">
                    (natioality)
                </div>
            </td>
            <td class="w-25 px-2">
                  @if (!is_null($datos['nacionalidades_id']))
                     {{ $datos['nacionalidad']['nacionalidad'] }}
                @endif
            </td>
        </tr>
         <tr class="size-15px">
            <td class="w-25  bold px-2 py-1">
               edad
                 <div class="w-normal">
                    (age)
                </div>
            </td>
            <td class="w-25 px-2">{{ $datos['edad'] }}</td>
            <td class="w-25  bold px-2 py-1">
              estado civil
                 <div class="w-normal">
                    (marital status)
                </div>
            </td>
            <td class="w-25 px-2">
                 @if (!is_null($datos['estados_civiles_id']))
                     {{ $datos['estado_civil']['estado'] }}
                @endif
            </td>
        </tr>
           <tr class="size-15px">
            <td class="w-25  bold px-2 py-1">
               domcicilio actual
                 <div class="w-normal">
                    (permanent address)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">{{ $datos['direccion_fallecido'] }}</td>
        </tr>
            <tr class="size-15px">
            <td class="w-25  bold px-2 py-1">
               escolaridad
                 <div class="w-normal">
                    (academic degree)
                </div>
            </td>
            <td class="w-25 px-2">
                  @if (!is_null($datos['escolaridades_id']))
                     {{ $datos['escolaridad']['escolaridad'] }}
                @endif
            </td>
            <td class="w-25  bold px-2 py-1">
              ocupación
                 <div class="w-normal">
                    (ocupation)
                </div>
            </td>
            <td class="w-25 px-2">{{ $datos['ocupacion'] }}</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-15px">
            <td class="w-35  bold px-2 py-1">
               lugar del fallecimiento
                 <div class="w-normal">
                    (place where the death took place)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">{{ $datos['lugar_muerte'] }}</td>
        </tr>
    </table>
 <table class="w-100 texto-base mt-1 datos_tabla uppercase">
    </tr>
            <tr class="size-15px">
            <td class="w-21  bold px-2 py-1">
               fecha y hora de muerte
                 <div class="w-normal">
                    (date and time of death)
                </div>
            </td>
            <td class="w-20 px-2">
                @if (!is_null($datos['fechahora_defuncion']))
                     {{ $datos['fecha_muerte_texto'] }}
                @endif
            </td>
            <td class="w-30  bold px-2 py-1">
              ¿atención médica antes de morir?
                 <div class="w-normal">
                    (medical attention before death?)
                </div>
            </td>
            <td class="w-10 px-2">
                 @if (!is_null($datos['atencion_medica_b']))
                     {{ $datos['atencion_medica_texto'] }}
                @endif
            </td>
        </tr>
        
    </table>
     <table class="w-100 texto-base mt-1 datos_tabla uppercase">
          <tr class="size-15px">
            <td class="w-35  bold px-2 py-1">
               ¿padecía alguna enfermedad?
                 <div class="w-normal">
                    (any disease?)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">{{ $datos['enfermedades_padecidas'] }}</td>
        </tr>
     </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-15px">
            <td class="w-30  bold px-2 py-1">
               nombre del informante
                 <div class="w-normal">
                    (informant's name)
                </div>
            </td>
            <td class="w-70 px-2" colspan="3">{{ $datos['certificado_informante'] }}</td>
        </tr>
    </table>


     <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-15px">
            <td class="w-35  bold px-2 py-1">
              parentesco con el fallecido
                 <div class="w-normal">
                    (relationship with the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_parentesco'] }}</td>
        </tr>
         <tr class="size-15px">
            <td class="w-35  bold px-2 py-1">
              teléfono
                 <div class="w-normal">
                    (telephone number)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_telefono'] }}</td>
        </tr>
          @if (!is_null($datos['medico_legista']))
             <tr class="size-15px">
            <td class="w-35  bold px-2 py-1">
              médico legista
                 <div class="w-normal">
                    (medical examiner)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['medico_legista'] }}</td>
        </tr>
        @endif
         
    </table>

    <div class="w-100 center">
            <div class="w-50  mt-25 mr-auto ml-auto">
                <div class="w-90 border-top">
                    <div class="pt-3 pb-1"><span class="uppercase  texto-sm"></span></div>
                    <span class="uppercase bold texto-sm">firma del informante</span>
                </div>
            </div>
        </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>