<template>
  <view class="asset-edit-container">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="编辑账户" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    
    <view class="edit-content">
      <!-- 账户基本信息卡片 -->
      <view class="asset-card">
        <view class="card-header">
          <text class="card-title">账户信息</text>
        </view>
        
        <!-- 编辑表单 -->
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
          
          <view class="form-item">
            <text class="label">备注</text>
            <textarea class="textarea" v-model="editForm.remark" placeholder="请输入备注信息" />
          </view>
          
          <view class="form-item">
            <text class="label">币种</text>
            <picker 
              class="picker" 
              @change="onCurrencyChange" 
              :value="editForm.currency" 
              :range="currencies" 
              range-key="name"
            >
              <view class="picker-value">{{editForm.currency || '请选择币种'}}</view>
            </picker>
          </view>
        </view>
      </view>
      
      <!-- 操作按钮 -->
      <view class="action-buttons">
        <button class="cancel-btn" @click="goBack">取消</button>
        <button class="save-btn" @click="saveEdit">保存</button>
      </view>
    </view>
  </view>
</template>

<script>
import assetsApi from '@/api/assets'

export default {
  data() {
    return {
      assetId: '',
      editForm: {
        name: '',
        type: '',
        amount: 0,
        remark: '',
        currency: '人民币'
      },
      assetTypes: [],
      currencies: [
        { name: '人民币', value: '人民币' },
        { name: '美元', value: '美元' },
        { name: '欧元', value: '欧元' },
        { name: '日元', value: '日元' },
        { name: '其他', value: '其他' }
      ]
    }
  },
  
  async onLoad(options) {
    // 从路由参数获取资产ID
    this.assetId = options.id
    
    // 加载资产类型列表
    await this.loadAssetTypes()
    
    // 加载资产详情
    await this.loadAssetDetail(this.assetId)
  },
  
  methods: {
    // 加载资产类型列表
    async loadAssetTypes() {
      try {
        const res = await assetsApi.getAssetsType()
        if (res.code === 1 || res.code === 200) {
          // 处理API返回的对象格式数据
          const data = res.data || {}
          // 将对象转换为数组格式，用于picker组件
          this.assetTypes = Object.keys(data).map(key => {
            const item = data[key]
            return {
              id: parseInt(key),
              name: item.name,
              is_debt: item.is_debt,
              icon: item.icon,
              color: item.color
            }
          })
        } else {
          throw new Error(res.message || res.msg || '获取资产类型失败')
        }
      } catch (error) {
        console.error('加载资产类型失败:', error)
        // 设置默认资产类型，与API返回的数据结构对应
        this.assetTypes = [
          { id: 1, name: '现金', is_debt: '0' },
          { id: 2, name: '支付宝', is_debt: '0' },
          { id: 3, name: '微信', is_debt: '0' },
          { id: 4, name: '投资账户', is_debt: '0' },
          { id: 5, name: '银行卡', is_debt: '0' },
          { id: 6, name: '信用卡', is_debt: '1' },
          { id: 7, name: '贷款', is_debt: '1' },
          { id: 8, name: '其它负债', is_debt: '1' }
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
          
          this.editForm = {
            name: accountInfo.name || '',
            type: this.getAssetTypeName(accountInfo.account_type),
            amount: parseFloat(accountInfo.balance) || 0,
            remark: accountInfo.remark || '',
            currency: accountInfo.currency || '人民币'
          }
        } else {
          throw new Error(res.message || res.msg || '获取资产详情失败')
        }
      } catch (error) {
        console.error('加载资产详情失败:', error)
        uni.showToast({
          title: error.message || '加载资产详情失败',
          icon: 'none'
        })
      }
    },
    
    // 根据账户类型ID获取类型名称
    getAssetTypeName(typeId) {
      // 从资产类型列表中查找对应的名称
      const typeItem = this.assetTypes.find(item => item.id === parseInt(typeId))
      return typeItem ? typeItem.name : '其他'
    },
    
    // 根据账户类型名称获取类型ID
    getAssetTypeId(typeName) {
      // 从资产类型列表中查找对应的ID
      const typeItem = this.assetTypes.find(item => item.name === typeName)
      return typeItem ? typeItem.id : 8 // 默认返回"其他负债"的ID
    },
    
    // 类型选择变化
    onTypeChange(e) {
      const index = e.detail.value
      if (this.assetTypes[index]) {
        this.editForm.type = this.assetTypes[index].name
      }
    },
    
    // 币种选择变化
    onCurrencyChange(e) {
      const index = e.detail.value
      if (this.currencies[index]) {
        this.editForm.currency = this.currencies[index].name
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
        
        // 获取账户类型ID
        const typeId = this.getAssetTypeId(this.editForm.type)
        
        // 调用修改API
        const res = await assetsApi.updateAsset({
          id: this.assetId,
          name: this.editForm.name,
          account_type: typeId,
          balance: parseFloat(this.editForm.amount) || 0,
          remark: this.editForm.remark,
          currency: this.editForm.currency
        })
        
        if (res.code === 1 || res.code === 200) {
          uni.showToast({
            title: '修改成功',
            icon: 'success'
          })
          
          // 返回上一页
          setTimeout(() => {
            uni.navigateBack()
          }, 1500)
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
    
    // 返回上一页
    goBack() {
      uni.navigateBack()
    }
  }
}
</script>

<style scoped>
.asset-edit-container {
  min-height: 100vh;
  background-color: #f5f5f5;
  padding: 0 0 40rpx 0;
}

.asset-edit-container .edit-content {
  padding: 40rpx 30rpx 0;
}

.asset-edit-container .asset-card {
  background-color: white;
  border-radius: 20rpx;
  padding: 0 30rpx;

}

.asset-edit-container .asset-card .card-header {
  padding: 30rpx 0;
  border-bottom: 2rpx solid #f5f5f5;
}

.asset-edit-container .asset-card .card-header .card-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
}

.asset-edit-container .edit-form {
  padding: 0;
}

.asset-edit-container .edit-form .form-item {
  padding: 30rpx 0;
  border-bottom: 2rpx solid #f5f5f5;
  display: flex;
  align-items: center;
}

.asset-edit-container .edit-form .form-item:last-child {
  border-bottom: none;
}

.asset-edit-container .edit-form .form-item .label {
  width: 160rpx;
  font-size: 30rpx;
  color: #333;
}

.asset-edit-container .edit-form .form-item .input,
.asset-edit-container .edit-form .form-item .textarea {
  flex: 1;
  font-size: 30rpx;
  text-align: right;
  border: none;
  background: transparent;
  padding: 0;
}

.asset-edit-container .edit-form .form-item .textarea {
  height: 120rpx;
  resize: none;
  text-align: left;
}

.asset-edit-container .edit-form .form-item .picker {
  flex: 1;
}

.asset-edit-container .edit-form .form-item .picker .picker-value {
  text-align: right;
  font-size: 30rpx;
  color: #333;
  padding: 0;
  border: none;
  background: transparent;
}

.asset-edit-container .action-buttons {
  padding: 15px 15px;
  display: flex;
  justify-content: space-between;
  gap: 20rpx;
}

.asset-edit-container .action-buttons .cancel-btn,
.asset-edit-container .action-buttons .save-btn {
  flex: 1;
  border-radius: 40rpx;
  font-size: 32rpx;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.asset-edit-container .action-buttons .cancel-btn {
  background-color: #f0f0f0;
  color: #666;
}

.asset-edit-container .action-buttons .save-btn {
  background-color: #07C160;
  color: white;
}

.asset-edit-container .action-buttons .cancel-btn:active,
.asset-edit-container .action-buttons .save-btn:active {
  transform: scale(0.98);
}
</style>