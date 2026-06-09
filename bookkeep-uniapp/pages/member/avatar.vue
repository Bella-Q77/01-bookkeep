<template>
  <view class="avatar-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="修改头像" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    
    <!-- 头像预览 -->
    <view class="avatar-preview">
      <image class="avatar" :src="tempAvatar || userAvatar"></image>
    </view>
    
    <!-- 操作按钮 -->
    <view class="action-buttons">
      <button class="btn upload-btn" @click="chooseImage">选择图片</button>
      <button class="btn save-btn" :disabled="!tempAvatar || loading" @click="saveAvatar">
        {{ loading ? '上传中...' : '保存头像' }}
      </button>
    </view>
    
    <!-- 裁剪工具 -->
    <view class="cropper-container" v-if="showCropper">
      <!-- 这里可以添加图片裁剪组件 -->
    </view>
  </view>
</template>

<script>
import userApi from '@/api/user'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'

export default {
  components: {
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      userAvatar: '', // 用户当前头像
      tempAvatar: '', // 临时头像路径
      showCropper: false, // 是否显示裁剪工具
      loading: false // 加载状态
    }
  },
  onLoad() {
    // 获取当前用户头像
    // 实际应用中应该从全局状态或API获取
    const userInfo = uni.getStorageSync('userInfo') || {};
    const headPicUrl = userInfo.head_pic_url;
    // 确保head_pic_url是字符串类型
    this.userAvatar = (typeof headPicUrl === 'string' && headPicUrl) ? headPicUrl : '/static/images/default-avatar.png';

    console.log('userAvatar', this.userAvatar)

  },
  methods: {
    // 返回上一页
    goBack() {
      uni.navigateBack();
    },
    
    // 选择图片
    chooseImage() {
      uni.chooseImage({
        count: 1,
        sizeType: ['compressed'],
        sourceType: ['album'],
        success: (res) => {
          // 确保tempFilePaths[0]是字符串类型
          if (res.tempFilePaths && res.tempFilePaths[0] && typeof res.tempFilePaths[0] === 'string') {
            this.tempAvatar = res.tempFilePaths[0];
            // 这里可以添加图片裁剪功能
            this.showCropper = true;
          } else {
            uni.showToast({
              title: '图片路径无效',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          uni.showToast({
            title: '选择图片失败',
            icon: 'none'
          });
        }
      });
    },
    
    // 保存头像
    async saveAvatar() {
      if (!this.tempAvatar) return;
      
      if (this.loading) return;
      this.loading = true;
      
      try {
        uni.showLoading({
          title: '上传中...'
        });
        
        // 调用真实API上传头像
        const avatarUrl = await userApi.uploadAvatar(this.tempAvatar);
        
        // 更新本地存储和全局状态
        const userInfo = uni.getStorageSync('userInfo') || {};
        userInfo.head_pic_url = avatarUrl;
        uni.setStorageSync('userInfo', userInfo);
        
        // 发送全局事件通知头像已更新
        uni.$emit('avatarChanged', avatarUrl);
        
        // 更新页面栈中的member/index页面数据
        this.updateMemberPageAvatar(avatarUrl);
        
        uni.hideLoading();
        uni.showToast({
          title: '头像设置成功',
          icon: 'success'
        });
        
        setTimeout(() => {
          uni.navigateBack();
        }, 1500);
      } catch (error) {
        uni.hideLoading();
        uni.showToast({
          title: error.message || '头像上传失败',
          icon: 'none'
        });
      } finally {
        this.loading = false;
      }
    },
    
    // 更新member/index页面的头像
    updateMemberPageAvatar(avatarUrl) {
      // 获取页面栈
      const pages = getCurrentPages();
      
      // 查找member/index页面
      for (let i = pages.length - 1; i >= 0; i--) {
        const page = pages[i];
        if (page.route === 'pages/member/index') {
          // 如果找到了member/index页面，更新其数据
          if (page.$vm && page.$vm.userInfo) {
            page.$vm.userInfo.head_pic_url = avatarUrl;
          }
          break;
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.avatar-page {
  padding-top: 0;
  padding-left: 30px;
  padding-right: 30px;
  padding-bottom: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.avatar-preview {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 40px;
  margin-top: 20px;;
  border: 2px solid #f0f0f0;
  
  .avatar {
    width: 100%;
    height: 100%;
  }
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  width: 100%;
  max-width: 400px;
  
  .btn {
    flex: 1;
    margin: 0 10px;
    border-radius: 22px;
    font-size: 16px;
	padding: 0;
    
    &.upload-btn {
      background-color: #f5f5f5;
      color: #333;
    }
    
    &.save-btn {
      background-color: #07C160;
      color: white;
      
      &[disabled] {
        opacity: 0.6;
      }
    }
  }
}

.cropper-container {
  margin-top: 20px;
  width: 100%;
  height: 300px;
  border: 1px solid #eee;
}
</style>