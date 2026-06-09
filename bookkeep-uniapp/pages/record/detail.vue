<template>
  <view class="detail-container">
    <!-- 顶部导航栏 -->
    <uni-nav-bar title="账单详情" left-icon="left" :status-bar="true" @click-left="navigateBack">
      <view slot="left" @click="navigateBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>
    
    <!-- 账单详情卡片 -->
    <view class="detail-card">
      <view class="amount-section" :class="record.type === 'expense' ? 'expense' : 'income'">
        <text class="amount">{{ record.amount }}</text>
        <text class="type">{{ record.category_name }}</text>
      </view>
      
      <view class="info-section">
        <view class="info-item">
          <uni-icons type="calendar" size="20" color="#666"></uni-icons>
          <text class="info-label">日期：</text>
          <text class="info-text">{{ formatDate(record.transaction_date) }}</text>
        </view>
        
        <view class="info-item">
          <uni-icons type="wallet" size="20" color="#666"></uni-icons>
          <text class="info-label">账户：</text>
          <text class="info-text">{{ record.account_name || '现金' }}</text>
        </view>
        
        <view class="info-item">
          <uni-icons type="list" size="20" color="#666"></uni-icons>
          <text class="info-label">分类：</text>
          <text class="info-text">{{ record.category_name }}</text>
        </view>
        
        <view class="info-item" v-if="record.remark">
          <uni-icons type="chat" size="20" color="#666"></uni-icons>
          <text class="info-label">备注：</text>
          <text class="info-text">{{ record.remark }}</text>
        </view>
      </view>
    </view>
    
    <!-- 操作按钮 -->
    <view class="action-bar">
      <view class="action-btn edit-btn" @click="editRecord">
        <uni-icons type="compose" size="24" color="#007AFF"></uni-icons>
        <text>修改</text>
      </view>
      
      <view class="action-btn delete-btn" @click="showDeleteConfirm">
        <uni-icons type="trash" size="24" color="#FF2D55"></uni-icons>
        <text>删除</text>
      </view>
    </view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue';
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue';
import recordApi from '@/api/record';

export default {
  components: {
    uniIcons,
    uniNavBar
  },
  data() {
    return {
      recordId: '', // 存储当前记录ID
      record: {
        id: '',
        type: '',
        amount: '',
        category_name: '',
        transaction_date: '',
        remark: '',
        account_name: ''
      }
    };
  },
  onLoad(options) {
    if (options.id) {
      this.recordId = options.id;
      this.loadRecordDetail(options.id);
    }
    // 监听记录更新事件
    uni.$on('recordUpdated', this.handleRecordUpdated);
  },
  
  onUnload() {
    // 页面卸载时移除事件监听
    uni.$off('recordUpdated', this.handleRecordUpdated);
  },
  methods: {
    async loadRecordDetail(id) {
      try {
        uni.showLoading({ title: '加载中...' });
        // 调用API获取账单详情
        const response = await recordApi.getRecordDetail(id);
        if (response.code === 1) {
          this.record = {
            ...response.data,
            type: response.data.type === 1 ? 'income' : 'expense'
          };
        } else {
          uni.showToast({ title: response.msg || '未找到该记录', icon: 'none' });
        }
      } catch (error) {
        console.error('加载详情失败:', error);
        uni.showToast({ title: '加载详情失败', icon: 'none' });
      } finally {
        uni.hideLoading();
      }
    },
    
    formatDate(dateStr) {
      if (!dateStr) return '';
      // 处理日期格式，显示为YYYY年MM月DD日
      const date = new Date(dateStr);
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      const day = date.getDate();
      return `${year}年${month}月${day}日`;
    },
    
    editRecord() {
      uni.navigateTo({
        url: `/pages/record/add?edit=true&id=${this.record.id}`
      });
    },
    
    showDeleteConfirm() {
      uni.showModal({
        title: '删除账单',
        content: '确定要删除这条账单记录吗？删除后无法恢复',
        success: (res) => {
          if (res.confirm) {
            this.deleteRecord();
          }
        }
      });
    },
    
    async deleteRecord() {
      try {
        uni.showLoading({ title: '删除中...' });
        const response = await recordApi.deleteRecord(this.record.id);
        if (response.code === 1) {
          uni.showToast({ title: '删除成功', icon: 'success' });
          setTimeout(() => {
            uni.navigateBack();
          }, 1500);
        } else {
          uni.showToast({ title: response.msg || '删除失败', icon: 'none' });
        }
      } catch (error) {
        console.error('删除失败:', error);
        uni.showToast({ title: '删除失败', icon: 'none' });
      } finally {
        uni.hideLoading();
      }
    },
    
    navigateBack() {
      uni.navigateBack();
    },
    
    // 处理记录更新事件
    handleRecordUpdated(updatedRecord) {
      // 如果更新的记录ID与当前显示的记录ID匹配，则重新加载数据
      if (updatedRecord && updatedRecord.id === this.recordId) {
        console.log('检测到记录更新，重新加载详情数据');
		this.loadRecordDetail(this.recordId);
      }
    }
  }
}
</script>

<style>
.detail-container {
  min-height: 100vh;
  background-color: #f5f5f5;
  padding-top: 0;
}

/* 调整uni-nav-bar样式 */
.detail-container ::v-deep .uni-navbar {
  width: 100%;
  margin: 0;
  padding: 0;
  border-radius: 0;
}

.detail-container ::v-deep .uni-navbar__header {
  width: 100%;
  margin: 0;
  padding: 0 30rpx;
  box-sizing: border-box;
}

.detail-container ::v-deep .uni-navbar__content {
  width: 100%;
  margin: 0;
  padding: 0;
}

.detail-container ::v-deep .uni-navbar__header-bd {
  width: 100%;
  margin: 0;
  padding: 0;
}

.detail-container ::v-deep .uni-navbar__container {
  width: 100%;
  margin: 0;
  padding: 0;
}

.detail-card {
  background-color: #fff;
  border-radius: 20rpx;
  padding: 40rpx;
  margin: 30rpx;
  margin-top: 20rpx;
  box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.08);
}

.detail-card .amount-section {
  text-align: center;
  margin-bottom: 50rpx;
  padding-bottom: 30rpx;
  border-bottom: 2rpx solid #f0f0f0;
}

.detail-card .amount-section .amount {
  font-size: 72rpx;
  font-weight: bold;
  display: block;
  margin-bottom: 16rpx;
  letter-spacing: 2rpx;
}

.detail-card .amount-section .type {
  font-size: 32rpx;
  color: #666;
  font-weight: 500;
}

.detail-card .amount-section.expense .amount {
  color: #FF2D55;
}

.detail-card .amount-section.income .amount {
  color: #07C160;
}

.detail-card .info-section .info-item {
  display: flex;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f8f8f8;
}

.detail-card .info-section .info-item:last-child {
  border-bottom: none;
}

.detail-card .info-section .info-item .info-label {
  font-size: 28rpx;
  color: #999;
  min-width: 120rpx;
  margin-left: 20rpx;
}

.detail-card .info-section .info-item .info-text {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  flex: 1;
}

.action-bar {
  display: flex;
  justify-content: space-around;
  background-color: #fff;
  border-radius: 20rpx;
  padding: 30rpx 20rpx;
  margin: 30rpx;
  box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.08);
}

.action-bar .action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx 40rpx;
  border-radius: 16rpx;
  transition: all 0.3s ease;
}

.action-bar .action-btn:active {
  background-color: #f8f8f8;
  transform: scale(0.95);
}

.action-bar .action-btn text {
  font-size: 26rpx;
  margin-top: 12rpx;
  font-weight: 500;
}

.action-bar .action-btn.edit-btn {
  color: #007AFF;
}

.action-bar .action-btn.delete-btn {
  color: #FF2D55;
}
</style>