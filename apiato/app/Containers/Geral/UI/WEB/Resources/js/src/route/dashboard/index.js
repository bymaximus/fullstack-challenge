var routes = [];

routes.push({
		path: '/',
		redirect: '/dashboard'
	}, {
		path: '/dashboard',
		name: 'dashboard-analytics',
		component: () => import('@/views/DashboardAnalytics.vue'),
		meta: {
			rule: 'admin',
		}
	}
);

Array.prototype.push.apply(routes, require('./seguranca/perfil.js'));
Array.prototype.push.apply(routes, require('./seguranca/usuario.js'));
Array.prototype.push.apply(routes, require('./seguranca/funcionalidade.js'));

Array.prototype.push.apply(routes, require('./cadastro/categoria.js'));
Array.prototype.push.apply(routes, require('./cadastro/subcategoria.js'));
Array.prototype.push.apply(routes, require('./cadastro/produto.js'));

module.exports = {
	path: '',
	component: () => import('@/layouts/main/Main.vue'),
	children: routes
};
