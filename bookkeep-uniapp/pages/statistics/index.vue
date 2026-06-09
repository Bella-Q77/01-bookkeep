<template>
  <view class="statistics-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="分类统计" 
      background-color="#07C160"
      color="#FFFFFF"
      :status-bar="true" 
      :fixed="true"
    >
      <view slot="right" class="nav-right">
        <share-button 
          :shareConfig="shareConfig"
          @share="handleShare"
        />
      </view>
    </uni-nav-bar>
    
    <!-- 页面内容 -->
    <view class="statistics-container">
      <!-- 导航链接 -->
      <view class="nav-links">
        <view class="nav-item active">
          <text>分类统计</text>
        </view>
        <view class="nav-item" @click="navigateToTrend">
          <text>收支趋势</text>
        </view>
      </view>
      
      <!-- 时间筛选 -->
      <view class="filter-row">
		  <!-- 时间类型选择 -->
		  <view class="time-type-selector">
			<view 
			  class="type-item" 
			  :class="{ active: timeType === 'month' }" 
			  @click="() => changeTimeType('month')"
			>
			  月度
			</view>
			<view 
			  class="type-item" 
			  :class="{ active: timeType === 'year' }" 
			  @click="() => changeTimeType('year')"
			>
			  年度
			</view>
		  </view>
		  
		  <picker 
			mode="date" 
			:fields="timeType" 
			:value="date" 
			@change="dateChange"
		  >
			<view class="date-picker">{{displayDate}}</view>
		  </picker>
		</view>
		
		<!-- 加载状态 -->
		<view class="loading-container" v-if="loading">
		  <text class="loading-text">加载中...</text>
		</view>
		
		<!-- 统计卡片 -->
		<view class="stats-cards" v-else>
		  <view class="card">
			<view class="stats-row">
			  <view class="stat-item">
				<text class="amount income">¥ {{incomeTotal}}</text>
				<text class="label">总收入</text>
			  </view>
			  <view class="stat-item">
				<text class="amount expense">¥ {{expenseTotal}}</text>
				<text class="label">总支出</text>
			  </view>
			  <view class="stat-item">
				<text class="amount balance">¥ {{(parseFloat(incomeTotal) - parseFloat(expenseTotal)).toFixed(2)}}</text>
				<text class="label">结余</text>
			  </view>
			</view>
		  </view>
		</view>
		
		<!-- 图表 -->
		<view class="charts" v-if="!loading">
		  <view class="chart-container">
			<view class="chart-header">
			  <text class="chart-title">{{activeTab === 'income' ? '收入' : '支出'}}分类占比</text>
			  <view class="tab-buttons">
				  <view
					class="tab-btn expense" 
					:class="{ active: activeTab === 'expense' }"
					@click="() => switchTab('expense')"
				  >
					支出
				  </view>
				<view 
				  class="tab-btn income" 
				  :class="{ active: activeTab === 'income' }"
				  @click="() => switchTab('income')"
				>
				  收入
				</view>

			  </view>
			</view>
			<canvas canvas-id="pieChart" class="chart"></canvas>
			
			<!-- 分类金额列表 -->
			<view class="category-list">
			  <view 
				class="category-item" 
				v-for="(item, index) in (activeTab === 'income' ? pieChartData.income.series : pieChartData.expense.series)" 
				:key="index"
				@click="() => navigateToCategoryDetail(item)"
			  >
				<view class="category-info">
				  <view class="category-color" :style="{ backgroundColor: getCategoryColor(index) }"></view>
				  <text class="category-name">{{item.name}}</text>
				</view>
				<text class="category-amount">¥ {{item.data.toFixed(2)}}</text>
			  </view>
			</view>
		  </view>
		</view>
		
		<!-- 分类明细弹窗 -->
		<view class="category-detail-modal" v-if="showDetailModal">
		  <view class="modal-content">
			<view class="modal-header">
			  <text class="modal-title">{{selectedCategory.name}} 明细</text>
			  <text class="close-btn" @click="() => closeDetailModal()">×</text>
			</view>
			
			<view class="detail-list">
			  <view class="detail-item" v-for="(item, index) in categoryDetails" :key="index">
				<view class="detail-left">
				  <text class="detail-date">{{item.date}}</text>
				  <text class="detail-remark">{{item.remark || '无备注'}}</text>
				</view>
				<text class="detail-amount">¥ {{item.amount.toFixed(2)}}</text>
			  </view>
			  
			  <view class="empty-tip" v-if="categoryDetails.length === 0">
				暂无明细数据
			  </view>
			</view>
		  </view>
		</view>
	 </view>
  

  </view>
</template>

<script>
import uCharts from '../../static/js/u-charts.min.js'
import recordApi from '@/api/record'
// 使用 uni-app 的 easycom 自动注册组件，无需手动导入

export default {
  components: {},
  data() {
    return {
      timeType: 'month', // 'month' 或 'year'
      date: this.getCurrentMonth(),
      activeTab: 'expense', // 'income' 或 'expense'
      incomeTotal: '0.00',
      expenseTotal: '0.00',
      loading: false,
      pieChartData: {
        income: {
          series: []
        },
        expense: {
          series: []
        }
      },
      categoryColors: ['#FF6B6B', '#4ECDC4', '#FFD166', '#6A0572', '#6B76FF', '#FF9A8B', '#6BD5E1', '#FFA3FD', '#A5A5A5'],
      showDetailModal: false,
      selectedCategory: {},
      categoryDetails: [],

      currentBookId: '', // 当前账本ID
      
      // 分享相关数据
      shareConfig: {
        title: '科科记账 - 分类统计',
        desc: '查看我的收支分类统计，帮您更好地管理财务',
        path: '/pages/statistics/index',
        imageUrl: '/static/logo.png'
      }
    }
  },
  
  // 小程序分享给好友
  onShareAppMessage(res) {
    console.log('统计页面触发分享给好友', res);
    const currentBookId = this.currentBookId || '';
    
    // 确保数据有效
    const title = `科科记账 - ${this.displayDate}分类统计`;
    const income = this.incomeTotal || '0.00';
    const expense = this.expenseTotal || '0.00';
    const balance = (parseFloat(income) - parseFloat(expense)).toFixed(2);
    const desc = `总收入¥${income}，总支出¥${expense}，结余¥${balance}`;
    const path = `/pages/statistics/index?bookId=${currentBookId}`;
    
    return {
      title: title,
      desc: desc,
      path: path,
      imageUrl: '/static/logo.png'
    };
  },
  
  // 小程序分享到朋友圈
  onShareTimeline() {
    console.log('统计页面触发分享到朋友圈');
    const currentBookId = this.currentBookId || '';
    
    // 确保数据有效且标题格式正确
    const title = `${this.displayDate}财务统计 - 科科记账`;
    
    return {
      title: title,
      imageUrl: '/static/logo.png',
      query: {
        bookId: currentBookId
      }
    };
  },
  
  async onLoad(options) {
    // 优先从URL参数获取账本ID，如果没有则从Session获取默认账本ID
    this.currentBookId = options.bookId || ''
    
    // 如果没有传入账本ID，从Session获取默认账本ID
    if (!this.currentBookId) {
      try {
        const sessionBookId = uni.getStorageSync('currentBookId')
        if (sessionBookId) {
          this.currentBookId = sessionBookId
        }
      } catch (error) {
        console.error('获取Session账本ID失败:', error)
      }
    }
    
    this.loadStatisticsData()
  },
  
  onReady() {
    this.initCharts()
  },
  computed: {
    displayDate() {
      if (this.timeType === 'month') {
        return this.date; // 年-月格式
      } else {
        return this.date.split('-')[0]; // 只显示年份
      }
    }
  },
  methods: {
    // 跳转到趋势页面
    navigateToTrend() {
      uni.navigateTo({
        url: `/pages/statistics/trend?bookId=${this.currentBookId}`
      });
    },
    
    getCurrentMonth() {
      const date = new Date()
      const year = date.getFullYear()
      const month = (date.getMonth() + 1).toString().padStart(2, '0')
      return `${year}-${month}`
    },
    changeTimeType(type) {
      if (this.timeType === type) return;
      
      this.timeType = type;
      
      // 调整日期格式
      if (type === 'year') {
        this.date = this.date.split('-')[0];
      } else {
        // 如果从年切换到月，需要添加月份
        if (!this.date.includes('-')) {
          const currentMonth = (new Date().getMonth() + 1).toString().padStart(2, '0');
          this.date = `${this.date}-${currentMonth}`;
        }
      }
      
      // 更新图表数据
      this.updateChartData();
    },
    dateChange(e) {
      this.date = e.detail.value;
      // 这里应该根据新日期重新获取数据
      this.updateChartData();
    },
    updateChartData() {
      // 根据选择的时间类型和日期获取数据
      console.log(`获取${this.timeType}数据，日期: ${this.date}`);
      

      
      this.updateTabData();
    },
    
    // 切换收入/支出tab - 只更新图表数据，不更新卡片统计数据
    switchTab(tab) {
      if (this.activeTab === tab) return;
      this.activeTab = tab;
      this.updateChartDataOnly();
    },
    
    // 更新当前tab的数据
    updateTabData() {
      // 更新图表数据
      this.updateCharts();
      
      // 更新总金额显示 - 确保统计卡片数据正确刷新
      this.incomeTotal = this.pieChartData.income.series
        .reduce((sum, item) => sum + item.data, 0)
        .toFixed(2);
      this.expenseTotal = this.pieChartData.expense.series
        .reduce((sum, item) => sum + item.data, 0)
        .toFixed(2);
    },
    initCharts() {
      // 获取当前tab的数据
      const currentSeries = this.activeTab === 'income' 
        ? this.pieChartData.income.series 
        : this.pieChartData.expense.series;
      
      // 获取饼图canvas上下文
      const pieCanvasId = 'pieChart';
      const pieContext = uni.createCanvasContext(pieCanvasId, this);
      
      // 初始化饼图
      this.pieChart = new uCharts({
        $this: this,
        canvasId: pieCanvasId,
        context: pieContext,
        type: 'pie',
        fontSize: 11,
        legend: true,
        background: '#FFFFFF',
        pixelRatio: 1,
        series: currentSeries,
        animation: true,
        width: uni.upx2px(700),
        height: uni.upx2px(500),
        dataLabel: true,
        extra: {
          pie: {
            labelWidth: 15
          }
        }
      });
      

    },
    updateCharts() {
      // 根据当前tab更新图表数据
      const currentSeries = this.activeTab === 'income' 
        ? this.pieChartData.income.series 
        : this.pieChartData.expense.series;
      
      // 更新饼图数据
      this.pieChart.updateData({
        series: currentSeries,
        animation: true
      });
      

    },
    
    // 获取分类颜色
    getCategoryColor(index) {
      return this.categoryColors[index % this.categoryColors.length];
    },
    

    
    navigateToCategoryDetail(category) {
      // 跳转到账单列表页面，传递分类ID、时间类型和日期参数
      uni.navigateTo({
        url: `/pages/record/index?category_id=${category.id || ''}&period_type=${this.timeType}&period_date=${this.date}`
      });
    },
    

    
    // 关闭明细弹窗
    closeDetailModal() {
      this.showDetailModal = false;
    },
    
    // 加载统计数据
    async loadStatisticsData() {
      this.loading = true
      try {
        // 获取月度汇总数据
        const summaryParams = {
          book_id: this.currentBookId,
		  period_type:this.timeType,
          period_date: this.timeType === 'month' ? this.date : `${this.date.split('-')[0]}-01`
        }
        
        const summaryRes = await recordApi.getMonthSummary(summaryParams)
        this.incomeTotal = summaryRes.income || '0.00'
        this.expenseTotal = summaryRes.expense || '0.00'
        
        // 获取记录数据用于图表
        const recordParams = {
		  listtype:'statmonth',
          book_id: this.currentBookId,
		  period_type:this.timeType,
		  period_date: this.timeType === 'month' ? this.date : `${this.date.split('-')[0]}-01`,
          pageSize: 1000 // 获取足够的数据用于统计
        }
        
		//获得统计数据
		const listtype =this.activeTab === 'income'?'categoryincome':'categoryexpense'
        const recordsRes = await recordApi.getStatInfo(listtype,recordParams)
        this.processChartData(recordsRes.data || [])
        
        // 更新图表
        this.updateCharts()
        
      } catch (error) {
        console.error('加载统计数据失败:', error)
        uni.showToast({
          title: '加载数据失败',
          icon: 'none'
        })
      } finally {
        this.loading = false
      }
    },
    
    // 处理图表数据 - 适配API返回的数据格式
    processChartData(apiData) {
      if (!apiData || !apiData.series || !apiData.series.data) {
        console.error('API返回数据格式错误:', apiData)
        return
      }
      
      // API返回的数据格式：{name: "工资", value: 6500, ...}
      // 转换为uCharts需要的格式：{name: "工资", data: 6500}
      const seriesData = apiData.series.data.map(item => ({
        name: item.name,
        data: item.value || 0
      })).sort((a, b) => b.data - a.data)
      
      // 根据当前tab更新对应的数据
      if (this.activeTab === 'income') {
        this.pieChartData.income.series = seriesData
      } else {
        this.pieChartData.expense.series = seriesData
      }
    },
    
    // 获取分类明细数据
    async getCategoryDetailData(category) {
      try {
        const params = {
          ledgerId: this.currentBookId,
          category: category.name,
          month: this.timeType === 'month' ? this.date : undefined,
          year: this.timeType === 'year' ? this.date.split('-')[0] : undefined
        }
        
        const res = await recordApi.getRecords(params)
        this.categoryDetails = (res.records || []).map(record => ({
          date: record.transaction_date || record.create_time,
          amount: parseFloat(record.amount) || 0,
          remark: record.remark || '无备注'
        }))
        
      } catch (error) {
        console.error('获取分类明细失败:', error)
        this.categoryDetails = []
      }
    },
    
    // 更新图表数据时调用API - 日期时间或账单类型变化时调用
    updateChartData() {
      this.loadStatisticsData()
    },
    
    // 只更新图表数据，不更新卡片统计数据 - 收入/支出按钮点击时调用
    async updateChartDataOnly() {
      try {
        // 获取记录数据用于图表
        const recordParams = {
          listtype:'statmonth',
          book_id: this.currentBookId,
          period_type:this.timeType,
          period_date: this.timeType === 'month' ? this.date : `${this.date.split('-')[0]}-01`,
          pageSize: 1000
        }
        
        const listtype = this.activeTab === 'income'?'categoryincome':'categoryexpense'
        const recordsRes = await recordApi.getStatInfo(listtype, recordParams)
        this.processChartData(recordsRes.data || [])
        
        // 更新图表
        this.updateCharts()
        
      } catch (error) {
        console.error('更新图表数据失败:', error)
        uni.showToast({
          title: '更新图表数据失败',
          icon: 'none'
        })
      }
    },
    
    // 更新tab数据时调用API
    updateTabData() {
      this.loadStatisticsData()
    },
    
    // 处理分享事件
    handleShare({ updateShareData, showShare }) {
      // 更新分享数据，包含统计信息
      updateShareData({
        title: `科科记账 - ${this.displayDate}分类统计`,
        desc: `总收入¥${this.incomeTotal}，总支出¥${this.expenseTotal}，结余¥${(parseFloat(this.incomeTotal) - parseFloat(this.expenseTotal)).toFixed(2)}`,
        path: '/pages/statistics/index',
        imageUrl: '/static/logo.png'
      });
      
      // 显示分享弹窗
      showShare();
    }
  }
}
</script>

<style scoped>
.statistics-page {
  height: 100vh;
  background-color: #f5f5f5;
}
.statistics-container{
	padding: 40rpx 20rpx;
	min-height: calc(100vh - var(--window-top));
}

.statistics-container .nav-links {
  display: flex;
  background-color: #f5f5f5;
  border-radius: 40rpx;
  padding: 6rpx;
  margin-bottom: 30rpx;
}

.statistics-container .nav-links .nav-item {
  flex: 1;
  text-align: center;
  padding: 12rpx 0;
  border-radius: 36rpx;
  font-size: 28rpx;
  transition: all 0.3s;
  color: #666;
  background-color: rgba(255, 255, 255, 0.9);
}

.statistics-container .nav-links .nav-item.active {
  background-color: #07C160;
  color: white;
}

.statistics-container .nav-links .nav-item:not(.active) {
  cursor: pointer;
  background-color: rgba(255, 255, 255, 0.9);
}
.statistics-page .tab-row {
  display: flex;
  justify-content: center;
  margin-bottom: 30rpx;
}

.statistics-page .tab-row .tab-buttons {
  display: flex;
  gap: 30rpx;
}

.statistics-page .tab-row .tab-buttons .tab-btn {
  padding: 16rpx 50rpx;
  border-radius: 80rpx;
  font-size: 28rpx;
  font-weight: bold;
}

.statistics-page .tab-row .tab-buttons .tab-btn.income {
  background-color: rgba(7, 193, 96, 0.1);
  color: #07C160;
}

.statistics-page .tab-row .tab-buttons .tab-btn.income.active {
  background-color: #07C160;
  color: white;
}

.statistics-page .tab-row .tab-buttons .tab-btn.expense {
  background-color: rgba(255, 69, 0, 0.1);
  color: #FF4500;
}

.statistics-page .tab-row .tab-buttons .tab-btn.expense.active {
  background-color: #FF4500;
  color: white;
}

.statistics-page .filter-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30rpx;
}

.statistics-page .filter-row .time-type-selector {
  display: flex;
  background-color: #f5f5f5;
  border-radius: 40rpx;
  padding: 6rpx;
  width: 240rpx;
}

.statistics-page .filter-row .time-type-selector .type-item {
  flex: 1;
  text-align: center;
  padding: 6rpx 0;
  border-radius: 36rpx;
  font-size: 24rpx;
  transition: all 0.3s;
}

.statistics-page .filter-row .time-type-selector .type-item.active {
  background-color: #07C160;
  color: white;
}

.statistics-page .filter-row .date-picker {
  padding: 8rpx 32rpx;
  background-color: white;
  border-radius: 30rpx;
  color: #07C160;
  font-weight: bold;
  font-size: 28rpx;
}

.statistics-page .stats-cards {
  margin-bottom: 40rpx;
}

.statistics-page .stats-cards .card {
  padding: 30rpx;
  border-radius: 16rpx;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.08);
  color: white;
}

.statistics-page .stats-cards .card .stats-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.statistics-page .stats-cards .card .stat-item {
  flex: 1;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.statistics-page .stats-cards .card .amount {
  font-size: 32rpx;
  font-weight: bold;
  margin-bottom: 8rpx;
  line-height: 1.2;
}

.statistics-page .stats-cards .card .label {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.2;
}

.statistics-page .charts {
  background-color: white;
  border-radius: 20rpx;
  padding: 30rpx;
}

.statistics-page .charts .chart-container {
  margin-bottom: 60rpx;
  position: relative;
}



.statistics-page .charts .chart-container .chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30rpx;
}

.statistics-page .charts .chart-container .chart-header .chart-title {
  font-weight: bold;
  font-size: 32rpx;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons {
  display: flex;
  gap: 16rpx;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons .tab-btn {
  padding: 12rpx 24rpx;
  border-radius: 20rpx;
  font-size: 24rpx;
  font-weight: bold;
  transition: all 0.3s ease;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons .tab-btn.income {
  background-color: rgba(7, 193, 96, 0.1);
  color: #07C160;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons .tab-btn.income.active {
  background-color: #07C160;
  color: white;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons .tab-btn.expense {
  background-color: rgba(255, 69, 0, 0.1);
  color: #FF4500;
}

.statistics-page .charts .chart-container .chart-header .tab-buttons .tab-btn.expense.active {
  background-color: #FF4500;
  color: white;
}

.statistics-page .charts .chart-container .chart {
  width: 100%;
  height: 500rpx;
}

.statistics-page .charts .chart-container .category-list {
  margin-top: 40rpx;
  border-top: 1rpx solid #f0f0f0;
  padding-top: 30rpx;
}

.statistics-page .charts .chart-container .category-list .category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f5f5f5;
}

.statistics-page .charts .chart-container .category-list .category-item:active {
  background-color: #f9f9f9;
}

.statistics-page .charts .chart-container .category-list .category-item .category-info {
  display: flex;
  align-items: center;
}

.statistics-page .charts .chart-container .category-list .category-item .category-info .category-color {
  width: 24rpx;
  height: 24rpx;
  border-radius: 50%;
  margin-right: 16rpx;
}

.statistics-page .charts .chart-container .category-list .category-item .category-info .category-name {
  font-size: 28rpx;
}

.statistics-page .charts .chart-container .category-list .category-item .category-amount {
  font-size: 32rpx;
  font-weight: 500;
}

.statistics-page .category-detail-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.statistics-page .category-detail-modal .modal-content {
  width: 90%;
  max-height: 80%;
  background-color: white;
  border-radius: 20rpx;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.statistics-page .category-detail-modal .modal-content .modal-header {
  padding: 30rpx;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1rpx solid #f0f0f0;
}

.statistics-page .category-detail-modal .modal-content .modal-header .modal-title {
  font-size: 32rpx;
  font-weight: bold;
}

.statistics-page .category-detail-modal .modal-content .modal-header .close-btn {
  font-size: 44rpx;
  color: #999;
  padding: 0 10rpx;
}

.statistics-page .category-detail-modal .modal-content .detail-list {
  flex: 1;
  overflow-y: auto;
  padding: 0 30rpx;
}

.statistics-page .category-detail-modal .modal-content .detail-list .detail-item {
  display: flex;
  justify-content: space-between;
  padding: 30rpx 0;
  border-bottom: 1rpx solid #f5f5f5;
}

.statistics-page .category-detail-modal .modal-content .detail-list .detail-item .detail-left {
  display: flex;
  flex-direction: column;
}

.statistics-page .category-detail-modal .modal-content .detail-list .detail-item .detail-left .detail-date {
  font-size: 28rpx;
  margin-bottom: 10rpx;
}

.statistics-page .category-detail-modal .modal-content .detail-list .detail-item .detail-left .detail-remark {
  font-size: 24rpx;
  color: #999;
}

.statistics-page .category-detail-modal .modal-content .detail-list .detail-item .detail-amount {
  font-size: 32rpx;
  font-weight: 500;
}

.statistics-page .category-detail-modal .modal-content .detail-list .empty-tip {
  padding: 60rpx 0;
  text-align: center;
  color: #999;
  font-size: 28rpx;
}

/* 加载状态样式 */
.statistics-page .loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 100rpx 0;
}

.statistics-page .loading-container .loading-text {
  font-size: 32rpx;
  color: #999;
}

/* 导航栏标题居中 */
.statistics-page ::v-deep .uni-navbar__header-title {
  text-align: center !important;
  justify-content: center !important;
  display: flex !important;
  align-items: center !important;
}

.statistics-page ::v-deep .uni-navbar__content {
  justify-content: center !important;
}

/* 导航栏右侧分享按钮 */
.nav-right {
  padding-right: 15px;
}

.share-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 16px;
  background-color: rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.share-btn:active {
  background-color: rgba(255, 255, 255, 0.3);
  transform: scale(0.95);
}

.share-btn .iconfont {
  font-size: 18px;
  color: #FFFFFF;
}
</style>