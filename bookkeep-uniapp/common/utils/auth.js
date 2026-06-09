/**
 * 认证相关工具函数
 * 用于登录状态检查和路由拦截
 */

/**
 * 检查用户是否已登录
 * @returns {boolean} 是否已登录
 */
export const isLoggedIn = () => {
  const token = uni.getStorageSync('token')
  return !!token
}

/**
 * 检查页面路径是否需要登录
 * @param {string} pagePath - 页面路径
 * @returns {boolean} 是否需要登录
 */
export const requiresLogin = (pagePath) => {
  // 公开页面（不需要登录）
  const publicPages = [
    '/pages/member/login',
    '/pages/member/register'
  ]
  
  // 检查是否是公开页面
  const isPublicPage = publicPages.some(publicPath => 
    pagePath.includes(publicPath) || pagePath === publicPath
  )
  
  return !isPublicPage
}

/**
 * 检查当前页面是否需要登录并处理
 */
export const checkCurrentPageAuth = () => {
  // 获取当前页面路径
  const pages = getCurrentPages()
  if (pages.length === 0) return
  
  const currentPage = pages[pages.length - 1]
  const currentPath = currentPage.route
  
  console.log('当前页面路径:', currentPath)
  
  // 检查是否需要登录
  if (requiresLogin('/pages/' + currentPath) && !isLoggedIn()) {
    console.log('未登录，当前页面需要登录，跳转到登录页')
    
    // 如果是首页，直接跳转
    if (currentPath === 'dashboard/index') {
      uni.reLaunch({
        url: '/pages/member/login'
      })
    } else {
      uni.showToast({
        title: '请先登录',
        icon: 'none',
        duration: 1500
      })
      
      // 延迟跳转，避免Toast被覆盖
      setTimeout(() => {
        uni.redirectTo({
          url: '/pages/member/login'
        })
      }, 1500)
    }
  }
}

/**
 * 设置路由拦截器
 */
export const setupRouteGuard = () => {
  // 需要拦截的路由类型
  const routeTypes = ['navigateTo', 'redirectTo', 'reLaunch', 'switchTab']
  
  routeTypes.forEach(routeType => {
    uni.addInterceptor(routeType, {
      invoke: (e) => {
        console.log(`路由拦截: ${routeType} -> ${e.url}`)
        
        // 检查是否需要登录
        if (requiresLogin(e.url) && !isLoggedIn()) {
          console.log('未登录，跳转到登录页')
          uni.showToast({
            title: '请先登录',
            icon: 'none',
            duration: 1500
          })
          
          // 延迟跳转，避免Toast被覆盖
          setTimeout(() => {
            uni.navigateTo({
              url: '/pages/member/login'
            })
          }, 1500)
          
          return false // 阻止原路由跳转
        }
        
        // 允许路由跳转
        return true
      },
      
      fail: (err) => {
        console.error('路由拦截失败:', err)
      }
    })
  })
  
  // 应用启动时检查当前页面
  setTimeout(() => {
    checkCurrentPageAuth()
  }, 100)
  
  console.log('路由守卫已设置')
}

/**
 * 清除路由拦截器（用于调试或特殊情况）
 */
export const clearRouteGuard = () => {
  const routeTypes = ['navigateTo', 'redirectTo', 'reLaunch']
  
  routeTypes.forEach(routeType => {
    uni.removeInterceptor(routeType)
  })
  
  console.log('路由守卫已清除')
}

export default {
  isLoggedIn,
  requiresLogin,
  setupRouteGuard,
  clearRouteGuard,
  checkCurrentPageAuth
}