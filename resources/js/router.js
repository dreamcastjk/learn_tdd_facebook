import  Vue from 'vue';
import Start from "./views/Start";
import VueRouter from 'vue-router';

Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',

    routes: [
        {
            path: '/', name: 'home', component: Start,
        }
    ]
});
