<script>
import { setupRouteGuard } from '@/common/utils/auth'

export default {
	onLaunch: function() {
		console.log('App Launch')
		
		// 设置路由守卫
		setupRouteGuard()
	},
	onShow: function() {
		console.log('App Show')
	},
	onHide: function() {
		console.log('App Hide')
	}
}
</script>

<style>
	/*每个页面公共css */
	@import '@/common/main.css';
	@import '@/static/iconfont/iconfont.css';
</style>