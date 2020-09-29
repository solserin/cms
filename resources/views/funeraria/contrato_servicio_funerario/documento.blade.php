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
            line-height: 1em !important;
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

        .tabla_dato {
            border-collapse: collapse;
        }

        .tabla_dato .border-bottom {
            border-bottom: 1px solid #ddd;
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
    <p class="fecha  right">
        <span class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }} a
            {{ fecha_only(now()) }}.</span>
    </p>
    <div class="contenido w-100">
        <p class="justificar line-17 size-13px uppercase">
            CONTRATO DE PRESTACIÓN DE SERVICIOS FUNERARIOS DE USO INMEDIATO, QUE CELEBRAN POR UNA PARTE <span
                class="bold">{{ $empresa->razon_social }}</span>
            REPRESENTADA EN ESTE ACTO POR EL <span class="bold">SR. {{ $registro->rep_legal }}</span> A QUIEN EN
            LO SUCESIVO SE LE DENOMINARÁ <span class="bold">"EL PROVEEDOR"</span>, Y POR LA OTRA <span
                class="bold">{{ $datos['operacion']['cliente']['nombre'] }}</span>, A QUIEN EN LO SUCESIVO
            SE LE
            DENOMINARÁ <span class="bold">"EL CONSUMIDOR"</span>, A QUIENES DE MANERA CONJUNTA SE LES DENOMINARÁ COMO
            <span class="bold">"LAS PARTES"</span>. AL TENOR DEL
            SIGUIENTE GLOSARIO, ASI COMO DE LAS SIGUIENTES DECLARACIONES Y CLÁUSULAS:
        </p>

        <p class="justificar line-base size-13px uppercase">
            GLOSARIO.- Para los efectos de este Contrato y su Anexo, se entiende por:
        </p>


        <div class="lista pl-11 -mt-1 line-base size-14px">
            <p class="justificar ">
                <span class=" bold  -ml-6">a.</span>
                <span class="ml-1">
                    <span class="bold">Agencia Funeraria:</span> El establecimiento mercantil autorizado, en el que
                    proveedor prestará o proveerá
                    el Servicio
                    Funerario de uso
                    inmediato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">b.</span>
                <span class="ml-1">
                    <span class="bold">Anexo:</span> La orden de servicio, en la cual se enlistan los productos
                    contratados que se incluyen en el
                    servicio, así como
                    el costo total y
                    cualquier observación relativa a la prestación del servicio objeto del presente Contrato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">c.</span>
                <span class="ml-1">
                    <span class="bold">Consumidor:</span> A la persona física o moral que adquiere o utiliza bienes y
                    servicios funerarios.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">d.</span>
                <span class="ml-1">
                    <span class="bold">Proveedor:</span> A la persona física o moral que es comercializador de servicios
                    funerarios, de
                    conformidad con lo dispuesto
                    en el
                    presente Contrato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">e.</span>
                <span class="ml-1">
                    <span class="bold">Servicio:</span> es el servicio funerario de uso inmediato que ofrece el
                    proveedor y que contrata el
                    consumidor para ser
                    utilizado a la
                    firma del presente contrato y que incluirá los productos enlistados en el anexo del presente
                    Contrato.
                </span>
            </p>
        </div>

        <p class="justificar line-17 size-13px uppercase center bold">
            declaraciones
        </p>
        <p class="justificar line-17 size-13px bold">
            I. Declara "EL PROVEEDOR":
        </p>
        <p class="justificar line-17 size-13px">
            <span class="bold">a)</span> Ser una persona moral, legalmente constituida conforme a las leyes mexicanas lo
            que se acredita con
            testimonio de la
            escritura pública
            número <span class="bold">{{ $registro->t_nep }}</span>, de fecha <span
                class="bold">{{ fecha_no_day($registro->fecha_rpc )}}</span>, otorgada ante la
            fé del <span class="bold uppercase">Lic. {{ $registro->fe_lic }}</span>,
            Notario Público
            número <span class="bold uppercase">{{ $registro->num_np }}</span>, en <span
                class="bold uppercase">{{ $registro->ciudad_np }}, {{ $registro->estado }}</span> e inscrita en el
            Registro Público de Comercio de No. <span class="bold">{{ $registro->num_rpc }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b)</span> Cuenta con correo electrónico: <span class="bold">{{ $empresa->email }}</span>
            y teléfono: <span class="bold">{{ $empresa->telefono }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c)</span> Dentro de las actividades que constituyen su objeto social, se encuentra
            prevista la de <span class="bold">AGENCIA FUNERARIA</span>, así como
            posee los elementos adecuados y experiencia suficiente para obligarse a lo estipulado en el presente
            Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">d)</span> Su domicilio se encuentra ubicado en la calle <span
                class="bold">{{ $empresa->calle }}</span> No. <span class="bold">{{ $empresa->num_ext }}</span> COL.
            <span class="bold">{{ $empresa->colonia }}</span> C.P. <span class="bold">{{ $empresa->cp }}</span>, en
            <span class="bold">{{ $empresa->ciudad }}</span>,
            <span class="bold">{{ $empresa->estado }}</span>., el cual señala como domicilio convencional para todos los
            efectos legales del presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e)</span> Que se encuentra inscrita en el Registro Federal de Contribuyentes con la
            clave: <span class="bold">{{ $empresa->rfc }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">f)</span> Cuenta con la infraestructura, los elementos propios, los recursos técnicos y
            humanos suficientes para cumplir con sus
            obligaciones conforme a lo establecido en el presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">g)</span> Cumple con las licencias, permisos, avisos certificados y autorizaciones
            previstas en las disposiciones legales y normas
            vigentes que
            corresponden para llevar a cabo las actividades que comprende la prestación del Servicio objeto de este
            Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">h)</span> Para la atención de dudas, aclaraciones, reclamaciones o para proporcionar
            servicios de orientación señala el teléfono
            <span class="bold">{{ $empresa->telefono}}</span>
            y correo electrónico; <span class="bold">{{ $empresa->email }}</span>, con un horario de atención al público
            LAS 24 HORAS
        </p>
        <p class="justificar line-17 size-13px bold">
            II. Declara "EL CONSUMIDOR":
        </p>
        <p class="justificar line-17 size-13px">
            <span class="bold">a)</span>Ser una persona de nacionalidad <span
                class="bold">{{ $datos['nacionalidad']['nacionalidad'] }}</span> y suficiente capacidad para obligarse
            en los términos del
            presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b)</span> Cuenta con correo electrónico <span
                class="bold">{{ $datos['operacion']['cliente']['email']!=''?$datos['operacion']['cliente']['email']:'N/A' }}</span>
            y teléfono <span
                class="bold">{{ $datos['operacion']['cliente']['telefono']!=''?$datos['operacion']['cliente']['telefono']:'N/A' }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c)</span> Se encuentra registrado en Registro Federal de Contribuyente con la clave <span
                class="bold">{{ $datos['operacion']['cliente']['rfc']!=''?$datos['operacion']['cliente']['rfc']:'N/A' }}</span>.
        </p>
        <p class="justificar line-17 size-13px ">
            <span class="bold">d)</span>Su domicilio se encuentra ubicado <span
                class="bold">{{ $datos['operacion']['cliente']['direccion']!=''?$datos['operacion']['cliente']['direccion']:'N/A' }}</span>.
            El cual señala como domicilio
            convencional para todos los efectos legales del presente Contrato.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e)</span> Recibió de <span class="bold">“El proveedor”</span> toda la información
            relativa al servicio objeto del presente contrato en virtud de las
            Declaraciones anteriores,
            <span class="bold">“Las partes”</span> convienen en obligarse conforme a las siguientes:.
        </p>



        <div class="w-100 center hidden">
            <div class="w-50 float-left mt-20">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold">
                            @if ($datos['embalsamar_b']==1)
                            Dr.
                            @endif
                            {{ $datos['medico_responsable_embalsamado'] }}</span></div>
                </div>
                <span class="texto-base">Médico Responsable</span>
            </div>
            <div class="w-50 float-right mt-20">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class="pb-1"><span class="texto-base bold">{{ $datos['preparador'] }}</span>
                    </div>
                    <span class="texto-base">Embalsamador</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>