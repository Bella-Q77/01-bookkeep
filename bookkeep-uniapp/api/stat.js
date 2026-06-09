// 账单统计相关API
import {
	myRequest
} from '@/common/utils/request';

// 获取账本列表
export const getStatInfo = (listtype, data) => {
	data.id = listtype;
	return myRequest({
		url: '/portalmember/api.BkBook/getList',
		method: 'POST',
		data: params
	})
}

// 默认导出所有API方法
export default {
	getStatInfo,
}