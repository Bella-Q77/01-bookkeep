<template>
  <view class="trend-page">
	  <!-- 顶部导航栏 -->
	  <uni-nav-bar 
	    title="收支趋势" 
	    background-color="#07C160" 
	    color="#FFFFFF" 
	    :status-bar="true"
	    :fixed="true"
	    left-icon="back"
	    @click-left="goBack"
	  ></uni-nav-bar>
	  <view class="tred-container">

		<!-- 导航链接 -->
		<view class="nav-links">
		  <view class="nav-item" @click="navigateToStats">
			<text>分类统计</text>
		  </view>
		  <view class="nav-item active">
			<text>收支趋势</text>
		  </view>
		</view>
		
		<!-- 加载状态 -->
		<view class="loading-container" v-if="loading">
		  <text class="loading-text">加载中...</text>
		</view>
		
		<!-- 顶部筛选行 -->
		<view class="filter-row" v-else>
		  <picker mode="date" fields="year" :value="year" @change="yearChange">
			<view class="date-picker">{{year}}年</view>
		  </picker>
		  
		  <view class="chart-tabs">
			<view 
			  class="tab-item income-tab" 
			  :class="{active: activeTabs.includes('income')}" 
			  @click="switchTab('income')"
			>
			  收入
			</view>
			<view 
			  class="tab-item expense-tab" 
			  :class="{active: activeTabs.includes('expense')}" 
			  @click="switchTab('expense')"
			>
			  支出
			</view>
			<view 
			  class="tab-item balance-tab" 
			  :class="{active: activeTabs.includes('balance')}" 
			  @click="switchTab('balance')"
			>
			  结余
			</view>
		  </view>
		</view>

		<!-- 图表 -->
		<view class="chart-container">
		  <canvas canvas-id="lineChart" class="chart"></canvas>
		</view>

		<!-- 月度明细列表 -->
		<view class="monthly-list">
		  <view class="list-header">
			<text class="header-item">月份</text>
			<text class="header-item">收入</text>
			<text class="header-item">支出</text>
			<text class="header-item">结余</text>
		  </view>
		  
		  <view class="list-item" v-for="(item, index) in monthlyData" :key="index">
			<text class="list-cell month">{{item.month}}月</text>
			<text class="list-cell amount income">{{item.income.toFixed(2)}}</text>
			<text class="list-cell amount expense">{{item.expense.toFixed(2)}}</text>
			<text class="list-cell amount balance">{{(item.income - item.expense).toFixed(2)}}</text>
		  </view>
		  
		  <!-- 合计行 -->
		  <view class="list-item total-row">
			<text class="list-cell month">合计</text>
			<text class="list-cell amount income">{{totalIncome.toFixed(2)}}</text>
			<text class="list-cell amount expense">{{totalExpense.toFixed(2)}}</text>
			<text class="list-cell amount balance">{{(totalIncome - totalExpense).toFixed(2)}}</text>
		  </view>
		</view>
	  </view>
  </view>
</template>

<script>
import uCharts from '../../static/js/u-charts.min.js'
import recordApi from '@/api/record'

export default {
  data() {
    return {
      year: new Date().getFullYear().toString(),
      activeTabs: ['income'], // 当前选中的tabs
      lineChart: null,
      lineChartData: {
        categories: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        series: [
          { name: '收入', data: [] },
          { name: '支出', data: [] }
        ]
      },
      monthlyData: [
        { month: 1, income: 10000, expense: 6000 },
        { month: 2, income: 12000, expense: 6500 },
        { month: 3, income: 15000, expense: 7000 },
        { month: 4, income: 11000, expense: 5500 },
        { month: 5, income: 13000, expense: 6000 },
        { month: 6, income: 14000, expense: 6500 },
        { month: 7, income: 12000, expense: 7000 },
        { month: 8, income: 15000, expense: 7500 },
        { month: 9, income: 16000, expense: 8000 },
        { month: 10, income: 14000, expense: 6500 },
        { month: 11, income: 13000, expense: 6000 },
        { month: 12, income: 18000, expense: 8500 }
      ],
      currentBookId: '', // 当前账本ID
      loading: false // 加载状态
    }
  },
  computed: {
    totalIncome() {
      return this.monthlyData.reduce((sum, item) => sum + item.income, 0);
    },
    totalExpense() {
      return this.monthlyData.reduce((sum, item) => sum + item.expense, 0);
    }
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
    
    // 加载趋势数据
    this.loadTrendData()
  },
  
  onReady() {
    this.initChartData();
    this.initChart();
  },
  methods: {
    initChartData() {
      // 从API数据生成图表数据，确保数据为Number格式
      const xaxis = this.monthlyData.map(item => `${item.month}月`)
      const incomeSeries = this.monthlyData.map(item => Number(item.income) || 0)
      const expenseSeries = this.monthlyData.map(item => Number(item.expense) || 0)
      const balanceSeries = this.monthlyData.map(item => Number(item.balance) || 0)
      
      this.lineChartData.categories = xaxis
      this.lineChartData.series = [
        { name: '收入', data: incomeSeries },
        { name: '支出', data: expenseSeries },
        { name: '结余', data: balanceSeries }
      ]
    },
    initChart() {
      const canvasId = 'lineChart';
      const context = uni.createCanvasContext(canvasId, this);
      
      this.lineChart = new uCharts({
        $this: this,
        canvasId: canvasId,
        context: context,
        type: 'line',
        fontSize: 11,
        legend: true,
        background: '#FFFFFF',
        pixelRatio: 1,
        categories: this.lineChartData.categories,
        series: this.lineChartData.series,
        animation: true,
        width: uni.upx2px(700),
        height: uni.upx2px(400),
        xAxis: {
          disableGrid: true
        },
        yAxis: {
          gridType: 'dash',
          dashLength: 2
        },
        extra: {
          lineStyle: 'curve'
        }
      });
    },
    yearChange(e) {
      this.year = e.detail.value.split('-')[0];
      // 根据年份获取新数据
      this.loadTrendData();
    },
    
    switchTab(tab) {
      const index = this.activeTabs.indexOf(tab);
      if (index === -1) {
        this.activeTabs.push(tab);
      } else {
        this.activeTabs.splice(index, 1);
      }
      
      // 至少保持一个选中状态
      if (this.activeTabs.length === 0) {
        this.activeTabs.push('income');
      }
      
      this.updateChartSeries();
    },
    
    updateChartSeries() {
      const series = [];
      
      if (this.activeTabs.includes('income')) {
        series.push({ 
          name: '收入', 
          data: this.lineChartData.series[0].data.map(item => Number(item) || 0)
        });
      }
      
      if (this.activeTabs.includes('expense')) {
        series.push({ 
          name: '支出', 
          data: this.lineChartData.series[1].data.map(item => Number(item) || 0)
        });
      }
      
      if (this.activeTabs.includes('balance')) {
        series.push({ 
          name: '结余', 
          data: this.lineChartData.series[2].data.map(item => Number(item) || 0)
        });
      }
      
      this.lineChart.updateData({
        categories: this.lineChartData.categories,
        series: series,
        animation: true
      });
    },
    goBack() {
      uni.navigateBack();
    },
    
    // 跳转到分类统计页面
    navigateToStats() {
      uni.switchTab({
        url: `/pages/statistics/index`
      });
    },
    
    // 加载趋势数据
    async loadTrendData() {
      this.loading = true
      try {
        const recordParams = {
          book_id: this.currentBookId,
          period_type: 'year',
          period_date: `${this.year}-01`
        }
        
        // 获得统计数据
        const listtype = 'incomeexpensetrend'
        const recordsRes = await recordApi.getStatInfo(listtype, recordParams)
        this.processTrendData(recordsRes.data || [])
        
      } catch (error) {
        console.error('加载趋势数据失败:', error)
        uni.showToast({
          title: '加载数据失败',
          icon: 'none'
        })
      } finally {
        this.loading = false
      }
    },
    
    // 处理趋势数据 - 适配API返回的数据格式
    processTrendData(apiData) {
      if (!apiData || !apiData.series) {
        console.error('API返回数据格式错误:', apiData)
        return
      }
      
      // 解析API返回的数据
      const xaxis = apiData.xaxis || []
      const series = apiData.series || []
      
      // 处理月度数据
      this.monthlyData = xaxis.map((month, index) => {
        const monthNum = parseInt(month.split('-')[1]) // 从 "2025-01" 中提取月份数字
        const incomeSeries = series.find(s => s.name === '收入')
        const expenseSeries = series.find(s => s.name === '支出')
        const balanceSeries = series.find(s => s.name === '结余')
        
        return {
          month: monthNum,
          income: parseFloat(incomeSeries?.data?.[index] || 0),
          expense: parseFloat(expenseSeries?.data?.[index] || 0),
          balance: parseFloat(balanceSeries?.data?.[index] || 0)
        }
      })
      
      // 更新图表数据
      this.initChartData()
      if (this.lineChart) {
        this.updateChartSeries()
      }
    },
  }
}
</script>

<style scoped>
	
.trend-page {
  height: 100vh;
  background-color: #f5f5f5;
}	
.tred-container{
	padding: 40rpx 20rpx;
	min-height: calc(100vh - var(--window-top));
}

.tred-container .nav-links {
  display: flex;
  background-color: #f5f5f5;
  border-radius: 40rpx;
  padding: 6rpx;
  margin-bottom: 30rpx;
}

.tred-container .nav-links .nav-item {
  flex: 1;
  text-align: center;
  padding: 12rpx 0;
  border-radius: 36rpx;
  font-size: 28rpx;
  transition: all 0.3s;
  color: #666;
  background-color: rgba(255, 255, 255, 0.9);
}

.tred-container .nav-links .nav-item.active {
  background-color: #07C160;
  color: white;
}

.tred-container .nav-links .nav-item:not(.active) {
  cursor: pointer;
  background-color: rgba(255, 255, 255, 0.9);
}


.trend-page .header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.trend-page .header .back-btn {
  font-size: 20px;
  margin-right: 10px;
}

.trend-page .header .title {
  font-size: 18px;
  font-weight: bold;
}



.trend-page .filter-row {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
  justify-content: space-between;
}

.trend-page .filter-row .date-picker {
  padding: 3px 18px;
  background-color: #07C160;
  color: white;
  border-radius: 15px;
  font-weight: bold;
  font-size: 12px;
  white-space: nowrap;
}

.trend-page .filter-row .chart-tabs {
  display: flex;
  gap: 6px;
}

.trend-page .filter-row .chart-tabs .tab-item {
  padding: 3px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: bold;
  white-space: nowrap;
}

.trend-page .filter-row .chart-tabs .tab-item.income-tab {
  background-color: rgba(7, 193, 96, 0.1);
  color: #07C160;
}

.trend-page .filter-row .chart-tabs .tab-item.income-tab.active {
  background-color: #07C160;
  color: white;
}

.trend-page .filter-row .chart-tabs .tab-item.expense-tab {
  background-color: rgba(255, 69, 0, 0.1);
  color: #FF4500;
}

.trend-page .filter-row .chart-tabs .tab-item.expense-tab.active {
  background-color: #FF4500;
  color: white;
}

.trend-page .filter-row .chart-tabs .tab-item.balance-tab {
  background-color: rgba(51, 51, 51, 0.1);
  color: #333;
}

.trend-page .filter-row .chart-tabs .tab-item.balance-tab.active {
  background-color: #333;
  color: white;
}

.trend-page .chart-container {
  background-color: white;
  border-radius: 10px;
  padding: 15px 0px;
  margin-bottom: 20px;
}

.trend-page .chart-container .chart {
  width: 100%;
  height: 400rpx;
}

.trend-page .monthly-list {
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
}

.trend-page .monthly-list .list-header,
.trend-page .monthly-list .list-item {
  display: flex;
  padding: 12px 15px;
  border-bottom: 1px solid #f0f0f0;
}

.trend-page .monthly-list .list-header .header-item,
.trend-page .monthly-list .list-item .list-cell {
  flex: 1;
  text-align: center;
  font-size: 14px;
}

.trend-page .monthly-list .list-header .header-item:first-child,
.trend-page .monthly-list .list-item .month {
  flex: 0.6;
  text-align: left;
  padding-left: 10px;
}

.trend-page .monthly-list .list-header .header-item:not(:first-child),
.trend-page .monthly-list .list-item .amount {
  flex: 1.2;
}

.trend-page .monthly-list .list-item.total-row {
  font-weight: bold;
  background-color: #f9f9f9;
}

.trend-page .monthly-list .list-header .header-item {
  font-weight: bold;
}

.trend-page .monthly-list .list-item .amount {
  font-weight: 500;
  font-size: 13px;
}

.trend-page .monthly-list .list-item .amount.income {
  color: #07C160;
}

.trend-page .monthly-list .list-item .amount.expense {
  color: #FF4500;
}

.trend-page .monthly-list .list-item .amount.balance {
  color: #333;
}

.trend-page .monthly-list .list-header {
  background-color: #f5f5f5;
}

/* 加载状态样式 */
.trend-page .loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 100rpx 0;
}

.trend-page .loading-container .loading-text {
  font-size: 32rpx;
  color: #999;
}
</style>