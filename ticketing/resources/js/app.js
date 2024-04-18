import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import '../scss/styles.scss'
import * as bootstrap from 'bootstrap'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component('VueDatePicker', VueDatePicker)
      .mixin({ methods: { route } })
      .mount(el)
  },
})