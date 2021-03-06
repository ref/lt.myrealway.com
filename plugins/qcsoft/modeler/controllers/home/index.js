import Vue from 'vue'
import * as uiv from 'uiv'
import PortalVue from 'portal-vue'
import VueSplit from 'vue-split-panel'
import LoadingSpinner from './src/common/loading-spinner.vue'
import ToolbarBtn from './src/common/toolbar-btn.vue'
import app from './src/app.vue'
import './src/common/style.scss'

Vue.config.productionTip = false
Vue.use(uiv)
Vue.use(PortalVue)
Vue.use(VueSplit)
Vue.component(LoadingSpinner.name, LoadingSpinner)
Vue.component(ToolbarBtn.name, ToolbarBtn)

$(function ()
{
    let appEl = $('.app')

    new Vue({
        data      : {schema: appEl.data('schema')},
        el        : '#app',
        template  : `<app :start-zoom="${appEl.data('zoom')}" :start-x="${appEl.data('x')}" :start-y="${appEl.data('y')}"/>`,
        components: {app}
    })
})
