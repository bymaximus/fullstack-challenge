var routes = [];

routes.push({
	path: '/',
	name: 'shop',
	component: () => import('@/views/apps/shop/Index.vue'),
	meta: {
		pageTitle: 'ZARA',
		rule: 'public',
	}
});
routes.push({
	path: '/item/:item_id',
	name: 'shop-item-detail-view',
	component: () => import('@/views/apps/shop/ItemDetailView.vue'),
	meta: {
		pageTitle: 'ZARA',
		rule: 'public'
	}
});

module.exports = {
	path: '',
	component: () => import('@/layouts/shop/Main.vue'),
	children: routes
};
