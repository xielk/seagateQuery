<template>
	<view class="content">
		<view class="sousuo">
			<input type="text" v-model="vlaue" placeholder="请输入证书编号或者公司名称" />
			<view class="buton" @click="sousuo">
				<image src="../../static/sousuo.png" mode=""></image>
				<view>搜索</view>
			</view>
		</view>
		<view class="result">
			<view class="result-top" v-if="list.length > 0">
				<image src="../../static/user.png" mode=""></image>
				<view>共搜索到<text>{{ list.length }}</text>个经销商</view>
			</view>
			<view class="result-main">
				<view class="result-no" v-if="empty">查询不到记录</view>
				<view class="result-li" v-for="(item,index) in list" :key="index">
					<view class="result-li-top">
						<view class="num">{{ item.cert_id }}</view>
						<view class="name">{{ item.com_name }}</view>
					</view>
					<view class="result-nr">
						<text>授权类型：</text>
						<text>{{ item.com_type }}</text>
					</view>
					<view class="result-nr" v-if="item.shop_name">
						<text>授权店铺：</text>
						<text>{{ item.shop_name.String }}</text>
					</view>
					<view class="time">授权时间：{{ item.auth_time }}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { company } from '../../api/index.js'
	export default {
		data() {
			return {
				vlaue:'',
				list: [],
				empty: true
			}
		},
		onLoad() {

		},
		methods: {
			sousuo() {
				company({ keyword: this.vlaue }).then(res => {
					console.log(res)
					this.list = res
					this.empty  = false
				}).catch(()=>{
					uni.hideToast()
					this.list = []
					this.empty  = true
					uni.showToast({
						icon:'none',
						title:'查询不到记录'
					})
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	page{
		background: #333333;
	}
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		background: #333333;
		padding: 0 38rpx;
		padding-top: 60rpx ;
		padding-bottom: 20rpx;
		box-sizing: border-box;
	}
	.sousuo{
		width: 100%;
		height: 70rpx;
		border-radius: 10rpx;
		overflow: hidden;
		display: flex;
		align-items: center;
		background: #fff;
		input{
			width: 100%;
			font-size: 28rpx;
			padding: 0 30rpx;
			font-weight: 500;
		}
		.buton{
			flex-shrink: 0;
			width: 168rpx;
			height: 100%;
			background: #71b443;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 28rpx;
			image{
				width: 34rpx;
				height: 34rpx;
				margin-right: 12rpx;
			}
		}
	}
	.result{
		width: 100%;
		.result-top{
			display: flex;
			align-items: center;
			margin-top: 98rpx;
			image{
				width: 32rpx;
				height: 42rpx;
				margin-right: 16rpx;
			}
			view{
				font-size: 22rpx;
				color: #a2a2a2;
				text{
					color: #fff;
					margin: 0 12rpx;
				}
			}
		}
		.result-no{
			font-size: 26rpx;
			color: #969696;
			text-align: center;
			padding-top: 10vh;
		}
		.result-main{
			width: 100%;
			min-height: calc(100vh - 300rpx);
			margin-top: 28rpx;
			border-top: 4rpx solid #71b443;
			background: #2a2a2a;
			padding: 34rpx;
			box-sizing: border-box;
			.result-li{
				width: 100%;
				background: #454545;
				padding: 18rpx 26rpx;
				box-sizing: border-box;
				margin-bottom: 16rpx;
				.result-li-top{
					display: flex;
					align-items: center;
					justify-content: space-between;
					.num{
						max-width: 200rpx;
						font-size: 20rpx;
						margin-right: 20rpx;
						color: #73d149;
					}
					.name{
						font-size: 20rpx;
						color: #fff;
					}
				}
				.result-nr{
					font-size: 20rpx;
					color: #969696;
					margin-top: 30rpx;
					margin-bottom: 30rpx;
				}
				.time{
					font-size: 20rpx;
					color: #969696;
				}
			}
		}
	}
</style>
