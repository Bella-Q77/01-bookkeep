import App from './App'

// #ifndef VUE3
import Vue from 'vue'
import './uni.promisify.adaptor'

// 导入API模块
import api from '@/api'

// 使用 uni-app 的 easycom 自动注册组件，无需手动导入

// 将API挂载到Vue原型上
Vue.prototype.$api = api

Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
  ...App
})
app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  return {
    app
  }
}
// #endif