<template>
  <view class="register-page">
      <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="用户注册" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
  <view class="register-container">




    <view class="form-container">
      <view class="form-item">
        <input class="input" type="text" v-model="form.username" placeholder="请输入邮箱" />
      </view>
      
      <view class="form-item">
        <input class="input" type="password" v-model="form.password" placeholder="请输入密码(6-20位)" />
      </view>
      
      <view class="form-item">
        <input class="input" type="password" v-model="form.password_confirm" placeholder="请确认密码" />
      </view>
      
      <view class="form-item">
        <input class="input" type="text" v-model="form.nickname" placeholder="请输入昵称" />
      </view>

      <button class="register-btn" @click="handleRegister">注册</button>
    </view>


  </view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'
import userApi from '@/api/user'
export default {
  components: {
    'uni-icons': uniIcons,
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      form: {
		username:'a@a.com',
        password: '123456',
        password_confirm: '123456',
        nickname: 'testAA'
      }
    }
  },
  methods: {
    async handleRegister() {
		
      if (!this.validateForm()) return
      
      try {
        uni.showLoading({ title: '注册中...' })
        
        // 准备API数据，映射字段名
        const apiData = {
          username: this.form.username,  // 使用username作为邮箱
          email: this.form.username,     // 同时发送email字段
          password: this.form.password,
          password_confirm: this.form.password_confirm,
          nickname: this.form.nickname
        }
        
        // 调用注册API
        const res = await userApi.register(apiData)
        
        console.log('调用注册API:', res)
        
        if (res.code === 1) {
          // 注册成功
          uni.hideLoading()
          uni.showToast({ title: '注册成功', icon: 'success' })
          setTimeout(() => this.goBack(), 1500)
        } else {
          // 注册失败，显示API返回的错误信息
          throw new Error(res.msg || '注册失败')
        }
        

      } catch (e) {
        uni.hideLoading()
        uni.showToast({ title: e.message || '注册失败', icon: 'none' })
      }
    },
    
    validateForm() {
      const emailReg = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
      if (!emailReg.test(this.form.username)) {
        uni.showToast({ title: '请输入有效的邮箱地址', icon: 'none' })
        return false
      }
      
      if (this.form.password.length < 6 || this.form.password.length > 20) {
        uni.showToast({ title: '密码长度需在6-20位之间', icon: 'none' })
        return false
      }
      
      if (this.form.password !== this.form.password_confirm) {
        uni.showToast({ title: '两次输入的密码不一致', icon: 'none' })
        return false
      }
      
      if (!this.form.nickname) {
        uni.showToast({ title: '请输入昵称', icon: 'none' })
        return false
      }
      
      return true
    },
    
    goBack() {
      // 获取页面栈
      const pages = getCurrentPages()
      
      // 如果页面栈大于1，可以返回上一页
      if (pages.length > 1) {
        uni.navigateBack()
      } else {
        // 跳转到登录页而不是首页
        uni.navigateTo({
            url: '/pages/member/login'
        })
      }
      console.log('返回按钮被点击')
    }
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background-color: #fff;
  padding: 0px;
  margin: 0px;
  width: 100%;
}
.register-container {
  padding: 30rpx;
  padding-top: 60rpx; /* 进一步减少顶部间距，让导航栏更紧挨顶部 */
}

/* 移除原有的header样式，因为现在使用uni-nav-bar */
.register-container .header {
  display: none;
}

.register-container .form-container .form-item {
  margin-bottom: 40rpx;
}

.register-container .form-container .form-item .input {
  height: 100rpx;
  border-bottom: 1rpx solid #eee;
  font-size: 32rpx;
  padding: 0 20rpx;
}

.register-container .form-container .register-btn {
  height: 90rpx;
  line-height: 90rpx;
  background: #07C160;
  color: #fff;
  font-size: 32rpx;
  border-radius: 45rpx;
  margin-top: 80rpx;
}
</style>



