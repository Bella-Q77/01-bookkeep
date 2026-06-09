<template>
  <view class="member-page">
    <!-- 用户信息头部 -->
    <view class="user-header">
      <view class="avatar-container" @click="navigateTo('/pages/member/avatar')">
        <image class="avatar" :src="userInfo.head_pic_url || '/static/images/header.png'"></image>
      </view>
      <view class="user-info">
        <text class="username">{{ userInfo.username }}</text>
        <text class="user-id">ID: {{ userInfo.userId }}</text>
      </view>
      <view class="setting-arrow" @click="navigateTo('/pages/member/setting/index')">
        <uni-icons type="arrowright" size="18" color="rgba(255, 255, 255, 0.8)"></uni-icons>
      </view>
    </view>
    

    
    <!-- 账本设置区 -->
    <view class="section">
      <view class="section-title">
        <text class="title-text">账本设置</text>
      </view>
      <view class="menu-list">
        <view class="menu-item" @click="navigateTo('/pages/budget/index')">
          <view class="item-left">
            <uni-icons type="list" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">预算管理</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="navigateTo('/pages/assets/index')">
          <view class="item-left">
            <uni-icons type="wallet" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">资产管理</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="navigateTo('/pages/books/index')">
          <view class="item-left">
            <uni-icons type="wallet-filled" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">账本管理</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="navigateTo('/pages/category/index')">
          <view class="item-left">
            <uni-icons type="paperplane" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">分类管理</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
      </view>
    </view>
    
    <!-- 其他功能区 -->
    <view class="section">
      <view class="section-title">
        <text class="title-text">其他功能</text>
      </view>
      <view class="menu-list">
        <view class="menu-item" @click="navigateTo('/pages/member/faq')">
          <view class="item-left">
            <uni-icons type="chat" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">意见反馈</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="navigateTo('/pages/member/export')">
          <view class="item-left">
            <uni-icons type="upload" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">数据导出</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="navigateTo('/pages/member/about')">
          <view class="item-left">
            <uni-icons type="info" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">关于我们</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
        <view class="menu-item" @click="logout">
          <view class="item-left">
            <uni-icons type="redo" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
            <text class="item-text">退出登录</text>
          </view>
          <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'

export default {
  components: {
    'uni-icons': uniIcons
  },
  data() {
    return {
      userInfo: {
        username: '未登录',
        userId: '10086',
        avatar: '/static/images/default-avatar.png'
      }
    }
  },
  onLoad() {
    this.loadUserInfo()
    // 监听头像更新事件
    this.avatarChangedHandler = (avatarUrl) => {
      if (this.userInfo) {
        this.userInfo.head_pic_url = avatarUrl;
      }
    };
    uni.$on('avatarChanged', this.avatarChangedHandler);
  },
  
  onUnload() {
    // 页面卸载时移除事件监听
    if (this.avatarChangedHandler) {
      uni.$off('avatarChanged', this.avatarChangedHandler);
    }
  },
  methods: {
    // 加载用户信息
      loadUserInfo() {
      const storedUserInfo = uni.getStorageSync('userInfo')
      if (storedUserInfo) {
        this.userInfo = {
          ...this.userInfo,
          ...storedUserInfo,
		      userId:storedUserInfo.id
        }
      }
    },
    
    navigateTo(url) {
      uni.navigateTo({
        url: url
      });
    },
    
    async logout() {
      uni.showModal({
        title: '提示',
        content: '确定要退出登录吗？',
        success: async (res) => {
          if (res.confirm) {
            try {
              uni.showLoading({ title: '退出中...' })
              // 调用退出API
              // await this.$api.user.logout()
              
              // 清除本地存储
              uni.removeStorageSync('token')
              uni.removeStorageSync('userInfo')
              
              uni.hideLoading()
              uni.showToast({
                title: '已退出登录',
                icon: 'success'
              });
              
              // 跳转到登录页
              setTimeout(() => {
                uni.reLaunch({
                  url: '/pages/member/login'
                });
              }, 1500);
            } catch (e) {
              uni.hideLoading()
              uni.showToast({
                title: '退出失败',
                icon: 'none'
              });
            }
          }
        }
      });
    }
  }
}
</script>

<style scoped>
.member-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  padding-bottom: 30px;
}

.user-header {
  background-color: #07C160;
  padding: 80px 20px 40px 20px;
  display: flex;
  align-items: center;
}

.user-header .avatar-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid rgba(255, 255, 255, 0.8);
}

.user-header .avatar-container .avatar {
  width: 100%;
  height: 100%;
}

.user-header .user-info {
  margin-left: 20px;
  color: white;
}

.user-header .user-info .username {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 5px;
  display: block;
}

.user-header .user-info .user-id {
  font-size: 14px;
  opacity: 0.8;
}

.user-header .setting-arrow {
  margin-left: auto;
  padding: 8px;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.user-header .setting-arrow:active {
  background-color: rgba(255, 255, 255, 0.2);
}

.user-header .setting-arrow {
  margin-left: auto;
  padding: 8px;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.user-header .setting-arrow:active {
  background-color: rgba(255, 255, 255, 0.2);
}

.section {
  margin: 15px;
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.section .section-title {
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
}

.section .section-title .title-text {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.section .menu-list .menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
}

.section .menu-list .menu-item:last-child {
  border-bottom: none;
}

.section .menu-list .menu-item:active {
  background-color: #f9f9f9;
}

.section .menu-list .menu-item .item-left {
  display: flex;
  align-items: center;
}

.section .menu-list .menu-item .item-left .iconfont {
  font-size: 18px;
  color: #07C160;
  margin-right: 10px;
}

.section .menu-list .menu-item .item-left .item-text {
  font-size: 15px;
  color: #333;
}

.section .menu-list .menu-item .iconfont.icon-right {
  color: #ccc;
  font-size: 14px;
}


</style>