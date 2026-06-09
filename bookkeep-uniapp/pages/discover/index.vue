<template>
	<view class="discover-page">
		<!-- 顶部导航栏 -->
		<uni-nav-bar 
		  title="功能发现" 
		  background-color="#07C160" 
		  color="#FFFFFF" 
		  status-bar="true"
		  fixed="true"
		>
		  <view slot="right" class="nav-right">
		    <share-button 
		      :shareConfig="shareConfig"
		      @share="handleShare"
		    />
		  </view>
		</uni-nav-bar>
		
		<view class="discover-container">
		<!-- 账单区域 -->
		<view class="bill-section">
		  <view class="section-header" @click="navToBill">
			<text class="bill-title">账单</text>
			<uni-icons type="forward" size="18" color="#999"></uni-icons>
		  </view>
		  <view class="date-selector">
			<picker mode="date" fields="month" @change="handleMonthChange">
			  <text class="date-picker">{{ currentMonth }}</text>
			</picker>
		  </view>
		  <view class="bill-stats">
			<view class="stat-row">
			  <view class="stat-item">
				<text class="stat-value income">{{ income }}</text>
				<text class="stat-label">收入</text>
			  </view>
			  <view class="stat-item">
				<text class="stat-value expense">{{ expense }}</text>
				<text class="stat-label">支出</text>
			  </view>
			  <view class="stat-item">
				<text class="stat-value">{{ balance }}</text>
				<text class="stat-label">结余</text>
			  </view>
			</view>
		  </view>
		</view>

		<!-- 预算区域 -->
		<view class="budget-section">
		  <view class="budget-header" @click="navToBudget">
			<text class="section-title">
				<text class="bill-title">预算</text>
			</text>
			<uni-icons type="forward" size="18" color="#999"></uni-icons>
		  </view>
		  <view>{{ currentMonth }}</view>
		  <view class="budget-content">
			<view class="budget-chart">
			  <canvas class="chart" canvas-id="budgetChart"></canvas>
			  <view class="chart-label">{{ remainingPercentage }}%</view>
			</view>
			
			<view class="budget-stats">
			  <view class="stat-item">
				<text class="stat-label">预算总额</text>
				<text class="stat-value">{{ totalBudget }}</text>
			  </view>
			  <view class="stat-item">
				<text class="stat-label">已支出</text>
				<text class="stat-value">{{ totalExpense }}</text>
			  </view>
			  <view class="stat-item">
				<text class="stat-label">剩余预算</text>
				<text class="stat-value" :style="{color: remainingBudget >= 0 ? '#07C160' : '#FF2D55'}">
				  {{ remainingBudget }}
				</text>
			  </view>
			</view>
		  </view>
		</view>

		<!-- 资产管理区域 -->
		<view class="assets-section" @click="navToAssets">
		  <view class="section-header">
			<text style="font-size:36rpx;font-weight:bold;color:#222222">资产概况</text>
			<uni-icons type="forward" size="18" color="#999"></uni-icons>
		  </view>
		  <view class="assets-stats">
			<view class="assets-row">
			  <view class="asset-item">
				<text class="asset-value">{{ totalAssets }}</text>
				<text class="asset-label">总资产</text>
			  </view>
			  <view class="asset-item">
				<text class="asset-value" :style="{color: totalLiabilities > 0 ? '#FF2D55' : '#333333'}">{{ totalLiabilities }}</text>
				<text class="asset-label">总负债</text>
			  </view>
			  <view class="asset-item">
				<text class="asset-value" :style="{color: netWorth >= 0 ? '#07C160' : '#FF2D55'}">{{ netWorth }}</text>
				<text class="asset-label">净资产</text>
			  </view>
			</view>
		  </view>
		</view>

		<!-- 工具区域 -->
		<view class="tools-section">
		  <view class="section-header">
			<text style="font-size:36rpx;font-weight:bold;color:#222222">常用工具</text>
		  </view>
		  <view class="tools-grid">
			<view class="tool-item" v-for="tool in tools" :key="tool.name" @click="navToTool(tool.path)">
			  <uni-icons :type="tool.icon" size="30" :color="tool.color"></uni-icons>
			  <text>{{ tool.name }}</text>
			</view>
		  </view>
		</view>
	  </view>
  </view>
  
  
</template>

<script>
import { uniIcons } from '@dcloudio/uni-ui';
import recordApi from '@/api/record';
import budgetApi from '@/api/budget';
import assetsApi from '@/api/assets';

export default {
  components: {
    uniIcons
  },
  data() {
    return {
      currentMonth: '',
      currentBookId: '', // 当前账本ID
      loading: false,
      totalBudget: 0,
      totalExpense: 0,
      remainingBudget: 0,
      remainingPercentage: 0,
      income: 0,
      expense: 0,
      balance: 0,
      totalAssets: 0,
      totalLiabilities: 0,
      netWorth: 0,
      tools: [
        { name: '房贷计算', icon: 'home', color: '#007AFF', path: '/pages/tools/mortgage' },
        { name: '汇率换算', icon: 'loop', color: '#4CD964', path: '/pages/tools/currency' },
        { name: '税费计算', icon: 'paperplane', color: '#FF9500', path: '/pages/tools/tax' },
        { name: '储蓄计划', icon: 'plus', color: '#FF2D55', path: '/pages/tools/saving' }
      ],
      
      // 分享相关配置
      shareConfig: {
        title: '科科记账 - 功能发现',
        desc: '发现更多实用功能，让记账更轻松',
        path: '/pages/discover/index',
        imageUrl: '/static/logo.png'
      }
    };
  },
  methods: {
    // 获取当前账本ID
    getCurrentBookId() {
      // 优先从页面参数获取
      // const pages = uni.getCurrentPages();
      // const currentPage = pages[pages.length - 1];
      // const options = currentPage.options;
      
      // if (options && options.bookId) {
      //   return options.bookId;
      // }
      
      // 从本地存储获取
      const bookId = uni.getStorageSync('currentBookId');
      if (bookId) {
        return bookId;
      }
      
      return '';
    },
    
    // 获取月度账单统计
    async fetchMonthSummary() {
      try {
        const bookId = this.getCurrentBookId();
        if (!bookId) {
          console.warn('未获取到账本ID，无法获取账单统计');
          return;
        }
        
        const month = this.currentMonth.replace('年', '-').replace('月', '');
        const summary = await recordApi.getMonthSummary({
          book_id: bookId,
		  period_type: 'month',
          period_date: month
        });
        
        if (summary) {
			this.income = summary?.income || '0.00';
			this.expense = summary?.expense || '0.00';
			this.balance = summary?.balance || '0.00';
        }
      } catch (error) {
        console.error('获取月度账单统计失败:', error);
      }
    },
    
    // 获取预算数据
    async fetchBudgetData() {
      try {
        const bookId = this.getCurrentBookId();
        if (!bookId) {
          console.warn('未获取到账本ID，无法获取预算数据');
          return;
        }
        
        const result = await budgetApi.getBudgetData(bookId);
        if (result && result.data) {
          const budgetData = result.data;
          this.totalBudget = budgetData.budget_amount || 0;
          this.totalExpense = budgetData.budget_used_amount || 0;
          this.remainingBudget = budgetData.budget_remain_amount || 0;
          
          // 计算剩余百分比
          if (this.totalBudget > 0) {
            this.remainingPercentage = Math.round((this.remainingBudget / this.totalBudget) * 100);
          } else {
            this.remainingPercentage = 0;
          }
        }
      } catch (error) {
        console.error('获取预算数据失败:', error);
      }
    },
    
    // 获取资产数据
    async fetchAssetsData() {
      try {
        const bookId = this.getCurrentBookId();
        if (!bookId) {
          console.warn('未获取到账本ID，无法获取资产数据');
          return;
        }
        
        const result = await assetsApi.getAssetsData(bookId);
        if (result && result.code === 1 && result.data) {
          const assetsData = result.data;
          // 根据API返回的实际数据结构映射字段
          this.totalAssets = assetsData.total_assets || 0;
          this.totalLiabilities = assetsData.negative_assets || 0;
          this.netWorth = assetsData.net_assets || 0;
        } else {
          console.warn('获取资产数据失败:', result?.msg || '未知错误');
          // 设置默认值
          this.totalAssets = 0;
          this.totalLiabilities = 0;
          this.netWorth = 0;
        }
      } catch (error) {
        console.error('获取资产数据失败:', error);
        // 设置默认值
        this.totalAssets = 0;
        this.totalLiabilities = 0;
        this.netWorth = 0;
      }
    },
    
    // 加载所有数据
    async loadAllData() {
      this.loading = true;
      try {
        await Promise.all([
          this.fetchMonthSummary(),
          this.fetchBudgetData(),
          this.fetchAssetsData()
        ]);
      } catch (error) {
        console.error('加载数据失败:', error);
      } finally {
        this.loading = false;
      }
    },
    
    navToAssets() {
      uni.navigateTo({ url: '/pages/assets/index' });
    },
    navToBudget() {
      uni.navigateTo({ url: '/pages/budget/index' });
    },
    navToBill() {
      uni.navigateTo({ url: '/pages/record/index' });
    },
    navToTool(path) {
      // 检查路径是否存在
      if (path != '') {
        uni.navigateTo({ url: path });
      } else {
        uni.showToast({
          title: '功能开发中',
          icon: 'none'
        });
      }
    },
    initCurrentMonth() {
      const now = new Date();
      this.currentMonth = `${now.getFullYear()}年${now.getMonth() + 1}月`;
    },
    handleMonthChange(e) {
      this.currentMonth = e.detail.value.replace('-', '年') + '月';
      // 月份变化时重新加载数据
      this.loadAllData();
    },
    // 处理分享事件
    handleShare({ updateShareData, showShare }) {
      // 更新分享数据，包含当前账本概况
      updateShareData({
        title: `科科记账 - ${this.currentMonth}财务概况`,
        desc: `收入¥${this.income}，支出¥${this.expense}，结余¥${this.balance}。查看完整财务分析。`,
        path: '/pages/discover/index',
        imageUrl: '/static/logo.png'
      });
      
      // 显示分享弹窗
      showShare();
    },
    
    drawBudgetChart() {
      const ctx = uni.createCanvasContext('budgetChart', this);
      const centerX = 50;
      const centerY = 50;
      const radius = 45;
      
      // 绘制背景圆
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
      ctx.setFillStyle('#F5F5F5');
      ctx.fill();
      
      // 绘制进度弧
      const endAngle = (Math.PI * 2 * this.remainingPercentage) / 100;
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, -Math.PI / 2, -Math.PI / 2 + endAngle);
      ctx.setLineWidth(8);
      ctx.setStrokeStyle(this.remainingBudget >= 0 ? '#07C160' : '#FF2D55');
      ctx.stroke();
      ctx.draw();
    }
  },
  // 小程序分享给好友
  onShareAppMessage(res) {
    console.log('发现页面触发分享给好友', res);
    const currentBookId = this.getCurrentBookId() || '';
    
    return {
      title: `科科记账 - ${this.currentMonth}财务概况`,
      desc: `收入¥${this.income}，支出¥${this.expense}，结余¥${this.balance}。发现更多实用功能。`,
      path: `/pages/discover/index${currentBookId ? '?bookId=' + currentBookId : ''}`,
      imageUrl: '/static/logo.png'
    };
  },
  
  // 小程序分享到朋友圈
  onShareTimeline() {
    console.log('发现页面触发分享到朋友圈');
    const currentBookId = this.getCurrentBookId() || '';
    
    return {
      title: `${this.currentMonth}财务概况 - 科科记账`,
      imageUrl: '/static/logo.png',
      query: currentBookId ? { bookId: currentBookId } : undefined
    };
  },
  
  onLoad() {
    this.initCurrentMonth();
    this.loadAllData();
  },
  onShow() {
    this.loadAllData();
    this.drawBudgetChart();
  }
};
</script>

<style>
.discover-page{
  padding: 0;
  background-color: #f5f5f5;
  min-height: 100vh;
}
.discover-container {
  padding: 40rpx 20rpx;
}

.discover-container .bill-section {
  background: #FFFFFF;
  border-radius: 20rpx;
  padding: 24rpx;
}

.discover-container .assets-section,
.discover-container .tools-section,
.discover-container .budget-section {
  background: #FFFFFF;
  border-radius: 20rpx;
  padding: 24rpx;
  margin-top: 20rpx; /* section之间的分隔线 */
  margin-bottom: 20rpx;
}

.discover-container .bill-section .section-header,
.discover-container .bill-section .budget-header,
.discover-container .assets-section .section-header,
.discover-container .assets-section .budget-header,
.discover-container .tools-section .section-header,
.discover-container .tools-section .budget-header,
.discover-container .budget-section .section-header,
.discover-container .budget-section .budget-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24rpx;
}

.discover-container .bill-section .section-header .bill-title,
.discover-container .bill-section .budget-header .bill-title,
.discover-container .assets-section .section-header .bill-title,
.discover-container .assets-section .budget-header .bill-title,
.discover-container .tools-section .section-header .bill-title,
.discover-container .tools-section .budget-header .bill-title,
.discover-container .budget-section .section-header .bill-title,
.discover-container .budget-section .budget-header .bill-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #222222;
  flex: 1;
}

.discover-container .bill-section .section-header .date-picker,
.discover-container .bill-section .budget-header .date-picker,
.discover-container .assets-section .section-header .date-picker,
.discover-container .assets-section .budget-header .date-picker,
.discover-container .tools-section .section-header .date-picker,
.discover-container .tools-section .budget-header .date-picker,
.discover-container .budget-section .section-header .date-picker,
.discover-container .budget-section .budget-header .date-picker {
  font-size: 28rpx;
  color: #007AFF;
  text-decoration: underline;
}

.discover-container .bill-section .date-selector,
.discover-container .assets-section .date-selector,
.discover-container .tools-section .date-selector,
.discover-container .budget-section .date-selector {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 20rpx;
  padding-left: 10rpx;
}

.discover-container .stat-row {
  display: flex;
  justify-content: space-between;
}

.discover-container .stat-row .stat-item {
  flex: 1;
  text-align: center;
  padding: 0 10rpx;
}

.discover-container .stat-row .stat-item .income {
  color: #07C160;
}

.discover-container .stat-row .stat-item .expense {
  color: #FF2D55;
}

.discover-container .stat-row .stat-item .stat-value {
  font-size: 32rpx;
  font-weight: bold;
  color: #333333;
  display: block;
  margin-bottom: 8rpx;
}

.discover-container .stat-row .stat-item .stat-label {
  font-size: 24rpx;
  color: #999999;
}

.discover-container .assets-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.discover-container .assets-row .asset-item {
  flex: 1;
  text-align: center;
  padding: 16rpx;
  background: #F7F7F7;
  border-radius: 8rpx;
  margin: 0 8rpx;
}

.discover-container .assets-row .asset-item .asset-value {
  font-size: 32rpx;
  font-weight: bold;
  color: #333333;
  display: block;
  margin-bottom: 4rpx;
}

.discover-container .assets-row .asset-item .asset-label {
  font-size: 24rpx;
  color: #999999;
}

.discover-container .tool-icon-group {
  position: relative;
  width: 30rpx;
  height: 30rpx;
}

.discover-container .tools-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20rpx;
}

.discover-container .tools-grid .tool-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.discover-container .tools-grid .tool-item text {
  margin-top: 12rpx;
  font-size: 24rpx;
  color: #666666;
}

.discover-container .budget-content {
  display: flex;
  align-items: center;
}

.discover-container .budget-content .budget-chart {
  position: relative;
  width: 200rpx;
  height: 200rpx;
  margin-right: 100rpx;
}

.discover-container .budget-content .budget-chart .chart {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

.discover-container .budget-content .budget-chart .chart-label {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 32rpx;
  font-weight: bold;
  color: #07C160;
}

.discover-container .budget-content .budget-stats {
  flex: 1;
}

.discover-container .budget-content .budget-stats .stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24rpx;
}

.discover-container .budget-content .budget-stats .stat-item .stat-label {
  font-size: 24rpx;
  color: #999999;
  text-align: left;
  flex: 1;
}

.discover-container .budget-content .budget-stats .stat-item .stat-value {
  font-size: 32rpx;
  font-weight: bold;
  color: #333333;
  text-align: right;
  flex: 1;
}

/* 导航栏右侧分享按钮 */
.nav-right {
  padding-right: 15px;
}
</style>