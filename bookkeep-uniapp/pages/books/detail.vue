<template>
  <view class="book-detail-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar title="账本详情" left-icon="left" :status-bar="true" @click-left="goBack">
      <view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>

    <!-- 账本基本信息 -->
    <view class="basic-info">
      <view class="info-card">
        <view class="info-header">
          <text class="book-name">{{bookInfo.name}}</text>
          <view class="status-badge" :class="{'active': bookInfo.id == currentBookId}">
            {{bookInfo.id == currentBookId ? '当前账本' : '非当前账本'}}
          </view>
        </view>
        
        <view class="info-content">
          <view class="info-item">
            <text class="label">创建时间</text>
            <text class="value">{{formatDate(bookInfo.create_time)}}</text>
          </view>
          <view class="info-item">
            <text class="label">基准币种</text>
            <text class="value">{{bookInfo.currency || 'CNY'}}</text>
          </view>
          <view class="info-item">
            <text class="label">月起始日</text>
            <text class="value">{{bookInfo.monthStartDay || 1}}日</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 功能菜单 -->
    <view class="menu-section">
      <!-- 成员管理 -->
      <view class="menu-item" @click="goToMemberManagement">
        <view class="menu-left">
          <text class="iconfont icon-member"></text>
          <text class="menu-title">成员管理</text>
        </view>
        <uni-icons type="right" size="16" color="#999"></uni-icons>
      </view>

      <!-- 分类设置 -->
      <view class="menu-item" @click="goToCategorySetting">
        <view class="menu-left">
          <text class="iconfont icon-category"></text>
          <text class="menu-title">分类设置</text>
        </view>
        <uni-icons type="right" size="16" color="#999"></uni-icons>
      </view>

      <!-- 预算管理 -->
      <view class="menu-item" @click="goToBudgetManagement">
        <view class="menu-left">
          <text class="iconfont icon-budget"></text>
          <text class="menu-title">预算管理</text>
        </view>
        <uni-icons type="right" size="16" color="#999"></uni-icons>
      </view>

      <!-- 账本资料 -->
      <view class="menu-item" @click="editBookInfo">
        <view class="menu-left">
          <text class="iconfont icon-edit"></text>
          <text class="menu-title">账本资料</text>
        </view>
        <uni-icons type="right" size="16" color="#999"></uni-icons>
      </view>


    </view>
	
	<view class="menu-section">
		<!-- 删除账本 -->
		<view class="menu-item delete" @click="showDeleteConfirm">
		  <view class="menu-left">
		    <text class="iconfont icon-delete">️</text>
		    <text class="menu-title">删除账本</text>
		  </view>
		</view>
	</view>
	

    <!-- 切换账本按钮 -->
    <view class="action-section" v-if="bookInfo.id !== currentBookId">
      <button class="switch-btn" @click="switchToThisBook">切换为该账本</button>
    </view>

    <!-- 编辑账本表单 -->
    <view class="book-form" v-if="showForm">
      <view class="form-header">
        <text>编辑账本</text>
        <text class="close-btn" @click="closeForm">×</text>
      </view>
      
      <view class="form-content">
        <view class="form-item">
          <text class="label">账本名称</text>
          <input type="text" v-model="formData.name" placeholder="请输入账本名称" />
        </view>

        <view class="form-item">
          <text class="label">基准币种</text>
          <picker mode="selector" :range="currencyList" @change="(e) => formData.currency = currencyList[e.detail.value]">
            <view class="picker">{{formData.currency || '请选择基准币种'}}</view>
          </picker>
        </view>

        <view class="form-item">
          <text class="label">月起始日</text>
          <picker mode="selector" :range="dayList" @change="(e) => formData.monthStartDay = dayList[e.detail.value]">
            <view class="picker">{{formData.monthStartDay || '请选择账单周期起始日'}}</view>
          </picker>
        </view>
        
        <view class="form-actions">
          <button class="cancel-btn" @click="closeForm">取消</button>
          <button class="save-btn" @click="handleSaveBook" :loading="savingLoading">保存修改</button>
        </view>
      </view>
    </view>
    
    <!-- 半透明遮罩层 -->
    <view class="mask" v-if="showForm" @click="closeForm"></view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'
import booksApi from '@/api/books'

export default {
  components: {
    'uni-icons': uniIcons,
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      bookId: '',
      bookInfo: {},
      currentBookId: '',
      showForm: false,
      formData: {
        id: '',
        name: '',
        currency: 'CNY',
        monthStartDay: 1
      },
      currencyList: ['CNY', 'USD', 'EUR', 'JPY', 'HKD', 'GBP'],
      dayList: Array.from({length: 28}, (_, i) => i + 1),
      savingLoading: false
    }
  },
  onLoad(options) {
    this.bookId = options.id
    this.currentBookId = uni.getStorageSync('currentBookId') || ''

	console.log('当前账本');
	console.log(this.currentBookId);
    this.loadBookDetail()
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    // 加载账本详情
    async loadBookDetail() {
      try {
        const res = await booksApi.getBookDetail(this.bookId)
        this.bookInfo = res.data || {}
      } catch (error) {
        uni.showToast({
          title: '加载账本详情失败',
          icon: 'none'
        })
      }
    },
    
    // 跳转到成员管理
    goToMemberManagement() {
      uni.navigateTo({
        url: `/pages/member/index?bookId=${this.bookId}`
      })
    },
    
    // 跳转到分类设置
    goToCategorySetting() {
      uni.navigateTo({
        url: `/pages/category/index?bookId=${this.bookId}`
      })
    },
    
    // 跳转到预算管理
    goToBudgetManagement() {
      uni.navigateTo({
        url: `/pages/budget/index?bookId=${this.bookId}`
      })
    },
    
    // 编辑账本信息
    editBookInfo() {
      this.showForm = true
      this.formData = {
        id: this.bookInfo.id,
        name: this.bookInfo.name,
        currency: this.bookInfo.currency || 'CNY',
        monthStartDay: this.bookInfo.monthStartDay || 1
      }
    },
    
    // 关闭表单
    closeForm() {
      this.showForm = false
      this.formData = {
        id: '',
        name: '',
        currency: 'CNY',
        monthStartDay: 1
      }
    },
    
    // 保存账本修改
    async handleSaveBook() {
      if (!this.formData.name) {
        uni.showToast({
          title: '请输入账本名称',
          icon: 'none'
        })
        return
      }
      
      this.savingLoading = true
      try {
        const res = await booksApi.updateBook(this.formData.id, {
		  id:this.formData.id,
          name: this.formData.name,
          currency: this.formData.currency,
          monthStartDay: this.formData.monthStartDay
        })
        
        // 更新账本信息
        this.bookInfo.name = this.formData.name
        this.bookInfo.currency = this.formData.currency
        this.bookInfo.monthStartDay = this.formData.monthStartDay
        
        this.closeForm()
        uni.showToast({
          title: '修改成功',
          icon: 'success'
        })
        
        // 触发账本更新事件，通知列表页面更新数据
        uni.$emit('bookUpdated', this.bookInfo)
      } catch (error) {
        uni.showToast({
          title: '修改失败',
          icon: 'none'
        })
      } finally {
        this.savingLoading = false
      }
    },
    
    // 显示删除确认
    showDeleteConfirm() {
      uni.showModal({
        title: '确认删除',
        content: `确定要删除账本"${this.bookInfo.name}"吗？此操作不可恢复，所有相关数据将被删除。`,
        success: async (res) => {
          if (res.confirm) {
            await this.deleteBook()
          }
        }
      })
    },
    
    // 删除账本
    async deleteBook() {
      try {
        await booksApi.deleteBook(this.bookId)
        uni.showToast({
          title: '删除成功',
          icon: 'success'
        })
        
        // 如果删除的是当前账本，清除当前账本设置
        if (this.bookId == this.currentBookId) {
          uni.removeStorageSync('currentBookId')
        }
        
        // 返回账本列表
        setTimeout(() => {
          uni.navigateBack()
        }, 1500)
      } catch (error) {
        uni.showToast({
          title: '删除失败',
          icon: 'none'
        })
      }
    },
    
    // 切换为该账本
    async switchToThisBook() {
      try {
        await booksApi.switchBook(this.bookId)
		
        uni.setStorageSync('currentBookId', this.bookId)
		
        this.currentBookId = this.bookId
        
        uni.showToast({
          title: `已切换到账本: ${this.bookInfo.name}`,
          icon: 'success'
        })
        
        // 通知其他页面账本已切换
        uni.$emit('bookSelected', this.bookInfo)
      } catch (error) {
        uni.showToast({
          title: '切换失败',
          icon: 'none'
        })
      }
    },
    
    // 格式化日期
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString()
    }
  }
}
</script>
<style>
/* 引入iconfont图标库 */
@import '@/static/iconfont/iconfont.css';
</style>
<style lang="scss" scoped>

.book-detail-page {
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
  background-color: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 20px;
  
  .basic-info {
    padding: 15px;
    box-sizing: border-box;
    
    .info-card {
      width: 100%;
      box-sizing: border-box;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      
      .info-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        
        .book-name {
          font-size: 20px;
          font-weight: bold;
          color: white;
        }
        
        .status-badge {
          background: rgba(255, 255, 255, 0.2);
          color: white;
          padding: 4px 8px;
          border-radius: 12px;
          font-size: 12px;
          
          &.active {
            background: rgba(7, 193, 96, 0.8);
          }
        }
      }
      
      .info-content {
        .info-item {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 8px;
          
          .label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
          }
          
          .value {
            color: white;
            font-size: 14px;
          }
        }
      }
    }
  }
  
  .menu-section {
    box-sizing: border-box;
    background: white;
    margin: 15px;
    border-radius: 12px;
    overflow: hidden;
    
    .menu-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: all 0.2s ease;
      
      &:last-child {
        border-bottom: none;
      }
      
      &:hover {
        background: #f8f9fa;
      }
      
      &.delete {
        .menu-title {
          color: #ff3b30;
        }
        
        &:hover {
          background: #ffeaea;
        }
      }
      
      .menu-left {
        display: flex;
        align-items: center;
        
        .iconfont {
          margin-right: 12px;
          font-size: 18px;
        }
        
        .menu-title {
          font-size: 16px;
          color: #333;
        }
      }
    }
  }
  
  .action-section {
    width: 100%;
    box-sizing: border-box;
    padding: 0 15px;
    margin-top: 20px;
    
    .switch-btn {
      width: 100%;
      background: linear-gradient(135deg, #07C160 0%, #05a854 100%);
      color: white;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      box-shadow: 0 2px 8px rgba(7, 193, 96, 0.3);
    }
  }
  
  .book-form {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100vw;
    box-sizing: border-box;
    background-color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    z-index: 100;
    max-height: 80vh;
    overflow-y: auto;
    
    .form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
      
      text {
        font-size: 18px;
        font-weight: bold;
      }
      
      .close-btn {
        font-size: 24px;
        color: #999;
        cursor: pointer;
      }
    }
    
    .form-content {
      .form-item {
        margin-bottom: 20px;
        
        .label {
          display: block;
          margin-bottom: 8px;
          color: #666;
          font-size: 14px;
        }
        
        input, .picker {
          
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 8px;
          font-size: 14px;
          
          &:focus {
            border-color: #07C160;
            outline: none;
          }
        }
      }
      
      .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        
        .cancel-btn {
          flex: 1;
          background: #f5f5f5;
          color: #666;
          border: none;
          border-radius: 25px;
        }
        
        .save-btn {
          flex: 1;
          background: #07C160;
          color: white;
          border: none;
          border-radius: 25px;
        }
      }
    }
  }
  
  .mask {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 99;
  }
}

/* 修复uni-nav-bar宽度问题 */
::v-deep .uni-nav-bar {
  width: 100vw !important;
  box-sizing: border-box !important;
  overflow: hidden !important;
}


</style>