<template>
  <view class="network-test">
    <view class="header">
      <text class="title">网络连接测试</text>
    </view>
    
    <view class="test-section">
      <text class="section-title">环境信息</text>
      <view class="info-item">
        <text>当前环境: {{ envInfo }}</text>
      </view>
      <view class="info-item">
        <text>API地址: {{ apiUrl }}</text>
      </view>
      <view class="info-item">
        <text>平台: {{ platform }}</text>
      </view>
    </view>
    
    <view class="test-section">
      <text class="section-title">测试接口</text>
      <button class="test-btn" @click="testLoginAPI">测试登录接口</button>
      <button class="test-btn" @click="testDirectRequest">测试直接请求</button>
    </view>
    
    <view class="test-section">
      <text class="section-title">测试结果</text>
      <view class="result-item" :class="{ success: testResult.success, error: !testResult.success }">
        <text>{{ testResult.message }}</text>
      </view>
      <view class="result-details" v-if="testResult.details">
        <text>详细信息: {{ testResult.details }}</text>
      </view>
    </view>
    
    <view class="actions">
      <button class="back-btn" @click="goBack">返回</button>
    </view>
  </view>
</template>

<script>
import { getConfig } from '@/common/utils/config.js'
import userApi from '@/api/user'

export default {
  data() {
    return {
      envInfo: '',
      apiUrl: '',
      platform: '',
      testResult: {
        success: false,
        message: '未测试',
        details: ''
      }
    }
  },
  
  onLoad() {
    this.loadEnvInfo()
  },
  
  methods: {
    loadEnvInfo() {
      // 获取环境配置
      const config = getConfig()
      this.envInfo = process.env.NODE_ENV || 'development'
      this.apiUrl = config.baseURL
      this.platform = uni.getSystemInfoSync().platform
      
      console.log('环境配置:', config)
      console.log('系统信息:', uni.getSystemInfoSync())
    },
    
    async testLoginAPI() {
      this.testResult = {
        success: false,
        message: '测试中...',
        details: ''
      }
      
      try {
        // 测试登录API
        const testData = {
          username: 'test',
          password: 'test123'
        }
        
        const result = await userApi.login(testData)
        console.log('API测试结果:', result)
        
        this.testResult = {
          success: true,
          message: 'API接口调用成功',
          details: `响应状态: ${result.code}, 消息: ${result.msg || result.message}`
        }
        
      } catch (error) {
        console.error('API测试失败:', error)
        
        this.testResult = {
          success: false,
          message: 'API接口调用失败',
          details: `错误代码: ${error.code}, 错误信息: ${error.message}`
        }
      }
    },
    
    async testDirectRequest() {
      this.testResult = {
        success: false,
        message: '直接请求测试中...',
        details: ''
      }
      
      try {
        // 测试直接网络请求
        const config = getConfig()
        const testUrl = config.baseURL.startsWith('/') 
          ? `http://localhost:8080${config.baseURL}/portalmember/api.Login/login`
          : `${config.baseURL}/portalmember/api.Login/login`
          
        const result = await new Promise((resolve, reject) => {
          uni.request({
            url: testUrl,
            method: 'POST',
            data: {
              username: 'test',
              password: 'test123'
            },
            header: {
              'content-type': 'application/json'
            },
            success: resolve,
            fail: reject
          })
        })
        
        console.log('直接请求结果:', result)
        
        this.testResult = {
          success: true,
          message: '直接网络请求成功',
          details: `HTTP状态码: ${result.statusCode}`
        }
        
      } catch (error) {
        console.error('直接请求失败:', error)
        
        this.testResult = {
          success: false,
          message: '直接网络请求失败',
          details: `错误信息: ${error.errMsg || error.message}`
        }
      }
    },
    
    goBack() {
      uni.navigateBack()
    }
  }
}
</script>

<style>
.network-test {
  padding: 30rpx;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.header {
  text-align: center;
  margin-bottom: 40rpx;
}

.title {
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
}

.test-section {
  background: white;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
  box-shadow: 0 2rpx 12rpx rgba(0,0,0,0.1);
}

.section-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 20rpx;
  display: block;
}

.info-item {
  padding: 15rpx 0;
  border-bottom: 1rpx solid #eee;
}

.info-item:last-child {
  border-bottom: none;
}

.test-btn {
  background: #007AFF;
  color: white;
  border: none;
  border-radius: 8rpx;
  padding: 20rpx;
  margin: 10rpx 0;
  font-size: 28rpx;
}

.test-btn:active {
  background: #0056CC;
}

.result-item {
  padding: 20rpx;
  border-radius: 8rpx;
  margin: 10rpx 0;
}

.result-item.success {
  background: #d4edda;
  color: #155724;
  border: 1rpx solid #c3e6cb;
}

.result-item.error {
  background: #f8d7da;
  color: #721c24;
  border: 1rpx solid #f5c6cb;
}

.result-details {
  padding: 15rpx;
  background: #f8f9fa;
  border-radius: 8rpx;
  margin-top: 10rpx;
  font-size: 24rpx;
  color: #666;
}

.actions {
  text-align: center;
}

.back-btn {
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 8rpx;
  padding: 20rpx 40rpx;
  font-size: 28rpx;
}
</style>