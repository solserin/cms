<template>
    <div>
        <vx-card>
            <vs-tabs v-model="activeTab" class="tab-action-btn-fill-conatiner mb-3">
                <vs-tab label="Funeraria" icon-pack="feather" icon="icon-book-open"></vs-tab>
                <vs-tab label="Registro Publico" icon-pack="feather" icon="icon-book-open"></vs-tab>
                <vs-tab label="Cementerio" icon-pack="feather" icon="icon-layers"></vs-tab>
                <vs-tab label="Crematorio" icon-pack="feather" icon="icon-minimize"></vs-tab>
                <vs-tab label="Velatorio" icon-pack="feather" icon="icon-home"></vs-tab>
                <vs-tab label="Facturacion" icon-pack="feather" icon="icon-home"></vs-tab>
            </vs-tabs>
            <Funeraria v-if="activeTab === 0"/>
            <RegistroPublico v-if="activeTab === 1"/>
            <Cementerio v-if="activeTab === 2"/>
            <Crematorio v-if="activeTab === 3"/>
            <Velatorio v-if="activeTab === 4"/>
            <Facturacion v-if="activeTab === 5"/>
        </vx-card>
    </div>
</template>
<script>
import Funeraria from './Funeraria'
import RegistroPublico from './RegistroPublico'
import Cementerio from './Cementerio'
import Crematorio from './Crematorio'
import Velatorio from './Velatorio'
import Facturacion from './Facturacion'

export default {
    components: {
        Funeraria,
        RegistroPublico,
        Cementerio,
        Crematorio,
        Velatorio,
        Facturacion
    },
    data() {
        return {
            activeTab: 0
        }
    }
}
</script>
<style>
[dir] .tab-action-btn-fill-conatiner.con-vs-tabs .vs-tabs--content {
  height: 10px;
  padding: 0px !important;

}
</style>