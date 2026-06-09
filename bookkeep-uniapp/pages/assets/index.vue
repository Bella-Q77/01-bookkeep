<template>
  <view class="assets-page">
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
    
    <!-- 资产统计卡片 -->
    <view class="stats-card">
      <view class="stats-row highlight">
        <view class="net-worth-col">
          <text class="title">净资产</text>
          <text class="amount">¥ {{netWorth}}</text>
        </view>
      </view>
      
      <view class="stats-row">
        <view class="stats-item">
          <text class="title">总资产</text>
          <text class="amount">¥{{totalAmount}}</text>
        </view>
        
        <view class="stats-item">
          <text class="title">总负债</text>
          <text class="amount">¥{{totalDebt}}</text>
        </view>
      </view>
    </view>

    <!-- 资产列表 -->
	<view class="asset-box">
    <view class="asset-list">
      <view class="list-header">
        <text class="title">资产账户</text>
        <text class="add-btn" @click="showAddForm = true">+ 添加</text>
      </view>

      <view class="list-content">
        <view 
          class="asset-item" 
          v-for="(item, index) in assets" 
          :key="index"
          @click="navigateToDetail(item)"
        >
          <view class="asset-info">
            <text class="icon">{{item.icon}}</text>
            <text class="name">{{item.name}}</text>
          </view>
          <text class="amount">¥ {{item.amount}}</text>
        </view>
      </view>
    </view>

	</view>

    <!-- 添加资产表单 (默认隐藏) -->
    <view class="add-form" v-if="showAddForm">
      <view class="form-header">
        <text>添加资产账户</text>
        <text class="close-btn" @click="showAddForm = false">×</text>
      </view>

      <view class="form-content">
        <view class="form-item">
          <text class="label">资产名称</text>
          <input type="text" v-model="newAsset.name" placeholder="例如: 招商银行卡" />
        </view>

        <view class="form-item">
          <text class="label">资产类型</text>
          <picker mode="selector" :range="assetTypes" @change="selectAssetType">
            <view class="picker">{{newAsset.type || '请选择资产类型'}}</view>
          </picker>
        </view>

        <view class="form-item">
          <text class="label">金额</text>
          <input type="number" v-model="newAsset.amount" placeholder="0.00" />
        </view>

        <button class="save-btn" @click="addAsset" :disabled="addingLoading">
          {{ addingLoading ? '保存中...' : '保存' }}
        </button>
      </view>
    </view>

    <!-- 半透明遮罩层 -->
    <view class="mask" v-if="showAddForm" @click="showAddForm = false"></view>
  </view>
</template>

<script>
import assetsApi from '@/api/assets'

// 使用 uni-app 的 easycom 自动注册组件，无需手动 import
export default {
  data() {
    return {
      showAddForm: false,
      totalAmount: '0.00',
      totalDebt: '0.00',
      netWorth: '0.00',
      assets: [],
      assetTypes: ['现金', '支付宝', '微信', '投资账户', '银行卡', '信用卡', '贷款', '其它负债'],
      assetTypeMap: {}, // 存储类型ID和名称的映射
      newAsset: {
        name: '',
        type: '',
        type_id: '', // 添加类型ID字段
        amount: ''
      },
      loading: false,
      addingLoading: false,

    }
  },
  onLoad() {
    this.loadAssetsData()
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    async loadAssetsData() {
      this.loading = true
      try {
        // 并行获取资产数据和资产类型
        const [assetsRes, typesRes] = await Promise.all([
          assetsApi.getAssetsData(),
          assetsApi.getAssetsType()
        ])
        
        if (assetsRes.code === 1 || assetsRes.code === 200) {
          const data = assetsRes.data || {}
          this.totalAmount = data.total_assets || '0.00'
          this.totalDebt = data.negative_assets || '0.00'
          this.netWorth = data.net_assets || '0.00'
          
          // 处理资产列表数据
          const accountList = data.account_list || []
          this.assets = accountList.map(asset => ({
            id: asset.id,
            name: asset.name,
            type: this.getAccountTypeName(asset.account_type),
            amount: asset.balance,
            icon: this.getAssetIcon(this.getAccountTypeName(asset.account_type))
          }))

          // 保存到Session中，下次直接使用
					uni.setStorageSync('assetsAccountListData', this.assets);

        } else {
          throw new Error(assetsRes.message || assetsRes.msg || '获取资产数据失败')
        }
        
        // 处理资产类型数据
        if (typesRes.code === 1 || typesRes.code === 200) {
          const typeData = typesRes.data || {}
          console.log('typeData', typeData)
          
          // 存储类型ID和名称的映射关系
          this.assetTypeMap = typeData
          
          // 将对象转换为数组，提取类型名称，过滤空值和重复项
          const allTypes = Object.values(typeData).map(type => type.name || '')
          this.assetTypes = [...new Set(allTypes.filter(name => name))]
        } else {
          // 如果获取类型失败，使用默认类型
          this.assetTypes = ['现金', '银行卡', '支付宝', '微信钱包', '投资账户', '其他']
        }
        
      } catch (error) {
        uni.showToast({
          title: error.message || '加载资产数据失败',
          icon: 'none'
        })
        // 设置默认数据（与API返回的数据一致）
        this.assetTypes = ['现金', '支付宝', '微信', '投资账户', '银行卡', '信用卡', '贷款', '其它负债']
      } finally {
        this.loading = false
      }
    },
    
    // 根据账户类型ID获取类型名称
    getAccountTypeName(typeId) {
      const typeMap = {
        1: '现金',
        2: '支付宝',
        3: '微信钱包',
        4: '银行卡',
        5: '投资账户',
        6: '信用卡',
        7: '信用卡',
        8: '其他'
      }
      return typeMap[typeId] || '其他'
    },
    
    selectAssetType(e) {
      const selectedIndex = e.detail.value
      this.newAsset.type = this.assetTypes[selectedIndex]
      
      // 根据选择的类型名称找到对应的类型ID
      const typeName = this.newAsset.type
      for (const [typeId, typeInfo] of Object.entries(this.assetTypeMap)) {
        if (typeInfo.name === typeName) {
          this.newAsset.type_id = typeId
          break
        }
      }
    },
    
    async addAsset() {
      if (!this.newAsset.name || !this.newAsset.type || !this.newAsset.amount) {
        uni.showToast({
          title: '请填写完整信息',
          icon: 'none'
        })
        return
      }

      this.addingLoading = true
      try {

        const params = {
          name: this.newAsset.name,
          account_type: this.newAsset.type_id, // 提交类型ID
          amount: this.newAsset.amount
        }
        
        const res = await assetsApi.addAsset(params)
        
        if (res.code === 1 || res.code === 200) {
          const newAssetData = params
          // 为新增的资产添加图标
          newAssetData.icon = this.getAssetIcon(newAssetData.type || this.newAsset.type)
          this.assets.push(newAssetData)
          this.showAddForm = false
          this.newAsset = { name: '', type: '', type_id: '', amount: '' }
          
          uni.showToast({
            title: res.message || res.msg || '资产添加成功',
            icon: 'success'
          })
        } else {
          throw new Error(res.message || res.msg || '添加资产失败')
        }
      } catch (error) {
        uni.showToast({
          title: error.message || '添加资产失败',
          icon: 'none'
        })
      } finally {
        this.addingLoading = false
      }
    },
    navigateToDetail(asset) {
      uni.navigateTo({
        url: `/pages/assets/detail?id=${asset.id}`
      })
    },
    

    getAssetIcon(type) {
      const icons = {
        '现金': '💰',
        '银行卡': '💳',
        '支付宝': '📱',
        '微信钱包': '💬',
        '投资账户': '📈',
        '信用卡': '💳',
        '其他': '🏦'
      }
      return icons[type] || '🏦'
    }
  }
}
</script>

<style scoped>
.assets-page {
  padding: 0 0 20px 0;
  position: relative;
}



.assets-page .stats-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 15px;
  margin: 10px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  color: white;
  position: relative;
  overflow: hidden;
}



.assets-page .stats-card .stats-row {
  display: flex;
  padding: 8px 0;
}


.assets-page .stats-card .stats-row.highlight .net-worth-col {
  flex: 1;
  text-align: left;
}

.assets-page .stats-card .stats-row.highlight .net-worth-col .title {
  color: rgba(255, 255, 255, 0.9);
  font-size: 14px;
  display: block;
  margin-bottom: 4px;
  font-weight: 500;
}

.assets-page .stats-card .stats-row.highlight .net-worth-col .amount {
  color: white;
  font-size: 20px;
  font-weight: bold;
  display: block;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.assets-page .stats-card .stats-item {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
}

.assets-page .stats-card .stats-item .title {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 500;
}

.assets-page .stats-card .stats-item .amount {
  font-size: 14px;
  font-weight: bold;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.assets-page .asset-box {
  padding: 10px;
}

.assets-page .asset-list {
  background-color: white;
  border-radius: 10px;
  padding: 15px;
}

.assets-page .asset-list .list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.assets-page .asset-list .list-header .title {
  font-size: 16px;
  font-weight: bold;
}

.assets-page .asset-list .list-header .add-btn {
  color: #07C160;
  font-size: 14px;
}

.assets-page .asset-list .asset-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.assets-page .asset-list .asset-item .asset-info {
  display: flex;
  align-items: center;
  flex: 1;
}

.assets-page .asset-list .asset-item .asset-info .icon {
  font-size: 20px;
  margin-right: 10px;
}

.assets-page .asset-list .asset-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.assets-page .asset-list .asset-item .asset-info {
  display: flex;
  align-items: center;
  flex: 1;
}

.assets-page .asset-list .asset-item .asset-info .icon {
  font-size: 20px;
  margin-right: 10px;
}



.assets-page .add-form {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: white;
  border-radius: 15px 15px 0 0;
  padding: 20px;
  z-index: 100;
}

.assets-page .add-form .form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.assets-page .add-form .form-header .close-btn {
  font-size: 24px;
  color: #999;
}

.assets-page .add-form .form-item {
  margin-bottom: 15px;
}

.assets-page .add-form .form-item .label {
  display: block;
  margin-bottom: 8px;
  color: #666;
}

.assets-page .add-form .form-item input,
.assets-page .add-form .form-item .picker {
  width: 100%;
  padding: 12px;
  border: 1px solid #eee;
  border-radius: 8px;
}

.assets-page .add-form .save-btn {
  margin-top: 10px;
  background-color: #07C160;
  color: white;
  border-radius: 20px;
}

.assets-page .mask {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 99;
}
</style>