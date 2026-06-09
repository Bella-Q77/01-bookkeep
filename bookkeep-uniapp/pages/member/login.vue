<template>
	<view class="login-page">
		<!-- 顶部导航栏 -->
		<uni-nav-bar 
			title="登录"
			background-color="#07C160"
			color="#FFFFFF"
			:fixed="true"
			:status-bar="true"
		>
			<view slot="left" class="nav-left">
				<view class="home-btn" @click="goToHome">
					<text class="iconfont icon-shouye"></text>
				</view>
			</view>
		</uni-nav-bar>
		
		<view class="login-container">
			<view class="logo">
				<image src="/static/logo.png" mode="aspectFit"></image>
			</view>

		<view class="form-container">
			<view class="form-item">
				<input class="input" type="text" v-model="form.username" placeholder="请输入账号/邮箱" />
			</view>

			<view class="form-item">
				<input class="input" type="password" v-model="form.password" placeholder="请输入密码" />
			</view>

			<button class="login-btn" @click="handleLogin">登录</button>

			<view class="quick-login">
				<text class="divider">或</text>
				<button class="wechat-btn" @click="handleWechatLogin">
					<uni-icons type="weixin" size="20" color="#fff"></uni-icons>
					<text>微信登录</text>
				</button>
				<!-- 获取手机号按钮（可选） -->
				<button class="wechat-phone-btn" open-type="getPhoneNumber" @getphonenumber="onGetPhoneNumber" v-if="false">
					<text>获取手机号</text>
				</button>
			</view>

			<view class="footer">
				<text class="register-text" @click="navToRegister">没有账号？立即注册</text>
			</view>
		</view>
	</view>
	</view>
</template>

<script>
	import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
	import userApi from '@/api/user'
	export default {
		components: {
			uniIcons
		},
		data() {
			return {
				form: {
					username: '',
					password: ''
				}
			}
		},
		methods: {
			async handleLogin() {
				if (!this.form.username || !this.form.password) {
					uni.showToast({
						title: '请输入账号和密码',
						icon: 'none'
					})
					return
				}

				try {
					uni.showLoading({
						title: '登录中...'
					})

					// 调用真实登录API
					const res = await userApi.login(this.form)

					console.log('登录响应:', res)

					uni.hideLoading()

					// 登录成功，保存token和用户信息
					// API返回格式: {code: 1, msg: '操作成功', data: {access_token, user_token, userinfo}, exe_time: '...'}
					// 注意：这里的res.data是API返回的data字段，不是整个响应对象

					// 处理token保存
					if (res.data && (res.data.token || res.data.user_token)) {
						// 数据在res.data中
						const token = res.data.token || res.data.user_token;
						const access_token = res.data.access_token || ''

						uni.setStorageSync('token', token)
						if (access_token) {
							uni.setStorageSync('access_token', access_token)
						}
						console.log('Token保存成功:', token)

					} 

					// 保存用户信息
					if (res.data && res.data.userinfo) {
						// 用户信息在res.data.userinfo中
						const userInfo = res.data.userinfo
						uni.setStorageSync('userInfo', userInfo)
						console.log('用户信息保存成功:', userInfo)

					} 

					// 如果有刷新token，也保存
					if (res.refreshToken || res.refresh_token) {

						const refreshToken = res.refreshToken || res.refresh_token
						uni.setStorageSync('refreshToken', refreshToken)

					}

					uni.showToast({
						title: '登录成功',
						icon: 'success'
					})

					// 跳转到首页（dashboard是tabbar页面，使用switchTab）
					setTimeout(() => {
						uni.switchTab({
							url: '/pages/dashboard/index'
						})
					}, 1500)

				} catch (error) {
					uni.hideLoading()

					// 处理不同类型的错误
					let errorMessage = '登录失败'

					if (error.code === 401) {
						errorMessage = '用户名或密码错误'
					} else if (error.code === 403) {
						errorMessage = '账号已被禁用'
					} else if (error.code === 429) {
						errorMessage = '登录尝试过于频繁，请稍后再试'
					} else if (error.code === -1) {
						errorMessage = '网络连接失败，请检查网络'
					} else if (error.message) {
						errorMessage = error.message
					}

					uni.showToast({
						title: errorMessage,
						icon: 'none',
						duration: 3000
					})

					console.error('登录失败:', error)
				}
			},

			// 微信登录（基于openid）
			async handleWechatLogin() {
				try {
					uni.showLoading({
						title: '微信登录中...'
					})

					// 方案1：通过code获取openid（推荐）
					const loginRes = await new Promise((resolve, reject) => {
						uni.login({
							provider: 'weixin',
							success: resolve,
							fail: reject
						})
					})

					if (!loginRes.code) {
						throw new Error('获取微信登录code失败')
					}

					// 获取用户信息（可选，用于显示昵称和头像）
					let userInfo = null
					try {
						const userInfoRes = await new Promise((resolve, reject) => {
							uni.getUserInfo({
								provider: 'weixin',
								success: resolve,
								fail: reject
							})
						})
						userInfo = userInfoRes.userInfo
					} catch (error) {
						console.log('用户拒绝授权用户信息，继续登录流程')
					}

					// 调用后端微信登录接口
					// 这里有两种方式：
					// 1. 使用code方式（后端通过code获取openid）
					const res = await userApi.wechatLogin({
						code: loginRes.code
					})

					// 或者使用方案2：如果后端支持直接传openid
					// const res = await userApi.wechatOpenidLogin({
					//   openid: '从后端获取的openid',
					//   nickname: userInfo ? userInfo.nickName : '',
					//   avatar: userInfo ? userInfo.avatarUrl : ''
					// })

					uni.hideLoading()

					// 处理登录成功
					if (res.data && (res.data.token || res.data.user_token)) {
						const token = res.data.token || res.data.user_token
						const access_token = res.data.access_token || res.data.access_token

						uni.setStorageSync('token', token)
						uni.setStorageSync('access_token', access_token)
						uni.setStorageSync('userInfo', res.data.userinfo)

						console.log('微信登录成功，Token:', token)
					} 

					// 保存用户信息
					if (res.data && res.data.userinfo) {
						uni.setStorageSync('userInfo', res.data.userinfo)
					} 

					// 保存刷新token
					if (res.refreshToken || res.refresh_token) {
						const refreshToken = res.refreshToken || res.refresh_token
						uni.setStorageSync('refreshToken', refreshToken)
					}

					uni.showToast({
						title: '微信登录成功',
						icon: 'success'
					})

					// 跳转到首页
					setTimeout(() => {
						uni.switchTab({
							url: '/pages/dashboard/index'
						})
					}, 1500)

				} catch (error) {
					uni.hideLoading()

					let errorMessage = '微信登录失败'
					if (error.code === 401) {
						errorMessage = '微信授权失败'
					} else if (error.code === 403) {
						errorMessage = '账号已被禁用'
					} else if (error.message) {
						errorMessage = error.message
					}

					uni.showToast({
						title: errorMessage,
						icon: 'none',
						duration: 3000
					})

					console.error('微信登录失败:', error)
				}
			},

			onGetPhoneNumber(e) {
				// 微信获取手机号（可选功能）
				console.log('微信获取手机号回调', e)
				if (e.detail.errMsg === 'getPhoneNumber:ok') {
					// 获取手机号成功，可以绑定到用户信息
					console.log('获取手机号成功:', e.detail)
				} else {
					console.log('用户拒绝授权手机号')
				}
			},

			navToRegister() {
				uni.navigateTo({
					url: '/pages/member/register'
				})
			},
			
			// 返回主页
			goToHome() {
				uni.switchTab({
					url: '/pages/dashboard/index'
				})
			}
		}
	}
</script>

<style>
	.login-page {
		min-height: 100vh;
		background-color: #fff;
	}

	.login-container {
		padding: 40rpx;
		padding-top: 120rpx; /* 为导航栏留出空间 */
		min-height: calc(100vh - var(--window-top));
		background-color: #fff;
	}

	.login-container .logo {
		display: flex;
		justify-content: center;
		/* #ifdef H5 */
		margin-bottom: 80rpx;
		/* #endif */
		/* #ifdef MP-WEIXIN */
		margin-bottom: 60rpx;
		/* 小程序中适当减少底部边距 */
		/* #endif */
	}

	.login-container .logo image {
		width: 200rpx;
		height: 200rpx;
	}

	.login-container .form-container .form-item {
		margin-bottom: 40rpx;
	}

	.login-container .form-container .form-item .input {
		height: 100rpx;
		border-bottom: 1rpx solid #eee;
		font-size: 32rpx;
		padding: 0 20rpx;
	}

	.login-container .form-container .login-btn {
		height: 90rpx;
		line-height: 90rpx;
		background: #07C160;
		color: #fff;
		font-size: 32rpx;
		border-radius: 45rpx;
		margin-top: 60rpx;
	}

	.login-container .form-container .quick-login {
		margin-top: 60rpx;
		text-align: center;
	}

	.login-container .form-container .quick-login .divider {
		display: block;
		color: #999;
		font-size: 28rpx;
		margin-bottom: 30rpx;
		position: relative;
	}

	.login-container .form-container .quick-login .divider::before,
	.login-container .form-container .quick-login .divider::after {
		content: '';
		position: absolute;
		top: 50%;
		width: 30%;
		height: 1rpx;
		background: #eee;
	}

	.login-container .form-container .quick-login .divider::before {
		left: 0;
	}

	.login-container .form-container .quick-login .divider::after {
		right: 0;
	}

	.login-container .form-container .quick-login .wechat-btn {
		height: 90rpx;
		line-height: 90rpx;
		background: #09BB07;
		color: #fff;
		font-size: 32rpx;
		border-radius: 45rpx;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.login-container .form-container .quick-login .wechat-btn uni-icons {
		margin-right: 15rpx;
	}

	.login-container .form-container .footer {
		margin-top: 60rpx;
		text-align: center;
	}

	.login-container .form-container .footer .register-text {
		color: #07C160;
		font-size: 28rpx;
	}

	/* 导航栏左侧主页按钮 */
	.nav-left {
		padding-left: 15px;
	}

	.home-btn {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 32px;
		height: 32px;
		border-radius: 16px;
		background-color: rgba(255, 255, 255, 0.2);
		transition: all 0.3s ease;
	}

	.home-btn:active {
		background-color: rgba(255, 255, 255, 0.3);
		transform: scale(0.95);
	}

	.home-btn .iconfont {
		font-size: 18px;
		color: #FFFFFF;
	}
</style>