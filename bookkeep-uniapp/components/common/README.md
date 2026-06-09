# 通用顶部组件使用说明

## 组件概述

本项目提供了两个通用组件，用于实现跨平台（小程序、H5、App）的统一顶部布局：

1. **NavigationBar.vue** - 顶部导航栏组件
2. **PageContainer.vue** - 页面内容容器组件

## 组件特性

### NavigationBar 组件特性
- ✅ 适配小程序、H5、App平台
- ✅ 自动处理状态栏高度
- ✅ 支持自定义标题、左右按钮
- ✅ 支持返回按钮和自定义事件
- ✅ 支持自定义颜色和样式

### PageContainer 组件特性
- ✅ 自动计算安全区域
- ✅ 适配不同平台的顶部间距
- ✅ 支持自定义内边距
- ✅ 支持插槽布局

## 使用方法

### 基本使用

```vue
<template>
  <page-container>
    <!-- 顶部导航栏 -->
    <navigation-bar 
      slot="navigation"
      title="页面标题"
      :show-back="true"
      back-text="返回"
      right-text="操作"
      @back="handleBack"
      @right-click="handleRightClick"
    />
    
    <!-- 页面内容 -->
    <view class="page-content">
      <!-- 你的页面内容 -->
    </view>
  </page-container>
</template>

<script>
import NavigationBar from '@/components/common/NavigationBar.vue'
import PageContainer from '@/components/common/PageContainer.vue'

export default {
  components: {
    NavigationBar,
    PageContainer
  },
  methods: {
    handleBack() {
      uni.navigateBack()
    },
    handleRightClick() {
      // 处理右侧按钮点击
    }
  }
}
</script>
```

### NavigationBar 组件参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| title | String | '' | 导航栏标题 |
| showBack | Boolean | false | 是否显示返回按钮 |
| backText | String | '' | 返回按钮文字 |
| leftText | String | '' | 左侧自定义文字 |
| leftIcon | String | '' | 左侧自定义图标 |
| rightText | String | '' | 右侧自定义文字 |
| rightIcon | String | '' | 右侧自定义图标 |
| backgroundColor | String | '#07C160' | 背景颜色 |
| color | String | '#ffffff' | 文字颜色 |
| fixed | Boolean | true | 是否固定定位 |
| height | String/Number | '' | 自定义高度 |

### NavigationBar 组件事件

| 事件名 | 参数 | 说明 |
|--------|------|------|
| back | - | 返回按钮点击事件 |
| left-click | - | 左侧按钮点击事件 |
| right-click | - | 右侧按钮点击事件 |

### PageContainer 组件参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| safeArea | Boolean | true | 是否使用安全区域 |
| paddingTop | String/Number | '' | 自定义顶部间距 |
| paddingBottom | String/Number | '' | 自定义底部间距 |
| backgroundColor | String | '#ffffff' | 背景颜色 |

## 平台适配说明

### 小程序环境
- 自动获取状态栏高度
- 导航栏高度固定为44px
- 内容自动下移避免被导航栏遮挡

### H5环境
- 状态栏高度为0（浏览器处理）
- 使用浏览器默认导航栏
- 内容从页面顶部开始

### App环境
- 自动获取状态栏高度
- 支持全屏或自定义导航栏
- 处理安全区域

## 示例代码

### 带返回按钮的页面
```vue
<template>
  <page-container>
    <navigation-bar 
      slot="navigation"
      title="详情页"
      :show-back="true"
      back-text="返回"
    />
    
    <view class="content">
      <!-- 页面内容 -->
    </view>
  </page-container>
</template>
```

### 带左右按钮的页面
```vue
<template>
  <page-container>
    <navigation-bar 
      slot="navigation"
      title="设置"
      left-text="取消"
      right-text="保存"
      @left-click="handleCancel"
      @right-click="handleSave"
    />
    
    <view class="content">
      <!-- 设置表单 -->
    </view>
  </page-container>
</template>
```

### 自定义样式的页面
```vue
<template>
  <page-container :padding-top="0" :safe-area="false">
    <navigation-bar 
      slot="navigation"
      title="首页"
      :show-back="false"
      right-text="统计"
      background-color="#ffffff"
      color="#333333"
      @right-click="navigateToStatistics"
    />
    
    <view class="home-content">
      <!-- 首页内容 -->
    </view>
  </page-container>
</template>
```

## 注意事项

1. **NavigationBar组件必须放在PageContainer的navigation插槽中**
2. **PageContainer会自动处理不同平台的间距，无需手动设置**
3. **在小程序环境中，建议使用pages.json配置navigationStyle为custom**
4. **组件已处理点击事件防抖，无需额外处理**

## 兼容性

- ✅ 微信小程序
- ✅ H5浏览器
- ✅ App（iOS/Android）
- ✅ 支付宝小程序
- ✅ 百度小程序
- ✅ 字节跳动小程序
- ✅ QQ小程序

通过使用这两个组件，可以确保项目在所有平台上都有统一的顶部布局体验。