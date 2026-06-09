<template>
  <view class="password-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="修改密码" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    <view class="password-container">
      <!-- 密码修改表单 -->
      <view class="form-container">
        <view class="form-item">
          <text class="label">原密码</text>
          <input class="input" v-model="form.old_password" placeholder="请输入原密码" password />
        </view>
        
        <view class="form-item">
          <text class="label">新密码</text>
          <input class="input" v-model="form.new_password" placeholder="请输入新密码" password />
        </view>
        
        <view class="form-item">
          <text class="label">确认密码</text>
          <input class="input" v-model="form.confirm_password" placeholder="请再次输入新密码" password />
        </view>
      </view>
      
      <!-- 保存按钮 -->
      <view class="save-btn-container">
        <button class="save-btn" @click="changePassword" :disabled="loading">
          {{ loading ? '修改中...' : '确认修改' }}
        </button>
      </view>
    </view>
</view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'
import { changePassword } from '@/api/user'

export default {
  components: {
    'uni-icons': uniIcons,
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      form: {
        old_password: '',
        new_password: '',
        confirm_password: ''
      },
      loading: false
    }
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    async changePassword() {
      // 表单验证
      if (!this.form.old_password) {
        uni.showToast({
          title: '请输入原密码',
          icon: 'none'
        });
        return;
      }
      
      if (!this.form.new_password) {
        uni.showToast({
          title: '请输入新密码',
          icon: 'none'
        });
        return;
      }
      
      if (this.form.new_password.length < 6) {
        uni.showToast({
          title: '新密码长度不能少于6位',
          icon: 'none'
        });
        return;
      }
      
      if (this.form.new_password !== this.form.confirm_password) {
        uni.showToast({
          title: '两次输入密码不一致',
          icon: 'none'
        });
        return;
      }
      
      this.loading = true;
      uni.showLoading({
        title: '修改中...'
      });
      
      try {
        // 调用修改密码API
        await changePassword({
          old_password: this.form.old_password,
          new_password: this.form.new_password
        });
        
        uni.hideLoading();
        uni.showToast({
          title: '密码修改成功',
          icon: 'success'
        });
        
        // 清空表单
        this.form = {
          old_password: '',
          new_password: '',
          confirm_password: ''
        };
        
        // 返回上一页
        setTimeout(() => {
          uni.navigateBack();
        }, 1500);
      } catch (error) {
        uni.hideLoading();
        uni.showToast({
          title: error.message || '修改失败，请重试',
          icon: 'none'
        });
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>


.password-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}
.password-container {
  padding: 15px;
  width: 100%;
  box-sizing: border-box;
  max-width: 100vw;
}

/* 移除原有的自定义导航栏样式，使用uni-nav-bar组件 */

.password-page .form-container {
  background-color: white;
  border-radius: 20rpx;
  padding: 0 30rpx;

  margin-bottom: 40rpx;
}

.password-page .form-item {
  padding: 30rpx 0;
  border-bottom: 2rpx solid #f5f5f5;
  display: flex;
  align-items: center;
}

.password-page .form-item:last-child {
  border-bottom: none;
}
    
    .label {
      width: 160rpx;
      font-size: 30rpx;
      color: #333;
    }
    
.password-page .form-item .input {
  flex: 1;
  font-size: 15px;
  text-align: right;
}
  
.password-page .save-btn-container {
  padding: 0;
}

.password-page .save-btn-container .save-btn {
  background-color: #07C160;
  color: white;
  border-radius: 20px;
  font-size: 32rpx;
}
</style>