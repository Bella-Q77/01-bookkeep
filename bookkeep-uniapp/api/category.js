/**
 * 分类相关API
 */
import myRequest from '@/common/utils/request';

/**
 * 获取分类列表
 * @param {string} type - 'expense' | 'income' 
 * @returns {Promise} 分类列表Promise
 */
export const getCategories = (type = 'expense', bookId) => {
	const data = {
		"catetype": type
	};
	if (bookId) {
		data.book_id = bookId;
	}
	return myRequest({
		url: `/portalmember/api.BkCategory/getList`,
		method: 'POST',
		data: data
	});
};

/**
 * 创建分类
 * @param {Object} data - 分类数据
 * @returns {Promise} 创建结果Promise
 */
export const addCategory = (data) => {
	return myRequest({
		url: '/portalmember/api.BkCategory/add',
		method: 'POST',
		data
	});
};

/**
 * 更新分类
 * @param {string|number} id - 分类ID
 * @param {Object} data - 分类数据
 * @returns {Promise} 更新结果Promise
 */
export const editCategory = (id, data) => {
	data.id = id;
	return myRequest({
		url: `/portalmember/api.BkCategory/edit`,
		method: 'PUT',
		data
	});
};

/**
 * 删除分类
 * @param {string|number} id - 分类ID
 * @returns {Promise} 删除结果Promise
 */
export const delCategory = (id) => {
	return myRequest({
		url: `/portalmember/api.BkCategory/del`,
		method: 'POST',
		data: {
			'id': id
		}
	});
};

// 导出分类API模块
export default {
	getCategories,
	addCategory,
	editCategory,
	delCategory
};