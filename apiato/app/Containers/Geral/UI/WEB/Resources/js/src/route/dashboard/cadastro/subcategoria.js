let titulo = 'SubCategoria';
let url = 'subcategoria';
let icone = 'fa fa-tags';

module.exports = [
	{
		path: '/cadastro/' + url,
		name: 'cadastro-' + url,
		component: () => import('@/views/apps/cadastro/SubCategoria/Index.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					active: true
				},
			],
			pageTitle: titulo,
			rule: 'admin',
		}
	}, {
		path: '/cadastro/' + url + '/criar',
		name: 'cadastro-' + url + '-criar',
		component: () => import('@/views/apps/cadastro/SubCategoria/CriarRegistro.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Criar',
					active: true
				},
			],
			pageTitle: titulo + ' - Criar',
			rule: 'admin',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/editar/:id',
		name: 'cadastro-' + url + '-editar',
		component: () => import('@/views/apps/cadastro/SubCategoria/EditarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Alterar',
					active: true
				}
			],
			pageTitle: titulo + ' - Alterar',
			rule: 'admin',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/historico/:id',
		name: 'cadastro-' + url + '-historico',
		component: () => import('@/views/apps/cadastro/SubCategoria/Historico/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/historico/:id/:idHistorico',
		name: 'cadastro-' + url + '-historico-visualizar',
		component: () => import('@/views/apps/cadastro/SubCategoria/Historico/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Histórico',
					url: '/cadastro/' + url + '/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido',
		name: 'cadastro-' + url + '-removido',
		component: () => import('@/views/apps/cadastro/SubCategoria/Removido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido/historico/:id',
		name: 'cadastro-' + url + '-removido-historico',
		component: () => import('@/views/apps/cadastro/SubCategoria/HistoricoRemovido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					url: '/cadastro/' + url + '/removido'
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido/historico/:id/:idHistorico',
		name: 'cadastro-' + url + '-removido-historico-visualizar',
		component: () => import('@/views/apps/cadastro/SubCategoria/HistoricoRemovido/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					url: '/cadastro/' + url + '/removido'
				},
				{
					title: 'Histórico',
					url: '/cadastro/' + url + '/removido/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}
];
