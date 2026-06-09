<template>
  <view class="notification-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="消息通知" 
      left-icon="left" 
      :status-bar="true" 
      @click-left="goBack"
    >
      <view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>

    <view class="notification-container">
    
      <!-- 通知设置列表 -->
      <view class="setting-list">
        <view class="setting-item">
          <text class="label">新消息通知</text>
          <switch class="switch" color="#07C160" :checked="settings.newMessage" @change="handleChange('newMessage', $event)" />
        </view>
        
        <view class="setting-item">
          <text class="label">声音提醒</text>
          <switch class="switch" color="#07C160" :checked="settings.sound" @change="handleChange('sound', $event)" />
        </view>
        
        <view class="setting-item">
          <text class="label">震动提醒</text>
          <switch class="switch" color="#07C160" :checked="settings.vibration" @change="handleChange('vibration', $event)" />
        </view>
        
        <view class="setting-item">
          <text class="label">夜间免打扰</text>
          <switch class="switch" color="#07C160" :checked="settings.nightMode" @change="handleChange('nightMode', $event)" />
        </view>
      </view>

    </view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import { getNotificationSettings, updateNotificationSettings } from '@/api/notification'

export default {
  components: {
    'uni-icons': uniIcons
  },
  data() {
    return {
      settings: {
        newMessage: true,
        sound: true,
        vibration: true,
        nightMode: false
      },
      loading: false
    }
  },
  onLoad() {
    this.loadSettings()
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    async loadSettings() {
      try {
        uni.showLoading({ title: '加载中...' })
        const res = await getNotificationSettings()
        this.settings = res.data
        uni.hideLoading()
      } catch (error) {
        uni.hideLoading()
        uni.showToast({
          title: '获取设置失败',
          icon: 'none'
        })
      }
    },
    
    async handleChange(key, event) {
      const value = event.detail.value
      this.settings[key] = value
      
      try {
        // 更新单个设置
        const updateData = { [key]: value }
        await updateNotificationSettings(updateData)
        
        uni.showToast({
          title: '设置已更新',
          icon: 'success',
          duration: 1000
        })
      } catch (error) {
        // 恢复原值
        this.settings[key] = !value
        
        uni.showToast({
          title: '设置更新失败',
          icon: 'none'
        })
      }
    }
  }
}
</script>

<style scoped>

.notification-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}

.notification-container {
  padding: 15px;
  width: 100%;
  box-sizing: border-box;
  max-width: 100vw;
}

.notification-page .header {
  display: flex;
  align-items: center;
  height: 90rpx;
  padding: 0 30rpx;
  position: relative;
  background-color: #fff;
}

.notification-page .header .back-icon {
  position: absolute;
  left: 30rpx;
  z-index: 1;
}

.notification-page .header .title {
  flex: 1;
  text-align: center;
  font-size: 36rpx;
  font-weight: bold;
}

.notification-page .setting-list {
  background-color: white;
  border-radius: 20rpx;
  padding: 0 30rpx;

}

.notification-page .setting-item {
  padding: 30rpx 0;
  border-bottom: 2rpx solid #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.notification-page .setting-item:last-child {
  border-bottom: none;
}
    
.notification-page .setting-item .label {
  font-size: 30rpx;
  color: #333;
}
</style>