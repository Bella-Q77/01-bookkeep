<template>
  <view class="asset-detail-container">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="资产管理" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    
    <view class="asset-detail">
      <!-- 账户信息与统计 -->
      <view class="asset-header">
        
        <!-- 编辑模式弹窗 -->
        <view class="edit-modal" v-if="isEditing">
          <view class="modal-mask" @click="cancelEdit"></view>
          <view class="modal-content">
            <view class="modal-header">
              <text class="modal-title">编辑账户</text>
              <view class="close-btn" @click="cancelEdit">×</view>
            </view>
            
            <view class="edit-form">
              <view class="form-item">
                <text class="label">账户名称</text>
                <input class="input" v-model="editForm.name" placeholder="请输入账户名称" />
              </view>
              <view class="form-item">
                <text class="label">账户类型</text>
                <picker 
                  class="picker" 
                  @change="onTypeChange" 
                  :value="editForm.type" 
                  :range="assetTypes" 
                  range-key="name"
                >
                  <view class="picker-value">{{editForm.type || '请选择账户类型'}}</view>
                </picker>
              </view>
              <view class="form-item">
                <text class="label">账户余额</text>
                <input class="input" type="number" v-model="editForm.amount" placeholder="请输入账户余额" />
              </view>
              <view class="edit-actions">
                <button class="cancel-btn" @click="cancelEdit">取消</button>
                <button class="save-btn" @click="saveEdit">保存</button>
              </view>
            </view>
          </view>
        </view>
        
        <!-- 查看模式 -->
        <view v-else class="asset-card">
          <!-- 账户基本信息 -->
          <view class="asset-basic">
            <text class="icon">{{asset.icon}}</text>
            <view class="name-type-container">
              <text class="name">{{asset.name}}</text>
              <text class="type">{{asset.type}}</text>
            </view>
            <view class="action-trigger" @click="showActionSheet = true">
              <text class="more-icon">⋮</text>
            </view>
          </view>
          
          <!-- 账户余额 -->
          <view class="amount-section">
            <text class="amount">¥ {{formatAmount(asset.amount)}}</text>
          </view>
          
          <!-- 流入流出统计 -->
          <view class="flow-stats">
            <view class="stat-item">
              <text class="label">总流入</text>
              <text class="value income">+¥ {{formatAmount(accountStat ? accountStat.total_inflow : 0)}}</text>
            </view>
            <view class="stat-item">
              <text class="label">总流出</text>
              <text class="value expense">-¥ {{formatAmount(accountStat ? accountStat.total_outflow : 0)}}</text>
            </view>
          </view>
        </view>
        
        <!-- 操作弹窗 -->
        <view class="action-sheet" v-if="showActionSheet">
          <view class="action-mask" @click="showActionSheet = false"></view>
          <view class="action-content">
            <view class="action-item edit-action" @click="handleEdit">
              <text class="action-icon">✏️</text>
              <text class="action-text">编辑账户</text>
            </view>
            <view class="action-item delete-action" @click="handleDelete">
              <text class="action-icon">🗑️</text>
              <text class="action-text">删除账户</text>
            </view>
            <view class="action-cancel" @click="showActionSheet = false">
              <text>取消</text>
            </view>
          </view>
        </view>

      </view>

      <!-- 资产明细列表 -->
      <view class="asset-detail-list">
        <!-- 日期筛选区域 -->
        <view class="date-filter">
          
          <!-- 第一行：标题和日期选择器 -->
          <view class="filter-header">
            <text class="title">资产明细</text>
            <view class="date-controls">
              <view class="date-picker" @click="showDatePicker = !showDatePicker">
                <view class="picker-content">
                  <text class="date-value">{{selectedDate || currentDate}}</text>
                  <text class="dropdown-icon">▼</text>
                </view>
              </view>
            </view>
          </view>
          
          <!-- 第二行：统计信息 -->
          <view class="stats-info">
            <text class="count">共{{total}}条</text>
            <view class="flow-stats">
              <text class="flow-income">流入：+¥ {{formatAmount(flowStat ? flowStat.total_inflow : 0)}}</text>
              <text class="flow-expense">流出：-¥ {{formatAmount(flowStat ? flowStat.total_outflow : 0)}}</text>
            </view>
          </view>
        </view>
        
        <!-- 日期选择器弹层 -->
        <view class="date-picker-popup" v-if="showDatePicker">
          <view class="popup-mask" @click="showDatePicker = false"></view>
          <view class="popup-content">
            <view class="popup-header">
              <text class="popup-title">选择日期</text>
              <text class="close-btn" @click="showDatePicker = false">×</text>
            </view>
            
            <!-- 按年/按月切换 -->
            <view class="mode-switch">
              <view 
                class="mode-btn" 
                :class="{ active: dateMode === 'month' }"
                @click="dateMode = 'month'"
              >
                <text>按月</text>
              </view>
              <view 
                class="mode-btn" 
                :class="{ active: dateMode === 'year' }"
                @click="dateMode = 'year'"
              >
                <text>按年</text>
              </view>
            </view>
            
            <!-- 年份切换 -->
            <view class="year-selector" v-if="dateMode === 'month'">
              <view class="year-btn prev-year" @click="changeYear(-1)">
                <text>‹</text>
              </view>
              <text class="current-year">{{ currentYear }}</text>
              <view class="year-btn next-year" @click="changeYear(1)">
                <text>›</text>
              </view>
            </view>
            
            <!-- 月份网格 -->
            <view class="month-grid" v-if="dateMode === 'month'">
              <view 
                class="month-item" 
                v-for="month in monthList" 
                :key="month.value"
                :class="{ active: isCurrentMonth(month.value) }"
                @click="selectMonth(month.value)"
              >
                <text>{{ month.label }}</text>
              </view>
            </view>
            
            <!-- 年份网格 -->
            <view class="year-grid" v-if="dateMode === 'year'">
              <view 
                class="year-item" 
                v-for="year in yearRange" 
                :key="year"
                :class="{ active: isCurrentYear(year) }"
                @click="selectYear(year)"
              >
                <text>{{ year }}</text>
              </view>
            </view>
          </view>
        </view>
        
        <view class="list-content">
          <view 
            class="detail-item" 
            v-for="(item, index) in filteredList" 
            :key="index"
          >
            <view class="item-left">
              <text class="type-icon">{{item.type === 'income' ? '💰' : '💸'}}</text>
              <view class="item-info">
                <text class="category">{{item.category || '未分类'}}</text>
                <text class="date">{{formatDate(item.date)}}</text>
              </view>
            </view>
            <view class="item-right">
              <text class="amount" :class="{ income: item.type === 'income' }">
                {{item.type === 'income' ? '+' : '-'}}¥ {{formatAmount(item.amount)}}
              </text>
              <text class="remark">{{item.remark || ''}}</text>
            </view>
          </view>
          
          <!-- 空状态 -->
          <view v-if="detailList.length === 0 && !isLoading" class="empty-state">
            <text>暂无明细记录</text>
          </view>
          
          <!-- 加载更多提示 -->
          <view v-if="detailList.length > 0" class="load-more-container">
            <view v-if="isLoading" class="loading-text">
              <text>加载中...</text>
            </view>
            <view v-else-if="hasMore" class="load-more-text">
              <text>上拉加载更多</text>
            </view>
            <view v-else class="no-more-text">
              <text>已加载全部数据</text>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import assetsApi from '@/api/assets'

export default {
  data() {
    return {
      asset: {
        id: '',
        name: '',
        type: '',
        amount: 0,
        icon: ''
      },
      isEditing: false,   // 是否处于编辑模式
      editForm: {
        name: '',
        type: '',
        amount: 0
      },
      assetTypes: [],     // 资产类型列表
      detailList: [],     // 资产明细列表
      accountStat: null,   // 账户统计信息
      selectedDate: null,   // 选中的日期（月份筛选）
      currentDate: '',      // 当前年月
	  flowStat:'',//资产明细列表流入
      // 日期选择器状态
      showDatePicker: false,
      currentYear: new Date().getFullYear(),
      dateMode: 'month', // 'month' or 'year'
      periodType: 'month', // 'month' or 'year'
      // 分页相关
      currentPage: 1,      // 当前页码
      perPage: 15,         // 每页数量
      total: 0,           // 总记录数
      lastPage: 1,        // 总页数
      isLoading: false,    // 是否正在加载
      hasMore: true,       // 是否还有更多数据
      // 操作弹窗
      showActionSheet: false  // 是否显示操作弹窗
    }
  },
  
  async onLoad(options) {
    // 从路由参数获取资产ID
    const assetId = options.id
    
    // 设置当前日期
    this.setCurrentDate()
    
    // 加载资产类型列表
    await this.loadAssetTypes()
    
    // 加载资产详情
    await this.loadAssetDetail(assetId)
    
    // 加载资产流水
    await this.loadAssetFlows(assetId)
  },
  
  computed: {
    // 使用API返回的统计信息
    incomeTotal() {
      return this.accountStat ? this.accountStat.total_inflow : 0
    },
    
    // 使用API返回的统计信息
    expenseTotal() {
      return this.accountStat ? this.accountStat.total_outflow : 0
    },
    
    // 筛选后的明细列表
    filteredList() {
      if (!this.selectedDate) {
        return this.detailList
      }
      
      return this.detailList.filter(item => {
        const itemDate = new Date(item.date)
        
        // 如果selectedDate是年份格式（比如"2024"），则只按年份筛选
        if (this.periodType === 'year' && this.selectedDate.match(/^\d{4}$/)) {
          return itemDate.getFullYear() === parseInt(this.selectedDate)
        }
        
        // 如果selectedDate是年月格式（比如"2024-06"），则按年月筛选
        const selectedDate = new Date(this.selectedDate)
        return itemDate.getFullYear() === selectedDate.getFullYear() && 
               itemDate.getMonth() === selectedDate.getMonth()
      })
    },
    


    
    // 年份范围（当前年份前后5年）
    yearRange() {
      const currentYear = this.currentYear;
      const years = [];
      for (let i = currentYear - 5; i <= currentYear + 5; i++) {
        years.push(i);
      }
      return years;
    },
    
    // 月份列表（1-12月）
    monthList() {
      const months = [];
      for (let i = 1; i <= 12; i++) {
        months.push({
          value: i,
          label: `${i}月`
        });
      }
      return months;
    }
  },
  
  methods: {
    // 加载资产类型列表
    async loadAssetTypes() {
      try {
        const res = await assetsApi.getAssetsType()
        if (res.code === 1 || res.code === 200) {
          this.assetTypes = res.data || []
        } else {
          throw new Error(res.message || res.msg || '获取资产类型失败')
        }
      } catch (error) {
        console.error('加载资产类型失败:', error)
        // 设置默认资产类型
        this.assetTypes = [
          { name: '现金', value: '现金' },
          { name: '银行卡', value: '银行卡' },
          { name: '支付宝', value: '支付宝' },
          { name: '微信钱包', value: '微信钱包' },
          { name: '投资账户', value: '投资账户' },
          { name: '其他', value: '其他' }
        ]
      }
    },
    
    // 加载资产详情
    async loadAssetDetail(assetId) {
      try {
        const res = await assetsApi.getAssetDetail(assetId)
        if (res.code === 1 || res.code === 200) {
          const data = res.data || {}
          const accountInfo = data.account_info || {}
          
          this.asset = {
            id: accountInfo.id || assetId,
            name: accountInfo.name || '未知资产',
            type: this.getAssetTypeName(accountInfo.account_type),
            amount: parseFloat(accountInfo.balance) || 0,
            icon: this.getAssetIcon(this.getAssetTypeName(accountInfo.account_type))
          }
          
          // 保存账户统计信息
          this.accountStat = data.account_stat || null
        } else {
          throw new Error(res.message || res.msg || '获取资产详情失败')
        }
      } catch (error) {
        console.error('加载资产详情失败:', error)
        uni.showToast({
          title: error.message || '加载资产详情失败',
          icon: 'none'
        })
        // 设置默认资产信息
        this.asset = {
          id: assetId,
          name: '资产详情',
          type: '其他',
          amount: 0,
          icon: this.getAssetIcon('其他')
        }
      }
    },
    
    formatAmount(amount) {
      // 格式化金额，保留两位小数
      const num = parseFloat(amount) || 0
      return num.toFixed(2)
    },
    
    formatDate(dateString) {
      // 将 YYYY-MM-DD 格式转换为 MM/DD
      const parts = dateString.split('-')
      if (parts.length === 3) {
        return `${parts[1]}/${parts[2]}`
      }
      return dateString
    },
    
    // 加载资产流水
    async loadAssetFlows(assetId, periodType = null, periodDate = null, page = 1, isLoadMore = false) {
      if (this.isLoading) return
      
      try {
        this.isLoading = true
        
        // 设置默认的日期筛选参数
        const defaultPeriodType = this.periodType || 'month'
        const defaultPeriodDate = this.selectedDate || this.currentDate
        
        const params = {
          pageNum: page,
          pageSize: this.perPage,
          period_type: periodType || defaultPeriodType,
          period_date: periodDate || defaultPeriodDate
        }
        
        console.log('loadAssetFlows - 请求参数:', {
          assetId,
          params,
          isLoadMore
        });
        
        const res = await assetsApi.getAssetFlows(assetId, params)
        if (res.code === 1 || res.code === 200) {
          const data = res.data || {}
          const flowList = data.flow_list || []
          
          console.log('loadAssetFlows - API响应:', {
            code: res.code,
            data: data,
            flowListLength: flowList.length
          });
          
          const newList = flowList.map(item => {
            const amount = parseFloat(item.amount) || 0
            const isIncome = amount >= 0
            return {
              id: item.id,
              type: isIncome ? 'income' : 'expense',
              category: item.category_name || '未分类',
              amount: Math.abs(amount),
              date: item.transaction_date,
              remark: item.remark || '',
              flowType: item.flow_type
            }
          })
          
          if (isLoadMore) {
            // 加载更多，追加数据
            this.detailList = [...this.detailList, ...newList]
            console.log('loadAssetFlows - 加载更多后，总数:', this.detailList.length)
          } else {
            // 首次加载或刷新，替换数据
            this.detailList = newList
            console.log('loadAssetFlows - 替换数据后，总数:', this.detailList.length)
          }
          
          // 更新分页信息
          this.total = data.total || 0
          this.currentPage = data.current_page || page
          this.lastPage = data.last_page || 1
          this.perPage = parseInt(data.per_page) || this.perPage
          this.hasMore = this.currentPage < this.lastPage
          
          // 更新账户统计信息
          if (data.flow_stat) {
            this.flowStat = data.flow_stat
          }
        } else {
          throw new Error(res.message || res.msg || '获取资产流水失败')
        }
      } catch (error) {
        console.error('加载资产流水失败:', error)
        uni.showToast({
          title: error.message || '加载资产流水失败',
          icon: 'none'
        })
        if (!isLoadMore) {
          this.detailList = []
        }
      } finally {
        this.isLoading = false
      }
    },
    
    // 根据账户类型ID获取类型名称
    getAssetTypeName(typeId) {
      const typeMap = {
        1: '现金',
        2: '银行卡',
        3: '支付宝',
        4: '微信钱包',
        5: '投资账户',
        6: '其他'
      }
      return typeMap[typeId] || '其他'
    },
    
    getAssetIcon(type) {
      const icons = {
        '现金': '💰',
        '银行卡': '💳',
        '支付宝': '📱',
        '微信钱包': '💬',
        '投资账户': '📈',
        '其他': '🏦'
      }
      return icons[type] || '🏦'
    },
    
    // 编辑资产
    editAsset() {
      // 进入编辑模式
      this.isEditing = true
      this.editForm = {
        name: this.asset.name,
        type: this.asset.type,
        amount: this.asset.amount
      }
    },
    
    // 保存编辑
    async saveEdit() {
      try {
        if (!this.editForm.name.trim()) {
          uni.showToast({
            title: '请输入账户名称',
            icon: 'none'
          })
          return
        }
        
        if (!this.editForm.type.trim()) {
          uni.showToast({
            title: '请选择账户类型',
            icon: 'none'
          })
          return
        }
        
        uni.showLoading({
          title: '保存中...'
        })
        
        // 调用修改API
        const res = await assetsApi.updateAsset({
          id: this.asset.id,
          name: this.editForm.name,
          type: this.editForm.type,
          amount: parseFloat(this.editForm.amount) || 0
        })
        
        if (res.code === 1 || res.code === 200) {
          uni.showToast({
            title: '修改成功',
            icon: 'success'
          })
          
          // 更新本地数据
          this.asset.name = this.editForm.name
          this.asset.type = this.editForm.type
          this.asset.amount = parseFloat(this.editForm.amount) || 0
          this.asset.icon = this.getAssetIcon(this.editForm.type)
          
          // 退出编辑模式
          this.isEditing = false
        } else {
          throw new Error(res.message || res.msg || '修改失败')
        }
      } catch (error) {
        console.error('修改资产失败:', error)
        uni.showToast({
          title: error.message || '修改失败',
          icon: 'none'
        })
      } finally {
        uni.hideLoading()
      }
    },
    
    // 取消编辑
    cancelEdit() {
      this.isEditing = false
    },
    
    // 类型选择变化
    onTypeChange(e) {
      const index = e.detail.value
      if (this.assetTypes[index]) {
        this.editForm.type = this.assetTypes[index].name
      }
    },
    
    // 删除资产
    async deleteAsset() {
      try {
        // 确认删除
        const result = await new Promise((resolve) => {
          uni.showModal({
            title: '确认删除',
            content: `确定要删除资产账户"${this.asset.name}"吗？此操作不可恢复。`,
            confirmColor: '#FF4D4F',
            success: resolve
          })
        })
        
        if (result.confirm) {
          uni.showLoading({
            title: '删除中...'
          })
          
          // 调用删除API
          const res = await assetsApi.deleteAsset({
            id: this.asset.id
          })
          
          if (res.code === 1 || res.code === 200) {
            uni.showToast({
              title: '删除成功',
              icon: 'success'
            })
            
            // 返回上一页
            setTimeout(() => {
              uni.navigateBack()
            }, 1500)
          } else {
            throw new Error(res.message || res.msg || '删除失败')
          }
        }
      } catch (error) {
        console.error('删除资产失败:', error)
        uni.showToast({
          title: error.message || '删除失败',
          icon: 'none'
        })
      } finally {
        uni.hideLoading()
      }
    },
    
    // 设置当前日期
    setCurrentDate() {
      const now = new Date()
      this.currentDate = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
    },
    
    // 日期选择变化
    onDateChange(e) {
      this.selectedDate = e.detail.value
    },
    
    // 重置筛选
    resetFilter() {
      this.selectedDate = null
    },
    
    // 返回上一页
    goBack() {
      uni.navigateBack()
    },
    
    // 加载更多数据
    async loadMore() {
      if (!this.hasMore || this.isLoading) return
      
      const nextPage = this.currentPage + 1
      await this.loadAssetFlows(this.asset.id, this.periodType, this.selectedDate, nextPage, true)
    },
    
    // 下拉刷新
    async onPullDownRefresh() {
      await this.loadAssetFlows(this.asset.id, this.periodType, this.selectedDate, 1, false)
      uni.stopPullDownRefresh()
    },
    
    // 上拉加载更多
    onReachBottom() {
      this.loadMore()
    },
    
    // 处理编辑
    handleEdit() {
      this.showActionSheet = false
      // 跳转到编辑页面
      uni.navigateTo({
        url: `/pages/assets/edit?id=${this.asset.id}`
      })
    },
    
    // 处理删除
    handleDelete() {
      this.showActionSheet = false
      this.deleteAsset()
    },
    
    // 年份切换
    changeYear(delta) {
      this.currentYear += delta;
    },
    
    // 检查是否为当前选中月份
    isCurrentMonth(month) {
      const [currentYear, currentMonth] = (this.selectedDate || this.currentDate).split('-');
      return parseInt(currentYear) === this.currentYear && parseInt(currentMonth) === month;
    },
    
    // 选择月份
    selectMonth(month) {
      const monthStr = month.toString().padStart(2, '0');
      this.selectedDate = `${this.currentYear}-${monthStr}`;
      this.periodType = 'month';
      this.showDatePicker = false;
      
      // 调用资产流水接口，传递月份参数
      this.loadAssetFlows(this.asset.id, 'month', `${this.currentYear}-${monthStr}`);
    },
    
    // 检查是否为当前选中年份
    isCurrentYear(year) {
      const [currentYear] = (this.selectedDate || this.currentDate).split('-');
      return parseInt(currentYear) === year;
    },
    
    // 选择年份
    selectYear(year) {
      this.currentYear = year;
      this.selectedDate = `${year}`;
      this.periodType = 'year';
      this.showDatePicker = false;
      console.log('selectYear - 加载年份流水:', { 
        assetId: this.asset.id, 
        periodType: 'year', 
        year: `${this.currentYear}` 
      });
      // 调用资产流水接口，传递年份参数
      this.loadAssetFlows(this.asset.id, 'year', `${this.currentYear}`);
    }
  }
}
</script>

<style lang="scss" scoped>
.asset-detail-container {
  min-height: 100vh;
  background-color: #f5f5f5;

  .asset-detail {
    padding: 20rpx;
    .asset-header {
      background-color: white;
      border-radius: 16rpx;
      padding: 30rpx;
      margin-bottom: 20rpx;
      box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.05);

      .asset-basic {
        display: flex;
        align-items: center;
        padding-bottom: 20rpx;
        border-bottom: 1rpx solid #f8f8f8;

        .icon {
          font-size: 56rpx;
          margin-right: 24rpx;
        }

        .name-type-container {
          flex: 1;
          display: flex;
          flex-direction: column;
          
          .name {
            font-size: 34rpx;
            font-weight: 600;
            margin-bottom: 6rpx;
            color: #333;
          }
          
          .type {
            color: #999;
            font-size: 24rpx;
          }
        }

        .amount {
          font-size: 38rpx;
          font-weight: 700;
          color: #07C160;
        }
      }

      .edit-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1001;
        
        .modal-mask {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 600rpx;
          background-color: white;
          border-radius: 20rpx;
          overflow: hidden;
          animation: modalScale 0.3s ease;
          
          .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30rpx;
            border-bottom: 1rpx solid #f0f0f0;
            
            .modal-title {
              font-size: 32rpx;
              font-weight: 600;
              color: #333;
            }
            
            .close-btn {
              font-size: 40rpx;
              color: #999;
              cursor: pointer;
              padding: 10rpx;
              
              &:active {
                color: #666;
              }
            }
          }
        }
        
        .edit-form {
          padding: 30rpx;

          .form-item {
            display: flex;
            align-items: center;
            margin-bottom: 30rpx;

            .label {
              width: 180rpx;
              font-size: 30rpx;
              color: #333;
              font-weight: 500;
            }

            .input {
              flex: 1;
              padding: 20rpx 24rpx;
              border: 1rpx solid #ddd;
              border-radius: 12rpx;
              font-size: 30rpx;
              background-color: #fafafa;
              
              &:focus {
                border-color: #07C160;
                background-color: white;
              }
            }

            .picker {
              flex: 1;
              position: relative;
              z-index: 1002;
              
              .picker-value {
                padding: 20rpx 24rpx;
                border: 1rpx solid #ddd;
                border-radius: 12rpx;
                font-size: 30rpx;
                color: #333;
                background-color: #fafafa;
              }
            }
          }

          .edit-actions {
            display: flex;
            justify-content: space-between;
            gap: 24rpx;
            margin-top: 40rpx;

            .cancel-btn, .save-btn {
              flex: 1;
              padding: 24rpx 0;
              border-radius: 12rpx;
              font-size: 32rpx;
              font-weight: 600;
              border: none;
              cursor: pointer;
              transition: all 0.3s ease;
            }

            .cancel-btn {
              background-color: #f0f0f0;
              color: #666;
              
              &:active {
                background-color: #e8e8e8;
                transform: translateY(1rpx);
              }
            }

            .save-btn {
              background: linear-gradient(135deg, #07C160 0%, #06B456 100%);
              color: white;
              
              &:active {
                transform: translateY(1rpx);
              }
            }
          }
        }
      }
      
      @keyframes modalScale {
        from {
          opacity: 0;
          transform: translate(-50%, -50%) scale(0.8);
        }
        to {
          opacity: 1;
          transform: translate(-50%, -50%) scale(1);
        }
      }

      .action-trigger {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60rpx;
        height: 60rpx;
        border-radius: 50%;
        background-color: #f8f8f8;
        cursor: pointer;
        transition: all 0.3s ease;
        
        &:active {
          background-color: #f0f0f0;
          transform: scale(0.95);
        }
        
        .more-icon {
          font-size: 32rpx;
          color: #666;
          font-weight: bold;
        }
      }
      
      .amount-section {
        text-align: center;
        margin: 20rpx 0;
        
        .amount {
          font-size: 42rpx;
          font-weight: 700;
          color: #07C160;
        }
      }
      
      .action-sheet {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        
        .action-mask {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: rgba(0, 0, 0, 0.5);
        }
        
        .action-content {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background-color: white;
          border-radius: 20rpx 20rpx 0 0;
          padding: 30rpx;
          animation: slideUp 0.3s ease;
          
          .action-item {
            display: flex;
            align-items: center;
            padding: 30rpx 0;
            border-bottom: 1rpx solid #f0f0f0;
            cursor: pointer;
            
            .action-icon {
              font-size: 36rpx;
              margin-right: 20rpx;
            }
            
            .action-text {
              font-size: 32rpx;
              font-weight: 500;
            }
            
            &.edit-action {
              color: #07C160;
            }
            
            &.delete-action {
              color: #FF4D4F;
            }
          }
          
          .action-cancel {
            text-align: center;
            padding: 30rpx 0;
            font-size: 32rpx;
            color: #666;
            cursor: pointer;
            font-weight: 500;
          }
        }
      }
      
      @keyframes slideUp {
        from {
          transform: translateY(100%);
        }
        to {
          transform: translateY(0);
        }
      }

      .flow-stats {
        display: flex;
        justify-content: space-around;
        margin: 25rpx 0;
        padding: 20rpx 0;
        background: linear-gradient(135deg, #f8fff8 0%, #f0f9f4 100%);
        border-radius: 12rpx;

        .stat-item {
          display: flex;
          flex-direction: column;
          align-items: center;
          flex: 1;

          .label {
            color: #666;
            font-size: 26rpx;
            margin-bottom: 10rpx;
            font-weight: 500;
          }

          .value {
            font-size: 30rpx;
            font-weight: 700;

            &.income {
              color: #07C160;
            }

            &.expense {
              color: #FF4D4F;
            }
          }
        }
      }
    }

    .asset-detail-list {
      background-color: white;
      border-radius: 16rpx;
      padding: 30rpx;
      box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.05);

      .date-filter {
        .filter-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20rpx;

          .title {
            font-weight: bold;
            font-size: 32rpx;
          }

          .date-controls {
            display: flex;
            align-items: center;

            .date-picker {
              display: flex;
              align-items: center;
              padding: 16rpx 20rpx;

              .date-value {
                color: #07C160;
                font-size: 28rpx;
                font-weight: 500;
                margin-right: 10rpx;
              }
              .dropdown-icon {
                color: #07C160;
                font-size: 18rpx;
              }
            }

            .reset-btn {
              padding: 12rpx 16rpx;
              border: 1rpx solid #ddd;
              border-radius: 8rpx;
              background-color: #f5f5f5;
              color: #666;
              font-size: 22rpx;
              white-space: nowrap;
            }
          }
        }

        .stats-info {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20rpx;
          padding-bottom: 15rpx;
          border-bottom: 1rpx solid #f5f5f5;

          .count {
            color: #666;
            font-size: 24rpx;
          }

          .flow-stats {
            display: flex;
            gap: 20rpx;

            .flow-income {
              color: #07C160;
              font-size: 24rpx;
            }

            .flow-expense {
              color: #FF4D4F;
              font-size: 24rpx;
            }
          }
        }
      }

      .list-content {
        .detail-item {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 24rpx 0;
          border-bottom: 1rpx solid #f5f5f5;

          .item-left {
            display: flex;
            align-items: center;
            flex: 1;

            .type-icon {
              font-size: 40rpx;
              margin-right: 20rpx;
            }

            .item-info {
              display: flex;
              flex-direction: column;
              
              .category {
                font-size: 28rpx;
                color: #333;
                margin-bottom: 5rpx;
              }
              
              .date {
                color: #666;
                font-size: 24rpx;
              }
            }
          }

          .item-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 160rpx;
            
            .amount {
              font-weight: bold;
              font-size: 30rpx;
              text-align: right;
              
              &.income {
                color: #07C160;
              }
              
              &:not(.income) {
                color: #FF4D4F;
              }
            }
            
            .remark {
              font-size: 20rpx;
              color: #666;
              margin-top: 5rpx;
            }
          }
        }
        
        .empty-state {
          text-align: center;
          padding: 80rpx 0;
          color: #999;
          font-size: 32rpx;
          
          &::before {
            content: "📊";
            font-size: 60rpx;
            display: block;
            margin-bottom: 20rpx;
          }
        }
        
        .load-more-container {
          text-align: center;
          padding: 30rpx 0;
          color: #999;
          font-size: 26rpx;
          
          .loading-text {
            color: #07C160;
          }
          
          .load-more-text {
            color: #666;
          }
          
          .no-more-text {
            color: #999;
          }
        }
      }
    }
  }
}

/* 修复重点：将日期选择弹层样式移出scoped作用域，使用::v-deep */
::v-deep .date-picker-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;

  .popup-mask {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
  }

  .popup-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #fff;
    border-radius: 32rpx 32rpx 0 0;
    padding: 40rpx;
    max-height: 70vh;
    display: flex;
    flex-direction: column;
    animation: slide-up 0.3s ease;
  }
}

@keyframes slide-up {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

::v-deep .popup-content {
  .popup-header {
    flex-shrink: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 30rpx;
    border-bottom: 2rpx solid #f5f5f5;
    margin-bottom: 40rpx;
  }

  .popup-header .popup-title {
    font-size: 36rpx;
    font-weight: 600;
    color: #333;
  }

  .popup-header .close-btn {
    font-size: 48rpx;
    color: #999;
    cursor: pointer;
  }

  .mode-switch {
    flex-shrink: 0;
    display: flex;
    background-color: #f8f9fa;
    border-radius: 40rpx;
    padding: 4rpx;
    margin-bottom: 40rpx;
    width: 240rpx;
    margin-left: auto;
    margin-right: auto;
  }

  .mode-switch .mode-btn {
    flex: 1;
    text-align: center;
    padding: 12rpx 16rpx;
    border-radius: 36rpx;
    font-size: 24rpx;
    font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .mode-switch .mode-btn.active {
    background-color: #07C160;
    color: #fff;
    box-shadow: 0 4rpx 8rpx rgba(7, 193, 96, 0.2);
  }

  .year-selector {
    flex-shrink: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40rpx;
    padding: 0 40rpx;
  }

  .year-selector .year-btn {
    width: 80rpx;
    height: 80rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16rpx;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .year-selector .year-btn:active {
    background-color: #e9ecef;
  }

  .year-selector .current-year {
    font-size: 40rpx;
    font-weight: 600;
    color: #333;
  }

  .month-grid,
  .year-grid {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    height: 560rpx;
  }

  .month-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24rpx;
    padding: 0 20rpx;
  }

  .month-grid .month-item {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 120rpx;
    border-radius: 16rpx;
    background-color: #f8f9fa;
    font-size: 32rpx;
    color: #666;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .month-grid .month-item:active {
    background-color: #e9ecef;
  }

  .month-grid .month-item.active {
    background-color: #07C160;
    color: #fff;
  }

  .year-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24rpx;
    padding: 0 20rpx;
  }

  .year-grid .year-item {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 120rpx;
    border-radius: 16rpx;
    background-color: #f8f9fa;
    font-size: 32rpx;
    color: #666;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .year-grid .year-item:active {
    background-color: #e9ecef;
  }

  .year-grid .year-item.active {
    background-color: #07C160;
    color: #fff;
  }
}
</style>



