<template>
  <view class="profile-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="个人资料" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    
    <view class="profile-container">

    <!-- 用户信息表单 -->
    <view class="form-container">
      <view class="form-item">
        <text class="label">呢称</text>
        <input class="input" 
          v-model="userInfo.nickname" 
          placeholder="请输入用户名" />
      </view>
      <view class="form-item">
        <text class="label">手机</text>
        <input class="input" 
          v-model="userInfo.mobile" 
          placeholder="请输入手机号" 
          type="email" />
      </view>
      <view class="form-item">
        <text class="label">邮箱</text>
        <input class="input" 
          v-model="userInfo.email" 
          placeholder="请输入邮箱" 
          type="email" />
      </view>
    </view>
    <!-- 保存按钮 -->
    <view class="save-btn-container">
      <button class="save-btn" @click="saveProfile" :disabled="loading">
        {{ loading ? '保存中...' : '保存修改' }}
      </button>
    </view>
  </view>

  </view>
</template>

<script>
import {getUserInfo,updateUserInfo} from '@/api/user'
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'

export default {
  components: {
    'uni-icons': uniIcons,
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      loading: false,
      userInfo: {
        nickname: '',
        gender: '',
        birthday: '',
		mobile:'',
        email: ''
      }
    }
  },
  onLoad() {
    this.loadUserInfo()
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    async loadUserInfo() {
      try {
        // 直接调用API获取用户信息
        const res = await getUserInfo()
        if (res.code === 1) {
          this.userInfo = res.data
        } else {
          throw new Error(res.message || '获取用户信息失败')
        }
      } catch (e) {
        console.error('获取用户信息失败，使用本地数据', e)
      }
    },
    async saveProfile() {
      this.loading = true
      try {
        uni.showLoading({ title: '保存中...' })
        
        // 直接调用API接口更新用户信息
		
		const apiData = {
		  email: this.userInfo.email,     // 同时发送email字段
		  mobile: this.userInfo.mobile,
		  birthday: this.userInfo.birthday,
		  nickname: this.userInfo.nickname
		}
        await updateUserInfo(apiData)
        
        uni.hideLoading()
        uni.showToast({
          title: '资料保存成功',
          icon: 'success'
        })
        
        setTimeout(() => {
          uni.navigateBack()
        }, 1500)
      } catch (e) {
        uni.hideLoading()
        uni.showToast({
          title: e.message || '保存失败',
          icon: 'none'
        })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}
.profile-container {
  padding: 15px;
  width: 100%;
  box-sizing: border-box;
  max-width: 100vw;
}

/* 移除原有的自定义导航栏样式，使用uni-nav-bar组件 */

.profile-page .form-container {
  background-color: white;
  border-radius: 20rpx;
  padding: 0 30rpx;
  margin-bottom: 40rpx;
}

.profile-page .form-item {
  padding: 30rpx 0;
  border-bottom: 2rpx solid #f5f5f5;
  display: flex;
  align-items: center;
}

.profile-page .form-item:last-child {
  border-bottom: none;
}

.profile-page .form-item .label {
  width: 160rpx;
  font-size: 30rpx;
  color: #333;
}

.profile-page .form-item .input {
  flex: 1;
  font-size: 15px;
  text-align: right;
}

.profile-page .form-item .picker {
  flex: 1;
}

.profile-page .form-item .picker .picker-value {
  text-align: right;
  font-size: 15px;
  color: #333;
}

.profile-page .save-btn-container {
  padding: 0;
}

.profile-page .save-btn-container .save-btn {
  background-color: #07C160;
  color: white;
  border-radius: 20px;
  font-size: 32rpx;
}
</style>