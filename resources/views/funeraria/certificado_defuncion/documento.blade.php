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

        .opcion {
            width: 20px !important;
            height: 20px !important;
            display: inline-block;
            text-align: center !important;
            border: 1px solid #ddd !important;
        }

        .display {
            visibility: visible !important;
        }

        .hidden {
            visibility: hidden !important;
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
        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                Folio del Certificado
                <div class="w-normal">
                    (Certificate Folio)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['folio_certificado'] }}</td>
        </tr>

        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                nombre completo del fallecido
                <div class="w-normal">
                    (full name of the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['nombre_afectado'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                sexo
                <div class="w-normal">
                    (gender)
                </div>
            </td>
            <td class="w-75 px-2">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Hombre/Male
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Mujer/Female
            </td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                fecha de nacimiento
                <div class="w-normal">
                    (date of birth)
                </div>
            </td>
            <td class="w-35 px-2">
                @if (!is_null($datos['fecha_nacimiento']))
                {{ $datos['fecha_nacimiento_texto'] }}
                @endif
            </td>
            <td class="w-10  bold px-2 ">
                edad
                <div class="w-normal">
                    (age)
                </div>
            </td>
            <td class="w-30 px-2">{{ $datos['edad'] }}</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                lugar de nacimiento
                <div class="w-normal">
                    (place of birth)
                </div>
            </td>
            <td class="w-35 px-2">{{ $datos['lugar_nacimiento'] }}</td>
            <td class="w-15  bold px-2 ">
                nacionalidad
                <div class="w-normal">
                    (nationality)
                </div>
            </td>
            <td class="w-25 px-2">
                @if (!is_null($datos['nacionalidades_id']))
                {{ $datos['nacionalidad']['nacionalidad'] }}
                @endif
            </td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25 bold px-2 ">
                Estado civil
                <div class="w-normal">
                    (marital status)
                </div>
            </td>
            <td class="w-75 px-2">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Soltero(a)/Single
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Casado(a)/Married
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==4){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Divorciado(a)/Divorced
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==6){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Separado(a)/Separated
                <div class="opcion mt-1 ml-2">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==3){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Viudo(a)/Widow
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==5){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Unión Libre/Free Union
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==7){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Se ignora/Unknown
                <br>
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                domicilio actual
                <div class="w-normal">
                    (last known address)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">{{ $datos['direccion_fallecido'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                escolaridad
                <div class="w-normal">
                    (academic degree)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Ninguna/None
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Preescolar/Preschool
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==3){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Primaria/elementary
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==4){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                secundaria(a)/middle school
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==5){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                preparatoria/high school
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==6){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                profesional/college
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==7){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                postgrado(a)/master
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==8){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                se ignora(a)/unknown
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                Afiliación a
                <div class="w-normal">
                    (AFFILIATION TO)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Ninguna/None
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                imss
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==3){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                pemex
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==7){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                semar
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==5){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                imss prospera
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==6){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                isste
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==8){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                sedena
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==9){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                otros
                <div class="my-3">
                    <span class="bold">
                        otra afiliación/affiliation to:
                    </span> {{ $datos['afiliacion_nota'] }}
                </div>
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                ocupación
                <div class="w-normal">
                    (occupation)
                </div>
            </td>
            <td class="w-25 px-2">
                {{ $datos['ocupacion'] }}
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-27  bold px-2 ">
                lugar del fallecimiento
                <div class="w-normal">
                    (place of death)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                domicilio/Home address
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                hospital imss
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==3){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                pemex
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==4){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                hospital privado/private hospital
                <div class="opcion ml-2 mt-1">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==6){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                hospital isste
                <div class="opcion ml-2 mt-1">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==7){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                hospital semar
                <div class="opcion ml-2">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==8){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                hospital sedena
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==9){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                otro/other
                <div class="my-3">
                    <span class="bold">
                        Donde/Where:
                    </span> {{ $datos['lugar_muerte'] }}
                </div>
            </td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        </tr>
        <tr class="size-13px">
            <td class="w-21  bold px-2 ">
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
            <td class="w-30  bold px-2 ">
                ¿atención médica antes de morir?
                <div class="w-normal">
                    (medical attention before death?)
                </div>
            </td>
            <td class="w-10 px-2">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                si/yes
                <br>
                <div class="opcion mt-1">
                    <span
                        class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b']==0){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                no
            </td>
        </tr>

    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                ¿padecía alguna enfermedad?
                <div class="w-normal">
                    (medical problems)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">{{ $datos['enfermedades_padecidas'] }}</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-30  bold px-2 ">
                nombre del informante
                <div class="w-normal">
                    (informant's name)
                </div>
            </td>
            <td class="w-70 px-2" colspan="3">{{ $datos['certificado_informante'] }}</td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                parentesco con el fallecido
                <div class="w-normal">
                    (relationship with the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_parentesco'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                teléfono
                <div class="w-normal">
                    (telephone number)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_telefono'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                médico legista
                <div class="w-normal">
                    (medical examiner)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['medico_legista'] }}</td>
        </tr>
    </table>

    <div class="w-100 center mt-10">
        <div class="w-50  mr-auto ml-auto">
            <div class="w-90 border-top">
                <div class="pt-3 pb-1"><span class="uppercase  texto-sm"></span></div>
                <span class="uppercase bold texto-sm">firma del informante</span>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>