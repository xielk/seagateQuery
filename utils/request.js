import {toast, clearStorageSync, getStorageSync, useRouter} from './utils'
import {BASE_URL} from '@/config/index'
import RequestManager from '@/utils/requestManager.js'


const manager = new RequestManager()

const baseRequest = async (url, method, data = {}, loading = true) =>{
	// let requestId = manager.generateId(method, url, data)
	// if(!requestId) {
	// 	console.log('重复请求')
	// }
	// if(!requestId)return false;
	if(getStorageSync('uid')){
		data['uid'] = getStorageSync('uid')
	}
	const header = {}
	header.token = getStorageSync('token') || ''
	return new Promise((reslove, reject) => {
		console.log('BASE_URL', BASE_URL)
		// loading && uni.showLoading({title: 'loading'})
		uni.request({
			url: BASE_URL + url,
			method: method || 'GET',
			header: header,
			timeout: 10000,
			data: data || {},
			complete: ()=>{
				uni.hideLoading()
				// manager.deleteById(requestId)
			},
			success: (successData) => {
				const res = successData.data
				if(res.code === 0){
					// 业务逻辑，自行修改
					reslove(res.data)
				}else{
					if(res.msg !== '暂无数据') {
						toast(res.msg)
					}
					reject(res)
				}
			},
			fail: (msg) => {
				toast('网络连接失败，请稍后重试')
				reject(msg)
			}
		})
	})
}

const request = {};

['options', 'get', 'post', 'put', 'head', 'delete', 'trace', 'connect'].forEach((method) => {
	request[method] = (api, data, loading) => baseRequest(api, method, data, loading)
})

export default request